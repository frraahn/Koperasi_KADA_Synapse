<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headeralk.php';
include 'dbconnect.php';

if (!isset($_SESSION['email'])) {
    die("Session user ID is not set.");
}

if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<script>alert('Status berjaya dikemaskini!');</script>";
}



$sql = "SELECT 
            applicant.staffNo,
            applicant.applicantName,
            membership.membershipApplyDate,
            membership.membershipStatus,
            membership.membershipApproveDate,
            membership.membershipReviewDate,
            membership.alkStaffNo,
            membership.adminStaffNo,
            status.statusDesc
        FROM membership
        LEFT JOIN applicant ON membership.staffNo = applicant.staffNo
        LEFT JOIN status ON membership.membershipStatus = status.status
        WHERE membership.membershipStatus=5";

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
    <title>Permohonan Anggota</title>
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
         background-color: #D6536D !important;
         border-color: #D6536D !important;
        }


        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: green;
            border-color: green;
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
       <script>
        function confirmApproval(staffNo) {
            const alkStaffNo = prompt("Sila masukkan No ALK untuk diluluskan:");
            if (alkStaffNo) {
                window.location.href = `membershipListProcess.php?staffNo=${encodeURIComponent(staffNo)}&action=approve&alkStaffNo=${encodeURIComponent(alkStaffNo)}`;
            }
        }

        function confirmRejection(staffNo) {
            const alkStaffNo = prompt("Sila masukkan No ALK untuk ditolak:");
            if (alkStaffNo) {
                window.location.href = `membershipListProcess.php?staffNo=${encodeURIComponent(staffNo)}&action=reject&alkStaffNo=${encodeURIComponent(alkStaffNo)}`;
            }
        }


        function bulkAction(action) {
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
                if (!reason) return;
            }

            let form = document.createElement("form");
            form.method = "POST";
            form.action = "bulkApproveProcess.php";
            
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

    if (!selectAll) {
        console.error("Select All checkbox not found!");
        return;
    }

    // When clicking "Select All"
    selectAll.addEventListener("click", function() {
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    // When clicking individual checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("click", function() {
            selectAll.checked = document.querySelectorAll(".staffCheckbox:checked").length === checkboxes.length;
        });
    });
});



    </script>

</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Permohonan Anggota</h2>
        <button class="btn btn-success" onclick="bulkAction('approve')">Terima Pilihan</button>
        <button class="btn btn-danger" onclick="bulkAction('reject')">Tolak Pilihan</button>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th scope="col">No Anggota</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Tarikh Memohon</th>
                    <th scope="col">Tindakan</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tarikh Disemak</th>
                    <th scope="col">Disemak oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><input type="checkbox" class="staffCheckbox" name="staffNo[]" value="<?php echo htmlspecialchars($row['staffNo']); ?>"></td>

                        <td><?php echo htmlspecialchars($row['staffNo'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['applicantName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['membershipApplyDate'] ?? 'N/A'); ?></td>
                        <td>
                            <button onclick="confirmApproval('<?php echo htmlspecialchars($row['staffNo']); ?>')" class='btn btn-success btn-sm'>Diterima</button>
                            <button onclick="confirmRejection('<?php echo htmlspecialchars($row['staffNo']); ?>')" class='btn btn-danger btn-sm'>Ditolak</button>
                            <a href='viewMembership.php?staffNo=<?php echo urlencode($row['staffNo']); ?>' class='btn btn-primary btn-sm'>Semak Semula</a>
                        </td>
                        <td><?php echo htmlspecialchars($row['statusDesc'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['membershipReviewDate'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['adminStaffNo'] ?? 'N/A'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>