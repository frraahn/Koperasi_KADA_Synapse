<?php
session_start();

require 'vendor/autoload.php';
include 'dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffNo = $_POST['staffNo'] ?? null;
    $email = $_POST['email'] ?? null;
    $name = $_POST['name'] ?? null;
    $status = $_POST['status'] ?? null;

    try {
        if (!$staffNo || !$email || !$name || !$status) {
            throw new Exception('Invalid input');
        }

        // Initialize email content
        $emailSubject = '';
        $emailBody = '';

        if ($status == 2) {
            $emailSubject = 'PERMOHONAN ANGGOTA DITERIMA';
            $emailBody = "
                <p>Salam sejahtera, $name.</p>
                <p>Permohonan anggota anda telah DITERIMA.</p>
                <p>Salam hormat,</p>
                <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
            ";
        } elseif ($status == 3) {
            $emailSubject = 'PERMOHONAN ANGGOTA DITOLAK';
            $emailBody = "
                <p>Salam sejahtera, $name.</p>
                <p>Permohonan anggota anda telah DITOLAK.</p>
                <p>Salam hormat,</p>
                <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
            ";
        } else {
            throw new Exception('Invalid status type');
        }

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        include 'smtpconnect.php';

        $mail->clearAddresses();

        // Add recipient
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = $emailBody;

        if ($mail->send()) {
            // Update database if email is sent successfully
            $updateQuery = "UPDATE membership SET sendStatus = 1 WHERE staffNo = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param('i', $staffNo);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Email sent and database updated'];
            } else {
                throw new Exception('Failed to update database after sending email');
            }
        } else {
            // Log failure to send email
            $updateQuery = "UPDATE membership SET sendStatus = 2 WHERE staffNo = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param('i', $staffNo);
            $stmt->execute();

            throw new Exception('Failed to send email');
        }

        $mail->clearAddresses();
        
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }

    // Return JSON response
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>