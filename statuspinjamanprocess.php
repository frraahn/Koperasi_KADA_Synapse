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
    $query = "SELECT loanID, loanStatus, loanApplyDate, loanApproveDate, loan.loanType, loantype.loanType, loantype.loanName
            FROM loan 
            LEFT JOIN applicant ON loan.staffNo = applicant.staffNo
            LEFT JOIN loantype ON loantype.loanType = loan.loanType
            WHERE applicant.email = '$userEmail' 
            ORDER BY loanApplyDate DESC";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $loanStatus = $row['loanStatus'];
            if ($row['loanStatus'] == 1||$row['loanStatus'] == 4||$row['loanStatus'] == 5) {
                $currentApplication = $row;

            }elseif ($row['loanStatus'] == 2 || $row['loanStatus'] == 3) {
                $pastApplications[] = $row;
            }
        }
    }
}

mysqli_close($con);

?>
