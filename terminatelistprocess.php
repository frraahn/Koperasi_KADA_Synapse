<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
include 'dbconnect.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer
include 'sendemail.php';

// Validate required GET parameters
if (!isset($_GET['staffNo'], $_GET['action'])) {
    die("Error: Missing required parameters (staffNo or action).");
}

$staffNo = mysqli_real_escape_string($con, $_GET['staffNo']);
$action = $_GET['action'];

$status = null; // Loan status
$approvalDate = null; // Approval date
$alkStaffNo = $_GET['alkStaffNo'] ?? null;

// Prompt for ALK Staff No if missing
if ($action === 'approve' || $action === 'reject') {
    if (!$alkStaffNo) {
        echo "<script>
            var inputALK = prompt('Masukkan ALK Staff No:');
            if (inputALK === null || inputALK.trim() === '') {
                alert('ALK Staff No diperlukan!');
                window.location.href = 'terminatelist.php';
            } else {
                window.location.href = 'terminatelistprocess.php?staffNo=$staffNo&action=$action&alkStaffNo=' + encodeURIComponent(inputALK);
            }
        </script>";
        exit;
    }
}

// Map actions to corresponding status and handle additional data
if ($action === 'approve') {
    $status = 2; // Status ID for "Lulus" (Approved)
    $membershipStatus = 6;
    $approvalDate = date('Y-m-d'); // Current date
} elseif ($action === 'reject') {
    // Prompt for reason if missing
    if (!isset($_GET['reason'])) {
        echo "<script>
            var reason = prompt('Nyatakan sebab keahlian tidak lengkap:');
            if (reason === null || reason.trim() === '') {
                alert('Reason is required!');
                window.location.href = 'terminatelist.php';
            } else {
                window.location.href = 'terminatelistprocess.php?staffNo=$staffNo&action=$action&alkStaffNo=$alkStaffNo&reason=' + encodeURIComponent(reason);
            }
        </script>";
        exit;
    }
    $reason = mysqli_real_escape_string($con, $_GET['reason']);
    $status = 3; // Status ID for "Ditolak" (Rejected)
}

// Build and execute SQL query
$alkStaffNoValue = ($alkStaffNo !== null) ? "'$alkStaffNo'" : "NULL";
$approvalDateValue = ($approvalDate !== null) ? "'$approvalDate'" : "NULL";

$sql = "UPDATE membershipend 
        SET status = $status, 
            approveDate = $approvalDateValue, 
            alkStaffNo = $alkStaffNoValue 
        WHERE staffNo = '$staffNo'";

// Execute the query and handle result
if (mysqli_query($con, $sql)) {
    // Handle rejection email & feedback
    if ($action === 'reject') {
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
            mysqli_query($con, $insertFeedbackQuery);

            // Send email to the applicant
            $emailSubject = 'Maklumbalas Keahlian';
            $emailBody = "
                <p>Salam sejahtera, $applicantName.</p>
                <p>Permohonan berhenti anggota anda telah ditolak kerana:</p>
                <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
                <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
                <p>Salam hormat,</p>
                <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
            ";

            sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
        }
    }

    if($status === 2){
    $membershipSql = "UPDATE membership 
        SET membershipStatus = $membershipStatus
        WHERE staffNo = '$staffNo'";

    if (!mysqli_query($con, $membershipSql)) {
            error_log("SQL Error (Membership Update): " . mysqli_error($con), 0);
            echo "<script type='text/javascript'>
                    alert('Status gagal dikemaskini: " . mysqli_error($con) . "');
                    window.location.href = 'terminatelist.php';
                  </script>";
            exit();
    }
}

    echo "<script type='text/javascript'>
            alert('Status berjaya dikemaskini!');
            window.location.href = 'terminatelist.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Status gagal dikemaskini! " . mysqli_error($con) . "');
            window.location.href = 'terminatelist.php';
          </script>";
}

// Close the database connection
mysqli_close($con);
?>
