<?php
include('crssession.php');
if(!session_id())
{
  session_start();
}
  
include 'headeradmin.php';
include 'dbconnect.php';
include 'reportretrieve.php';


// Query to count membershipStatus = 1 and get the names
$membershipCountQuery = "SELECT COUNT(*) AS total, 
                         GROUP_CONCAT(applicant.applicantName ORDER BY applicant.applicantName LIMIT 3) AS names 
                         FROM membership 
                         INNER JOIN applicant ON membership.staffNo = applicant.staffNo 
                         WHERE membership.membershipStatus = 1";
$membershipResult = mysqli_query($con, $membershipCountQuery);
$membershipData = mysqli_fetch_assoc($membershipResult);

// Query to count loanStatus = 1 and get the names
$loanCountQuery = "SELECT COUNT(*) AS total, 
                   GROUP_CONCAT(applicant.applicantName ORDER BY applicant.applicantName LIMIT 3) AS names 
                   FROM loan 
                   INNER JOIN applicant ON loan.staffNo = applicant.staffNo 
                   WHERE loan.loanStatus = 1";
$loanResult = mysqli_query($con, $loanCountQuery);
$loanData = mysqli_fetch_assoc($loanResult);

$terminateCountQuery = "SELECT COUNT(*) AS total, 
                   GROUP_CONCAT(applicant.applicantName ORDER BY applicant.applicantName LIMIT 3) AS names 
                   FROM membershipend 
                   INNER JOIN applicant ON membershipend.staffNo = applicant.staffNo 
                   WHERE membershipend.status = 1";
$terminateResult = mysqli_query($con, $terminateCountQuery);
$terminateData = mysqli_fetch_assoc($terminateResult);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
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
            text-align: center;
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

        .content-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            max-width: 85%;
            display: block;
            margin: auto;
            width: 75%;
            height: 50%;
        }

        .button {
            display: block;
            width: 300px;
            margin: 10px auto;
            padding: 15px;
            background-color: #ccc;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        
        .button:hover {
            background-color: #aaa;
        }

        .row-container{
            display: flex;
          justify-content: space-between;
          margin-top: 20px;
          gap: 20px;
          max-width: 75%;
          margin: auto;
          margin-bottom: 20px;
          }

        .sub-container {
            flex: 1;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex; /* Enable flex layout */
            flex-direction: column; /* Arrange content vertically */
        }

        .sub-container .btn {
            margin-top: auto; /* Push the button to the bottom */
            margin-left: auto;
            display: inline-block;
            padding: 4px 9px;
            background-color: var(--accent);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            width: 15%;
            height: auto;
        }

        .sub-container .btn:hover {
            background-color: lightpink;
        }


        .sub-container h4 {
            font-size: 1.25rem;
            color: var(--accent);
            margin-bottom: 10px;
            text-align: left;
        }

        .sub-container .subsection {
            margin-bottom: 15px;
            text-align: left;
        }

        .sub-container .subsection h5 {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sub-container ul {
            list-style-type: none;
            padding: 0;
            margin: 0 0 10px;
        }

        .sub-container ul li {
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: var(--dark-blue);
            color: white;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
        }

        .report-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .report-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .stats-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

    </style>
</head>
<body>
    <div class="page-header"><h2 class="page-title">Laman Utama</h2>
    </div>
    <div class="row-container">
        <div class="sub-container">
            <h4>Jumlah Permohonan Anggota Semasa</h4>
            <div class="subsection">
                <p>Jumlah: <?php echo $membershipData['total']; ?></p>
            </div>
            <div class="subsection">
                <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><h5>Senarai Pemohon</h5></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($membershipData['names'])) {
                        $names = explode(',', $membershipData['names']);
                        foreach ($names as $name) {
                            echo "<tr>
                                    <td>{$name}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr>
                                <td>Tiada rekod.</td>
                              </tr>";
                    }
                    ?>
                </tbody>
    </table>
            </div>
            <a href="reviewMembershipList.php" class="btn">Semak</a>
        </div>

        <div class="sub-container">
            <h4>Jumlah Permohonan Pembiayaan Semasa</h4>
            <div class="subsection">
                <p>Jumlah: <?php echo $loanData['total']; ?></p>
            </div>
            <div class="subsection">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><h5>Senarai Pemohon</h5></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($loanData['names'])) {
                            $names = explode(',', $loanData['names']);
                            foreach ($names as $name) {
                                echo "<tr>
                                        <td>{$name}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr>
                                    <td>Tiada rekod.</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="reviewloanList.php" class="btn">Semak</a>
        </div>

        <div class="sub-container">
            <h4>Jumlah Permohonan Berhenti Semasa</h4>
            <div class="subsection">
                <p>Jumlah: <?php echo $terminateData['total']; ?></p>
            </div>
            <div class="subsection">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><h5>Senarai Pemohon</h5></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($terminateData['names'])) {
                            $names = explode(',', $terminateData['names']);
                            foreach ($names as $name) {
                                echo "<tr>
                                        <td>{$name}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr>
                                    <td>Tiada rekod.</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="reviewloanList.php" class="btn">Semak</a>
        </div>
</div>

        <div class="content-container">
            <h3>Graf Tahunan</h3>
            <!-- Dropdown Menu -->
            <div class="report-form">
                <label for="laporanSelect">Pilih Laporan:</label>
                <select id="laporanSelect" onchange="updateReport()">
                    <option value="anggota">Laporan Anggota</option>
                    <option value="pinjaman">Laporan Pinjaman</option>
                    <option value="pinjamanDiluluskan">Laporan Kewangan</option>
                    <option value="berhenti">Laporan Berhenti</option>
                </select>
            </div>

            <!-- Chart Containers -->
            <div class="stats-section">
                <div id="anggotaChartContainer" style="height: 370px; width: 100%;"></div>
                <div id="pinjamanChartContainer" style="height: 370px; width: 100%; display: none;"></div>
                <div id="kewanganChartContainer" style="height: 370px; width: 100%; display: none;"></div>
                <div id="berhentiChartContainer" style="height: 370px; width: 100%; display: none;"></div>
            </div>
        </div>
    </div>
    <?php include 'reportyearly.php' ?>
</body>
</html>
