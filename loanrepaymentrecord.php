<?php
include 'dbconnect.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to access this page.'); window.location.href='login.php';</script>";
    exit;
}

$userEmail = $_SESSION['email'];
$sqlStaffNo = "SELECT staffNo FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sqlStaffNo);
$staffNo = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['staffNo'] : null;

if (!$staffNo) {
    echo "<p>No staff found for this user.</p>";
    exit;
}

// Get the selected month and year, default to current if not set
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('n');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$selectedLoanID = isset($_POST['loan_id']) ? $_POST['loan_id'] : null;
$showAll = isset($_POST['show_all']);

$sqlLoans = "SELECT l.loanID, l.loanAmount, l.monthlyPayment, lt.loanName,
                    SUM(lr.repaymentAmount) AS totalRepaid, 
                    (l.loanAmount - IFNULL(SUM(lr.repaymentAmount), 0)) AS remainingLoan
             FROM loan l
             LEFT JOIN loanrepayment lr ON l.loanID = lr.loanID
             LEFT JOIN loantype lt ON lt.loanType = l.loanType
             WHERE l.staffNo = '$staffNo' 
             GROUP BY l.loanID";
$loansResult = mysqli_query($con, $sqlLoans);

$sqlYears = "SELECT DISTINCT YEAR(repaymentDate) as year FROM loanrepayment ORDER BY year DESC";
$resultYears = mysqli_query($con, $sqlYears);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekod Pembayaran Balik Pinjaman</title>
    <style>
        :root {
            --primary: #D6536D;
            --secondary: #E43D12;
            --accent: #EBE9E1;
            --yellow: #EFB11D;
            --light-pink: #FFA2B6;
            --dark-blue: #4150F1;
            --blue: #6686F6;
            --green: #1A8A16;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

        .page-header {
            background-color: var(--blue);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-align: left;
            margin: 0;
            padding-left: 4.5rem;
        }

        .container {
            max-width: 1450px;
            margin: 0 auto;
            padding: 2rem;
        }

        .loan-overview {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .loan-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .progress-container {
            width: 100%;
            background-color: #F6536A;
            height: 20px;
            border-radius: 13px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--green);
            transition: width 0.5s ease-in-out;
        }

        .loan-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .loan-id {
            font-weight: 600;
            color: var(--primary);
            width: 100%;
            text-align: left;
        }

        .loan-details p {
            margin: 0.5rem 0;
            color: #495057;
        }

        .report-form {
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 15px;
            justify-content: center;
            align-items: center; 
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .report-form select, 
        .report-form button {
            padding: 0.5rem 1rem;
            border-radius: 15px;
            border: 1px solid #dee2e6;
        }

        .report-form select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(102, 134, 246, 0.2);
        }

        .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            margin: 0.2rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--blue);
            border-color: var(--blue);
            color: white;
        }

        .btn-secondary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-secondary:hover {
            background-color: #4641a4; 
            border-color: #4641a4; 
        }

        .report-form button {
            background-color: var(--blue);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .report-form button:hover {
            background-color: var(--dark-blue);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .transaction-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .transaction-table thead {
            background-color: #4150F1;
            color: white;
        }

        .transaction-table th, 
        .transaction-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .transaction-table tbody tr:hover {
            background-color: rgba(102, 134, 246, 0.05);
        }

        .loan-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .summary-card {
            background-color: rgba(102, 134, 246, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
        }

        .summary-card h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .summary-card p {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-blue);
        }

        .no-loans {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .button-container {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php
    if ($loansResult && mysqli_num_rows($loansResult) > 0) {
        while ($loan = mysqli_fetch_assoc($loansResult)) {
            $loanID = $loan['loanID'];
            $loanName = $loan['loanName'];
            $loanAmount = $loan['loanAmount'];
            $monthlyPayment = $loan['monthlyPayment'];
            $totalRepaid = $loan['totalRepaid'] ?? 0;
            $remainingLoan = $loan['remainingLoan'] ?? $loanAmount;
            $progressPercentage = ($totalRepaid / $loanAmount) * 100;
    ?>
            <div class="loan-card" id="loan_<?php echo $loanID; ?>">
                <div class="loan-header">
                    <div class="loan-id">ID Pinjaman: <?php echo $loanID; ?></div>
                    <div class="loan-details">
                        <p>Jenis Pinjaman: <?php echo $loanName; ?></p>
                        <p>Bayaran Bulanan: RM <?php echo number_format($monthlyPayment, 2); ?></p>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo $progressPercentage; ?>%"></div>
                    </div>
                </div>

                <div class="loan-summary">
                    <div class="summary-card">
                        <h4>Jumlah Pinjaman</h4>
                        <p>RM <?php echo number_format($loanAmount, 2); ?></p>
                    </div>
                    <div class="summary-card">
                        <h4>Telah Dibayar</h4>
                        <p>RM <?php echo number_format($totalRepaid, 2); ?></p>
                    </div>
                    <div class="summary-card">
                        <h4>Baki</h4>
                        <p>RM <?php echo number_format($remainingLoan, 2); ?></p>
                    </div>
                </div>

                <form method="POST" class="report-form">
                    <input type="hidden" name="activeTab" value="loans">
                    <input type="hidden" name="loan_id" value="<?php echo $loanID; ?>">
                    <select name="month">
                        <?php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                            5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                        ];
                        foreach ($months as $num => $name) {
                            $selected = ($selectedMonth == $num && $selectedLoanID == $loanID) ? 'selected' : '';
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                    <select name="year">
                        <?php
                        mysqli_data_seek($resultYears, 0);
                        while ($year = mysqli_fetch_assoc($resultYears)) {
                            $selected = ($selectedYear == $year['year'] && $selectedLoanID == $loanID) ? 'selected' : '';
                            echo "<option value='{$year['year']}' $selected>{$year['year']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Papar</button>
                    <button type="submit" name="show_all" value="1">Semua</button>
                </form>

                <?php
                $sqlRepayments = "SELECT repaymentDate, repaymentAmount, repaymentDesc, repaymentReceipt 
                                FROM loanrepayment 
                                WHERE loanID = '$loanID'";
                
                if ($selectedLoanID == $loanID && !$showAll) {
                    $sqlRepayments .= " AND MONTH(repaymentDate) = '$selectedMonth' 
                                      AND YEAR(repaymentDate) = '$selectedYear'";
                }
                
                $sqlRepayments .= " ORDER BY repaymentDate DESC";
                $repaymentsResult = mysqli_query($con, $sqlRepayments);

                if ($repaymentsResult && mysqli_num_rows($repaymentsResult) > 0) {
                    $count = mysqli_num_rows($repaymentsResult);
                ?>
                    <table class="transaction-table">
                        <thead>
                            <tr>
                                <th>Tarikh</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>No Resit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rowCount = 0;
                            while ($repayment = mysqli_fetch_assoc($repaymentsResult)) { 
                                $display = $rowCount >= 5 ? 'style="display: none;"' : '';
                            ?>
                                <tr class="transaction-row" <?php echo $display; ?>>
                                    <td><?php echo $repayment['repaymentDate']; ?></td>
                                    <td>RM <?php echo number_format($repayment['repaymentAmount'], 2); ?></td>
                                    <td><?php echo $repayment['repaymentDesc']; ?></td>
                                    <td><?php echo $repayment['repaymentReceipt']; ?></td>
                                </tr>
                            <?php 
                                $rowCount++;
                            } 
                            ?>
                        </tbody>
                    </table>

                    <?php if ($count > 5) { ?>
                        <div class="button-container">
                            <button id="viewMoreBtn_<?php echo $loanID; ?>" class="btn btn-primary">Papar Semua Transaksi</button>
                            <button id="viewLessBtn_<?php echo $loanID; ?>" class="btn btn-secondary" style="display: none;">Kembali</button>
                        </div>
                    <?php } ?>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const loanID = '<?php echo $loanID; ?>';
                            const viewMoreBtn = document.getElementById('viewMoreBtn_' + loanID);
                            const viewLessBtn = document.getElementById('viewLessBtn_' + loanID);
                            const transactionRows = document.querySelectorAll(`#loan_${loanID} .transaction-row`);

                            if (viewMoreBtn && viewLessBtn) {
                                viewMoreBtn.addEventListener('click', function() {
                                    transactionRows.forEach(row => row.style.display = '');
                                    viewMoreBtn.style.display = 'none';
                                    viewLessBtn.style.display = 'inline-block';
                                });

                                viewLessBtn.addEventListener('click', function() {
                                    transactionRows.forEach((row, index) => {
                                        if (index >= 5) row.style.display = 'none';
                                    });
                                    viewLessBtn.style.display = 'none';
                                    viewMoreBtn.style.display = 'inline-block';
                                });
                            }
                        });
                    </script>

                <?php } else { ?>
                    <div class='no-loans'>Tiada Rekod Pembayaran</div>
                <?php } ?>
            </div>
    <?php
        }
    } else {
        echo "<div class='container no-loans'><p>Anda Tidak Mempunyai Sebarang Pinjaman</p></div>";
    }
    ?>
</body>
</html>