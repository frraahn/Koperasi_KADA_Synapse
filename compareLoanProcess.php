<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
include 'headeralk.php';
include 'dbconnect.php';
require 'vendor/autoload.php';
include 'sendemail.php'; 


// Check if applicantNetSalary and staffNo are provided
if (!isset($_POST['applicantNetSalary']) || !isset($_POST['staffNo'])) {
    die("Error: Missing required information (applicantNetSalary or staffNo).");
}

// Retrieve and sanitize inputs
$netSalary = floatval($_POST['applicantNetSalary']);
$staffNo = $_POST['staffNo']; 

// Loan term (minimum net salary required)
$loanTerm = 3000;

// Fetch loanID dynamically from the database
$sqlLoan = "SELECT loanID FROM loan WHERE staffNo = '$staffNo'";
$resultLoan = mysqli_query($con, $sqlLoan);
if ($resultLoan && mysqli_num_rows($resultLoan) > 0) {
    $loan = mysqli_fetch_assoc($resultLoan);
    $loanID = $loan['loanID']; // Fetch loanID for the current staffNo
} else {
    die("Error: No loan found for the provided staff number.");
}

// Determine eligibility
$isEligible = $netSalary > $loanTerm;

// Update loan status based on user action
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'approve') {
        $alkStaffNo = $_POST['alkStaffNo'];
        $currentDate = date('Y-m-d');

        $updateSql = "UPDATE loan SET loanStatus = 2, loanApproveDate = '$currentDate', alkStaffNo = '$alkStaffNo' WHERE loanID = '$loanID'";
        mysqli_query($con, $updateSql);

        echo "<script>alert('Pembiayaan diluluskan! Status telah dikemaskini.'); window.location.href = 'loanList.php';</script>";
    } elseif ($action === 'reject') {
        $alkStaffNo = $_POST['alkStaffNo'];
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Tiada sebab diberikan';

        $updateSql = "UPDATE loan SET loanStatus = 3, loanApproveDate = NULL, alkStaffNo = '$alkStaffNo' WHERE loanID = '$loanID'";
        mysqli_query($con, $updateSql);

        $getEmailQuery = "SELECT TRIM(u.email) AS email, u.firstName, u.lastName FROM users u LEFT JOIN applicant a ON a.email = u.email WHERE a.staffNo = ?";
        if ($emailStmt = mysqli_prepare($con, $getEmailQuery)) {
            mysqli_stmt_bind_param($emailStmt, "s", $staffNo);
            mysqli_stmt_execute($emailStmt);
            $emailResult = mysqli_stmt_get_result($emailStmt);

            if ($emailRow = mysqli_fetch_assoc($emailResult)) {
                $applicantEmail = $emailRow['email'];
                $applicantName = $emailRow['firstName'] . ' ' . $emailRow['lastName'];

                if (!empty($applicantEmail)) {
                    $feedbackQuery = "INSERT INTO alkapplicationfeedback (email, reason, dateGiven) VALUES (?, ?, NOW())";
                    if ($feedbackStmt = mysqli_prepare($con, $feedbackQuery)) {
                        mysqli_stmt_bind_param($feedbackStmt, "ss", $applicantEmail, $reason);
                        mysqli_stmt_execute($feedbackStmt);
                        mysqli_stmt_close($feedbackStmt);
                    }

                    $emailSubject = 'Maklumbalas Pembiayaan';
                    $emailBody = "<p>Salam sejahtera, $applicantName.</p>
                                  <p>Permohonan pembiayaan anda telah ditolak kerana:</p>
                                  <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
                                  <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
                                  <p>Salam hormat,</p>
                                  <p>Koperasi Kakitangan KADA Kelantan Berhad</p>";

                    $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
                    if ($sendingEmail != 1) {
                        echo "<script>alert('$sendingEmail'); window.location.href = 'loanList.php';</script>";
                        exit;
                    }
                }
            }
            mysqli_stmt_close($emailStmt);
        }

        echo "<script>alert('Pembiayaan ditolak! Status telah dikemaskini.'); window.location.href = 'loanList.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keputusan Perbandingan</title>
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
            text-align: center;
        }
        .table-container {
            background-color: var(--purple);
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: auto;
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
        .status-green {
            color: green;
            font-weight: bold;
        }
        .btn-danger {
         background-color: #D6536D !important;
         border-color: #D6536D !important;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Keputusan Perbandingan</h2>
    </div>

    <div class="table-container">
        <div class="content-container1">
            <h3>Syarat Pembiayaan</h3><br>
            <div class="container">
            <table class="table table-hover">
        <p>Gaji bersih pemohon hendaklah melebihi <strong>RM <?php echo $loanTerm; ?></strong> untuk lulus syarat pembiayaan.</p>
        <p>Gaji Bersih Pemohon: <strong>RM <?php echo (number_format($netSalary, 2)); ?></strong></p>
        <p class="<?php echo $isEligible ? 'status-green' : 'status-red'; ?>">
            <?php echo $isEligible ? 'Layak' : 'Tidak Layak'; ?>
        </p>
    </table>
</div>
        <a href="loanList.php" class="btn btn-secondary mt-3">Kembali</a>

        <?php if ($isEligible): ?>
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="staffNo" value="<?php echo $staffNo; ?>">
                <input type="hidden" name="applicantNetSalary" value="<?php echo $netSalary; ?>">
                <input type="hidden" name="loanID" value="<?php echo $loanID; ?>"> <!-- Hidden loanID -->
                <input type="hidden" name="alkStaffNo" id="alkStaffNoInput">
                <button type="button" class="btn btn-success mt-3" onclick="approveLoan()">Diterima</button>
            </form>
        <?php endif; ?>

        <form action="" method="POST" id="rejectForm" style="display: inline;">
    <input type="hidden" name="action" value="reject">
    <input type="hidden" name="staffNo" value="<?php echo $staffNo; ?>">
    <input type="hidden" name="applicantNetSalary" value="<?php echo $netSalary; ?>">
    <input type="hidden" name="loanID" value="<?php echo $loanID; ?>"> <!-- Hidden loanID -->
    <input type="hidden" name="alkStaffNo" id="alkStaffNoRejectInput">
    <button type="button" class="btn btn-danger mt-3" onclick="rejectLoan()">Ditolak</button>
</form>
    </div>

    <script>
        function approveLoan() {
            const alkStaffNo = prompt("Sila masukkan nombor kakitangan ALK:");
            if (alkStaffNo) {
                document.getElementById('alkStaffNoInput').value = alkStaffNo;
                document.querySelector('form[action=""] button[type="button"]').closest('form').submit();
                // Redirect to loanList.php after submission
                setTimeout(() => {
                    window.location.href = 'loanList.php';
                }, 500); // Adjust delay if needed
            } else {
                alert("Nombor kakitangan ALK diperlukan.");
            }
        }
    </script>

    <script>
    function rejectLoan() {
    const alkStaffNo = prompt("Sila masukkan nombor kakitangan ALK:");
    if (!alkStaffNo) {
        alert("Nombor kakitangan ALK diperlukan.");
        return;
    }

    const reason = prompt("Sila berikan sebab penolakan:");
    if (!reason) {
        alert("Sebab penolakan diperlukan.");
        return;
    }

    document.getElementById('alkStaffNoRejectInput').value = alkStaffNo;

    // Create a hidden input for reason dynamically
    let reasonInput = document.createElement("input");
    reasonInput.type = "hidden";
    reasonInput.name = "reason";
    reasonInput.value = reason;

    // Append to the form and submit
    let rejectForm = document.getElementById('rejectForm');
    rejectForm.appendChild(reasonInput);
    rejectForm.submit();
}

</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
