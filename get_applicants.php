<?php
require 'dbconnect.php'; // Ensure database connection

header('Content-Type: application/json'); // JSON Response

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $query = $pdo->prepare("SELECT applicantName, applicantIC, applicantPF, staffNo, applicantPhoneNumber 
                            FROM applicant 
                            WHERE applicantName LIKE ? 
                            LIMIT 10");
    $query->execute(["%$search%"]);

    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]); // Return empty array if no search term
}
?>
