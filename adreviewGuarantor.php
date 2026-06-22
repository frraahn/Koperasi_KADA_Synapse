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

if (!isset($_GET['loanID'])) {
    die("Error: loanID parameter is missing.");
}

$loanID = $_GET['loanID'];

// Query to retrieve loan and guarantor information
$sql = "
    SELECT 
        loan.loanID, 
        guarantorinfo.guarantorName, 
        guarantorinfo.guarantorIC, 
        guarantorinfo.guarantorID, 
        guarantorinfo.guarantorPF, 
        guarantorinfo.guarantorPhoneNumber,
        guarantorinfo.photo
    FROM loan
    LEFT JOIN guarantorinfo ON loan.loanID = guarantorinfo.loanID
    WHERE loan.staffNo = ? AND loan.loanID = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $staffNo, $loanID);
$stmt->execute();
$result = $stmt->get_result();

// Check if any guarantors exist
if ($result && $result->num_rows > 0) {
    $guarantors = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("No guarantor information found for the given Staff No.");
}

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat Penjamin</title>
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

        /* Debugging outline (remove after testing) */
        .debug-outline {
            outline: 1px solid red;
        }

        /* Content Container */
        .content-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin: 0; /* Ensure no additional margin */
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

        .table {
            width: 100%; /* Ensures the table stretches to fill the container */
            border-collapse: collapse; /* Ensures table cells collapse neatly */
            border-radius: 15px;
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

        /* Force consistent box model */
        * {
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Maklumat Penjamin</h2>
    </div>

    <div class="container">
        <div class="content-container">
            <h3>Maklumat Penjamin</h3><br>
            <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID Pembiayaan</th>
                        <th>Nama Penjamin</th>
                        <th>No Kad Pengenalan Penjamin</th>
                        <th>No Anggota Penjamin</th>
                        <th>No PF Penjamin</th>
                        <th>No Telefon Penjamin</th>
                        <th>Slip Gaji</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guarantors as $guarantor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($guarantor['loanID']); ?></td>
                        <td><?php echo htmlspecialchars($guarantor['guarantorName']); ?></td>
                        <td><?php echo htmlspecialchars($guarantor['guarantorIC']); ?></td>
                        <td><?php echo htmlspecialchars($guarantor['guarantorID']); ?></td>
                        <td><?php echo htmlspecialchars($guarantor['guarantorPF']); ?></td>
                        <td><?php echo htmlspecialchars($guarantor['guarantorPhoneNumber']); ?></td>
                        <td><a href="data:application/pdf;base64,<?php echo base64_encode($guarantor['photo']); ?>"download="Slip_Gaji.pdf">Muat Turun Slip Gaji </a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <div class="mt-3">
                <a href="reviewLoanList.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>
