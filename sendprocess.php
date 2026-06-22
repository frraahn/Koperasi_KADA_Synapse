<?php
include 'dbconnect.php';
require_once 'vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

// Set timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Initialize response array
$response = array(
    'success' => false,
    'error' => null,
    'notificationLatestDate' => null
);

try {
    if (!isset($_POST['staffNo'])) {
        throw new Exception('No staff number provided');
    }

    $staffNo = $_POST['staffNo'];
    $currentDate = date('Y-m-d H:i:s');
    $month = isset($_POST['month']) ? $_POST['month'] : date('n');
    $year = isset($_POST['year']) ? $_POST['year'] : date('Y');
    
    // Create date string for the end of selected month
    $dateString = "$year-$month-01";
    $endOfMonth = date('Y-m-t', strtotime($dateString));
    
    setlocale(LC_TIME, 'ms_MY.UTF-8', 'ms_MY', 'Malay');
    $formattedEndDate = strftime('%d %B %Y', strtotime($endOfMonth));
    
    // Get current KL time
    $klTime = date('d/m/Y h:i:s A');

    // Get member details with loan calculations
    $query = "SELECT 
                a.email, a.staffNo, a.applicantName, a.applicantIC, a.applicantPF,
                GREATEST(0, s.modahSyer - (
                    SELECT COALESCE(
                        SUM(CASE 
                            WHEN savingType = 'Potongan Gaji' AND s.modahSyer < 300 THEN 50
                            WHEN savingType != 'Potongan Gaji' THEN savingAmount
                            ELSE 0
                        END), 0
                    )
                    FROM saving 
                    WHERE staffNo = a.staffNo 
                    AND savingDate > ?
                    AND (savingType = 'Potongan Gaji' OR savingDesc LIKE '%modahSyer%')
                )) as modahSyer,
                
                GREATEST(0, s.simpananTetap - (
                    SELECT COALESCE(
                        SUM(CASE 
                            WHEN savingType = 'Potongan Gaji' AND s.modahSyer >= 300 THEN 50
                            WHEN savingType != 'Potongan Gaji' THEN savingAmount
                            ELSE 0
                        END), 0
                    )
                    FROM saving 
                    WHERE staffNo = a.staffNo 
                    AND savingDate > ?
                    AND (savingType = 'Potongan Gaji' OR savingDesc LIKE '%simpananTetap%')
                )) as simpananTetap,
                
                GREATEST(0, s.sumbanganTabung - (
                    SELECT COALESCE(
                        SUM(CASE 
                            WHEN savingType = 'Potongan Gaji' THEN 5
                            ELSE savingAmount
                        END), 0
                    )
                    FROM saving 
                    WHERE staffNo = a.staffNo 
                    AND savingDate > ?
                    AND (savingType = 'Potongan Gaji' OR savingType = 'sumbanganTabung')
                )) as sumbanganTabung,
                
                GREATEST(0, s.modalYuran - (
                    SELECT COALESCE(SUM(savingAmount), 0)
                    FROM saving 
                    WHERE staffNo = a.staffNo 
                    AND savingDate > ?
                    AND savingDesc LIKE '%modalYuran%'
                )) as modalYuran,
                
                GREATEST(0, s.wangDepositAnggota - (
                    SELECT COALESCE(SUM(savingAmount), 0)
                    FROM saving 
                    WHERE staffNo = a.staffNo 
                    AND savingDate > ?
                    AND savingDesc LIKE '%wangDepositAnggota%'
                )) as wangDepositAnggota,
                
                l.loanID,
                l.loanStatus, 
                l.loanAmount,
                l.loanType,
                l.monthlyPayment,
                l.loanApproveDate,
                (
                    SELECT COALESCE(SUM(repaymentAmount), 0)
                    FROM loanrepayment lr
                    WHERE lr.loanID = l.loanID
                    AND lr.repaymentDate <= ?
                ) as total_paid
              FROM applicant a 
              LEFT JOIN membership m ON a.staffNo = m.staffNo 
              LEFT JOIN loan l ON a.staffNo = l.staffNo 
              LEFT JOIN savingtype s ON a.staffNo = s.staffNo 
              WHERE a.staffNo = ?
              AND (l.loanStatus IS NULL OR l.loanStatus = 2)";

    $stmt = $con->prepare($query);
    $stmt->bind_param('sssssss', $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $staffNo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Anggota tidak dijumpai');
    }

    $member = null;
    $loans = [];
    $email = '';

    while ($row = $result->fetch_assoc()) {
        if (!$member) {
            $member = $row;
            $email = $row['email'];
        }

        if (isset($row['loanStatus']) && $row['loanStatus'] == 2 && isset($row['loanType'])) {
            $loanApproveDate = new DateTime($row['loanApproveDate']);
            $selectedMonthEnd = new DateTime($endOfMonth);
            
            $monthsPassed = $loanApproveDate->diff($selectedMonthEnd)->m 
                            + ($loanApproveDate->diff($selectedMonthEnd)->y * 12);
            
            $expectedTotalPaid = $row['monthlyPayment'] * $monthsPassed;
            
            $remainingLoan = max(0, 
                $row['loanAmount'] - min($expectedTotalPaid, $row['total_paid'])
            );

            $loans[$row['loanType']] = round($remainingLoan, 2);
        }
    }

    // Create PDF directory if it doesn't exist
    $pdfDir = __DIR__ . '/pdf_files';
    if (!file_exists($pdfDir)) {
        if (!mkdir($pdfDir, 0755, true)) {
            throw new Exception('Failed to create PDF directory');
        }
    }

    if (!is_writable($pdfDir)) {
        throw new Exception('PDF directory is not writable');
    }

    // Generate PDF
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9
    ]);

    // Define styles
    $stylesheet = "
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20pt;
            font-size: 14pt;
        }
        .sub-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20pt;
            background-color: #0066cc;
            color: white;
            padding: 10pt;
            font-size: 14pt;
        }
        .member-header {
            width: 100%;
            margin-bottom: 20pt;
        }
        .member-id {
            float: left;
            width: 60%;
        }
        .member-number {
            float: right;
            width: 40%;
            text-align: right;
        }
        .datetime {
            text-align: right;
            margin-bottom: 10pt;
            font-size: 10pt;
        }
        .info-section {
            margin-bottom: 20pt;
            clear: both;
        }
        .info-section h6 {
            font-weight: bold;
            margin-bottom: 10pt;
            text-decoration: underline;
            font-size: 12pt;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20pt;
        }
        .info-table td {
            border: 1px solid black;
            padding: 8pt;
        }
        .info-table td:first-child {
            width: 70%;
        }
        .info-table td:last-child {
            width: 30%;
            text-align: right;
        }
        .approval-section {
            margin: 20pt 0;
            line-height: 1.5;
            clear: both;
        }
        .notes {
            margin-top: 20pt;
            font-size: 10pt;
            clear: both;
        }
        .clearfix::after {
            content: '';
            display: table;
            clear: both;
        }
    ";

    // Write the stylesheet
    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

    // HTML content
    $html = "
    <body>   
        <div style='text-align: center;'>
            <img src='img/logoKADA.jpeg' style='height: 100pt; width: auto;'>
        </div>

        <div class='header'>PENYATA KEWANGAN</div>
        <div class='sub-header'>KOPERASI KAKITANGAN KADA KELANTAN BERHAD</div>

        <div class='member-header clearfix'>
            <div class='member-id'>
                <div><strong>NAMA:</strong> {$member['applicantName']}</div>
                <div><strong>NO. K/P:</strong> {$member['applicantIC']}</div>
                <div><strong>NO. PF:</strong> {$member['applicantPF']}</div>
            </div>
            <div class='member-number'>
                <strong>NO. AHLI:</strong> {$member['staffNo']}
            </div>
        </div>

        <div class='info-section'>
            <h6>MAKLUMAT SAHAM AHLI:</h6>
            <table class='info-table'>";

    foreach ([
        'Modal Syer' => $member['modahSyer'],
        'Modal Yuran' => $member['modalYuran'],
        'Simpanan Tetap' => $member['simpananTetap'],
        'Tabung Anggota' => $member['sumbanganTabung'],
        'Simpanan Anggota' => $member['wangDepositAnggota']
    ] as $label => $value) {
        $html .= "
            <tr>
                <td>{$label}</td>
                <td>RM " . number_format($value, 2) . "</td>
            </tr>";
    }

    $html .= "
            </table>

            <h6>MAKLUMAT PINJAMAN AHLI:</h6>
            <table class='info-table'>";

    $loanTypes = [
        1 => 'Al-Bai',
        2 => 'Al-Innah',
        3 => 'Skim Khas',
        4 => 'Karnival Musim Istimewa',
        5 => 'Baik Pulih Kenderaan',
        6 => 'Cukai Jalan',
        7 => 'Al-Qadrul Hassan'
    ];

    foreach ($loanTypes as $id => $name) {
        $amount = isset($loans[$id]) ? $loans[$id] : 0;
        $html .= "
            <tr>
                <td>" . htmlspecialchars($name) . "</td>
                <td>RM " . number_format($amount, 2) . "</td>
            </tr>";
    }

    $html .= "
            </table>
        </div>

        <div class='info-section'>
            <h6>PENGESAHAN BAGI PENYATA KEWANGAN AHLI KOPERASI KAKITANGAN KADA KELANTAN BERHAD BAGI TARIKH BERAKHIR {$formattedEndDate}</h6>
        </div>

        <div class='approval-section'>
            <div>Saya <strong>{$member['applicantName']}</strong>, No. Ahli: <strong>{$member['staffNo']}</strong> mengesahkan bahawa Penyata Kewangan Koperasi Kakitangan KADA Kelantan Berhad bagi tarikh berakhir {$endOfMonth} adalah benar.</div>
        </div>

        <div class='notes'>
            <div>Nota:</div>
            <div>1. Sekiranya pihak Koperasi tidak menerima sebarang maklumbalas daripada tuan/puan sehingga {$formattedEndDate}, maka pengesahan penyata ini dianggap betul dan tuan/puan bersetuju.</div>
        </div>
    </body>";

    // Write the HTML content
    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    $pdfPath = $pdfDir . "/{$staffNo}_" . time() . ".pdf";
    $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

    if (!file_exists($pdfPath)) {
        throw new Exception('PDF file was not created successfully');
    }

    // Configure and send email
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    include 'smtpconnect.php';
    $mail->addAttachment($pdfPath);
    
    $mail->isHTML(true);
    $mail->Subject = 'Penyata Kewangan Koperasi Kakitangan KADA Kelantan Berhad';
    $mail->Body = "
        <p>Salam sejahtera,</p>
        <p>Berikut adalah Penyata Kewangan anda. Sila semak penyata ini dan sahkan.</p>
        <p>Terima kasih.</p>
        <p>Salam hormat,</p>
        <p>Koperasi Kakitangan KADA Kelantan Berhad</p>
    ";

    // Send email
    if (!$mail->send()) {
        throw new Exception('Email failed to send: ' . $mail->ErrorInfo);
    }

    // Update database with UPSERT operation
    $sql_insert = "INSERT INTO managing (status, notificationLatestDate, email) 
                   VALUES ('Berjaya dihantar!', ?, ?) ON DUPLICATE KEY UPDATE 
                   status = 'Berjaya dihantar!', 
                   notificationLatestDate = ?";
                   
    $stmt = $con->prepare($sql_insert);
    $stmt->bind_param('sss', $currentDate, $email, $currentDate);
    
    if (!$stmt->execute()) {
        throw new Exception('Database update failed: ' . $stmt->error);
    }

    // Set success response
    $response['success'] = true;
    $response['notificationLatestDate'] = $currentDate;

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    error_log('Error in sendprocess.php: ' . $e->getMessage());
} finally {
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($pdfPath) && file_exists($pdfPath)) {
        try {
            unlink($pdfPath);
        } catch (Exception $e) {
            error_log('Failed to delete temporary PDF: ' . $e->getMessage());
        }
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}