<?php
include('crssession.php');
if(!session_id()) {
    session_start();
}
include 'dbconnect.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer
// use PHPMailer\PHPMailer\PHPMailer;

include 'sendemail.php';

// Check if required parameters are present
if (!isset($_GET['staffNo'], $_GET['action'])) {
    die("Invalid request.");
}

$staffNo = $_GET['staffNo'];
$action = $_GET['action'];
$reviewDate = date('Y-m-d');

// Determine the status based on the action
if ($action === 'lengkap') {
    $status = 5; // Status for "Lulus"
} elseif ($action === 'tidak lengkap') {
    $status = 4; // Status for "Ditolak"
} else {
    die("Invalid action.");
}

// Prompt for admin staff number if not provided
if (!isset($_GET['adminStaffNo'])) {
    echo "<script>
        var adminStaffNo = prompt('Masukkan nombor Kakitangan Admin:');
        if (adminStaffNo === null || adminStaffNo.trim() === '') {
            alert('Nombor kakitangan admin adalah diperlukan!');
            window.location.href = 'reviewMembershipList.php';
        } else {
            window.location.href = 'reviewMembershipListProcess.php?staffNo=$staffNo&action=$action&adminStaffNo=' + encodeURIComponent(adminStaffNo);
        }
    </script>";
    exit;
}

$adminStaffNo = $_GET['adminStaffNo'];

// Validate admin staff number
$checkAdminQuery = "SELECT staffNo FROM admin WHERE staffNo = '$adminStaffNo'";
$checkAdminResult = mysqli_query($con, $checkAdminQuery);

if (!$checkAdminResult || mysqli_num_rows($checkAdminResult) === 0) {
    echo "<script>
        alert('Error: Nombor kakitangan admin tidak dijumpai.');
        window.location.href = 'reviewMembershipList.php';
    </script>";
    exit;
}

// If "tidak lengkap," ask for rejection reason
if ($action === 'tidak lengkap') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab keahlian tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Sebab adalah diperlukan!');
                window.location.href = 'reviewMembershipList.php';
            } else {
                window.location.href = 'reviewMembershipListProcess.php?staffNo=$staffNo&action=$action&adminStaffNo=$adminStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }

    $reason = $_GET['reason'];

    // Retrieve email and name from Users table
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
            INSERT INTO adminApplicationFeedback (email, reason, dateGiven) 
            VALUES ('$applicantEmail', '$reason', CURDATE())";

        if (!mysqli_query($con, $insertFeedbackQuery)) {
            echo "<script>
                alert('Error saving feedback: " . mysqli_error($con) . "');
                window.location.href = 'reviewMembershipList.php';
            </script>";
            exit;
        }

        // Send email to the applicant
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
                window.location.href = 'reviewMembershipList.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Error: No matching email found for the provided staff number.');
            window.location.href = 'reviewMembershipList.php';
        </script>";
        exit;
    }
}

// Update membership table
$updateMembershipQuery = "
    UPDATE membership 
    SET membershipStatus = $status, 
        membershipReviewDate = '$reviewDate', 
        adminStaffNo = '$adminStaffNo'
    WHERE staffNo = '$staffNo'";

if (mysqli_query($con, $updateMembershipQuery)) {
    echo "<script>
        alert('Status dikemaskini dengan jayanya.');
        window.location.href = 'reviewMembershipList.php';
    </script>";
} else {
    echo "<script>
        alert('Error updating membership status: " . mysqli_error($con) . "');
        window.location.href = 'reviewMembershipList.php';
    </script>";
}
?>
