<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headeradmin.php';
include 'dbconnect.php';

if (!isset($_SESSION['email'])) {
    die("Session user ID is not set.");
}

$sql = "SELECT 
            applicant.staffNo,
            applicant.applicantName,
            membership.membershipApplyDate,
            membership.membershipStatus,
            membership.membershipApproveDate,
            status.statusDesc
        FROM membership
        LEFT JOIN applicant ON membership.staffNo = applicant.staffNo
        LEFT JOIN status ON membership.membershipStatus = status.status
        WHERE sendStatus = 1 AND membershipStatus = 2";

$result = mysqli_query($con, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($con));
}

$sqlTerminate = "SELECT 
            applicant.staffNo,
            applicant.applicantName,
            membershipend.id,
            membershipend.applyDate,
            membershipend.status,
            membershipend.approveDate,
            status.statusDesc
        FROM membershipend
        LEFT JOIN applicant ON membershipend.staffNo = applicant.staffNo
        LEFT JOIN status ON membershipend.status = status.status
        WHERE sendStatus = 1 AND membershipend.status = 2";

$resultTerminate = mysqli_query($con, $sqlTerminate);

if (!$resultTerminate) {
    die("SQL Error: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senarai Pemohon Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #f567a1;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
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

        /* Header Styles */
        .page-header {
            background-color: var(--primary);
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

        /* Table Container Styles */
        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            max-width: 100%;
        }

        /* Table Styles */
        .table {
            width: 100%;
            table-layout: auto;
            margin-bottom: 0;
            white-space: nowrap;
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
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

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

        .nav-tabs {
            flex: 1;
            border-bottom: 2px solid var(--primary);
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            padding: 1rem 2rem;
            margin-right: 0.5rem;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            background-color: rgba(102, 134, 246, 0.1);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            background-color: white;
            border-bottom: 3px solid var(--primary);
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Anggota Koperasi KADA</h2>
    </div>

    <div class="container">
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="staffSearch" class="search-input" placeholder="Cari No Anggota...">
                <button class="btn btn-primary" onclick="searchStaff()">Cari</button>
                <button class="btn btn-success" onclick="searchAll()">Semua Ahli</button>
            </div>
        </div>

        <div class="content-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#anggota">Senarai Anggota Koperasi KADA</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#berhenti">Senarai Tamat Anggota</a>
                </li>
            </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="anggota">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Anggota</th>
                        <th>Nama</th>
                        <th>Tarikh Memohon</th>
                        <th>Status</th>
                        <th>Tarikh Permohonan Disemak</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . ($row['staffNo'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['applicantName'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['membershipApplyDate'] ?? 'N/A') . "</td>";
                        echo "<td><span class='status-badge'>" . ($row['statusDesc'] ?? 'N/A') . "</span></td>";
                        echo "<td>" . ($row['membershipApproveDate'] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="berhenti">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Berhenti</th>
                        <th>No Anggota</th>
                        <th>Nama</th>
                        <th>Tarikh Memohon</th>
                        <th>Status</th>
                        <th>Tarikh Permohonan Disemak</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    while ($row = mysqli_fetch_assoc($resultTerminate)) {
                        echo "<tr>";
                        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['staffNo'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['applicantName'] ?? 'N/A') . "</td>";
                        echo "<td>" . ($row['applyDate'] ?? 'N/A') . "</td>";
                        echo "<td><span class='status-badge'>" . ($row['statusDesc'] ?? 'N/A') . "</span></td>";
                        echo "<td>" . ($row['approveDate'] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

    <script>
        function searchStaff() {
            const input = document.getElementById('staffSearch').value.toLowerCase();
            const tbody = document.getElementById('tableBody');
            const rows = tbody.getElementsByTagName('tr');

            for (let row of rows) {
                const staffNo = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                row.style.display = 
                    staffNo.includes(input) || name.includes(input) ? '' : 'none';
            }
        }

        // Add event listener for Enter key
        document.getElementById('staffSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStaff();
            }
        });

        function searchAll() {
            const tbody = document.getElementById('tableBody');
            const rows = tbody.getElementsByTagName('tr');
            document.getElementById('staffSearch').value = ''; // Clear the input

            for (let row of rows) {
                row.style.display = ''; // Show all rows
            }
        }

        // Add event listener for Enter key
        document.getElementById('staffSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStaff();
            }
        });
    </script>
</body>
</html>