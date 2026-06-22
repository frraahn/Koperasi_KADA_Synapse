<?php
// Set Database Parameters
$servername = "localhost:3307"; // Corrected port format
$username = "root";
$password = "";
$dbname = "db_kada";

// Connect to Database
$con = mysqli_connect($servername, $username, $password, $dbname);

// Check Connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>
