<?php
include 'dbconnect.php';
require_once 'statementfunctions.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

date_default_timezone_set('Asia/Kuala_Lumpur');

$response = array(
    'success' => false,
    'error' => null,
    'results' => array()
);

try {
    if (!isset($_POST['staffNos']) || !is_array($_POST['staffNos'])) {
        throw new Exception('No staff numbers provided');
    }

    $staffNos = $_POST['staffNos'];
    $currentDate = date('Y-m-d H:i:s');
    $month = isset($_POST['month']) ? $_POST['month'] : date('n');
    $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
    
    $dateString = "$year-$month-01";
    $endOfMonth = date('Y-m-t', strtotime($dateString));

    $processor = new StatementProcessor($con);

    foreach ($staffNos as $staffNo) {
        $result = array(
            'staffNo' => $staffNo,
            'success' => false,
            'error' => null,
            'notificationLatestDate' => null
        );

        try {
            // Process each member
            $memberData = $processor->getMemberData($staffNo, $endOfMonth);
            $pdfPath = $processor->generatePDF($memberData, $endOfMonth);
            $processor->sendEmail($memberData['member']['email'], $pdfPath);
            $processor->updateDatabase($memberData['member']['email'], $currentDate);

            $result['success'] = true;
            $result['notificationLatestDate'] = $currentDate;

            // Clean up PDF
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $response['results'][] = $result;
    }

    // If at least one staff member was processed successfully
    if (count(array_filter($response['results'], function($r) { return $r['success']; })) > 0) {
        $response['success'] = true;
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log('Error in batch_send.php: ' . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);