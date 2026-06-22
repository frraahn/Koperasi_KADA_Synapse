<?php 
include 'dbconnect.php';

// Calculate new member applications for the current month (not affected by form date)
$currentMonth = date('m');
$currentYear = date('Y');
$queryNewMembersCurrent = "SELECT COUNT(*) as total FROM membership 
                           WHERE MONTH(membershipApplyDate) = $currentMonth 
                           AND YEAR(membershipApplyDate) = $currentYear";
$resultNewMembersCurrent = mysqli_query($con, $queryNewMembersCurrent);
$newMembersCurrent = mysqli_fetch_assoc($resultNewMembersCurrent)['total'];

// Calculate the number of loan applications for the current month (not affected by form date)
$queryLoanApplicationsCurrent = "SELECT COUNT(*) as total FROM loan 
                                  WHERE MONTH(loanApplyDate) = $currentMonth 
                                  AND YEAR(loanApplyDate) = $currentYear";
$resultLoanApplicationsCurrent = mysqli_query($con, $queryLoanApplicationsCurrent);
$loanApplicationsCurrent = mysqli_fetch_assoc($resultLoanApplicationsCurrent)['total'];

// Calculate approved loans for the current month (not affected by form date)
$queryApprovedLoansCurrent = "SELECT SUM(loanAmount) as total_amount FROM loan 
                               WHERE MONTH(loanApproveDate) = $currentMonth 
                               AND YEAR(loanApproveDate) = $currentYear 
                               AND loanStatus = 2";
$resultApprovedLoansCurrent = mysqli_query($con, $queryApprovedLoansCurrent);
$approvedLoansCurrent = mysqli_fetch_assoc($resultApprovedLoansCurrent)['total_amount'];

// Get selected month, year and annual year from form
$selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$selectedAnnualYear = isset($_POST['annual_year']) ? $_POST['annual_year'] : date('Y');

// Calculate total members
$queryTotalMembers = "SELECT COUNT(*) as total FROM membership WHERE membershipStatus = 2";
$resultTotalMembers = mysqli_query($con, $queryTotalMembers);
$totalMembers = mysqli_fetch_assoc($resultTotalMembers)['total'];

// Calculate new member applications for selected month
$queryNewMembers = "SELECT COUNT(*) as total FROM membership 
                    WHERE MONTH(membershipApplyDate) = $selectedMonth 
                    AND YEAR(membershipApplyDate) = $selectedYear";
$resultNewMembers = mysqli_query($con, $queryNewMembers);
$newMembers = mysqli_fetch_assoc($resultNewMembers)['total'];

// Calculate membership statistics for selected month
$queryMembership = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN membershipStatus = 2 THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN membershipStatus = 3 THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN membershipStatus IN (1,4,5) THEN 1 ELSE 0 END) as pending
FROM membership 
WHERE MONTH(membershipApplyDate) = $selectedMonth 
AND YEAR(membershipApplyDate) = $selectedYear";
$resultMembership = mysqli_query($con, $queryMembership);
$membershipStats = mysqli_fetch_assoc($resultMembership);

// Calculate loan applications for selected month
$queryLoans = "SELECT 
    COUNT(*) as total_applications,
    SUM(CASE WHEN loanStatus = 2 THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN loanStatus = 3 THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN loanStatus IN (1,4,5) THEN 1 ELSE 0 END) as pending
FROM loan 
WHERE MONTH(loanApplyDate) = $selectedMonth 
AND YEAR(loanApplyDate) = $selectedYear";
$resultLoans = mysqli_query($con, $queryLoans);
$loanStats = mysqli_fetch_assoc($resultLoans);

// Get available years for the dropdown
$queryYears = "SELECT DISTINCT YEAR(membershipApplyDate) as year FROM membership 
               UNION 
               SELECT DISTINCT YEAR(loanApplyDate) as year FROM loan 
               UNION 
               SELECT DISTINCT YEAR(applyDate) as year FROM membershipend
               ORDER BY year";
$resultYears = mysqli_query($con, $queryYears);
$resultYearsGraph = mysqli_query($con, $queryYears);

$years = array();
while ($row = mysqli_fetch_assoc($resultYearsGraph)) {
    $years[] = $row['year'];
}

// Initialize arrays for graph data
$memberCount = array();
$memberApplications = array();
$approvedMembers = array();
$rejectedMembers = array();
$loanApplications = array();
$approvedLoans = array();
$rejectedLoans = array();
$amountLoans = array();
$terminateCount = array();
$terminateApplications = array();
$approvedTerminate = array();
$rejectedTerminate = array();

// Get data for graph
foreach ($years as $year) {
    $queryAnnualGraph = "SELECT 
        (SELECT COUNT(*) FROM membership WHERE membershipStatus = 2 AND YEAR(membershipApplyDate) <= $year) as total_members,
        (SELECT COUNT(*) FROM membership WHERE YEAR(membershipApplyDate) = $year) as total_member_applications,
        (SELECT COUNT(*) FROM membership WHERE membershipStatus = 2 AND YEAR(membershipApplyDate) = $year) as approved_members,
        (SELECT COUNT(*) FROM membership WHERE membershipStatus = 3 AND YEAR(membershipApplyDate) = $year) as rejected_members,
        (SELECT COUNT(*) FROM loan WHERE YEAR(loanApplyDate) = $year) as total_loan_applications,
        (SELECT COUNT(*) FROM loan WHERE loanStatus = 2 AND YEAR(loanApplyDate) = $year) as approved_loans,
        (SELECT COUNT(*) FROM loan WHERE loanStatus = 3 AND YEAR(loanApplyDate) = $year) as rejected_loans,
        (SELECT SUM(loanAmount) FROM loan WHERE loanStatus = 2 AND YEAR(loanApproveDate) = $year) as total_loan_amount,
        (SELECT COUNT(*) FROM membershipend WHERE status = 2 AND YEAR(applyDate) <= $year) as total_terminate,
        (SELECT COUNT(*) FROM membershipend WHERE YEAR(applyDate) = $year) as total_terminate_applications,
        (SELECT COUNT(*) FROM membershipend WHERE status = 2 AND YEAR(applyDate) = $year) as approved_terminate,
        (SELECT COUNT(*) FROM membershipend WHERE status = 3 AND YEAR(applyDate) = $year) as rejected_terminate";
    
    $resultAnnualGraph = mysqli_query($con, $queryAnnualGraph);
    $stats = mysqli_fetch_assoc($resultAnnualGraph);
    
    $memberCount[] = array("x" => $year, "y" => (int)$stats['total_members']);
    $memberApplications[] = array("x" => $year, "y" => (int)$stats['total_member_applications']);
    $approvedMembers[] = array("x" => $year, "y" => (int)$stats['approved_members']);
    $rejectedMembers[] = array("x" => $year, "y" => (int)$stats['rejected_members']);
    $loanApplications[] = array("x" => $year, "y" => (int)$stats['total_loan_applications']);
    $approvedLoans[] = array("x" => $year, "y" => (int)$stats['approved_loans']);
    $rejectedLoans[] = array("x" => $year, "y" => (int)$stats['rejected_loans']);
    $amountLoans[] = array("x" => $year, "y" => (int)$stats['total_loan_amount']);
    $terminateCount[] = array("x" => $year, "y" => (int)$stats['total_terminate']);
    $terminateApplications[] = array("x" => $year, "y" => (int)$stats['total_terminate_applications']);
    $approvedTerminate[] = array("x" => $year, "y" => (int)$stats['approved_terminate']);
    $rejectedTerminate[] = array("x" => $year, "y" => (int)$stats['rejected_terminate']);
}
?>