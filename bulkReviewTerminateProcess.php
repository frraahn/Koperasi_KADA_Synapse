<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
include 'dbconnect.php';
require 'vendor/autoload.php';
include 'sendemail.php';

if (!isset($_SESSION['email'])) {
    die("Session user ID is not set.");
}

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method. Expected POST, got " . $_SERVER['REQUEST_METHOD']);
}

// Retrieve data from POST
$action = $_POST['action'] ?? null;
$adminStaffNo = $_POST['adminStaffNo'] ?? null;
$staffNos = $_POST['staffNos'] ?? [];

if (!$action) {
    die("Error: Action is required.");
}
if (!$adminStaffNo) {
    die("Error: Admin Staff Number is required.");
}
if (empty($staffNos)) {
    die("Error: No applicants selected.");
}

// Validate admin staff number
$checkAdminQuery = "SELECT staffNo FROM admin WHERE staffNo = ?";
if ($checkAdminStmt = mysqli_prepare($con, $checkAdminQuery)) {
    mysqli_stmt_bind_param($checkAdminStmt, "s", $adminStaffNo);
    mysqli_stmt_execute($checkAdminStmt);
    $checkAdminResult = mysqli_stmt_get_result($checkAdminStmt);

    if ($checkAdminResult->num_rows == 0) {
        die("Error: Admin Staff Number '$adminStaffNo' does not exist.");
    }
    mysqli_stmt_close($checkAdminStmt);
}

// Set status based on action
$status = ($action === 'approve') ? 5 : 4; // 5 = Approved, 4 = Rejected
$reason = $_POST['reason'] ?? '';

if ($action === 'reject' && empty($reason)) {
    die("Error: Rejection reason is required.");
}

$updatedRows = 0; // Track number of rows updated

foreach ($staffNos as $staffNo) {
    if (empty($staffNo)) continue; // Skip empty values

    // Update existing record in membershipend
    $query = "UPDATE membershipend SET status = ?, reason = ?, approveDate = NOW(), adminStaffNo = ? WHERE staffNo = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "isss", $status, $reason, $adminStaffNo, $staffNo);
        mysqli_stmt_execute($stmt);
        $updatedRows += mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
    } else {
        error_log("SQL Error: " . mysqli_error($con));
        die("Error updating staffNo: $staffNo - " . mysqli_error($con));
    }

    // Send rejection email if action is 'reject'
    if ($action === 'reject') {
        $getEmailQuery = "
            SELECT TRIM(u.email) AS email, u.firstName, u.lastName 
            FROM users u 
            LEFT JOIN applicant a ON a.email = u.email 
            WHERE a.staffNo = ?";

        if ($emailStmt = mysqli_prepare($con, $getEmailQuery)) {
            mysqli_stmt_bind_param($emailStmt, "s", $staffNo);
            mysqli_stmt_execute($emailStmt);
            $emailResult = mysqli_stmt_get_result($emailStmt);

            if ($emailRow = mysqli_fetch_assoc($emailResult)) {
                $applicantEmail = $emailRow['email'];
                $applicantName = $emailRow['firstName'] . ' ' . $emailRow['lastName'];

                if (!empty($applicantEmail)) {
                    // Insert feedback into adminApplicationFeedback table
                    $feedbackQuery = "INSERT INTO adminapplicationfeedback (email, reason, dateGiven) VALUES (?, ?, NOW())";
                    if ($feedbackStmt = mysqli_prepare($con, $feedbackQuery)) {
                        mysqli_stmt_bind_param($feedbackStmt, "ss", $applicantEmail, $reason);
                        mysqli_stmt_execute($feedbackStmt);
                        mysqli_stmt_close($feedbackStmt);
                    }

                    // Send email notification
                    $emailSubject = 'Maklumbalas Keahlian';
                    $emailBody = "
                        <p>Salam sejahtera, $applicantName.</p>
                        <p>Permohonan tamat keahlian anda telah ditolak kerana:</p>
                        <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
                        <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
                        <p>Salam hormat,</p>
                        <p>Koperasi Kakitangan KADA Kelantan Berhad</p>";

                    $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
                    if ($sendingEmail != 1) {
                        echo "<script>alert('Email failed: $sendingEmail'); window.location.href = 'reviewterminate.php';</script>";
                        exit;
                    }
                }
            }
            mysqli_stmt_close($emailStmt);
        }
    }
}

// Redirect with success message
if ($updatedRows > 0) {
    echo "<script>alert('Status berjaya dikemaskini!'); window.location.href = 'reviewterminate.php';</script>";
} else {
    echo "<script>alert('No records were updated. Check if staffNo exists.'); window.location.href = 'reviewterminate.php';</script>";
}

// Close DB connection
mysqli_close($con);
?>
