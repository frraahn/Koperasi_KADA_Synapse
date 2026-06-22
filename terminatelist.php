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
            membershipend.id,
            membershipend.applyDate,
            membershipend.status,
            membershipend.approveDate,
            membershipend.reviewDate,
            membershipend.alkStaffNo,
            membershipend.adminStaffNo,
            status.statusDesc
        FROM membershipend
        LEFT JOIN applicant ON membershipend.staffNo = applicant.staffNo
        LEFT JOIN status ON membershipend.status = status.status
        WHERE membershipend.status = 5";

$result = mysqli_query($con, $sql);

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
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Permohonan Berhenti Menjadi Anggota</h2>
    </div>

    <div class="container">
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="staffSearch" class="search-input" placeholder="Cari No Anggota...">
                <button class="btn btn-primary" onclick="searchStaff()">Cari</button>
                <button class="btn btn-success" onclick="searchAll()">Semua Ahli</button>
            </div>
        </div>

 <div class="container">
        <div class="content-container">
            <form id="batchForm" method="POST" action="bulkApproveTerminateProcess.php">
                <button class="btn btn-success" onclick="bulkAction('approve', event)">Luluskan Pilihan</button>

                <button class="btn btn-danger" onclick="bulkAction('reject', event)">Tolak Pilihan</button>

                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>No Anggota</th>
                            <th>Nama</th>
                            <th>Tarikh Memohon</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Tarikh Disemak</th>
                            <th>Disemak oleh</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
       echo "<td><input type='checkbox' class='staffCheckbox' name='staffNos[]' value='" . htmlspecialchars($row['staffNo']) . "'></td>";

        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['staffNo'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['applicantName'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['applyDate'] ?? 'N/A') . "</td>";
        echo "<td>
                <a href='confirmterminate.php?staffNo=" . urlencode($row['staffNo']) ."' 
                    class='btn btn-success btn-sm'>Diterima</a>
                <a href='terminatelistprocess.php?staffNo=" . urlencode($row['staffNo']) . "&action=reject' 
                   class='btn btn-danger btn-sm'>Ditolak</a>
                <a href='applicantinfoterminate.php?staffNo=" . urlencode($row['staffNo']) . "' 
                   class='btn btn-primary btn-sm'>Semak Semula</a>
              </td>";
        echo "<td>" . ($row['statusDesc'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['reviewDate'] ?? 'N/A') . "</td>";
        echo "<td>" . ($row['adminStaffNo'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    ?>
</tbody>

                </table>
            </form>
        </div>
    </div>

    <script>
document.getElementById("selectAll").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll(".staffCheckbox");
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        function bulkAction(action, event) {
    event.preventDefault(); // Prevent default form submission

    let selected = [];
    document.querySelectorAll(".staffCheckbox:checked").forEach(checkbox => {
        selected.push(checkbox.value);
    });

    if (selected.length === 0) {
        alert("Sila pilih sekurang-kurangnya satu permohonan.");
        return;
    }

    let alkStaffNo = prompt("Masukkan nombor Kakitangan ALK:");
    if (!alkStaffNo) return;

    let reason = "";
    if (action === 'reject') {
    reason = prompt("Masukkan sebab penolakan:");
    if (reason === null || reason.trim() === "") {
        alert("Sebab penolakan diperlukan!");
        return;
    }
}

    let form = document.createElement("form");
    form.method = "POST";
    form.action = "bulkApproveTerminateProcess.php";

    let inputAction = document.createElement("input");
    inputAction.type = "hidden";
    inputAction.name = "action";
    inputAction.value = action;
    form.appendChild(inputAction);

    let inputAlk = document.createElement("input");
    inputAlk.type = "hidden";
    inputAlk.name = "alkStaffNo";
    inputAlk.value = alkStaffNo;
    form.appendChild(inputAlk);

    let inputReason = document.createElement("input");
    inputReason.type = "hidden";
    inputReason.name = "reason";
    inputReason.value = reason;
    form.appendChild(inputReason);

    selected.forEach(staffNo => {
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "staffNos[]";
        input.value = staffNo;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}



document.addEventListener("DOMContentLoaded", function() {
    const selectAll = document.getElementById("selectAll");
    const checkboxes = document.querySelectorAll(".staffCheckbox");

    if (!selectAll || checkboxes.length === 0) {
        console.error("Select All checkbox or staff checkboxes not found!");
        return;
    }

    // Select/Deselect all checkboxes
    selectAll.addEventListener("click", function() {
        checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
    });

    // Ensure "Select All" updates when individual checkboxes are clicked
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            selectAll.checked = checkboxes.length === document.querySelectorAll(".staffCheckbox:checked").length;
        });
    });
});



</script>
</body>
</html>
