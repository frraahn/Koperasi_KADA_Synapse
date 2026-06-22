<?php 
include('crssession.php');
if(!session_id()) {
    session_start();
}

// Connect to DB
include('dbconnect.php');
$femail = $_SESSION['email'];

$currentApplication = null;

// Fetch current and past applications
if ($femail) {
    $query = "SELECT  membershipStatus, membershipApplyDate, membershipReviewDate
        FROM membership 
        LEFT JOIN applicant ON membership.staffNo = applicant.staffNo
        WHERE applicant.email = '$femail'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $membershipStatus = $row['membershipStatus'];
            if ($row['membershipStatus'] == 1||$row['membershipStatus'] == 4||$row['membershipStatus'] == 5) {
                $currentApplication = $row;

            }elseif ($row['membershipStatus'] == 2 || $row['membershipStatus'] == 3) {
                $currentApplications[] = $row;
            }
        }
    }
}

mysqli_close($con);

?>
