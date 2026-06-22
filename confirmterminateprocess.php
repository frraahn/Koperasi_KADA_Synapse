<?php
include 'dbconnect.php';

// Start session if not already started
if (!session_id()) {
    session_start();
}

// Validate required GET parameters
if (!isset($_GET['staffNo'], $_GET['action'])) {
    die("Error: Missing required parameters (staffNo, or action).");
}

$staffNo = mysqli_real_escape_string($con, $_GET['staffNo']);
$action = $_GET['action'];

$status = null; // MembershipEnd status
$membershipStatus = null; // Membership status
$approvalDate = null; // Approval date
$alkStaffNo = null; // Approver staff number

// Map actions to corresponding status and handle additional data
if ($action === 'approve') {
    // Ensure approver staff number is provided for approval
    if (!isset($_GET['alkStaffNo'])) {
        die("Error: Missing approver staff number (alkStaffNo).");
    }
    $alkStaffNo = mysqli_real_escape_string($con, $_GET['alkStaffNo']);
    $status = 2; // Status ID for "Lulus" (Approved)
    $membershipStatus = 6;
    $approvalDate = date('Y-m-d'); // Current date
} elseif ($action === 'reject') {
    $status = 3; // Status ID for "Ditolak" (Rejected)
    $approvalDate = null; // No approval date for rejection
    $alkStaffNo = null; // Set approver to NULL for rejection
} else {
    die("Error: Invalid action specified.");
}

$sql = "UPDATE membershipend 
        SET status = $status, 
            approveDate = $approvalDate, 
            alkStaffNo = $alkStaffNo
        WHERE staffNo = '$staffNo'";

// Execute the query and handle result
if (mysqli_query($con, $sql)) {

    // if($status === 2){
    // $membershipSql = "UPDATE membership 
    //     SET status = $membershipStatus
    //     WHERE staffNo = '$staffNo'";

    // if (!mysqli_query($con, $membershipSql)) {
    //         error_log("SQL Error (Membership Update): " . mysqli_error($con), 0);
    //         echo "<script type='text/javascript'>
    //                 alert('Error updating membership status: " . mysqli_error($con) . "');
    //                 window.location.href = 'terminatelist.php';
    //               </script>";
    //         exit();
    //     }
    // }

    echo "<script type='text/javascript'>
            alert('Status updated successfully.');
            window.location.href = 'terminatelist.php';
          </script>";
} else {
    // Log and display the error
    error_log("SQL Error: " . mysqli_error($con), 0);
    echo "<script type='text/javascript'>
            alert('Error updating status: " . mysqli_error($con) . "');
            window.location.href = 'terminatelist.php';
          </script>";
}

// Close the database connection
mysqli_close($con);
?>
