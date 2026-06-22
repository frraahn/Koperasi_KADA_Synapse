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

if (!isset($_GET['staffNo']) || empty($_GET['staffNo'])) {
    die("Error: Staff No is missing.");
}

$staffNo = mysqli_real_escape_string($con, $_GET['staffNo']);

// SQL Query to fetch details
$sql = "
    SELECT 
        applicant.staffNo, 
        applicant.applicantName, 
        applicant.applicantIC, 
        applicant.applicantPF, 
        applicant.applicantStreet, 
        applicant.applicantPostcode, 
        applicant.applicantCity, 
        applicant.applicantState, 
        applicant.applicantGender, 
        applicant.applicantReligion, 
        applicant.applicantRace, 
        applicant.applicantPosition, 
        applicant.officeStreet, 
        applicant.officePostcode, 
        applicant.officeCity, 
        applicant.applicantPhoneNumber, 
        applicant.applicantDOB, 
        applicant.applicantAge, 
        applicant.maritalStatus, 
        applicant.applicantGrade, 
        applicant.officeFax, 
        applicant.applicantPhoneHome, 
        applicant.applicantSalary,
        applicant.salaryStatement,
        familyinfo.familyName, 
        familyinfo.relationship, 
        familyinfo.familyIC,
        bankinfo.bankAccountName, 
        bankinfo.accountBankNumber,
        bankinfo.bankStatement,
        savingtype.feeMasuk, savingtype.modahSyer, 
        savingtype.modalYuran, savingtype.wangDepositAnggota, savingtype.sumbanganTabung,
        savingtype.simpananTetap
    FROM applicant
    LEFT JOIN familyinfo ON applicant.staffNo = familyinfo.staffNo
    LEFT JOIN bankinfo ON applicant.staffNo = bankinfo.staffNo
    LEFT JOIN savingtype ON applicant.staffNo = savingtype.staffNo
    WHERE applicant.staffNo = '$staffNo'
";

$result = mysqli_query($con, $sql);

// Check if applicant exists
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
} else {
    die("No membership record found for the given Staff No.");
}

// Fetch family details
$sqlFamily = "
    SELECT familyinfo.familyName, familyinfo.relationship, familyinfo.familyIC
    FROM familyinfo
    WHERE familyinfo.staffNo = '$staffNo'
";

$resultFamily = mysqli_query($con, $sqlFamily);

mysqli_close($con);
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
            --pink: #D6536D;
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
        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            margin: 0 auto;
            max-width: 50%;
            width: 75%; /* Ensures the container has a consistent width */
            text-align: center; /* Centers inline elements inside */
        }
        .content-container1 {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 95%;
            margin: 0 auto; /* Center the content horizontally */
        }

        .nav-tabs {
            display: inline-flex; /* Makes the tabs appear inline */
            border-bottom: none; /* Removes the default border */
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        .nav-tabs .nav-item {
            margin: 0; /* Removes spacing between tabs */
        }

        .nav-tabs .nav-link {
            color: var(--primary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            text-decoration: none;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-radius: 5px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 2rem;
        }
        .table-container {
            background-color: var(--purple);
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table {
            width: 100%;
            table-layout: auto;
            margin-bottom: 1.5rem;
            white-space: nowrap;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .table th, .table td {
            padding: 1rem;
            text-align: left;
            vertical-align: middle;
            font-size: 1rem;
            border-bottom: 1px solid #ddd;
        }

        /* Button Styles */
        .btn {
            border-radius: 30px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .section-header {
    background-color: #D6536D; /* Same color as .btn-secondary */
    color: white; /* White text for contrast */
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

        .btn-success {
            background-color: green;
            border-color: green;
        }

        .btn-danger {
         background-color: #D6536D !important;
         border-color: #D6536D !important;
        }

    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Permohonan Anggota</h2>
    </div>

<div class="container mt-5">
<h2 class="section-header">Maklumat Pemohon</h2>
        <table class="table table-bordered">
            <tr>
                <th>No Anggota</th>
                <td><?php echo($row['staffNo']); ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?php echo($row['applicantName']); ?></td>
            </tr>
            <tr>
                <th>No Kad Pengenalan</th>
                <td><?php echo($row['applicantIC']); ?></td>
            </tr>
            <tr>
                <th>Jantina</th>
                <td><?php echo($row['applicantGender']); ?></td>
            </tr>
            <tr>
                <th>No PF</th>
                <td><?php echo($row['applicantPF']); ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?php echo($row['applicantStreet']); ?></td>
            </tr>
            <tr>
                <th>Poskod</th>
                <td><?php echo($row['applicantPostcode']); ?></td>
            </tr>
            <tr>
                <th>Bandar</th>
                <td><?php echo($row['applicantCity']); ?></td>
            </tr>
            <tr>
                <th>Negeri</th>
                <td><?php echo($row['applicantState']); ?></td>
            </tr>
            <tr>
                <th>Agama</th>
                <td><?php echo($row['applicantReligion']); ?></td>
            </tr>
            <tr>
                <th>Bangsa</th>
                <td><?php echo($row['applicantRace']); ?></td>
            </tr>
            <tr>
                <th>No Telefon</th>
                <td><?php echo($row['applicantPhoneNumber']); ?></td>
            </tr>
            <tr>
                <th>Tarikh Lahir</th>
                <td><?php echo($row['applicantDOB']); ?></td>
            </tr>
            <tr>
                <th>Umur</th>
                <td><?php echo($row['applicantAge']); ?></td>
            </tr>
            <tr>
                <th>Status Perkahwinan</th>
                <td><?php echo($row['maritalStatus']); ?></td>
            </tr>
            <tr>
                <th>Gred</th>
                <td><?php echo($row['applicantGrade']); ?></td>
            </tr>
            <tr>
                <th>Posisi</th>
                <td><?php echo($row['applicantPosition']); ?></td>
            </tr>
            <tr>
                <th>No Telefon Rumah</th>
                <td><?php echo($row['applicantPhoneHome']); ?></td>
            </tr>
            <tr>
                <th>Gaji</th>
                <td><?php echo($row['applicantSalary']); ?></td>
            </tr>
            <tr>
                    <th>Penyata Gaji</th>
                    <td><a href="data:application/pdf;base64,<?php echo base64_encode($row['salaryStatement']); ?>"download="penyata_gaji.pdf">Muat Turun Penyata Gaji </a></td>
                </tr>
        </table>

<h2 class="section-header">Maklumat Keluarga</h2>

        <?php
        $familyCount = 1;
        if ($resultFamily && mysqli_num_rows($resultFamily) > 0) {
            while ($rowFamily = mysqli_fetch_assoc($resultFamily)) {
                echo '<h3>Maklumat Keluarga ' . $familyCount . '</h3>';
                echo '<table class="table table-bordered">';
                echo '<tr><th>Nama Ahli Keluarga</th><td>' . $rowFamily['familyName'] . '</td></tr>';
                echo '<tr><th>Hubungan</th><td>' . $rowFamily['relationship'] . '</td></tr>';
                echo '<tr><th>No IC Ahli Keluarga</th><td>' . $rowFamily['familyIC'] . '</td></tr>';
                echo '</table>';
                $familyCount++;
            }
        } else {
            echo "<p>Tiada maklumat keluarga yang tersedia.</p>";
        }
        ?>

        <h2 class="section-header">Maklumat Bank</h2>
        <table class="table table-bordered">
            <tr>
                <th>Nama Akaun Bank</th>
                <td><?php echo($row['bankAccountName']); ?></td>
            </tr>
            <tr>
                <th>No Akaun Bank</th>
                <td><?php echo($row['accountBankNumber']); ?></td>
            </tr>
            <tr>
                    <th>Penyata Bank</th>
                    <td><a href="data:application/pdf;base64,<?php echo base64_encode($row['bankStatement']); ?>"download="Penyata_Bank.pdf">Muat Turun Penyata Bank </a></td>
                </tr>
        </table>

<h2 class="section-header">Maklumat Simpanan</h2>
        <table class="table table-bordered">
            <tr>
                <th>Yuran Masuk</th>
                <td><?php echo($row['feeMasuk']); ?></td>
            </tr>
            <tr>
                <th>Modah Syer</th>
                <td><?php echo($row['modahSyer']); ?></td>
            </tr>
            <tr>
                <th>Modal Yuran</th>
                <td><?php echo($row['modalYuran']); ?></td>
            </tr>
            <tr>
                <th>Wang Deposit Anggota</th>
                <td><?php echo($row['wangDepositAnggota']); ?></td>
            </tr>
            <tr>
                <th>Sumbangan Tabung</th>
                <td><?php echo($row['sumbanganTabung']); ?></td>
            </tr>
            <tr>
                <th>Simpanan Tetap</th>
                <td><?php echo($row['simpananTetap']); ?></td>
            </tr>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<div class="mt-3 d-flex justify-content-center mb-4">
    <a href="membershipList.php" class="btn btn-secondary mx-2">Kembali</a>
    <button onclick="approveMembership('<?php echo htmlspecialchars($staffNo, ENT_QUOTES); ?>')" class="btn btn-success mx-2">Diterima</button>
    <button onclick="rejectMembership('<?php echo htmlspecialchars($staffNo, ENT_QUOTES); ?>')" class="btn btn-danger mx-2">Ditolak</button>
</div>



   <script>
    function approveMembership(staffNo) {
        const alkStaffNo = prompt("Sila masukkan No ALK:");
        if (alkStaffNo) {
            window.location.href = `viewMembershipProcess.php?action=approve&staffNo=${encodeURIComponent(staffNo)}&alkStaffNo=${encodeURIComponent(alkStaffNo)}`;
        }
    }

    function rejectMembership(staffNo) {
        const alkStaffNo = prompt("Sila masukkan No ALK:");
        if (alkStaffNo) {
            const reason = prompt("Nyatakan sebab keahlian tidak lengkap:");
            if (reason && reason.trim() !== "") {
                window.location.href = `viewMembershipProcess.php?action=reject&staffNo=${encodeURIComponent(staffNo)}&alkStaffNo=${encodeURIComponent(alkStaffNo)}&reason=${encodeURIComponent(reason)}`;
            } else {
                alert("Sebab wajib diisi!");
            }
        }
    }
</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>