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
if (!isset($_GET['staffNo']) || !isset($_GET['action'])) {
    die("Invalid request.");
}

$staffNo = $_GET['staffNo'];
$action = $_GET['action'];
$reviewDate = date('Y-m-d');

// Map actions to corresponding status values in the status table
if ($action === 'Lengkap') {
    $status = 5; // Status ID for "Lulus"
} elseif ($action === 'Tidak Lengkap') {
    $status = 4; // Status ID for "Ditolak"
} else {
    die("Invalid action.");
}

// Handle adminStaffNo input
if (!isset($_GET['adminStaffNo'])) {
    // Prompt the admin to enter their staff number
    echo "<script type='text/javascript'>
            var adminStaffNo = prompt('Masukkan nombor Kakitangan Admin :');
            if (adminStaffNo === null || adminStaffNo.trim() === '') {
                alert('Nombor kakitangan admin adalah diperlukan!');
                window.location.href = 'reviewterminate.php';
            } else {
                window.location.href = 'reviewterminateprocess.php?staffNo=$staffNo&action=$action&adminStaffNo=' + encodeURIComponent(adminStaffNo);
            }
          </script>";
    exit;
}

// If adminStaffNo is already provided, validate it
$adminStaffNo = $_GET['adminStaffNo'];
$checkAdminQuery = "SELECT staffNo FROM admin WHERE staffNo = '$adminStaffNo'";
$checkAdminResult = mysqli_query($con, $checkAdminQuery);

if (!$checkAdminResult || mysqli_num_rows($checkAdminResult) === 0) {
    echo "<script type='text/javascript'>
            alert('Error: Nombor kakitangan admin tidak dijumpai.');
            window.location.href = 'reviewterminate.php';
          </script>";
    exit;
}

if ($action === 'Tidak Lengkap') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab permohonan tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Sebab adalah diperlukan!');
                window.location.href = 'reviewterminate.php';
            } else {
                window.location.href = 'reviewterminateprocess.php?staffNo=$staffNo&action=$action&adminStaffNo=$adminStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }

    $reason = $_GET['reason'];


        $insertFeedbackQuery = "
        INSERT INTO adminApplicationFeedback (email, reason, dateGiven) 
        VALUES (
            (SELECT u.email 
             FROM users u 
             JOIN applicant a ON a.email = u.email 
             WHERE a.staffNo = '$staffNo'), 
            '$reason', 
            CURDATE()
        )";

    if (!mysqli_query($con, $insertFeedbackQuery)) {
        echo "<script>
            alert('Error saving feedback: " . mysqli_error($con) . "');
            window.location.href = 'reviewLoanList.php';
        </script>";
        exit;
    }

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

    // Validate email format
    if (!filter_var($applicantEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email format: $applicantEmail");
        echo "<script>
            alert('Invalid email address retrieved for staffNo $staffNo.');
            window.location.href = 'reviewterminateprocess.php';
        </script>";
        exit;
    }

    $emailSubject = 'Maklumbalas Permohonan Pinjaman';
    $emailBody = "
        <p>Salam sejahtera, $applicantName.</p>
        <p>Permohonan pinjaman anda telah ditolak kerana:</p>
        <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
        <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
        <p>Salam hormat,</p>
        <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
    ";


    $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
    if($sendingEmail != 1){
        echo "<script>
            alert('$sendingEmail');
            window.location.href = 'reviewterminate.php';
        </script>";
        exit;
    }
    
} else {
    echo "<script>
        alert('Error retrieving applicant email.');
        window.location.href = 'reviewterminate.php';
    </script>";
    exit;
}
}


// Update the loan table with the provided adminStaffNo
$updateMembershipendQuery = "UPDATE membershipend 
        SET status = $status, 
            reviewDate = '$reviewDate', 
            adminStaffNo = '$adminStaffNo'
        WHERE staffNo = '$staffNo'";

// Execute the query
if (mysqli_query($con, $updateMembershipendQuery)) {
    echo "<script type='text/javascript'>
            alert('Status berjaya dikemaskini.');
            window.location.href = 'reviewterminate.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Error updating status: " . mysqli_error($con) . "');
            window.location.href = 'reviewterminate.php';
          </script>";
}
?>
