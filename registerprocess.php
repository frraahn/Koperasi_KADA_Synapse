<?php
// Connect to DB
include('dbconnect.php');

// Retrieve data from form
$fName = $_POST['fName'];
$lName = $_POST['lName'];
$femail = $_POST['femail'];
$fpwd = $_POST['fpwd'];
$confirmPassword = $_POST['confirmPassword'];
$uType = $_POST['uType'];

if ($fpwd !== $confirmPassword){
    header('Location: register.php?error=password_mismatch');
    exit;
}

// Hash the password
$hashedPassword = password_hash($fpwd, PASSWORD_DEFAULT);

// Insert hashed password into the database
$sql = "INSERT INTO users (firstName, lastName, email, email_verified_at, password, userType, created_at)
        VALUES ('$fName', '$lName', '$femail', CURRENT_TIMESTAMP(), '$hashedPassword', '$uType', CURRENT_TIMESTAMP())";

if (mysqli_query($con, $sql)) {
    //Registration successful
    header('Location: login.php');
} else {
    //Error handling (optional)
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
}

// Close connection
mysqli_close($con);
?>
