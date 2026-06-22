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
$approvalDate = date('Y-m-d');

if ($action === 'approve') {
    $alkStaffNo = $_GET['alkStaffNo'] ?? null;
    if (!$alkStaffNo) {
        die("ALK Staff No is required for approval.");
    }
    $status = 2; 
} elseif ($action === 'reject') {
    $alkStaffNo = $_GET['alkStaffNo'] ?? null;
    $status = 3;
    $approvalDate = null;  
} else {
    die("Invalid action.");
}

if ($action === 'reject') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab keahlian tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Reason is required!');
                window.location.href = 'membershipList.php';
            } else {
                window.location.href = 'membershipListProcess.php?staffNo=$staffNo&action=$action&alkStaffNo=$alkStaffNo&reason=' + encodeURIComponent(reason);
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
            INSERT INTO alkApplicationFeedback (email, reason, dateGiven) 
            VALUES ('$applicantEmail', '$reason', CURDATE())";

        if (!mysqli_query($con, $insertFeedbackQuery)) {
            echo "<script>
                alert('Error saving feedback: " . mysqli_error($con) . "');
                window.location.href = 'membershipList.php';
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

        // try {
        //     $mail = new PHPMailer(true);
        //     include 'smtpconnect.php'; // Include SMTP configuration
        //     $mail->addAddress($applicantEmail);
        //     $mail->isHTML(true);
        //     $mail->Subject = $emailSubject;
        //     $mail->Body = $emailBody;
        //     $mail->send();
        // } catch (Exception $e) {
        //     echo "<script>
        //         alert('Email gagal dihantar: " . $e->getMessage() . "');
        //         window.location.href = 'reviewMembershipList.php';
        //     </script>";
        //     exit;
        // }

        $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
        if($sendingEmail != 1){
            echo "<script>
                alert('$sendingEmail');
                window.location.href = 'membershipList.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Error: No matching email found for the provided staff number.');
            window.location.href = 'membershipList.php';
        </script>";
        exit;
    }
}
// Update the `membership` table
$sql = "UPDATE membership 
        SET membershipStatus = $status, 
            membershipApproveDate = " . ($approvalDate ? "'$approvalDate'" : "NULL") . ",
            alkStaffNo = " . ($alkStaffNo ? "'$alkStaffNo'" : "NULL") . "
        WHERE staffNo = '$staffNo'";

// Execute the query
if (mysqli_query($con, $sql)) {
    echo "<script type='text/javascript'>
            alert('Status berjaya dikemaskini!.');
            window.location.href = 'membershipList.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Status gagal dikemaskini: " . mysqli_error($con) . "');
            window.location.href = 'membershipList.php';
          </script>";
}
?>
