<?php
// transactionprocess.php
session_start();
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $staffNo = filter_input(INPUT_POST, 'staffNo', FILTER_VALIDATE_INT);
    $transactionType = filter_input(INPUT_POST, 'transactionType', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $paymentType = filter_input(INPUT_POST, 'paymentType', FILTER_SANITIZE_STRING);
    $receiptNo = isset($_POST['receiptNo']) ? mysqli_real_escape_string($con, $_POST['receiptNo']) : null;

    // Additional fields for savings
    $savingsType = isset($_POST['savingsType']) ? mysqli_real_escape_string($con, $_POST['savingsType']) : null;
    $otherSavings = isset($_POST['otherSavings']) ? mysqli_real_escape_string($con, $_POST['otherSavings']) : null;
    
    // For loan transactions
    $loanID = isset($_POST['loanID']) ? filter_input(INPUT_POST, 'loanID', FILTER_VALIDATE_INT) : null;

    $errors = [];
    if (!$staffNo) $errors[] = "Invalid Staff Number";
    if (!$transactionType) $errors[] = "Transaction Type is required";
    if (!$amount || $amount <= 0) $errors[] = "Invalid Amount";
    if (!$paymentType) $errors[] = "Payment Type is required";

    if (empty($errors)) {
        try {
            mysqli_begin_transaction($con);

            if ($transactionType === 'pinjaman' && $loanID) {
                // Handle loan repayment
                $repayment_query = "INSERT INTO loanrepayment 
                                  (repaymentAmount, repaymentDate, repaymentDesc, 
                                   repaymentType, loanID, repaymentReceipt) 
                                  VALUES 
                                  ($amount, CURDATE(), '$description', 
                                   '$paymentType', $loanID, '$receiptNo')";
                
                if (!mysqli_query($con, $repayment_query)) {
                    throw new Exception("Failed to record loan repayment: " . mysqli_error($con));
                }
            } 
            else if ($transactionType === 'simpanan') {
                // Handle savings transaction
                $saving_query = "INSERT INTO saving 
                               (savingAmount, savingDate, savingDesc, savingType, 
                                savingReceipt, staffNo) 
                               VALUES 
                               ($amount, CURDATE(), '$description', '$paymentType', 
                                '$receiptNo', $staffNo)";
                
                if (!mysqli_query($con, $saving_query)) {
                    throw new Exception("Failed to record saving: " . mysqli_error($con));
                }

                // Update savingtype table if needed
                if ($savingsType === 'modahSyer') {
                    $update_query = "UPDATE savingtype SET modahSyer = modahSyer + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                } else if ($savingsType === 'modalYuran') {
                    $update_query = "UPDATE savingtype SET modalYuran = modalYuran + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                } else if ($savingsType === 'wangDepositAnggota') {
                    $update_query = "UPDATE savingtype SET wangDepositAnggota = wangDepositAnggota + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                } else if ($savingsType === 'sumbanganTabung') {
                    $update_query = "UPDATE savingtype SET sumbanganTabung = sumbanganTabung + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                } else if ($savingsType === 'simpananTetap') {
                    $update_query = "UPDATE savingtype SET simpananTetap = simpananTetap + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                } else if ($savingsType === 'lainLain') {
                    $update_query = "UPDATE savingtype SET lainLain = lainLain + $amount WHERE staffNo = $staffNo";
                    if (!mysqli_query($con, $update_query)) {
                        throw new Exception("Failed to update saving type: " . mysqli_error($con));
                    }
                }   
            }

            mysqli_commit($con);
            header("Location: transaction.php?status=success&message=" . urlencode("Transaksi berjaya!"));
            exit();

        } catch (Exception $e) {
            mysqli_rollback($con);
            header("Location: transaction.php?status=error&message=" . urlencode("Ralat: " . $e->getMessage()));
            exit();
        }
    } else {
        $errorMessage = implode(", ", $errors);
        header("Location: transaction.php?status=error&message=" . urlencode($errorMessage));
        exit();
    }
} else {
    header("Location: transaction.php");
    exit();
}
?>