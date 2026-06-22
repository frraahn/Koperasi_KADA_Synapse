<?php
// transaction.php
session_start();

include 'headeradmin.php';
include 'dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get admin's staff number
$email = $_SESSION['email'];
$staff_query = "SELECT staffNo, applicantName FROM applicant ORDER BY applicantName";
$staff_result = mysqli_query($con, $staff_query);

if (!$staff_result) {
    die("Error fetching staff numbers: " . mysqli_error($con));
}

// Fetch loan types
$loan_types_query = "SELECT * FROM loantype";
$loan_types_result = mysqli_query($con, $loan_types_query);

// Fetch existing loans
$loans_query = "SELECT l.loanID, lt.loanName, a.applicantName, a.staffNo
                FROM loan l 
                JOIN loantype lt ON l.loanType = lt.loanType 
                JOIN applicant a ON l.staffNo = a.staffNo";
$loans_result = mysqli_query($con, $loans_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #f567a1;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
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
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .transaction-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 2rem;
        }

        .form-label {
            color: var(--dark-blue);
            font-weight: 500;
        }

        .form-select, .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border-color: #dee2e6;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(102, 134, 246, 0.2);
        }

        .btn-submit {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        @media (max-width: 576px) {
            .transaction-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Transaksi</h2>
    </div>
    <div class="container">
        <div class="transaction-container">
            <form action="transactionprocess.php" method="POST">
                <!-- Staff Selection -->
                <div class="mb-3">
                    <label for="staffNo">Pilih Staf:</label>
                    <select id="staffNo" name="staffNo" class="form-select" required>
                        <option value="">Pilih Staf</option>
                        <?php while($staff = mysqli_fetch_assoc($staff_result)): ?>
                            <option value="<?php echo $staff['staffNo']; ?>">
                                <?php echo $staff['staffNo'] . " - " . $staff['applicantName']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>    

                <!-- Transaction Type -->
                <div class="mb-3">
                    <label for="transactionType">Jenis Transaksi:</label>
                    <select id="transactionType" name="transactionType" class="form-select" required>
                        <option value="">Pilih Jenis Transaksi</option>
                        <option value="simpanan">Simpanan</option>
                        <option value="pinjaman">Pinjaman</option>
                    </select>
                </div>

                <!-- Loan Section -->
                <div id="loanSection" style="display:none;" class="mb-3">
                    <label for="loanID">Pilih Pinjaman:</label>
                    <select id="loanID" name="loanID" class="form-select">
                        <option value="">Pilih Pinjaman</option>
                        <?php while($loan = mysqli_fetch_assoc($loans_result)): ?>
                            <option value="<?php echo $loan['loanID']; ?>" data-staff="<?php echo $loan['staffNo']; ?>">
                                Loan ID: <?php echo $loan['loanID'] . " - " . $loan['loanName']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Savings Type Section -->
                <div id="savingsSection" style="display:none;" class="mb-3">
                    <label for="savingsType">Jenis Simpanan:</label>
                    <select id="savingsType" name="savingsType" class="form-select">
                        <option value="">Pilih Jenis Simpanan</option>
                        <option value="modahSyer">Modah Syer</option>
                        <option value="modalYuran">Modal Yuran</option>
                        <option value="wangDepositAnggota">Wang Deposit Anggota</option>
                        <option value="sumbanganTabung">Sumbangan Tabung</option>
                        <option value="simpananTetap">Simpanan Tetap</option>
                        <option value="lainLain">Lain-lain</option>
                    </select>
                </div>

                <!-- Other Fields -->
                <div id="otherSavingsDiv" style="display:none;" class="mb-3">
                    <label for="otherSavings">Nyatakan Lain-lain:</label>
                    <input type="text" id="otherSavings" name="otherSavings" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="amount">Jumlah:</label>
                    <input type="number" step="0.01" id="amount" name="amount" class="form-select" required>
                </div>

                <div class="mb-3">
                    <label for="description">Keterangan:</label>
                    <input type="text" id="description" name="description" class="form-select">
                </div>

                <div class="mb-3">
                    <label for="paymentType">Jenis Bayaran:</label>
                    <select id="paymentType" name="paymentType" class="form-select" required>
                        <option value="">Pilih Jenis Bayaran</option>
                        <option value="potongan_gaji">Potongan Gaji</option>
                        <option value="duitNow">DuitNow</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="receiptNo">No resit:</label>
                    <input type="text" id="receiptNo" name="receiptNo" class="form-select">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">Hantar Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('transactionType').addEventListener('change', function() {
        var loanSection = document.getElementById('loanSection');
        var savingsSection = document.getElementById('savingsSection');
        
        if (this.value === 'pinjaman') {
            loanSection.style.display = 'block';
            savingsSection.style.display = 'none';
        } else if (this.value === 'simpanan') {
            loanSection.style.display = 'none';
            savingsSection.style.display = 'block';
        } else {
            loanSection.style.display = 'none';
            savingsSection.style.display = 'none';
        }
    });

    document.getElementById('savingsType').addEventListener('change', function() {
        var otherSavingsDiv = document.getElementById('otherSavingsDiv');
        otherSavingsDiv.style.display = (this.value === 'lainLain') ? 'block' : 'none';
    });

    document.getElementById('staffNo').addEventListener('change', function() {
        var staffNo = this.value;
        var loanOptions = document.querySelectorAll('#loanID option');
        
        loanOptions.forEach(function(option) {
            if (option.value === '') return; // Skip the placeholder option
            option.style.display = (option.dataset.staff === staffNo) ? '' : 'none';
        });
    });

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    window.onload = function() {
        var status = getUrlParameter('status');
        var message = getUrlParameter('message');
        
        if (status && message) {
            if (status === 'success') {
                alert(message);
                // Optional: Redirect to clean URL after showing alert
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'error') {
                alert('Error: ' + message);
                // Optional: Redirect to clean URL after showing alert
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Adakah anda pasti untuk meneruskan transaksi ini?')) {
            this.submit();
        }
    });
    </script>
</body>
</html>