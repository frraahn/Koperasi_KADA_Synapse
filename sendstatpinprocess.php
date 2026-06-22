<?php
session_start();

require 'vendor/autoload.php';
include 'dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST data received: " . print_r($_POST, true));
    
    $staffNo = $_POST['staffNo'] ?? null;
    $email = $_POST['email'] ?? null;
    $name = $_POST['name'] ?? null;
    $status = $_POST['status'] ?? null;

    try {
        if (!$staffNo || !$email || !$name || !$status) {
            error_log("Missing data - staffNo: $staffNo, email: $email, name: $name, status: $status");
            throw new Exception('Invalid input');
        }

        // Get loanID from staffNo
        $queryLoanID = "SELECT loanID FROM loan WHERE staffNo = ? AND loanStatus = ?";
        $stmtLoanID = $con->prepare($queryLoanID);
        $stmtLoanID->bind_param('ii', $staffNo, $status);
        
        if (!$stmtLoanID->execute()) {
            throw new Exception('Failed to get loanID');
        }
        
        $resultLoanID = $stmtLoanID->get_result();
        if ($resultLoanID->num_rows === 0) {
            throw new Exception('Loan record not found');
        }
        
        $row = $resultLoanID->fetch_assoc();
        $loanID = $row['loanID'];

        $emailSubject = '';
        $emailBody = '';

        if ($status == 2) {
            $emailSubject = 'PERMOHONAN PINJAMAN DITERIMA';
            $emailBody = "
                <p>Salam sejahtera, $name.</p>
                <p>Permohonan pinjaman anda telah DITERIMA.</p>
                <p>Salam hormat,</p>
                <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
            ";
        } elseif ($status == 3) {
            $emailSubject = 'PERMOHONAN PINJAMAN DITOLAK';
            $emailBody = "
                <p>Salam sejahtera, $name.</p>
                <p>Malangnya, permohonan pinjaman anda telah DITOLAK.</p>
                <p>Salam hormat,</p>
                <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
            ";
        } else {
            throw new Exception('Invalid status type');
        }

        $mail = new PHPMailer(true);

        include 'smtpconnect.php';

        $mail->clearAddresses();

        // Add recipient
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = $emailBody;

        if ($mail->send()) {
            // Update using loanID instead of staffNo
            $updateQuery = "UPDATE loan SET sendStatus = 1 WHERE loanID = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param('i', $loanID);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Email sent and database updated'];
            } else {
                throw new Exception('Failed to update database after sending email');
            }
        } else {
            // Update using loanID for failed email
            $updateQuery = "UPDATE loan SET sendStatus = 2 WHERE loanID = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param('i', $loanID);
            $stmt->execute();

            throw new Exception('Failed to send email');
        }

        $mail->clearAddresses();
        
    } catch (Exception $e) {
        error_log("Error in sendstatpinprocess.php: " . $e->getMessage());
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }

    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>