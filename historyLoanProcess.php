<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Retrieve parameters from the request
    $staffNo = $_GET['staffNo'];
    $loanID = $_GET['loanID'];
    $currentStatus = (int)$_GET['currentStatus'];
    $alkStaffNo = $_GET['alkStaffNo'];

    // Debug: Ensure required parameters are present
    if (!$loanID || !$alkStaffNo || !$currentStatus) {
        die("Missing required parameters.");
    }

    // Determine the new status
    $newStatus = ($currentStatus == 2) ? 3 : 2; // Toggle between 2 (Lulus) and 3 (Ditolak)
    $changeDate = date('Y-m-d'); // Current date

    // Check if the loanID exists in the historyloanmod table
    $checkHistorySQL = "SELECT * FROM historymodiloan WHERE loanID = '$loanID'";
    $historyResult = mysqli_query($con, $checkHistorySQL);

    if (mysqli_num_rows($historyResult) > 0) {
        // If the record exists, update it
        $updateHistorySQL = "UPDATE historymodiloan
                             SET newLoanStatus = '$newStatus', 
                                 loanChangeDate = '$changeDate', 
                                 loanModifiedBy = '$alkStaffNo' 
                             WHERE loanID = '$loanID'";
        if (mysqli_query($con, $updateHistorySQL)) {
            // Update the loan status in the loan table
            $updateLoanSQL = "UPDATE loan 
                              SET loanStatus = '$newStatus' 
                              WHERE loanID = '$loanID'";
            if (mysqli_query($con, $updateLoanSQL)) {
                echo "<script>alert('Status berjaya dikemaskini.'); window.location.href='historyLoan.php';</script>";
            } else {
                echo "Error updating loan status: " . mysqli_error($con);
            }
        } else {
            echo "Error updating historyloanmod: " . mysqli_error($con);
        }
    } else {
        // If the record does not exist, insert it
        $insertHistorySQL = "INSERT INTO historymodiloan (loanID, newLoanStatus, loanChangeDate, loanModifiedBy) 
                             VALUES ('$loanID', '$newStatus', '$changeDate', '$alkStaffNo')";
        if (mysqli_query($con, $insertHistorySQL)) {
            // Update the loan status in the loan table
            $updateLoanSQL = "UPDATE loan 
                              SET loanStatus = '$newStatus' 
                              WHERE loanID = '$loanID'";
            if (mysqli_query($con, $updateLoanSQL)) {
                echo "<script>alert('Status berjaya dikemaskini.'); window.location.href='historyLoan.php';</script>";
            } else {
                echo "Error updating loan status: " . mysqli_error($con);
            }
        } else {
            echo "Error inserting into historyloanmod: " . mysqli_error($con);
        }
    }

    mysqli_close($con);
}
?>
