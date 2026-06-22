<?php
session_start();
include 'dbconnect.php';
include 'headerapplicant.php';

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in to access this page.'); window.location.href='login.php';</script>";
    exit;
}

$userEmail = $_SESSION['email'];

$sqlStaffNo = "SELECT staffNo FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sqlStaffNo);
$staffNo = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['staffNo'] : null;

$activeTab = isset($_POST['activeTab']) ? $_POST['activeTab'] : 'savings';

if (!$staffNo) {
    echo "<p>No staff found for this user.</p>";
    exit;
}

// Get saving types
$sqlSavingTypes = "SELECT * FROM savingtype WHERE staffNo = '$staffNo'";
$savingTypesResult = mysqli_query($con, $sqlSavingTypes);
$savingTypes = mysqli_fetch_assoc($savingTypesResult);

// Get savings data without filtering
$sqlSavings = "SELECT savingID, savingAmount, savingDate, savingDesc, savingType, savingReceipt 
               FROM saving 
               WHERE staffNo = '$staffNo'
               ORDER BY savingDate DESC";

$savingsResult = mysqli_query($con, $sqlSavings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekod Pembayaran dan Simpanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #D6536D;
            --secondary: #E43D12;
            --accent: #EBE9E1;
            --yellow: #EFB11D;
            --light-pink: #FFA2B6;
            --blue: #6686F6;
            --green: #CFFFDC;
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
            background-color: #6686F6;
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

        .nav-tabs {
            border-bottom: 2px solid #D6536D;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            color: #D6536D;
            border: none;
            padding: 1rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background-color: #FFA2B6;
            border-radius: 10px 10px 0 0;
        }

        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            border: none;
        }

        .card-header {
            background-color: #E43D12;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .saving-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr));
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .saving-type-item {
            background-color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .saving-type-item h5 {
            color: var(--secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .saving-type-item p {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-blue);
            margin: 0;
        }

        .container {
            max-width: 1450px;
            margin: 0 auto;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Rekod Pembayaran Simpanan dan Pinjaman</h2>
    </div>

    <div class="container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo ($activeTab == 'savings' ? 'active' : ''); ?>" data-bs-toggle="tab" href="#savings">Simpanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($activeTab == 'loans' ? 'active' : ''); ?>" data-bs-toggle="tab" href="#loans">Pinjaman</a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Savings Tab -->
            <div id="savings" class="tab-pane <?php echo ($activeTab == 'savings' ? 'active' : 'fade'); ?>">
                <!-- Saving Types Summary -->
                <div class="card">
                    <div class="card-header" style="background-color: #4150F1;">
                        <h4 class="mb-0">Maklumat Simpanan</h4>
                    </div>
                    <div class="card-body">
                        <div class="saving-type-grid">
                            <?php if ($savingTypes) { ?>
                                <div class="saving-type-item">
                                    <h5>Fee Masuk</h5>
                                    <p>RM <?php echo number_format($savingTypes['feeMasuk'], 2); ?></p>
                                </div>
                                <div class="saving-type-item">
                                    <h5>Modal Syer</h5>
                                    <p>RM <?php echo number_format($savingTypes['modahSyer'], 2); ?></p>
                                </div>
                                <div class="saving-type-item">
                                    <h5>Modal Yuran</h5>
                                    <p>RM <?php echo number_format($savingTypes['modalYuran'], 2); ?></p>
                                </div>
                                <div class="saving-type-item">
                                    <h5>Wang Deposit Anggota</h5>
                                    <p>RM <?php echo number_format($savingTypes['wangDepositAnggota'], 2); ?></p>
                                </div>
                                <div class="saving-type-item">
                                    <h5>Sumbangan Tabung</h5>
                                    <p>RM <?php echo number_format($savingTypes['sumbanganTabung'], 2); ?></p>
                                </div>
                                <div class="saving-type-item">
                                    <h5>Simpanan Tetap</h5>
                                    <p>RM <?php echo number_format($savingTypes['simpananTetap'], 2); ?></p>
                                </div>
                            <?php } else { ?>
                                <p>Tiada maklumat simpanan dijumpai.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Savings Transactions -->
                <div class="card">
                    <div class="card-header" style="background-color: #4150F1;">
                        <h4 class="mb-0">Transaksi Simpanan</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($savingsResult && mysqli_num_rows($savingsResult) > 0) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tarikh</th>
                                        <th>Jumlah</th>
                                        <th>Jenis</th>
                                        <th>Keterangan</th>
                                        <th>No Resit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($saving = mysqli_fetch_assoc($savingsResult)) { ?>
                                        <tr>
                                            <td><?php echo $saving['savingDate']; ?></td>
                                            <td>RM <?php echo number_format($saving['savingAmount'], 2); ?></td>
                                            <td><?php echo $saving['savingType']; ?></td>
                                            <td><?php echo $saving['savingDesc']; ?></td>
                                            <td><?php echo $saving['savingReceipt']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="text-center py-3">Tiada transaksi simpanan dijumpai.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Loans Tab -->
            <div id="loans" class="tab-pane <?php echo ($activeTab == 'loans' ? 'active' : 'fade'); ?>">
                <?php include 'loanrepaymentrecord.php'; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var triggerTabList = [].slice.call(document.querySelectorAll('.nav-tabs a'))
            triggerTabList.forEach(function(triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>
</body>
</html>