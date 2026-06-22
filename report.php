<?php 
session_start(); 
include 'headeradmin.php'; 
include 'dbconnect.php';
include 'reportretrieve.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan KADA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png'); /* Replace with the correct path to your background image */
          background-size: cover;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }

        :root {
            --primary: #D6536D;
            --secondary: #E43D12;
            --accent: #EBE9E1;
            --yellow: #EFB11D;
            --light-pink: #FFA2B6;
            --blue: #6686F6;
            --green: #CFFFDC;
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
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            display: flex;
            flex: 1;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;    
        }

        .card-content {
            flex-grow: 1;
        }

        .card-content h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: normal;
            opacity: 0.9;
        }

        .card-content p {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 5px 0 0 0;
        }

        .report-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .report-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .report-form select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .report-form button {
            padding: 8px 15px;
            background-color: #6686F6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .report-form button:hover {
            background-color: #0052a3;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .stats-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .stats-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
            font-size: 18px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .stat-item {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: white;
        }

        .orange-card {
            background-color: #F4C00A;
            color: white;
        }

        .blue-card {
            background-color: #3498db;
            color: white;
        }

        .green-card {
            background-color: #2ecc71;
            color: white;
        }

        .red-card {
            background-color: #E43D12;
            color: white;
        }
    </style>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</head>

<body>
    <div class="page-header"><h2 class="page-title">Laporan Koperasi</h2>
    </div>
    
    <div class="container">
        <!-- Summary Cards -->
        <div class="card-container">
            <div class="summary-card red-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="card-content">
                    <p><?php echo $totalMembers; ?></p>
                    <h3>Jumlah Anggota KADA</h3>
                </div>
            </div>

            <div class="summary-card blue-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                </div>
                <div class="card-content">
                    <p><?php echo $newMembersCurrent; ?></p>
                    <h3>Permohonan Anggota Bulan Ini</h3>
                </div>
            </div>

            <div class="summary-card green-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <div class="card-content">
                    <p><?php echo $loanApplicationsCurrent; ?></p>
                    <h3>Permohonan Pinjaman Bulan Ini</h3>
                </div>
            </div>

            <div class="summary-card orange-card">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                        <circle cx="12" cy="12" r="3"></circle>
                        <line x1="6" y1="9" x2="6" y2="9"></line>
                        <line x1="18" y1="9" x2="18" y2="9"></line>
                        <line x1="6" y1="15" x2="6" y2="15"></line>
                        <line x1="18" y1="15" x2="18" y2="15"></line>
                    </svg>
                </div>
                <div class="card-content">
                    <p>RM <?php echo $approvedLoansCurrent ? number_format($approvedLoansCurrent, 2) : '0.00'; ?></p>
                    <h3>Pinjaman Diluluskan Bulan Ini</h3>
                </div>
            </div>
        </div>

        <?php include 'reportmonthly.php' ?>

        <!-- Annual Report with Graph -->
        <div class="report-card">
            <h3>Laporan Tahunan</h3>
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