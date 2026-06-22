<?php
include 'dbconnect.php';
include 'headeradmin.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if staffNo is provided
if (!isset($_GET['staffNo'])) {
    die("Error: staffNo parameter is missing.");
}

$staffNo = $_GET['staffNo'];

// Fetch applicant details
$sql = "SELECT staffNo, applicantName, applicantIC, applicantPF, applicantStreet, applicantPostcode, applicantCity, applicantState, applicantGender, applicantReligion, applicantRace, applicantPosition, officeStreet, officePostcode, applicantPhoneNumber, applicantDOB, applicantAge, officeFax, officeCity, applicantPhoneHome, applicantSalary, applicantPosition
        FROM applicant 
        WHERE staffNo = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $staffNo);
$stmt->execute();
$result = $stmt->get_result();

// Check if applicant exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("No applicant found with the given Staff No.");
}

$stmt->close();
$con->close();
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
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 3rem;
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
        <h2 class="page-title">Permohonan Berhenti Menjadi Anggota</h2>
    </div>

<div class="container">
        <div class="content-container">
            <h3>Maklumat Pemohon</h3><br>
            <div class="table-container">
            <table class="table table-hover">
            <tr>
                <th>No Anggota</th>
                <td><?php echo htmlspecialchars($row['staffNo']); ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?php echo htmlspecialchars($row['applicantName']); ?></td>
            </tr>
            <tr>
                <th>No Kad Pengenalan</th>
                <td><?php echo htmlspecialchars($row['applicantIC']); ?></td>
            </tr>
            <tr>
                <th>Jantina</th>
                <td><?php echo htmlspecialchars($row['applicantGender']); ?></td>
            </tr>
            <tr>
                <th>No PF</th>
                <td><?php echo htmlspecialchars($row['applicantPF']); ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?php echo htmlspecialchars($row['applicantStreet']); ?></td>
            </tr>
            <tr>
                <th>Poskod</th>
                <td><?php echo htmlspecialchars($row['applicantPostcode']); ?></td>
            </tr>
            <tr>
                <th>Bandar</th>
                <td><?php echo htmlspecialchars($row['applicantCity']); ?></td>
            </tr>
            <tr>
                <th>Negeri</th>
                <td><?php echo htmlspecialchars($row['applicantState']); ?></td>
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
                <th>No Telefon</th>
                <td><?php echo htmlspecialchars($row['applicantPhoneNumber']); ?></td>
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
                <th>Posisi</th>
                <td><?php echo htmlspecialchars($row['applicantPosition']); ?></td>
            </tr>
            <tr>
                <th>No Telefon Rumah</th>
                <td><?php echo htmlspecialchars($row['applicantPhoneHome']); ?></td>
            </tr>
            <tr>
                <th>Gaji</th>
                <td><?php echo htmlspecialchars($row['applicantSalary']); ?></td>
            </tr>
            <tr>
                <th>Alamat Pejabat</th>
                <td><?php echo htmlspecialchars($row['officeStreet']); ?></td>
            </tr>
            <tr>
                <th>Poskod Pejabat</th>
                <td><?php echo htmlspecialchars($row['officePostcode']); ?></td>
            </tr>
            <tr>
                <th>Bandar Pejabat</th>
                <td><?php echo htmlspecialchars($row['officeCity'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Fax Pejabat</th>
                <td><?php echo htmlspecialchars($row['officeFax']); ?></td>
            </tr>
        </table>
        <a href="terminatelist.php" class="btn btn-secondary mt-3">Kembali</a>
         <a href="applicantinforeason.php?staffNo=<?php echo urlencode($row['staffNo']); ?>" class="btn btn-primary mt-3">Seterusnya : Sebab Berhenti</a>
    </div>
            
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>