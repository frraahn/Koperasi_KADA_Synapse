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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $adminStaffNo = $_POST['adminStaffNo'];
    $loanSelections = $_POST['loanSelections']; // Expecting an array of staffNo-loanID pairs
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    if (empty($loanSelections)) {
        die("No loan applications selected.");
    }

    foreach ($loanSelections as $selection) {
        list($staffNo, $loanID) = explode('-', $selection);

        if ($action === 'approve') {
            $query = "UPDATE loan SET loanStatus = 2, loanReviewDate = NOW(), alkStaffNo = ? WHERE staffNo = ? AND loanID = ?";
        } elseif ($action === 'reject') {
            $query = "UPDATE loan SET loanStatus = 3, loanReviewDate = NOW(), alkStaffNo = ? WHERE staffNo = ? AND loanID = ?";
        } else {
            die("Invalid action.");
        }

        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "sii", $alkStaffNo, $staffNo, $loanID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($action === 'reject') {
            // Retrieve applicant email and name based on loanID
            $getEmailQuery = "
                SELECT TRIM(u.email) AS email, u.firstName, u.lastName 
                FROM users u 
                LEFT JOIN applicant a ON a.email = u.email 
                LEFT JOIN loan l ON a.staffNo = l.staffNo 
                WHERE a.staffNo = ? AND l.loanID = ?";
                
            if ($emailStmt = mysqli_prepare($con, $getEmailQuery)) {
                mysqli_stmt_bind_param($emailStmt, "si", $staffNo, $loanID);
                mysqli_stmt_execute($emailStmt);
                $emailResult = mysqli_stmt_get_result($emailStmt);

                if ($emailRow = mysqli_fetch_assoc($emailResult)) {
                    $applicantEmail = $emailRow['email'];
                    $applicantName = $emailRow['firstName'] . ' ' . $emailRow['lastName'];

                    if (!empty($applicantEmail)) {
                        // Insert feedback only if email is valid
                        $feedbackQuery = "INSERT INTO alkapplicationfeedback (email, reason, dateGiven) VALUES (?, ?, NOW())";
                        if ($feedbackStmt = mysqli_prepare($con, $feedbackQuery)) {
                            mysqli_stmt_bind_param($feedbackStmt, "ss", $applicantEmail, $reason);
                            mysqli_stmt_execute($feedbackStmt);
                            mysqli_stmt_close($feedbackStmt);
                        }

                        // Send email to the applicant
                        $emailSubject = 'Maklumbalas Keahlian';
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
                    } else {
                        echo "<script>alert('Error: Email not found for StaffNo: $staffNo and LoanID: $loanID');</script>";
                    }
                } else {
                    echo "<script>alert('Error: No matching user found for StaffNo: $staffNo and LoanID: $loanID');</script>";
                }
                mysqli_stmt_close($emailStmt);
            }
        }
    }

    echo "Bulk action successfully processed.";
    header("Location: loanList.php?status=success");
    exit();
} else {
    die("Invalid request method.");
}
?>
