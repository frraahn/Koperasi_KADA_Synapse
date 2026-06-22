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

// SQL Query
$sql = "
    SELECT 
        applicant.applicantName, 
        applicant.applicantIC, 
        applicant.applicantDOB, 
        applicant.applicantAge, 
        applicant.applicantStreet, 
        applicant.applicantPostcode, 
        applicant.applicantGender, 
        applicant.applicantReligion, 
        applicant.applicantRace, 
        applicant.applicantPF, 
        applicant.applicantPosition, 
        applicant.officeStreet, 
        applicant.officePostcode, 
        applicant.applicantPhoneNumber,
        loan.loanID
    FROM applicant
    LEFT JOIN loan ON loan.staffNo = applicant.staffNo
    WHERE applicant.staffNo = '$staffNo'  AND loan.loanID = '$loanID'";

$result = mysqli_query($con, $sql);

// Check if a record exists
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    die("No applicant record found for the given Staff No.");
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
            width: 95%;
            margin: 0 auto; /* Center the content horizontally */
        }

        .nav-tabs {
            display: inline-flex; /* Makes the tabs appear inline */
            border-bottom: none; /* Removes the default border */
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        .nav-tabs .nav-item {
            margin: 0; /* Removes spacing between tabs */
        }

        .nav-tabs .nav-link {
            color: var(--primary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            text-decoration: none;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-radius: 5px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
        }
        .table-container {
            background-color: var(--purple);
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
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
            <h3>Maklumat Pemohon</h3><br>
            <div class="table-container">
            <table class="table table-hover">
            <tr>
                <th>Nama Pemohon</th>
                <td><?php echo htmlspecialchars($row['applicantName']); ?></td>
            </tr>
            <tr>
                <th>No IC</th>
                <td><?php echo htmlspecialchars($row['applicantIC']); ?></td>
            </tr>
            <tr>
                <th>Tarikh Lahir</th>
                <td><?php echo htmlspecialchars($row['applicantDOB']); ?></td>
            </tr>
            <tr>
                <th>Umur</th>
                <td><?php echo htmlspecialchars($row['applicantAge']); ?></td>
            </tr>
            <tr>
                <th>Jantina</th>
                <td><?php echo htmlspecialchars($row['applicantGender']); ?></td>
            </tr>
            <tr>
                <th>Agama</th>
                <td><?php echo htmlspecialchars($row['applicantReligion']); ?></td>
            </tr>
            <tr>
                <th>Bangsa</th>
                <td><?php echo htmlspecialchars($row['applicantRace']); ?></td>
            </tr>
            <tr>
                <th>Alamat Pemohon</th>
                <td><?php echo htmlspecialchars($row['applicantStreet'] . ', ' . $row['applicantPostcode']); ?></td>
            </tr>
            <tr>
                <th>Jawatan</th>
                <td><?php echo htmlspecialchars($row['applicantPosition']); ?></td>
            </tr>
            <tr>
                <th>No Telefon</th>
                <td><?php echo htmlspecialchars($row['applicantPhoneNumber']); ?></td>
            </tr>
            <tr>
                <th>Alamat Pejabat</th>
                <td><?php echo htmlspecialchars($row['officeStreet'] . ', ' . $row['officePostcode']); ?></td>
            </tr>
        </table>
    </div>
      <!-- Buttons -->
        <div class="mt-3">
            <a href="loanList.php" class="btn btn-secondary">Kembali</a>
            <a href="compareLoan.php?staffNo=<?php echo($staffNo)?>&loanID=<?php echo urlencode($row['loanID']); ?>" class="btn btn-primary">Banding</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
