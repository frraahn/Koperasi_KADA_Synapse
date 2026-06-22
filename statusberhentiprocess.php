<?php 
include('crssession.php');
if(!session_id()) {
    session_start();
}

// Connect to DB
include('dbconnect.php');
$userEmail = $_SESSION['email'];

$currentApplication = null;
$pastApplications = [];

// Fetch current and past applications
if ($userEmail) {
    $query = "SELECT id, status, applyDate, approveDate
            FROM membershipend 
            LEFT JOIN applicant ON membershipend.staffNo = applicant.staffNo
            WHERE applicant.email = '$userEmail'
            ORDER BY applyDate DESC";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] == 1 || $row['status'] == 4 || $row['status'] == 5) {
                $currentApplication = $row;
            } elseif ($row['status'] == 2 || $row['status'] == 3) {
                $pastApplications[] = $row;
            }
        }

    }
}

mysqli_close($con);

?>
