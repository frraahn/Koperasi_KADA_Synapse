<?php
session_start();
include 'headeradmin.php';
include 'dbconnect.php';

// Get all members with their savings information
$sql = "SELECT a.staffNo, m.membershipStatus, a.applicantName, st.modahSyer, st.sumbanganTabung, st.simpananTetap
        FROM applicant a
        LEFT JOIN membership m ON a.staffNo = m.staffNo
        LEFT JOIN savingtype st ON a.staffNo = st.staffNo
        WHERE m.membershipStatus = 2";
$result = mysqli_query($con, $sql);

$memberData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $staffNo = $row['staffNo'];
    $memberData[$staffNo] = [
        'name' => $row['applicantName'],
        'modahSyer' => $row['modahSyer'],
        'sumbanganTabung' => $row['sumbanganTabung'],
        'simpananTetap' => $row['simpananTetap'],
        'loans' => []
    ];
}

// Get approved loans
$sql = "SELECT a.staffNo, l.loanID, l.loanAmount, l.monthlyPayment, 
               (l.loanAmount - IFNULL(SUM(lr.repaymentAmount), 0)) AS remainingLoan
        FROM applicant a
        INNER JOIN loan l ON a.staffNo = l.staffNo
        LEFT JOIN loanrepayment lr ON l.loanID = lr.loanID
        WHERE l.loanStatus = 2
        GROUP BY a.staffNo, l.loanID";
$result = mysqli_query($con, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $staffNo = $row['staffNo'];
    if (isset($memberData[$staffNo])) {
        $memberData[$staffNo]['loans'][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengesahan Transaksi Potongan Gaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #E43D12;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
            --light-purple: #7571d1;
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
            background-color: var(--primary);
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

        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            table-layout: auto;
            margin-top: 1rem;
            white-space: nowrap;
        }

        .table thead {
            background-color: var(--primary);
            color: white;
        }

        .table th {
            padding: 1rem;
            font-weight: 500;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 134, 246, 0.05);
        }

        .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            margin: 0.2rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .btn-success {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .inner-table {
            width: 100%;
            margin-bottom: 1rem;
            border-radius: 10px;
            overflow: hidden;
        }

        .savings-table thead {
            background-color: var(--purple);
            color: white;
        }

        .savings-table tbody tr:hover {
            background-color: rgba(89, 84, 187, 0.05);
        }

        .savings-table td, .savings-table th {
            border: 2px solid rgba(89, 84, 187, 0.1);
        }

        .loan-table thead {
            background-color: var(--accent);
            color: white;
        }

        .loan-table td, .loan-table th {
            border: 2px solid rgba(245, 103, 161, 0.1);
        }

        .inner-table th {
            padding: 0.5rem;
        }

        .inner-table td {
            padding: 0.5rem;
        }

        .inner-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        .inner-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        .table-container {
            margin-bottom: 1rem;
        }

        .table-title {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--purple);
        }

        .loan-title {
            color: var(--accent);
        }

        .batch-controls {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .btn-batch {
            background-color: var(--purple);
            color: white;
            border: none;
        }

        .btn-batch:hover {
            background-color: var(--light-purple);
            color: white;
        }

        .btn-process-batch {
            background-color: var(--secondary);
            color: white;
            border: none;
            display: none;
        }

        .btn-process-batch:hover {
            background-color: #266a52;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Pengesahan Transaksi</h2>
    </div>

    <div class="container">
        <div class="batch-controls">
            <button id="toggleBatchMode" class="btn btn-batch">
                Pemprosesan Berkelompok
            </button>
            <div id="batchOptions" style="display: none;">
                <label class="me-3">
                    <input type="checkbox" id="selectAll"> Pilih Semua
                </label>
                <button id="processBatch" class="btn btn-process-batch">
                    Proses Berkelompok
                </button>
            </div>
        </div>

        <div class="content-container">
            <table class="table">
                <thead>
                    <tr>
                    <th class="checkbox-column batch-checkbox" style="display: none;"></th>
                        <th>No Ahli</th>
                        <th>Nama</th>
                        <th>Maklumat</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($memberData as $staffNo => $details): ?>
                        <tr>
                            <td class="checkbox-column batch-checkbox" style="display: none;">
                                <input type="checkbox" class="row-checkbox" data-staff-no="<?= $staffNo ?>">
                            </td>
                            <td><?= $staffNo ?></td>
                            <td><?= $details['name'] ?></td>
                            <td>
                                <div class="table-container">
                                    <h6 class="table-title">Maklumat Simpanan</h6>
                                    <table class="inner-table savings-table">
                                        <thead>
                                            <tr>
                                                <th>Jenis Simpanan</th>
                                                <th>Simpanan Semasa</th>
                                                <th>Bayaran</th>
                                                <th>Simpanan Baharu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $monthlyDeduction = 55;
                                            $toModahSyer = $details['modahSyer'] < 300 ? 50 : 0;
                                            $toSimpananTetap = $details['modahSyer'] >= 300 ? 50 : 0;
                                            $toSumbanganTabung = 5;
                                            ?>
                                            <tr>
                                                <td>Modal Syer</td>
                                                <td><?= $details['modahSyer'] ?></td>
                                                <td><?= $toModahSyer ?></td>
                                                <td><?= $details['modahSyer'] + $toModahSyer ?></td>
                                            </tr>
                                            <tr>
                                                <td>Simpanan Tetap</td>
                                                <td><?= $details['simpananTetap'] ?></td>
                                                <td><?= $toSimpananTetap ?></td>
                                                <td><?= $details['simpananTetap'] + $toSimpananTetap ?></td>
                                            </tr>
                                            <tr>
                                                <td>Sumbangan Tabung</td>
                                                <td><?= $details['sumbanganTabung'] ?></td>
                                                <td><?= $toSumbanganTabung ?></td>
                                                <td><?= $details['sumbanganTabung'] + $toSumbanganTabung ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (!empty($details['loans'])): ?>
                                    <div class="table-container">
                                        <h6 class="table-title loan-title">Maklumat Pinjaman</h6>
                                        <table class="inner-table loan-table">
                                            <thead>
                                                <tr>
                                                    <th>Loan ID</th>
                                                    <th>Pinjaman (RM)</th>
                                                    <th>Tunggakan Semasa (RM)</th>
                                                    <th>Bayaran (RM)</th>
                                                    <th>Tunggakan Baharu (RM)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($details['loans'] as $loan): 
                                                    $newBalance = $loan['remainingLoan'] - $loan['monthlyPayment'];
                                                ?>
                                                    <tr>
                                                        <td><?= $loan['loanID'] ?></td>
                                                        <td><?= $loan['loanAmount'] ?></td>
                                                        <td><?= $loan['remainingLoan'] ?></td>
                                                        <td><?= $loan['monthlyPayment'] ?></td>
                                                        <td><?= $newBalance ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="table-container">
                                        <h6 class="table-title loan-title">Maklumat Pinjaman</h6>
                                        <p>Tiada pinjaman aktif</p>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="loanrepaymentprocess.php" method="GET">
                                    <input type="hidden" name="staffNo" value="<?= $staffNo ?>">
                                    <?php 
                                        $loanIDs = [];
                                        $payments = [];
                                        foreach ($details['loans'] as $loan) {
                                            $loanIDs[] = $loan['loanID'];
                                            $payments[] = $loan['monthlyPayment'];
                                        }
                                    ?>
                                    <input type="hidden" name="loanIDs" value="<?= implode(',', $loanIDs) ?>">
                                    <input type="hidden" name="payments" value="<?= implode(',', $payments) ?>">
                                    <input type="hidden" name="savingDeduction" value="55">
                                    <input type="submit" class="btn btn-success btn-sm" value="Keluarkan" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBatchMode = document.getElementById('toggleBatchMode');
            const batchOptions = document.getElementById('batchOptions');
            const selectAll = document.getElementById('selectAll');
            const processBatch = document.getElementById('processBatch');
            const batchCheckboxes = document.querySelectorAll('.batch-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');

            // Toggle batch mode
            toggleBatchMode.addEventListener('click', function() {
                const isActive = batchOptions.style.display === 'none';
                batchOptions.style.display = isActive ? 'block' : 'none';
                batchCheckboxes.forEach(checkbox => {
                    checkbox.style.display = isActive ? 'table-cell' : 'none';
                });
                processBatch.style.display = isActive ? 'inline-block' : 'none';
                toggleBatchMode.classList.toggle('active');
            });

            // Select all functionality
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateProcessButton();
            });

            // Individual checkbox change handler
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateProcessButton);
            });

            // Update process button visibility
            function updateProcessButton() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                processBatch.style.display = checkedBoxes.length > 0 ? 'inline-block' : 'none';
            }

            // Process batch
            processBatch.addEventListener('click', function() {
                const selectedStaffNos = [];
                const selectedLoanIDs = [];
                const selectedPayments = [];

                rowCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const staffNo = checkbox.getAttribute('data-staff-no');
                        const row = checkbox.closest('tr');
                        const form = row.querySelector('form');
                        
                        selectedStaffNos.push(staffNo);
                        
                        // Get loan IDs and payments from the form
                        const loanIDsInput = form.querySelector('input[name="loanIDs"]');
                        const paymentsInput = form.querySelector('input[name="payments"]');
                        
                        if (loanIDsInput && loanIDsInput.value) {
                            selectedLoanIDs.push(loanIDsInput.value);
                        }
                        if (paymentsInput && paymentsInput.value) {
                            selectedPayments.push(paymentsInput.value);
                        }
                    }
                });

                // Create and submit form for batch processing
                const batchForm = document.createElement('form');
                batchForm.method = 'GET';
                batchForm.action = 'loanrepaymentprocess.php';

                // Add selected data to form
                const staffNosInput = document.createElement('input');
                staffNosInput.type = 'hidden';
                staffNosInput.name = 'staffNos';
                staffNosInput.value = selectedStaffNos.join(',');
                batchForm.appendChild(staffNosInput);

                const loanIDsInput = document.createElement('input');
                loanIDsInput.type = 'hidden';
                loanIDsInput.name = 'loanIDs';
                loanIDsInput.value = selectedLoanIDs.join('|');
                batchForm.appendChild(loanIDsInput);

                const paymentsInput = document.createElement('input');
                paymentsInput.type = 'hidden';
                paymentsInput.name = 'payments';
                paymentsInput.value = selectedPayments.join('|');
                batchForm.appendChild(paymentsInput);

                document.body.appendChild(batchForm);
                batchForm.submit();
            });
        });
    </script>
</body>
</html>