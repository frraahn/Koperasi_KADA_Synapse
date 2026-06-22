<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
include 'dbconnect.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer
include 'sendemail.php';

// Validate required GET parameters
if (!isset($_GET['staffNo'], $_GET['action'], $_GET['loanID'])) {
    die("Invalid request.");
}

$staffNo = mysqli_real_escape_string($con, $_GET['staffNo']);
$loanID = mysqli_real_escape_string($con, $_GET['loanID']);
$action = $_GET['action'];

$status = null; // Loan status
$approvalDate = null; // Approval date
$alkStaffNo = null; // Approver staff number

if ($action === 'approve' || $action === 'reject') {
    if (!isset($_GET['alkStaffNo'])) {
        echo "<script>
            var alkStaffNo = prompt('Sila masukkan No ALK:');
            if (alkStaffNo === null || alkStaffNo.trim() === '') {
                alert('No ALK diperlukan!');
                window.location.href = 'loanList.php';
            } else {
                window.location.href = 'loanListProcess.php?staffNo=$staffNo&loanID=$loanID&action=$action&alkStaffNo=' + encodeURIComponent(alkStaffNo);
            }
        </script>";
        exit;
    }
    $alkStaffNo = mysqli_real_escape_string($con, $_GET['alkStaffNo']);
}

if ($action === 'approve') {
    $status = 2; // Status ID for "Lulus" (Approved)
    $approvalDate = date('Y-m-d'); // Current date
} elseif ($action === 'reject') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab permohonan tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Reason is required!');
                window.location.href = 'loanList.php';
            } else {
                window.location.href = 'loanListProcess.php?staffNo=$staffNo&loanID=$loanID&action=$action&alkStaffNo=$alkStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }
    $status = 3; // Status ID for "Ditolak" (Rejected)
    $approvalDate = null;
    $reason = mysqli_real_escape_string($con, $_GET['reason']);

    $insertFeedbackQuery = "
        INSERT INTO alkApplicationFeedback (email, reason, dateGiven) 
        VALUES (
            (SELECT u.email FROM users u JOIN applicant a ON a.email = u.email WHERE a.staffNo = '$staffNo'), 
            '$reason', 
            CURDATE()
        )";

    if (!mysqli_query($con, $insertFeedbackQuery)) {
        echo "<script>alert('Error saving feedback: " . mysqli_error($con) . "'); window.location.href = 'loanList.php';</script>";
        exit;
    }
}

$alkStaffNoValue = ($alkStaffNo !== null) ? "'$alkStaffNo'" : "NULL";
$approvalDateValue = ($approvalDate !== null) ? "'$approvalDate'" : "NULL";

$sql = "UPDATE loan SET loanStatus = $status, loanApproveDate = $approvalDateValue, alkStaffNo = $alkStaffNoValue WHERE staffNo = '$staffNo' AND loanID = '$loanID'";

if (mysqli_query($con, $sql)) {
    echo "<script>alert('Status berjaya dikemaskini!'); window.location.href = 'loanList.php';</script>";
} else {
    error_log("SQL Error: " . mysqli_error($con), 0);
    echo "<script>alert('Status gagal dikemaskini!" . mysqli_error($con) . "'); window.location.href = 'loanList.php';</script>";
}

mysqli_close($con);
?>
