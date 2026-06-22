<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

// Connect to DB
include('dbconnect.php');

// Start transaction
mysqli_begin_transaction($con);

try {
    // Fetch loan data for the logged-in user
    $userEmail = $_SESSION['email'];

    if (isset($_POST['reason'])) {
        $reason = mysqli_real_escape_string($con, $_POST['reason']); // Sanitize the input
    } else {
        $reason = ''; // If no reason is provided, set it as an empty string
    }

    $sql = "SELECT staffNo
                FROM applicant 
                JOIN users ON users.email = applicant.email
                WHERE users.email = '$userEmail'";
    $result = mysqli_query($con, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        throw new Exception("No staff number found for the user.");
    }

    // Loop through each loan and process form data dynamically
    while ($applicant = mysqli_fetch_assoc($result)) {
        $staffNo = $applicant['staffNo'];

        // Handle null values for optional fields
        $reason = isset($_POST['reason']) ? "'".mysqli_real_escape_string($con, $_POST['reason'])."'" : "NULL";
        $endDate = isset($_POST['endDate']) && !empty($_POST['endDate']) ? "'".$_POST['endDate']."'" : "NULL";
        $reviewDate = isset($_POST['reviewDate']) && !empty($_POST['reviewDate']) ? "'".$_POST['reviewDate']."'" : "NULL";
        $approveDate = isset($_POST['approveDate']) && !empty($_POST['approveDate']) ? "'".$_POST['approveDate']."'" : "NULL";
        $adminStaffNo = isset($_POST['adminStaffNo']) && !empty($_POST['adminStaffNo']) ? "'".$_POST['adminStaffNo']."'" : "NULL";
        $alkStaffNo = isset($_POST['alkStaffNo']) && !empty($_POST['alkStaffNo']) ? "'".$_POST['alkStaffNo']."'" : "NULL";

        // Insert into rescheduleRequest table
        $insertQuery = "INSERT INTO membershipend (staffNo, applyDate, endDate, reason, reviewDate, approveDate, adminStaffNo, alkStaffNo, status) 
                        VALUES ('$staffNo', CURRENT_TIMESTAMP(), $endDate, $reason, $reviewDate, $approveDate, $adminStaffNo, $alkStaffNo, '1')";

        if (!mysqli_query($con, $insertQuery)) {
            throw new Exception("Error inserting reschedule request for staffNo $staffNo: " . mysqli_error($con));
        }
    }

    // Commit transaction
    mysqli_commit($con);
    header('Location: statusberhenti.php');
} catch (Exception $e) {
    // Rollback transaction in case of error
    mysqli_rollback($con);
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($con);
?>
