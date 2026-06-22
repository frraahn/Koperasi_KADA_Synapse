<?php
session_start();
include 'headeradmin.php';
include 'dbconnect.php';

$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

$firstDate = "$currentYear-" . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . "-01";
$lastDate = date("Y-m-t", strtotime($firstDate));

$yearQuery = "SELECT DISTINCT YEAR(membershipApproveDate) as year 
              FROM membership 
              WHERE membershipApproveDate IS NOT NULL 
              ORDER BY year ASC";
$yearResult = mysqli_query($con, $yearQuery);
$availableYears = [];
while ($yearRow = mysqli_fetch_assoc($yearResult)) {
    $availableYears[] = $yearRow['year'];
}

if (!in_array($currentYear, $availableYears)) {
    $availableYears[] = $currentYear;
    sort($availableYears);
}

// Fetch data for the table
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
          ORDER BY a.staffNo ASC";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyata Kewangan</title>
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
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png');
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
            max-width: 100%;
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
        }

        .table {
            width: 100%;
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

        .form-select {
            border-radius: 15px;
            padding: 0.5rem 2rem 0.5rem 1rem;
        }

        .modal {
            z-index: 1200; 
        }

        .modal-content {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .member-header { 
            display: flex; 
            justify-content: space-between; 
            border: 1px solid #000; 
            padding: 10px; 
            margin-bottom: 20px; 
        } 
    
        .member-id { 
            display: flex; 
            gap: 20px; 
        } 
    
        .member-number { 
            border: 1px solid #000; 
            padding: 5px 10px; 
        } 
    
        .statement-title { 
            font-weight: bold; 
            text-align: center; 
            margin: 20px 0; 
            text-decoration: underline; 
        } 
    
        .info-section { 
            margin-bottom: 30px; 
        } 
    
        .info-section h6 { 
            font-weight: bold; 
            margin-bottom: 10px; 
            text-decoration: underline; 
        } 
    
        .info-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
            gap: 10px; 
            margin-bottom: 20px; 
        } 
    
        .info-item { 
            display: flex; 
            flex-direction: column; 
        } 
    
        .approval-section { 
            margin-top: 30px; 
        } 
    
        .notes { 
            margin-top: 20px; 
            font-size: 0.9em; 
        } 

        .batch-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .select-all-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .batch-send-btn {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .batch-send-btn:hover {
            background-color: #266652;
            transform: translateY(-1px);
        }

        .batch-send-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .checkbox-column {
            width: 40px;
            text-align: center;
        }

        .checkbox-header {
            padding: 0.5rem !important;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2 class="page-title">Penyata Kewangan</h2>
    </div>

    <div class="container">
        <div class="search-container">
            <div class="search-box">
                <div class="filter-group" style="flex: 1;">
                    <select class="form-select" id="monthFilter">
                        <?php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                            5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
                        ];
                        foreach ($months as $num => $name) {
                            $selected = ($num == $currentMonth) ? 'selected' : '';
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1;">
                    <select class="form-select" id="yearFilter">
                        <?php
                        foreach ($availableYears as $year) {
                            $selected = ($year == $currentYear) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>
                </div>
                <input type="text" id="searchStaff" class="search-input" placeholder="Cari No Anggota...">
                <button class="btn btn-primary" onclick="applyFilters()">Cari</button>
                <button class="btn btn-success" onclick="resetFilters()">Semua Ahli</button>
            </div>
        </div>

        <div class="content-container">
            <div class="batch-controls">
                <div class="select-all-container">
                    <input type="checkbox" id="selectAll" class="form-check-input">
                    <label for="selectAll">Pilih Semua</label>
                </div>
                <button id="batchSendBtn" class="batch-send-btn" disabled>
                    Hantar Terpilih
                </button>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="checkbox-column checkbox-header">
                            <input type="checkbox" class="form-check-input" id="headerCheckbox">
                        </th>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Emel</th>
                        <th>Hasilkan</th>
                        <th>Status Penghantaran</th>
                        <th>Penghantaran Pada</th>
                    </tr>
                </thead>
                <tbody id="membersTableBody">
                    <?php
                    $i = 1;
                    while ($row = mysqli_fetch_array($result)) {
                        $statusClass = $row['status'] ? 'status-accepted' : 'status-rejected';
                        $statusText = $row['status'] ? 'Berjaya Dihantar!' : 'Tidak Berjaya!';
                        
                        echo "<tr>";
                        echo "<td class='checkbox-column'>";
                        echo "<input type='checkbox' class='form-check-input row-checkbox' data-staffNo='".$row['staffNo']."'>";
                        echo "</td>";
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
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalGenerate" tabindex="-1" aria-labelledby="modalGenerateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGenerateLabel">Penyata Kewangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="sendButton" class="btn btn-success">Hantar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Handle "Select All" checkbox
                $('#selectAll, #headerCheckbox').on('change', function() {
                    const isChecked = $(this).prop('checked');
                    $('.row-checkbox').prop('checked', isChecked);
                    $('#selectAll, #headerCheckbox').prop('checked', isChecked);
                    updateBatchSendButton();
                });

                // Handle individual checkboxes
                $(document).on('change', '.row-checkbox', function() {
                    updateBatchSendButton();
                    
                    // Update header and select all checkboxes
                    const totalCheckboxes = $('.row-checkbox').length;
                    const checkedCheckboxes = $('.row-checkbox:checked').length;
                    $('#selectAll, #headerCheckbox').prop('checked', totalCheckboxes === checkedCheckboxes);
                });

                function updateBatchSendButton() {
                    const checkedCount = $('.row-checkbox:checked').length;
                    $('#batchSendBtn').prop('disabled', checkedCount === 0);
                }

                // Handle batch send button
                $('#batchSendBtn').on('click', function() {
                    const selectedStaffNos = [];
                    $('.row-checkbox:checked').each(function() {
                        selectedStaffNos.push($(this).data('staffno'));
                    });

                    if (selectedStaffNos.length === 0) {
                        alert('Sila pilih sekurang-kurangnya satu ahli');
                        return;
                    }

                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();

                    // Disable button and show loading state
                    $(this).prop('disabled', true).text('Menghantar...');

                    // Send batch request
                    $.ajax({
                        url: 'batchsend.php',
                        method: 'POST',
                        data: {
                            staffNos: selectedStaffNos,
                            month: month,
                            year: year
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Update UI for successful sends
                                response.results.forEach(result => {
                                    if (result.success) {
                                        $('#status-' + result.staffNo).html(
                                            '<span class="status-badge status-accepted">Berjaya dihantar!</span>'
                                        );
                                        $('#date-' + result.staffNo).text(result.notificationLatestDate);
                                    }
                                });
                                alert('Proses berkelompok berjaya selesai!');
                            } else {
                                alert('Ralat dalam pemprosesan berkelompok: ' + response.error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', error);
                            alert('Ralat semasa pemprosesan berkelompok: ' + error);
                        },
                        complete: function() {
                            // Reset button state
                            $('#batchSendBtn').prop('disabled', false).text('Hantar Terpilih');
                            // Uncheck all checkboxes
                            $('.row-checkbox, #selectAll, #headerCheckbox').prop('checked', false);
                            updateBatchSendButton();
                        }
                    });
                });

                function applyFilters() {
                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();
                    const searchText = $('#searchStaff').val().toLowerCase();

                    $.ajax({
                        url: 'filter_members.php',
                        method: 'GET',
                        data: { 
                            month: month,
                            year: year,
                            search: searchText
                        },
                        success: function(response) {
                            $('#membersTableBody').html(response);
                            
                            // Update URL without page reload
                            history.pushState(
                                null, 
                                '', 
                                `?month=${month}&year=${year}`
                            );
                        },
                        error: function() {
                            alert('Error loading data');
                        }
                    });
                }

                function resetFilters() {
                    const currentMonth = new Date().getMonth() + 1;
                    const currentYear = new Date().getFullYear();

                    $('#searchStaff').val('');
                    $('#monthFilter').val(currentMonth);
                    $('#yearFilter').val(currentYear);

                    applyFilters();
                }

                $(document).on('click', '.hasilkan-btn', function() {
                    const staffNo = $(this).data('staffno');
                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();
                    
                    $.ajax({
                        url: 'generateprocess.php',
                        method: 'GET',
                        data: { 
                            staffNo: staffNo,
                            month: month,
                            year: year
                        },
                        success: function(response) {
                            $('#modalContent').html(response);
                            $('#sendButton').data('staffno', staffNo);
                        }
                    });
                });

                $('#sendButton').on('click', function () {
                    const staffNo = $(this).data('staffno');
                    const month = $('#monthFilter').val();
                    const year = $('#yearFilter').val();
    
                    // Show loading state
                    $(this).prop('disabled', true).text('Menghantar...');
                
                    $.ajax({
                        url: 'sendprocess.php',
                        method: 'POST',
                        data: { 
                            staffNo: staffNo,
                            month: month,
                            year: year
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $('#status-' + staffNo).html('<span class="status-badge status-accepted">Berjaya dihantar!</span>');
                                $('#date-' + staffNo).text(response.notificationLatestDate);
                                alert('Email Berjaya Dihantar!');
                                $('#modalGenerate').modal('hide');
                            } else {
                                console.error('Error:', response.error);
                                alert(response.error || 'Email Gagal Dihantar!');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Ajax error:', {
                                status: status,
                                error: error,
                                responseText: xhr.responseText
                            });
                            alert('Ralat semasa penghantaran: ' + error);
                        },
                        complete: function() {
                            // Re-enable button
                            $('#sendButton').prop('disabled', false).text('Send');
                        }
                    });
                });
            });
    </script>
</body>
</html>