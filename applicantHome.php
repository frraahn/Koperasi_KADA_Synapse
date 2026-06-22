<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headerapplicant.php';
include('dbconnect.php');

// Fetch savings and loan data
$userEmail = $_SESSION['email'];
$sqlStaffNo = "SELECT staffNo FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sqlStaffNo);
$staffNo = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['staffNo'] : null;

$savingsData = [];
$loanData = [];

if ($staffNo) {
    // Fetch savings data
    $savingsQuery = "SELECT feeMasuk, modahSyer, modalYuran, wangDepositAnggota, sumbanganTabung, simpananTetap, lainLain FROM savingtype WHERE staffNo = '$staffNo'";
    $savingsResult = mysqli_query($con, $savingsQuery);
    if ($savingsResult && mysqli_num_rows($savingsResult) > 0) {
        $savingsData = mysqli_fetch_assoc($savingsResult);
    }

    // Fetch loan data
    $loanQuery = "SELECT l.loanAmount, lt.loanName FROM loan l LEFT JOIN loantype lt ON l.loanType = lt.loanType WHERE l.staffNo = '$staffNo'";
    $loanResult = mysqli_query($con, $loanQuery);
    if ($loanResult && mysqli_num_rows($loanResult) > 0) {
        while ($row = mysqli_fetch_assoc($loanResult)) {
            $loanData[] = $row;
        }
    }
}

// Default values for savings and loans
$savingsDefaults = [
    'feeMasuk' => 0,
    'modahSyer' => 0,
    'modalYuran' => 0,
    'wangDepositAnggota' => 0,
    'sumbanganTabung' => 0,
    'simpananTetap' => 0,
    'lainLain' => 0
];
$savingsData = array_merge($savingsDefaults, $savingsData);

$loanTypesQuery = "SELECT loanType, loanName FROM loantype";
$loanTypesResult = mysqli_query($con, $loanTypesQuery);
$loanTypes = [];
if ($loanTypesResult && mysqli_num_rows($loanTypesResult) > 0) {
    while ($row = mysqli_fetch_assoc($loanTypesResult)) {
        $loanTypes[$row['loanType']] = $row['loanName'];
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .sub-header {
            background-color: #6686F6;
            height: 60px;
            text-align: center;
            color: white;
            font-size: 24px;
            line-height: 60px;
            margin: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .container {
            margin: 40px auto;
            max-width: 1200px;
            text-align: center;
        }

        .card-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            width: 350px;
            padding: 20px;
            text-align: center;
        }

        .card h3 {
            color: #a255b8;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .card p {
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }

        .card .button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ff66b2;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            margin-top: auto;
        }

        .card .button:hover {
            background-color: #e60073;
        }
    </style>
</head>
<body>
    <div class="sub-header"><strong>LAMAN UTAMA</strong></div>
    <div class="container">
        <div class="card-container">
            <!-- Membership Form -->
            <div class="card">
                <h3>Borang Keanggotaan</h3>
                <p>Isi borang untuk memohon keanggotaan.</p>
                <button class="button" onclick="location.href='applicant.php'">Memohon</button>
            </div>
            <!-- Financing Form -->
            <div class="card">
                <h3>Borang Pembiayaan</h3>
                <p>Isi borang untuk memohon pembiayaan.</p>
                <button class="button" onclick="location.href='pinjaman.php'">Memohon</button>
            </div>
            <!-- Calculator -->
            <div class="card">
                <h3>Kalkulator Bayaran Balik</h3>
                <p>Gunakan kalkulator untuk membuat kiraan bayaran balik.</p>
                <button class="button" onclick="location.href='calculator.php'">Kiraan</button>
            </div>
            <div class="card">
                <h3>Borang Berhenti</h3>
                <p>Isi borang untuk memohon berhenti menjadi anggota.</p>
                <button class="button" onclick="location.href='berhenti.php'">Memohon</button>
            </div>
        </div>

        <!-- Savings Information -->
        <h2 style="margin-top: 40px; color: #333;">Maklumat Yuran dan Sumbangan</h2>
        <div class="card-container">
            <?php foreach ($savingsData as $key => $value): ?>
                <div class="card">
                    <h3><?= ucfirst(str_replace('_', ' ', $key)) ?></h3>
                    <p>Jumlah: RM <?= number_format($value, 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Loan Information -->
        <h2 style="margin-top: 40px; color: #333;">Maklumat Pembiayaan</h2>
        <div class="card-container">
            <?php foreach ($loanTypes as $loanType => $loanName): ?>
                <?php 
                $loanAmount = "0.00";
                foreach ($loanData as $loan) {
                    if ($loan['loanName'] === $loanName) {
                        $loanAmount = $loan['loanAmount'];
                        break;
                    }
                }
                ?>
                <div class="card">
                    <h3><?= $loanName ?></h3>
                    <p>Jumlah: RM <?= number_format($loanAmount, 2) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
