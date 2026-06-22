<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
include('dbconnect.php');

// Initialize variables
$membershipApproveDate = $membershipApproveDate ?? null;
$membershipReviewDate = $membershipReviewDate ?? null;
$alkStaffNo = $alkStaffNo ?? null;
$adminStaffNo = $adminStaffNo ?? null;

// Check if user email exists in session
if (!isset($_SESSION['email'])) {
    echo "User email is not set in session.";
    exit();
}

$userEmail = $_SESSION['email'];

// Retrieve staffNo from the applicant table
$sqlStaffNo = "SELECT staffNo FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sqlStaffNo);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $staffNo = $row['staffNo'];

    // Insert into membership table
    $sql = "INSERT INTO membership (membershipStatus, membershipApplyDate, membershipApproveDate, membershipReviewDate, staffNo, alkStaffNo, adminStaffNo)
            VALUES ('1', CURRENT_TIMESTAMP(), 
                        " . ($membershipApproveDate ? "'$membershipApproveDate'" : "NULL") . ", 
                        " . ($membershipReviewDate ? "'$membershipReviewDate'" : "NULL") . ", 
                        '$staffNo', 
                        " . ($alkStaffNo ? "'$alkStaffNo'" : "NULL") . ", 
                        " . ($adminStaffNo ? "'$adminStaffNo'" : "NULL") . ")";

    if (mysqli_query($con, $sql)) {
        // Redirect to membership status page
        header('Location: membershipstatus.php');
        exit();
    } else {
        echo "Error inserting data: " . mysqli_error($con);
    }
} else {
    echo "Error: StaffNo not found for email $userEmail.";
}

// Close database connection
mysqli_close($con);
?>
