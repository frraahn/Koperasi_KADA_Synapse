<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve parameters from the request
    $staffNo = $_GET['staffNo'];
    $currentStatus = (int)$_GET['currentStatus'];
    $alkStaffNo = $_GET['alkStaffNo'];

    // Debug: Ensure required parameters are present
    if (!$staffNo || !$alkStaffNo || !$currentStatus) {
        die("Missing required parameters.");
    }

    // Determine the new status
    $newStatus = ($currentStatus == 2) ? 3 : 2; // Toggle between 2 (Lulus) and 3 (Ditolak)
    $changeDate = date('Y-m-d'); // Current date

    // Check if the staffNo exists in the historymodimem table
    $checkHistorySQL = "SELECT * FROM historymodimem WHERE staffNo = '$staffNo'";
    $historyResult = mysqli_query($con, $checkHistorySQL);

    if (mysqli_num_rows($historyResult) > 0) {
        // If the record exists, update it
$updateHistorySQL = "UPDATE historymodimem 
                     SET newMemStatus = '$newStatus', 
                         memChangeDate = '$changeDate', 
                         memModifiedBy = '$alkStaffNo' 
                     WHERE staffNo = '$staffNo'";

        if (mysqli_query($con, $updateHistorySQL)) {
            // Update the membership status in the membership table
            $updateMembershipSQL = "UPDATE membership 
                                     SET membershipStatus = '$newStatus' 
                                     WHERE staffNo = '$staffNo'";
            if (mysqli_query($con, $updateMembershipSQL)) {
                echo "<script>alert('Status berjaya dikemaskini.'); window.location.href='historyMembership.php';</script>";
            } else {
                echo "Error updating membership status: " . mysqli_error($con);
            }
        } else {
            echo "Error updating historymodimem: " . mysqli_error($con);
        }
    } else {
        // If the record does not exist, insert it
        $insertHistorySQL = "INSERT INTO historymodimem (staffNo, newMemStatus, memChangeDate, memModifiedBy) 
                             VALUES ('$staffNo', '$newStatus', '$changeDate', '$alkStaffNo')";
        if (mysqli_query($con, $insertHistorySQL)) {
            // Update the membership status in the membership table
            $updateMembershipSQL = "UPDATE membership 
                                     SET membershipStatus = '$newStatus' 
                                     WHERE staffNo = '$staffNo'";
            if (mysqli_query($con, $updateMembershipSQL)) {
                echo "<script>alert('Status berjaya dikemaskini.'); window.location.href='historyMembership.php';</script>";
            } else {
                echo "Error updating membership status: " . mysqli_error($con);
            }
        } else {
            echo "Error inserting into historymodimem: " . mysqli_error($con);
        }
    }

    mysqli_close($con);
}
?>
