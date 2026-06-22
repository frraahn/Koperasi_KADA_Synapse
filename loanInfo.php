<?php
include 'dbconnect.php';
include 'headeralk.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if staffNo is provided
if (!isset($_GET['staffNo'])) {
    die("Error: staffNo parameter is missing.");
}

$staffNo = $_GET['staffNo'];
if (!isset($_GET['loanID'])) {
    die("Error: loanID parameter is missing.");
}

$loanID = $_GET['loanID'];

// SQL Query to fetch loan details and join with loantype table to get loanName
$sql = "
    SELECT 
        loan.loanType, 
        loan.otherLoan, 
        loan.loanAmount, 
        loan.loanDuration, 
        loan.monthlyPayment,
        loan.applicantNetSalary, 
        loan.applicantBasicSalary,
        loan.loanID,
        loan.loanApplyDate,
        loan.loanReviewDate,
        loan.pengesahanMajikan,
        loantype.loanName
    FROM loan
    JOIN loantype ON loan.loanType = loantype.loanType
    WHERE loan.staffNo = '$staffNo' AND loan.loanID = '$loanID'
";

$result = mysqli_query($con, $sql);

// Check if loan record exists
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    die("No loan record found for the given Staff No.");
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Pembiayaan</title>
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
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png'); /* Replace with the correct path to your background image */
          background-size: cover;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }

        /* Header Styles */
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

        /* Container for content */
        .container {
            max-width: 1450px;
            margin: 0 auto;
            padding: 2rem;
        }

        .table-container {
            background-color: var(--purple);
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Content Container */
        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            margin: 0 auto;
            max-width: 50%;
            width: 75%; /* Ensures the container has a consistent width */
            text-align: center; /* Centers inline elements inside */
        }
        .content-container1 {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin: 0;
        }


        /* Table Styles */
        .table {
            width: 100%;
            table-layout: auto;
            margin-bottom: 1.5rem;
            white-space: nowrap;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }
        .table-container {
            background-color: var(--purple);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Ensures it fills its parent */
            margin: 0; /* Removes unnecessary margins */
            overflow: hidden; /* Ensures the table stays inside */
        }

        .table th, .table td {
            padding: 1rem;
            text-align: left;
            vertical-align: middle;
            font-size: 1rem;
            border-bottom: 1px solid #ddd;
        }

        /* Button Styles */
        .btn {
            border-radius: 30px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .mt-3 {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Permohonan Pembiayaan</h2>
    </div>
    <div class="content-container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="applicantInfoLoan.php?staffNo=<?php echo($staffNo)?>&loanID=<?php echo urlencode($row['loanID']); ?>" class="nav-link">Maklumat Pemohon</a>
        </li>
        <li class="nav-item">
            <a href="loanInfo.php?staffNo=<?php echo($staffNo)?>&loanID=<?php echo urlencode($row['loanID']); ?>" class="nav-link">Maklumat Pembiayaan</a>
        </li>
        <li class="nav-item">
            <a href="viewBankInfo.php?staffNo=<?php echo($staffNo)?>&loanID=<?php echo urlencode($row['loanID']); ?>" class="nav-link">Maklumat Bank</a>
        </li>
        <li class="nav-item">
            <a href="guarantorInfo.php?loanID=<?php echo($loanID)?>&loanID=<?php echo urlencode($row['loanID']); ?>&staffNo=<?php echo($staffNo); ?>" class="nav-link">Maklumat Penjamin</a>
        </li>
    </ul>
</div>

    <div class="container">
        <div class="content-container1">
            <h3>Maklumat Pembiayaan</h3><br>
            <div class="table-container">
            <table class="table table-hover">
            <tr>
                <th>ID Pembiayaan</th>
                <td><?php echo htmlspecialchars($row['loanID']); ?></td>
            </tr>
            <tr>
                <th>Jenis Pembiayaan</th>
                <td><?php echo htmlspecialchars($row['loanName']); ?></td>
            </tr>
            <tr>
                <th>Jumlah Pembiayaan</th>
                <td><?php echo htmlspecialchars($row['loanAmount']); ?></td>
            </tr>
            <tr>
                <th>Tempoh Pembiayaan</th>
                <td><?php echo htmlspecialchars($row['loanDuration']); ?> months</td>
            </tr>
            <tr>
                <th>Bayaran Bulanan</th>
                <td><?php echo htmlspecialchars($row['monthlyPayment']); ?></td>
            </tr>
            <tr>
                <th>Gaji Pokok</th>
                <td><?php echo htmlspecialchars($row['applicantBasicSalary']); ?></td>
            </tr>
            <tr>
                <th>Gaji Bersih</th>
                <td><?php echo htmlspecialchars($row['applicantNetSalary']); ?></td>
            </tr>
            <tr>
                <th>Surat Pengesahan Majikan</th>
                <td><a href="data:application/pdf;base64,<?php echo base64_encode($row['pengesahanMajikan']); ?>"download="Pengesahan_Majikan.pdf">Muat Turun Surat Pengesahan Majikan </a></td>
            </tr>
            <tr>
                <th>Pembiayaan Lain</th>
                <td><?php echo htmlspecialchars($row['otherLoan']); ?></td>
            </tr>
            <tr>
                <th>Tarikh Mohon</th>
                <td><?php echo htmlspecialchars($row['loanApplyDate']); ?></td>
            </tr>
            <tr>
                <th>Tarikh Semak</th>
                <td><?php echo htmlspecialchars($row['loanReviewDate']); ?></td>
            </tr>
        </table>
    </div>
              <!-- Buttons -->
        <div class="mt-3">
            <a href="loanList.php" class="btn btn-secondary">Kembali</a>
            <a href="compareLoan.php?staffNo=<?php echo($staffNo); ?>" class="btn btn-primary">Banding</a>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
