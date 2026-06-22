<?php
// statement_functions.php

require_once 'vendor/autoload.php';

class StatementProcessor {
    private $con;
    private $pdfDir;

    public function __construct($dbConnection) {
        $this->con = $dbConnection;
        $this->pdfDir = __DIR__ . '/pdf_files';
        $this->ensurePdfDirectory();
    }

    private function ensurePdfDirectory() {
        if (!file_exists($this->pdfDir)) {
            if (!mkdir($this->pdfDir, 0755, true)) {
                throw new Exception('Failed to create PDF directory');
            }
        }
        if (!is_writable($this->pdfDir)) {
            throw new Exception('PDF directory is not writable');
        }
    }

    public function getMemberData($staffNo, $endOfMonth) {
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

        $stmt = $this->con->prepare($query);
        $stmt->bind_param('sssssss', $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $endOfMonth, $staffNo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Member not found');
        }

        $member = null;
        $loans = [];

        while ($row = $result->fetch_assoc()) {
            if (!$member) {
                $member = $row;
            }

            if (isset($row['loanStatus']) && $row['loanStatus'] == 2 && isset($row['loanType'])) {
                $loans[$row['loanType']] = $this->calculateRemainingLoan($row, $endOfMonth);
            }
        }

        return ['member' => $member, 'loans' => $loans];
    }

    private function calculateRemainingLoan($loanData, $endOfMonth) {
        $loanApproveDate = new DateTime($loanData['loanApproveDate']);
        $selectedMonthEnd = new DateTime($endOfMonth);
        
        $monthsPassed = $loanApproveDate->diff($selectedMonthEnd)->m 
                        + ($loanApproveDate->diff($selectedMonthEnd)->y * 12);
        
        $expectedTotalPaid = $loanData['monthlyPayment'] * $monthsPassed;
        
        return max(0, 
            $loanData['loanAmount'] - min($expectedTotalPaid, $loanData['total_paid'])
        );
    }

    public function generatePDF($memberData, $endOfMonth) {
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
    
        // Format the end date
        $formattedEndDate = date('d/m/Y', strtotime($endOfMonth));
    
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
    
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
    
        // Access the member data correctly
        $member = $memberData['member'];
        $loans = $memberData['loans'];
    
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
    
        // Add share information
        $shareInfo = [
            'Modal Syer' => $member['modahSyer'],
            'Modal Yuran' => $member['modalYuran'],
            'Simpanan Tetap' => $member['simpananTetap'],
            'Tabung Anggota' => $member['sumbanganTabung'],
            'Simpanan Anggota' => $member['wangDepositAnggota']
        ];
    
        foreach ($shareInfo as $label => $value) {
            $formattedValue = number_format($value, 2);
            $html .= "
                <tr>
                    <td>{$label}</td>
                    <td>RM {$formattedValue}</td>
                </tr>";
        }
    
        $html .= "
                </table>
    
                <h6>MAKLUMAT PINJAMAN AHLI:</h6>
                <table class='info-table'>";
    
        // Define loan types
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
            $formattedAmount = number_format($amount, 2);
            $html .= "
                <tr>
                    <td>{$name}</td>
                    <td>RM {$formattedAmount}</td>
                </tr>";
        }
    
        $html .= "
                </table>
            </div>
    
            <div class='info-section'>
                <h6>PENGESAHAN BAGI PENYATA KEWANGAN AHLI KOPERASI KAKITANGAN KADA KELANTAN BERHAD BAGI TARIKH BERAKHIR {$formattedEndDate}</h6>
            </div>
    
            <div class='approval-section'>
                <div>Saya <strong>{$member['applicantName']}</strong>, No. Ahli: <strong>{$member['staffNo']}</strong> mengesahkan bahawa Penyata Kewangan Koperasi Kakitangan KADA Kelantan Berhad bagi tarikh berakhir {$formattedEndDate} adalah benar.</div>
            </div>
    
            <div class='notes'>
                <div>Nota:</div>
                <div>1. Sekiranya pihak Koperasi tidak menerima sebarang maklumbalas daripada tuan/puan sehingga {$formattedEndDate}, maka pengesahan penyata ini dianggap betul dan tuan/puan bersetuju.</div>
            </div>
        </body>";
    
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        
        $pdfPath = $this->pdfDir . "/{$member['staffNo']}_" . time() . ".pdf";
        $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);
    
        if (!file_exists($pdfPath)) {
            throw new Exception('PDF file was not created successfully');
        }
    
        return $pdfPath;
    }

    public function sendEmail($email, $pdfPath) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        include 'smtpconnect.php';
        
        $mail->addAddress($email);
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

        if (!$mail->send()) {
            throw new Exception('Email failed to send: ' . $mail->ErrorInfo);
        }

        return true;
    }

    public function updateDatabase($email, $currentDate) {
        $sql_insert = "INSERT INTO managing (status, notificationLatestDate, email) 
                       VALUES ('Berjaya dihantar!', ?, ?) 
                       ON DUPLICATE KEY UPDATE 
                       status = 'Berjaya dihantar!', 
                       notificationLatestDate = ?";
                       
        $stmt = $this->con->prepare($sql_insert);
        $stmt->bind_param('sss', $currentDate, $email, $currentDate);
        
        if (!$stmt->execute()) {
            throw new Exception('Database update failed: ' . $stmt->error);
        }

        return true;
    }
}