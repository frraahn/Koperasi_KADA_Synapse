<?php
include 'dbconnect.php';
include 'headeralk.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if required parameters are provided
if (!isset($_GET['staffNo'], $_GET['loanID'])) {
    die("Error: staffNo or loanID parameter is missing.");
}

$staffNo = htmlspecialchars($_GET['staffNo']);
$loanID = htmlspecialchars($_GET['loanID']);

// Fetch loan and applicant data
$sql = "SELECT 
            loan.loanID, 
            loan.loanAmount, 
            loan.loanDuration, 
            loan.monthlyPayment, 
            loan.staffNo, 
            loan.applicantNetSalary AS applicantNetSalary 
        FROM loan
        LEFT JOIN applicant ON loan.staffNo = applicant.staffNo
        WHERE loan.staffNo = '$staffNo' AND loan.loanID = '$loanID'";

$result = mysqli_query($con, $sql);

// Check if loan record exists
if ($result && mysqli_num_rows($result) > 0) {
    $loan = mysqli_fetch_assoc($result);
} else {
    die("No loan or applicant record found for the given Staff No and Loan ID.");
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banding</title>
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

        .row-container{
            display: flex;
          justify-content: space-between;
          margin-top: 20px;
          max-width: 15%;
          margin-right: auto;
          margin-bottom: 20px;
          }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Permohonan Pembiayaan</h2>
    </div>

    <div class="container mt-5">
        <h2>Periksa Maklumat Pembiayaan</h2>
        <table class="table table-bordered">
            <tr>
                <th>ID Pembiayaan</th>
                <td><?php echo htmlspecialchars($loan['loanID']); ?></td>
            </tr>
            <tr>
                <th>Jumlah Pembiayaan</th>
                <td><?php echo htmlspecialchars($loan['loanAmount']); ?></td>
            </tr>
            <tr>
                <th>Tempoh Pembiayaan</th>
                <td><?php echo htmlspecialchars($loan['loanDuration']); ?> months</td>
            </tr>
            <tr>
                <th>Bayaran Bulanan</th>
                <td><?php echo htmlspecialchars($loan['monthlyPayment']); ?></td>
            </tr>
            <tr>
                <th>Gaji Bersih</th>
                <td><?php echo htmlspecialchars($loan['applicantNetSalary']); ?></td>
            </tr>
        </table>
        <button class="btn btn-success mt-3" onclick="approveLoan()">Diterima</button>
        <a href="loanList.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>

    <script>
        function approveLoan() {
            const alkStaffNo = prompt("Sila masukkan nombor kakitangan ALK:");
            if (alkStaffNo) {
                window.location.href = `loanListProcess.php?staffNo=<?php echo $loan['staffNo']; ?>&loanID=<?php echo $loan['loanID']; ?>&action=approve&alkStaffNo=${encodeURIComponent(alkStaffNo)}`;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
