<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include 'dbconnect.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer
// use PHPMailer\PHPMailer\PHPMailer;

include 'sendemail.php';

// Validate input
if (!isset($_GET['action']) || !isset($_GET['staffNo'])) {
    die("Invalid request.");
}

$action = $_GET['action'];
$staffNo = $_GET['staffNo'];

// Initialize variables
$membershipStatus = null;
$approvalDate = null;
$alkStaffNo = null;

// Handle actions
if ($action === 'approve') {
    if (!isset($_GET['alkStaffNo'])) {
        die("ALK Staff No is required for approval.");
    }
    $membershipStatus = 2; 
    $approvalDate = date('Y-m-d');
    $alkStaffNo = $_GET['alkStaffNo'];
} elseif ($action === 'reject') {
    $membershipStatus = 3; 
    $alkStaffNo = $_GET['alkStaffNo'];
} else {
    die("Invalid action.");
}

if ($action === 'reject') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab keahlian tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Sebab adalah wajib!');
                window.location.href = 'viewMembership.php';
            } else {
                window.location.href = 'viewMembershipProcess.php?staffNo=$staffNo&action=$action&alkStaffNo=$alkStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }

    $reason = $_GET['reason'];

    $getEmailQuery = "
        SELECT u.email, u.firstName, u.lastName 
        FROM users u 
        JOIN applicant a ON a.email = u.email
        WHERE a.staffNo = '$staffNo'";
    $emailResult = mysqli_query($con, $getEmailQuery);

    if ($emailResult && mysqli_num_rows($emailResult) > 0) {
        $emailRow = mysqli_fetch_assoc($emailResult);
        $applicantEmail = trim($emailRow['email']);
        $applicantName = $emailRow['firstName'] . ' ' . $emailRow['lastName'];

        // Insert feedback into ApplicationFeedback table
        $insertFeedbackQuery = "
            INSERT INTO alkApplicationFeedback (email, reason, dateGiven) 
            VALUES ('$applicantEmail', '$reason', CURDATE())";

        if (!mysqli_query($con, $insertFeedbackQuery)) {
            echo "<script>
                alert('Error saving feedback: " . mysqli_error($con) . "');
                window.location.href = 'viewMembership.php';
            </script>";
            exit;
        }

                $emailSubject = 'Maklumbalas Keahlian';
        $emailBody = "
            <p>Salam sejahtera, $applicantName.</p>
            <p>Permohonan keahlian anda telah ditolak kerana:</p>
            <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
            <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
            <p>Salam hormat,</p>
            <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
        ";

 $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
        if($sendingEmail != 1){
            echo "<script>
                alert('$sendingEmail');
                window.location.href = 'viewMembership.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Error: No matching email found for the provided staff number.');
            window.location.href = 'viewMembership.php';
        </script>";
        exit;
    }
}


// Update query
$sql = "
    UPDATE membership 
    SET membershipStatus = '$membershipStatus', 
        membershipApproveDate = " . ($approvalDate ? "'$approvalDate'" : "NULL") . ", 
        alkStaffNo = " . ($alkStaffNo ? "'$alkStaffNo'" : "NULL") . " 
    WHERE staffNo = '$staffNo'
";

// Execute query
if (mysqli_query($con, $sql)) {
    echo "<script>alert('Status berjaya dikemas kini'); window.location.href = 'membershipList.php';</script>";
} else {
    echo "<script>alert('Error updating status: " . mysqli_error($con) . "'); window.location.href = 'viewMembership.php';</script>";
}

mysqli_close($con);
?>
