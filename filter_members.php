<?php
include 'dbconnect.php';

$currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$currentYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$searchText = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

$query = "SELECT a.email, a.staffNo, a.applicantName, u.email, m.membershipStatus, m.membershipApproveDate, 
          g.status, g.notificationLatestDate 
          FROM applicant a 
          JOIN users u ON a.email = u.email 
          JOIN membership m ON a.staffNo = m.staffNo 
          LEFT JOIN managing g ON a.email = g.email 
          WHERE m.membershipStatus = 2 
          AND m.membershipApproveDate IS NOT NULL
          AND (
              (YEAR(m.membershipApproveDate) < $currentYear) OR 
              (YEAR(m.membershipApproveDate) = $currentYear AND MONTH(m.membershipApproveDate) <= $currentMonth)
          )
          AND (a.staffNo LIKE '%$searchText%' OR a.applicantName LIKE '%$searchText%')
          ORDER BY a.staffNo ASC";

$result = mysqli_query($con, $query);

// Check if any results found
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $statusClass = $row['status'] ? 'status-accepted' : 'status-rejected';
        $statusText = $row['status'] ? 'Berjaya Dihantar!' : 'Tidak Berjaya!';
        
        echo "<tr>";
        echo "<td>".$row['staffNo']."</td>";
        echo "<td>".$row['applicantName']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>";
        echo "<button class='btn btn-secondary hasilkan-btn' data-staffNo='".$row['staffNo']."' data-bs-toggle='modal' data-bs-target='#modalGenerate'>Hasilkan</button>";
        echo "</td>";
        echo "<td id='status-".$row['staffNo']."'><span class='status-badge ".$statusClass."'>".$statusText."</span></td>";
        echo "<td id='date-".$row['staffNo']."'>".($row['notificationLatestDate'] ?: "-")."</td>";
        echo "</tr>";
    }
} else {
    // Display a message if no results found
    echo "<tr><td colspan='6' class='text-center'>Tiada rekod dijumpai.</td></tr>";
}
?>