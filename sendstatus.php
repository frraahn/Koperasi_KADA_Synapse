<?php
session_start();
include 'headeradmin.php';
include 'dbconnect.php';

// Fetch data for "anggota" table
$query_anggota = "SELECT a.staffNo, a.applicantName, a.email, m.membershipStatus 
                  FROM applicant a 
                  JOIN membership m ON a.staffNo = m.staffNo 
                  WHERE m.membershipStatus IN (2, 3) 
                  AND (m.sendStatus != 1 OR m.sendStatus IS NULL)
                  ORDER BY a.staffNo ASC";

$result_anggota = mysqli_query($con, $query_anggota);

// Fetch data for "pinjaman" table
$query_pinjaman = "SELECT a.staffNo, a.applicantName, a.email, p.loanStatus, p.loanID 
                   FROM applicant a 
                   JOIN loan p ON a.staffNo = p.staffNo 
                   WHERE p.loanStatus IN (2, 3) 
                   AND (p.sendStatus != 1 OR p.sendStatus IS NULL)
                   ORDER BY a.staffNo ASC";

$result_pinjaman = mysqli_query($con, $query_pinjaman);

// Fetch data for "berhenti" table
$query_berhenti = "SELECT a.staffNo, a.applicantName, a.email, e.status 
                   FROM applicant a 
                   JOIN membershipend e ON a.staffNo = e.staffNo 
                   WHERE e.status IN (2, 3) 
                   AND (e.sendStatus != 1 OR e.sendStatus IS NULL)
                   ORDER BY a.staffNo ASC";

$result_berhenti = mysqli_query($con, $query_berhenti);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Penghantaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #E43D12;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png'); /* Replace with the correct path to your background image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

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

        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            max-width: 100%;
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

        .table {
            width: 100%;
            table-layout: auto;
            margin-top: 1rem;
            white-space: nowrap;
        }

        .table thead {
            background-color: var(--primary);
            color: white;
        }

        .table th {
            padding: 1rem;
            font-weight: 500;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 134, 246, 0.05);
        }

        .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            margin: 0.2rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }

        .btn-secondary {
            background-color: var(--purple);
            border-color: var(--purple);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-secondary:hover {
            background-color: #4641a4; 
            border-color: #4641a4; 
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-accepted {
            background-color: var(--secondary);
            color: white;
        }

        .status-rejected {
            background-color: var(--accent);
            color: white;
        }

        batch-actions {
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .select-all-checkbox {
            margin: 0;
            padding: 0;
            width: 18px;
            height: 18px;
        }

        .row-checkbox {
            width: 16px;
            height: 16px;
        }

        .batch-send-btn {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            display: none;
        }

        .batch-send-btn.visible {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Penghantaran Status</h2>
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#anggota">Penghantaran Permohonan Anggota</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#pinjaman">Penghantaran Permohonan Pinjaman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#berhenti">Penghantaran Permohonan Berhenti</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="anggota">
                    <div class="batch-actions">
                        <input type="checkbox" class="select-all-checkbox" data-target="anggota" title="Select All">
                        <button class="btn btn-secondary batch-send-btn" onclick="sendSelectedEmails('anggota')">
                            Hantar Terpilih
                        </button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-column"></th>
                                <th>No Anggota</th>
                                <th>Nama</th>
                                <th>Emel</th>
                                <th>Status Permohonan</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_array($result_anggota)) {
                                $status = ($row['membershipStatus'] == 2) ? 'Diterima' : 'Ditolak';
                                $statusClass = ($row['membershipStatus'] == 2) ? 'status-accepted' : 'status-rejected';
                                
                                echo "<tr id='row-" . $row['staffNo'] . "'>";
                                echo "<td class='checkbox-column'>
                                        <input type='checkbox' class='row-checkbox' 
                                        data-staffno='" . $row['staffNo'] . "'
                                        data-email='" . $row['email'] . "'
                                        data-name='" . $row['applicantName'] . "'
                                        data-status='" . $row['membershipStatus'] . "'>
                                      </td>";
                                echo "<td>" . $row['staffNo'] . "</td>";
                                echo "<td>" . $row['applicantName'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td><span class='status-badge " . $statusClass . "'>" . $status . "</span></td>";
                                echo "<td><button class='btn btn-secondary hantar-btn' 
                                            data-staffno='" . $row['staffNo'] . "' 
                                            data-email='" . $row['email'] . "' 
                                            data-name='" . $row['applicantName'] . "'
                                            data-status='" . $row['membershipStatus'] . "'>Hantar</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="pinjaman">
                    <div class="batch-actions">
                        <input type="checkbox" class="select-all-checkbox" data-target="pinjaman" title="Select All">
                        <button class="btn btn-secondary batch-send-btn" onclick="sendSelectedEmails('pinjaman')">
                            Hantar Terpilih
                        </button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-column"></th>
                                <th>No Pembiayaan</th>
                                <th>No Anggota</th>
                                <th>Nama</th>
                                <th>Emel</th>
                                <th>Status Permohonan</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_array($result_pinjaman)) {
                                $status = ($row['loanStatus'] == 2) ? 'Diterima' : 'Ditolak';
                                $statusClass = ($row['loanStatus'] == 2) ? 'status-accepted' : 'status-rejected';
                                
                                echo "<tr id='row-" . $row['staffNo'] . "'>";
                                echo "<td class='checkbox-column'>
                                        <input type='checkbox' class='row-checkbox'
                                        data-staffno='" . $row['staffNo'] . "'
                                        data-email='" . $row['email'] . "'
                                        data-name='" . $row['applicantName'] . "'
                                        data-status='" . $row['loanStatus'] . "'>
                                      </td>";
                                echo "<td>" . $row['loanID'] . "</td>";
                                echo "<td>" . $row['staffNo'] . "</td>";
                                echo "<td>" . $row['applicantName'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td><span class='status-badge " . $statusClass . "'>" . $status . "</span></td>";
                                echo "<td><button class='btn btn-secondary hantar-btn' 
                                            data-staffno='" . $row['staffNo'] . "' 
                                            data-email='" . $row['email'] . "' 
                                            data-name='" . $row['applicantName'] . "'
                                            data-status='" . $row['loanStatus'] . "'>Hantar</button></td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="tab-pane fade" id="berhenti">
                    <div class="batch-actions">
                        <input type="checkbox" class="select-all-checkbox" data-target="berhenti" title="Select All">
                        <button class="btn btn-secondary batch-send-btn" onclick="sendSelectedEmails('berhenti')">
                            Hantar Terpilih
                        </button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="checkbox-column"></th>
                                <th>No Anggota</th>
                                <th>Nama</th>
                                <th>Emel</th>
                                <th>Status Permohonan</th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_array($result_berhenti)) {
                                $status = ($row['status'] == 2) ? 'Diterima' : 'Ditolak';
                                $statusClass = ($row['status'] == 2) ? 'status-accepted' : 'status-rejected';
                                
                                echo "<tr id='row-" . $row['staffNo'] . "'>";
                                echo "<td class='checkbox-column'>
                                        <input type='checkbox' class='row-checkbox'
                                        data-staffno='" . $row['staffNo'] . "'
                                        data-email='" . $row['email'] . "'
                                        data-name='" . $row['applicantName'] . "'
                                        data-status='" . $row['status'] . "'>
                                    </td>";
                                echo "<td>" . $row['staffNo'] . "</td>";
                                echo "<td>" . $row['applicantName'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td><span class='status-badge " . $statusClass . "'>" . $status . "</span></td>";
                                echo "<td><button class='btn btn-secondary hantar-btn' 
                                            data-staffno='" . $row['staffNo'] . "' 
                                            data-email='" . $row['email'] . "' 
                                            data-name='" . $row['applicantName'] . "'
                                            data-status='" . $row['status'] . "'>Hantar</button></td>";
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
            // Loop through both tables
            ['anggota', 'pinjaman'].forEach(function(tabId) {
                const table = document.querySelector(`#${tabId} .table tbody`);
                const rows = table.getElementsByTagName('tr');

                for (let row of rows) {
                    const staffNo = row.cells[0].textContent.toLowerCase();
                    const name = row.cells[1].textContent.toLowerCase();
                    row.style.display = 
                        staffNo.includes(input) || name.includes(input) ? '' : 'none';
                }
            });
        }

        // Add event listener for Enter key
        document.getElementById('staffSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStaff();
            }
        });

        function searchAll() {
            document.getElementById('staffSearch').value = ''; // Clear the input
            // Loop through both tables
            ['anggota', 'pinjaman'].forEach(function(tabId) {
                const table = document.querySelector(`#${tabId} .table tbody`);
                const rows = table.getElementsByTagName('tr');
                
                for (let row of rows) {
                    row.style.display = ''; // Show all rows
                }
            });
        }

        document.querySelectorAll('.select-all-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const tabId = this.dataset.target;
                const isChecked = this.checked;
                document.querySelectorAll(`#${tabId} .row-checkbox`).forEach(rowCheckbox => {
                    rowCheckbox.checked = isChecked;
                });
                updateBatchSendButton(tabId);
            });
        });

        function updateBatchSendButton(tabId) {
            const checkedCount = document.querySelectorAll(`#${tabId} .row-checkbox:checked`).length;
            const batchButton = document.querySelector(`#${tabId} .batch-send-btn`);
            batchButton.classList.toggle('visible', checkedCount > 0);
        }

        // Handle individual checkbox changes
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('row-checkbox')) {
                const tabPane = event.target.closest('.tab-pane').id;
                updateBatchSendButton(tabPane);
            }
        });

        // Send emails for selected rows
        function sendSelectedEmails(tabId) {
            const selectedRows = document.querySelectorAll(`#${tabId} .row-checkbox:checked`);
            let targetUrl;
            
            switch(tabId) {
                case 'anggota':
                    targetUrl = 'sendstatmemprocess.php';
                    break;
                case 'pinjaman':
                    targetUrl = 'sendstatpinprocess.php';
                    break;
                case 'berhenti':
                    targetUrl = 'sendstattermprocess.php';
                    break;
                default:
                    alert('Invalid tab selected');
                    return;
            }
            
            if (confirm(`Adakah anda pasti mahu menghantar e-mel kepada ${selectedRows.length} penerima?`)) {
                let successCount = 0;
                let failCount = 0;
                
                selectedRows.forEach(row => {
                    const data = {
                        staffNo: row.dataset.staffno,
                        email: row.dataset.email,
                        name: row.dataset.name,
                        status: row.dataset.status
                    };

                    fetch(targetUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams(data)
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            successCount++;
                            document.querySelector(`#row-${data.staffNo}`).remove();
                        } else {
                            failCount++;
                        }
                        
                        if (successCount + failCount === selectedRows.length) {
                            alert(`Selesai:\n${successCount} e-mel berjaya dihantar\n${failCount} e-mel gagal dihantar`);
                            updateBatchSendButton(tabId);
                        }
                    })
                    .catch(() => {
                        failCount++;
                        if (successCount + failCount === selectedRows.length) {
                            alert(`Selesai:\n${successCount} e-mel berjaya dihantar\n${failCount} e-mel gagal dihantar`);
                            updateBatchSendButton(tabId);
                        }
                    });
                });
            }
        }

        // Handle individual email sending
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('hantar-btn')) {
                const staffNo = event.target.dataset.staffno;
                const email = event.target.dataset.email;
                const name = event.target.dataset.name;
                const status = event.target.dataset.status;
                
                const targetUrl = document.querySelector('#anggota').classList.contains('show', 'active') ? 'sendstatmemprocess.php' : 'sendstatpinprocess.php';
                
                if (confirm(`Adakah anda pasti mahu menghantar e-mel kepada ${name}?`)) {
                    fetch(targetUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ staffNo, email, name, status })
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.status === 'success') {
                            alert(`E-mel berjaya dihantar kepada ${name}`);
                            document.querySelector(`#row-${staffNo}`).remove();
                        } else {
                            alert(`Error: ${res.message}`);
                        }
                    })
                    .catch(xhr => {
                        alert(`Gagal menghantar e-mel: ${xhr.responseText}`);
                    });
                }
            }
        });
    </script>
</body>
</html>