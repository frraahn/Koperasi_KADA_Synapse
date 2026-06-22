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
    $staffNos = $_POST['staffNos'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;
    
    if (empty($staffNos)) {
        die("No applicants selected.");
    }

    foreach ($staffNos as $staffNo) {
        if ($action === 'approve') {
            $query = "UPDATE membership SET membershipStatus = 5, membershipReviewDate = NOW(), adminStaffNo = ? WHERE staffNo = ?";
        } elseif ($action === 'reject') {
            $query = "UPDATE membership SET membershipStatus = 4, membershipReviewDate = NOW(), adminStaffNo = ? WHERE staffNo = ?";
        } else {
            die("Invalid action.");
        }
        
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "si", $adminStaffNo, $staffNo);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        if ($action === 'reject') {
            // Use LEFT JOIN to ensure we get email even if there is missing data
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
                        // Insert feedback only if email is valid
                        $feedbackQuery = "INSERT INTO adminapplicationfeedback (email, reason, dateGiven) VALUES (?, ?, NOW())";
                        if ($feedbackStmt = mysqli_prepare($con, $feedbackQuery)) {
                            mysqli_stmt_bind_param($feedbackStmt, "ss", $applicantEmail, $reason);
                            mysqli_stmt_execute($feedbackStmt);
                            mysqli_stmt_close($feedbackStmt);
                        }

                        // Send email to the applicant
                        $emailSubject = 'Maklumbalas Keahlian';
                        $emailBody = "<p>Salam sejahtera, $applicantName.</p>
                                      <p>Permohonan keahlian anda telah ditolak kerana:</p>
                                      <blockquote style='border-left: 4px solid #ccc; padding-left: 10px;'>$reason</blockquote>
                                      <p>Sila hubungi pihak koperasi untuk maklumat lanjut.</p>
                                      <p>Salam hormat,</p>
                                      <p>Koperasi Kakitangan KADA Kelantan Berhad</p>";

                        $sendingEmail = sendEmail($applicantEmail, $applicantName, $emailSubject, $emailBody);
                        if ($sendingEmail != 1) {
                            echo "<script>alert('$sendingEmail'); window.location.href = 'reviewMembershipList.php';</script>";
                            exit;
                        }
                    } else {
                        // Debugging output if email is empty
                        echo "<script>alert('Error: Email not found for StaffNo: $staffNo');</script>";
                    }
                } else {
                    echo "<script>alert('Error: No matching user found for StaffNo: $staffNo');</script>";
                }
                mysqli_stmt_close($emailStmt);
            }
        }
    }

    echo "Bulk action successfully processed.";
    header("Location: reviewMembershipList.php?status=success");
exit();

} else {
    die("Invalid request method.");
}
?>
