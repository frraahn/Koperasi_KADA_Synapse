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
if (!isset($_GET['staffNo'], $_GET['action'], $_GET['loanID'])) {
    die("Invalid request.");
}

$staffNo = $_GET['staffNo'];
$action = $_GET['action'];
$loanID = $_GET['loanID'];
$reviewDate = date('Y-m-d');

// Determine the status based on the action
if ($action === 'Lengkap') {
    $status = 5; // Status for "Lulus"
} elseif ($action === 'Tidak Lengkap') {
    $status = 4; // Status for "Ditolak"
} else {
    die("Invalid action.");
}

// Prompt for admin staff number if not provided
if (!isset($_GET['adminStaffNo'])) {
    echo "<script>
        var adminStaffNo = prompt('Masukkan nombor Kakitangan Admin:');
        if (adminStaffNo === null || adminStaffNo.trim() === '') {
            alert('Admin staff number is required!');
            window.location.href = 'reviewLoanList.php';
        } else {
            window.location.href = 'reviewLoanListProcess.php?staffNo=$staffNo&loanID=$loanID&action=$action&adminStaffNo=' + encodeURIComponent(adminStaffNo);
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
        alert('Error: Admin staff number not found.');
        window.location.href = 'reviewLoanList.php';
    </script>";
    exit;
}

// If "Tidak Lengkap," ask for rejection reason
if ($action === 'Tidak Lengkap') {
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab permohonan tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Reason is required!');
                window.location.href = 'reviewLoanList.php';
            } else {
                window.location.href = 'reviewLoanListProcess.php?staffNo=$staffNo&loanID=$loanID&action=$action&adminStaffNo=$adminStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }

    $reason = $_GET['reason'];

    // Insert feedback into ApplicationFeedback table
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
            window.location.href = 'reviewLoanList.php';
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
            window.location.href = 'reviewLoanList.php';
        </script>";
        exit;
    }
    
} else {
    echo "<script>
        alert('Error retrieving applicant email.');
        window.location.href = 'reviewLoanList.php';
    </script>";
    exit;
}
}

// Update the loan table
$updateLoanQuery = "
    UPDATE loan 
    SET loanStatus = $status, 
        loanReviewDate = '$reviewDate', 
        adminStaffNo = '$adminStaffNo'
    WHERE staffNo = '$staffNo' AND loanID = '$loanID'";

if (mysqli_query($con, $updateLoanQuery)) {
    echo "<script>
        alert('Status dikemaskini dengan jayanya.');
        window.location.href = 'reviewLoanList.php';
    </script>";
} else {
    echo "<script>
        alert('Error updating loan status: " . mysqli_error($con) . "');
        window.location.href = 'reviewLoanList.php';
    </script>";
}
?>
