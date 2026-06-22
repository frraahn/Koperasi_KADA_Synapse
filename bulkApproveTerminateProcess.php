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
    $action = $_POST['action'] ?? null;
    $alkStaffNo = $_POST['alkStaffNo'] ?? null;
    $staffNos = $_POST['staffNos'] ?? [];

    if (!$action) {
        die("Error: Action is required.");
    }
    if (!$alkStaffNo) {
        die("Error: ALK Staff Number is required.");
    }
    if (empty($staffNos)) {
        die("No applicants selected.");
    }

    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    if ($action === 'reject' && empty($reason)) {
        die("Error: Sebab penolakan diperlukan.");
    }
    $reason = !empty($reason) ? $reason : 'Tiada sebab diberikan'; // Default reason

    // Check if ALK StaffNo exists
    $checkALKQuery = "SELECT staffNo FROM alk WHERE staffNo = ?";
    if ($checkALKStmt = mysqli_prepare($con, $checkALKQuery)) {
        mysqli_stmt_bind_param($checkALKStmt, "s", $alkStaffNo);
        mysqli_stmt_execute($checkALKStmt);
        $checkALKResult = mysqli_stmt_get_result($checkALKStmt);

        if ($checkALKResult->num_rows == 0) {
            die("Error: ALK Staff Number '$alkStaffNo' does not exist in the `alk` table.");
        }
        mysqli_stmt_close($checkALKStmt);
    }

    foreach ($staffNos as $staffNo) {
        if (empty($staffNo)) {
            die("Error: Staff Number cannot be empty.");
        }

        // Update existing record in membershipend table
        $status = ($action === 'approve') ? 2 : 3; // 2 = Approved, 3 = Rejected
        $query = "UPDATE membershipend SET status = ?, reason = ?, approveDate = NOW(), alkStaffNo = ? WHERE staffNo = ?";
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "isss", $status, $reason, $alkStaffNo, $staffNo);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($status === 2) {
            $membership = 6; // Set membership status to 6
            $membershipSql = "UPDATE membership SET membershipStatus = ? WHERE staffNo = ?";
            if ($membershipStmt = mysqli_prepare($con, $membershipSql)) {
                mysqli_stmt_bind_param($membershipStmt, "is", $membership, $staffNo);
                mysqli_stmt_execute($membershipStmt);
                mysqli_stmt_close($membershipStmt);
            }
        }

        // Handle rejection email
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
                        // Insert feedback
                        $feedbackQuery = "INSERT INTO alkapplicationfeedback (email, reason, dateGiven) VALUES (?, ?, NOW())";
                        if ($feedbackStmt = mysqli_prepare($con, $feedbackQuery)) {
                            mysqli_stmt_bind_param($feedbackStmt, "ss", $applicantEmail, $reason);
                            mysqli_stmt_execute($feedbackStmt);
                            mysqli_stmt_close($feedbackStmt);
                        }

                        // Send email to the applicant
                        $emailSubject = 'Maklumbalas Keahlian';
                        $emailBody = "<p>Salam sejahtera, $applicantName.</p>
                                      <p>Permohonan tamat keahlian anda telah ditolak kerana:</p>
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
                        echo "<script>alert('Error: Email not found for StaffNo: $staffNo');</script>";
                    }
                } else {
                    echo "<script>alert('Error: No matching user found for StaffNo: $staffNo');</script>";
                }
                mysqli_stmt_close($emailStmt);
            }
        }
    }

    // Display success or failure message
    if ($action === 'approve') {
        echo "<script>
            alert('Permohonan diluluskan! Status berjaya dikemaskini.');
            window.location.href = 'terminateList.php';
        </script>";
    } else {
        echo "<script>
            alert('Permohonan ditolak! Status berjaya dikemaskini.');
            window.location.href = 'terminateList.php';
        </script>";
    }
    exit();
} else {
    die("Invalid request method.");
}
?>
