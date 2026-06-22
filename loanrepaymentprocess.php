<?php 
include 'dbconnect.php';

// Function to process single transaction
function processSingleTransaction($staffNo, $loanIDs, $payments, $receipt) {
    global $con;
    
    $receiptValue = $receipt === '' ? 'NULL' : "'$receipt'";
    $months = ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];
    $currentMonth = $months[date('n') - 1];

    // Process savings
    $sql = "SELECT modahSyer FROM savingtype WHERE staffNo = $staffNo";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $currentModahSyer = $row['modahSyer'];
    
    // Determine where to allocate the 50 ringgit
    $toModahSyer = $currentModahSyer < 300 ? 50 : 0;
    $toSimpananTetap = $currentModahSyer >= 300 ? 50 : 0;
    
    // Update savings
    $sql = "UPDATE savingtype SET 
            modahSyer = modahSyer + $toModahSyer,
            simpananTetap = simpananTetap + $toSimpananTetap,
            sumbanganTabung = sumbanganTabung + 5
            WHERE staffNo = $staffNo";
    if (!mysqli_query($con, $sql)) {
        return false;
    }

    // Record saving transaction
    $savingDescription = "Potongan Gaji pada bulan " . $currentMonth;
    $sqlSaving = "INSERT INTO saving (savingAmount, savingDate, savingDesc, savingType, savingReceipt, staffNo)
            VALUES (55, NOW(), '$savingDescription', 'Potongan Gaji', $receiptValue, $staffNo)";
    if (!mysqli_query($con, $sqlSaving)) {
        return false;
    }

    // Process loan repayments
    $loanIDArray = explode(',', $loanIDs);
    $paymentArray = explode(',', $payments);
    
    foreach ($loanIDArray as $i => $loanID) {
        if (!empty($loanID)) {
            $payment = floatval($paymentArray[$i]);

            $sql = "SELECT loanAmount, (loanAmount - IFNULL(SUM(repaymentAmount), 0)) AS remainingLoan 
                    FROM loan 
                    LEFT JOIN loanrepayment ON loan.loanID = loanrepayment.loanID 
                    WHERE loan.loanID = $loanID 
                    GROUP BY loan.loanID";
            $result = mysqli_query($con, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $remainingLoan = $row['remainingLoan'];

                if ($payment <= $remainingLoan) {
                    $loanDescription = "Potongan Gaji pada bulan " . $currentMonth;
                    $sqlInsert = "INSERT INTO loanrepayment (loanID, repaymentAmount, repaymentDate, repaymentType, repaymentDesc, repaymentReceipt)
                                VALUES ($loanID, $payment, NOW(), 'Potongan Gaji', '$loanDescription', $receiptValue)";
                    
                    if (!mysqli_query($con, $sqlInsert)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }
    
    return true;
}

// Check if it's a batch process
if (isset($_GET['staffNos'])) {
    $staffNos = explode(',', $_GET['staffNos']);
    $allLoanIDs = explode('|', $_GET['loanIDs']);
    $allPayments = explode('|', $_GET['payments']);
    
    // Check if receipt number is provided for batch
    if (!isset($_GET['receipt'])) {
        echo "<script>
            var receiptNumber = prompt('Masukkan nombor resit (tekan Cancel jika tiada):');
            if (receiptNumber !== null) {
                window.location.href = window.location.pathname + 
                    '?staffNos=" . urlencode($_GET['staffNos']) . 
                    "&loanIDs=" . urlencode($_GET['loanIDs']) . 
                    "&payments=" . urlencode($_GET['payments']) . 
                    "&receipt=' + encodeURIComponent(receiptNumber);
            } else {
                window.location.href = window.location.pathname + 
                    '?staffNos=" . urlencode($_GET['staffNos']) . 
                    "&loanIDs=" . urlencode($_GET['loanIDs']) . 
                    "&payments=" . urlencode($_GET['payments']) . 
                    "&receipt=';
            }
        </script>";
        exit();
    }
    
    $receipt = isset($_GET['receipt']) ? $_GET['receipt'] : '';
    $success = true;
    
    // Process each transaction in the batch
    for ($i = 0; $i < count($staffNos); $i++) {
        $staffNo = intval($staffNos[$i]);
        $success = $success && processSingleTransaction($staffNo, $allLoanIDs[$i], $allPayments[$i], $receipt);
        
        if (!$success) {
            break;
        }
    }
    
    if ($success) {
        echo "<script>
                alert('Semua transaksi potongan gaji dan simpanan berjaya direkod!');
                window.location.href = 'loanrepayment.php';
              </script>";
    } else {
        echo "<script>
                alert('Ralat semasa memproses pembayaran berkelompok. Sila cuba lagi.');
                window.location.href = 'loanrepayment.php';
              </script>";
    }
}
// Original single transaction processing
else if (isset($_GET['staffNo']) && isset($_GET['loanIDs']) && isset($_GET['payments'])) {
    // Check if receipt number is provided
    if (!isset($_GET['receipt'])) {
        echo "<script>
            var receiptNumber = prompt('Masukkan nombor resit (tekan Cancel jika tiada):');
            if (receiptNumber !== null) {
                window.location.href = window.location.pathname + 
                    '?loanIDs=" . urlencode($_GET['loanIDs']) . 
                    "&payments=" . urlencode($_GET['payments']) . 
                    "&staffNo=" . urlencode($_GET['staffNo']) . 
                    "&receipt=' + encodeURIComponent(receiptNumber);
            } else {
                window.location.href = window.location.pathname + 
                    '?loanIDs=" . urlencode($_GET['loanIDs']) . 
                    "&payments=" . urlencode($_GET['payments']) . 
                    "&staffNo=" . urlencode($_GET['staffNo']) . 
                    "&receipt=';
            }
        </script>";
        exit();
    }

    $staffNo = intval($_GET['staffNo']);
    $receipt = $_GET['receipt'];
    
    $success = processSingleTransaction($staffNo, $_GET['loanIDs'], $_GET['payments'], $receipt);
    
    if ($success) {
        echo "<script>
                alert('Potongan Gaji dan Simpanan berjaya direkod!');
                window.location.href = 'loanrepayment.php';
              </script>";
    } else {
        echo "<script>
                alert('Ralat semasa memproses pembayaran. Sila cuba lagi.');
                window.location.href = 'loanrepayment.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Maklumat pembayaran tidak lengkap.');
            window.location.href = 'loanrepayment.php';
          </script>";
}
?>