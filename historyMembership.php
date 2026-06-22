<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headeralk.php';
include 'dbconnect.php';

// Check if the session is set
if (!isset($_SESSION['email'])) {
    die("Session user ID is not set.");
}

// SQL query to fetch the required data
$sql = "SELECT 
            applicant.staffNo,
            applicant.applicantName,
            membership.membershipApplyDate,
            membership.membershipStatus,
            membership.membershipApproveDate,
            membership.alkStaffNo,
            historymodimem.newMemStatus,
            historymodimem.memChangeDate,
            historymodimem.memModifiedBy,
            status.statusDesc AS currentStatusDesc,
            newStatusDesc.statusDesc AS newStatusDesc
        FROM membership
        LEFT JOIN applicant ON membership.staffNo = applicant.staffNo
        LEFT JOIN status ON membership.membershipStatus = status.status
        LEFT JOIN historymodimem ON membership.staffNo = historymodimem.staffNo
        LEFT JOIN status AS newStatusDesc ON historymodimem.newMemStatus = newStatusDesc.status
        WHERE membership.membershipStatus IN (2, 3)"; 
 // Fetch rows with status 2 or 3

// Execute query
$result = mysqli_query($con, $sql);

// Check query execution
if (!$result) {
    die("SQL Error: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Pembiayaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmChange(staffNo, currentStatus, currentStatusDesc) {
    let newStatusDesc = currentStatus == 3 ? "Lulus" : "Ditolak"; 
    let message = `Adakah anda pasti untuk mengubah status ke ${newStatusDesc}?`;

    if (confirm(message)) {
        const alkStaffNo = prompt("Sila masukkan No ALK:");
        if (alkStaffNo) {
            window.location.href = `historyMembershipProcess.php?staffNo=${encodeURIComponent(staffNo)}&currentStatus=${encodeURIComponent(currentStatus)}&alkStaffNo=${encodeURIComponent(alkStaffNo)}`;
        }
    }
}


        function redirectToView(staffNo) {
            window.location.href = `hviewMembership.php?staffNo=${encodeURIComponent(staffNo)}`;
        }
    </script>
    <style>
        :root {
            --primary: #D6536D;
            --secondary: #E43D12;
            --accent: #EBE9E1;
            --yellow: #EFB11D;
            --light-pink: #FFA2B6;
            --blue: #6686F6;
            --light-blue: #258de4;
            --green: #CFFFDC;
        }

        body {
            background-color: #f8f9fa;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png'); /* Replace with the correct path to your background image */
          background-size: cover;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }
        .page-header {
            background-color: var(--blue);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-align: left;
            margin: 0;
            padding-left: 4.5rem;
        }
        .container {
            max-width: 1450px; 
            margin: 0 auto;
            padding: 2rem; 
        }

        /* Search Container Styles */
        .search-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
            align-items: center;
            max-width: 500px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 15px;
            font-size: 0.95rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(102, 134, 246, 0.2);
        }
        .content-container {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
    overflow-x: auto; /* Enables horizontal scrolling */
    max-width: 100%;
    white-space: nowrap; /* Prevents table content from wrapping */
}

.table {
    width: 100%;
    table-layout: auto;
    min-width: 800px; /* Ensures table has a minimum width */
}


       

        .table thead {
            background-color: var(--primary);
            color: white;
        }

        .table th, .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 134, 246, 0.05);
        }

        /* Button Styles */
        .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.2rem;
            font-size: 0.875rem;
        }

        .btn-success {
            background-color: green;
            border-color: green;
        }
a
        .btn-danger {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Status Badge Styles */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
            background-color: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Permohonan Anggota</h2>
    </div>

    <div class="container">
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="staffSearch" class="search-input" placeholder="Cari No Anggota...">
                <button class="btn btn-primary" onclick="searchStaff()">Cari</button>
                <button class="btn btn-success" onclick="searchAll()">Semua Ahli</button>
            </div>
        </div>

        <div class="container mt-5">
            <h2 class="mb-4">Permohonan Anggota</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">No Anggota</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Tarikh Memohon</th>
                        <th scope="col">Tindakan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Status Baharu</th>
                        <th scope="col">Tarikh Lulus Permohonan</th>
                        <th scope="col">Disahkan oleh</th>
                        <th scope="col">Tarikh Diubah</th>
                        <th scope="col">Diubah oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display rows from query results
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['staffNo'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['applicantName'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['membershipApplyDate'] ?? 'N/A') . "</td>";
                        echo "<td>
                                <button class='btn btn-warning btn-sm' onclick=\"confirmChange('" . htmlspecialchars($row['staffNo']) . "', " . htmlspecialchars($row['membershipStatus']) . ", '" . htmlspecialchars($row['currentStatusDesc']) . "')\">Ubah</button>

                                <button class='btn btn-primary btn-sm' onclick=\"redirectToView('" . htmlspecialchars($row['staffNo']) . "')\">Semak Semula</button>
                              </td>";
                        echo "<td>" . htmlspecialchars($row['currentStatusDesc'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['newStatusDesc'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['membershipApproveDate'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['alkStaffNo'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['memChangeDate'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['memModifiedBy'] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
