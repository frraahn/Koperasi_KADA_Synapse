<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
// Connect to DB
include('dbconnect.php');

// Retrieve session staffNo
$sNo = isset($_SESSION['staffNo']) ? $_SESSION['staffNo'] : null;

// Check if staffNo is set
if (!$sNo) {
    die("Error: staffNo is not set in the session.");
}

// Retrieve the total count of family members
$fCount = isset($_POST['fCount']) ? (int)$_POST['fCount'] : 0;

// Validate and sanitize form data
if (!empty($_POST['family']) && is_array($_POST['family']) && $fCount > 0) {
    foreach ($_POST['family'] as $index => $member) {
        // Retrieve and sanitize data for each family member
        $relationship = mysqli_real_escape_string($con, $member['relationship']);
        $name = mysqli_real_escape_string($con, $member['name']);
        $ic_number = mysqli_real_escape_string($con, $member['ic_number']);
        
        // Validate data
        if (!empty($relationship) && !empty($name) && !empty($ic_number)) {
            // SQL Insert Operation for each family member
            $sql = "INSERT INTO familyinfo (staffNo, count, relationship, familyName, familyIC)
                    VALUES ('$sNo', '$fCount', '$relationship', '$name', '$ic_number')";
            mysqli_query($con, $sql) or die("Error: " . mysqli_error($con));
        }
    }
}

// Close connection
mysqli_close($con);

// Redirect user to confirmation or next step
header('Location: bankInfo.php');
exit();
?>
