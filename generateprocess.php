<?php  
include 'dbconnect.php';

if (isset($_GET['staffNo'])) {
    $staffNo = $_GET['staffNo'];
    $month = isset($_GET['month']) ? $_GET['month'] : date('n');
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

    // Create date string for the end of the selected month
    $dateString = "$year-$month-01";
    $endOfMonth = date('Y-m-t', strtotime($dateString));

    setlocale(LC_TIME, 'ms_MY.UTF-8', 'ms_MY', 'Malay');
    $formattedEndDate = strftime('%d %B %Y', strtotime($endOfMonth));

    // Restructured query to avoid nested aggregate functions
    $query = "SELECT 
        a.staffNo, 
        a.applicantName, 
        a.applicantIC, 
        a.applicantPF,
        
        -- Modal Syer calculation
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
            AND savingDate > '$endOfMonth'
            AND (savingType = 'Potongan Gaji' OR savingDesc LIKE '%modahSyer%')
        )) as modahSyer,
        
        -- Simpanan Tetap calculation
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
            AND savingDate > '$endOfMonth'
            AND (savingType = 'Potongan Gaji' OR savingDesc LIKE '%simpananTetap%')
        )) as simpananTetap,
        
        -- Sumbangan Tabung calculation
        GREATEST(0, s.sumbanganTabung - (
            SELECT COALESCE(
                SUM(CASE 
                    WHEN savingType = 'Potongan Gaji' THEN 5
                    ELSE savingAmount
                END), 0
            )
            FROM saving 
            WHERE staffNo = a.staffNo 
            AND savingDate > '$endOfMonth'
            AND (savingType = 'Potongan Gaji' OR savingType = 'sumbanganTabung')
        )) as sumbanganTabung,
        
        -- Modal Yuran calculation
        GREATEST(0, s.modalYuran - (
            SELECT COALESCE(SUM(savingAmount), 0)
            FROM saving 
            WHERE staffNo = a.staffNo 
            AND savingDate > '$endOfMonth'
            AND savingDesc LIKE '%modalYuran%'
        )) as modalYuran,
        
        -- Wang Deposit Anggota calculation
        GREATEST(0, s.wangDepositAnggota - (
            SELECT COALESCE(SUM(savingAmount), 0)
            FROM saving 
            WHERE staffNo = a.staffNo 
            AND savingDate > '$endOfMonth'
            AND savingDesc LIKE '%wangDepositAnggota%'
        )) as wangDepositAnggota,
        
        -- Loan related fields (unchanged)
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
            AND lr.repaymentDate <= '$endOfMonth'
        ) as total_paid
        
        FROM applicant a 
        LEFT JOIN membership m ON a.staffNo = m.staffNo 
        LEFT JOIN loan l ON a.staffNo = l.staffNo 
        LEFT JOIN savingtype s ON a.staffNo = s.staffNo 
        WHERE a.staffNo = '$staffNo'
        AND (l.loanStatus IS NULL OR l.loanStatus = 2)";

    // Rest of the code remains the same
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $member = null;
        $loans = [];

        while ($row = mysqli_fetch_assoc($result)) {
            if (!$member) {
                $member = [
                    'applicantName' => $row['applicantName'],
                    'applicantIC' => $row['applicantIC'],
                    'applicantPF' => $row['applicantPF'],
                    'staffNo' => $row['staffNo'],
                    'modalSyer' => $row['modahSyer'],
                    'modalYuran' => $row['modalYuran'],
                    'simpananTetap' => $row['simpananTetap'],
                    'sumbanganTabung' => $row['sumbanganTabung'],
                    'wangDepositAnggota' => $row['wangDepositAnggota'],
                ];
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

                $loans[$row['loanType']] = [
                    'remaining' => round($remainingLoan, 2),
                    'totalPaid' => round($row['total_paid'], 2),
                    'expectedPaid' => round($expectedTotalPaid, 2),
                    'monthlyPayment' => $row['monthlyPayment']
                ];
            }
        }
?>
        <!-- HTML part remains exactly the same -->
        <div class="member-header">
            <div class="member-id">
                <div><strong>NAMA:</strong> <?= $member['applicantName'] ?></div>
                <div><strong>NO. K/P:</strong> <?= htmlspecialchars($member['applicantIC']) ?></div>
                <div><strong>NO. PF:</strong> <?= $member['applicantPF'] ?></div>
            </div>
            <div class="member-number">
                <strong>NO. AHLI:</strong> <?= $member['staffNo'] ?>
            </div>
        </div>

        <div class="info-section">
            <h6>MAKLUMAT SAHAM AHLI:</h6>
            <div class="info-grid">
                <div class="info-item">
                    <span>Modal Syer:</span>
                    <span>RM <?= number_format($member['modalSyer'], 2) ?></span>
                </div>
                <div class="info-item">
                    <span>Modal Yuran:</span>
                    <span>RM <?= number_format($member['modalYuran'], 2) ?></span>
                </div>
                <div class="info-item">
                    <span>Simpanan Tetap:</span>
                    <span>RM <?= number_format($member['simpananTetap'], 2) ?></span>
                </div>
                <div class="info-item">
                    <span>Tabung Anggota:</span>
                    <span>RM <?= number_format($member['sumbanganTabung'], 2) ?></span>
                </div>
                <div class="info-item">
                    <span>Simpanan Anggota:</span>
                    <span>RM <?= number_format($member['wangDepositAnggota'], 2) ?></span>
                </div>
            </div>

            <h6>MAKLUMAT PINJAMAN AHLI:</h6>
            <div class="info-grid">
                <?php
                $loanTypes = [
                    1 => 'Al-Bai',
                    2 => 'Al-Innah',
                    3 => 'Skim Khas',
                    4 => 'Karnival Musim Istimewa',
                    5 => 'Baik Pulih Kenderaan',
                    6 => 'Cukai Jalan',
                    7 => 'Al-Qadrul Hassan',
                ];

                foreach ($loanTypes as $id => $name) {
                    if (isset($loans[$id])) {
                    ?>
                    <div class="info-item">
                        <span><?= htmlspecialchars($name) ?>:</span>
                        <span>RM <?= number_format($loans[$id]['remaining'], 2) ?></span>
                    </div>
                    <?php
                    } else {
                    ?>
                    <div class="info-item">
                        <span><?= htmlspecialchars($name) ?>:</span>
                        <span>RM 0.00</span>
                    </div>
                    <?php
                    }
                }
                ?>
            </div>
        </div>

        <div class="info-section">
            <h6>PENGESAHAN BAGI PENYATA KEWANGAN AHLI KOPERASI KAKITANGAN KADA KELANTAN BERHAD BAGI TARIKH BERAKHIR <?= htmlspecialchars($formattedEndDate) ?></h6>
        </div>

        <div class="approval-section">
            <div>Saya <strong><?= htmlspecialchars($member['applicantName']) ?></strong>, No. Ahli: <strong><?= htmlspecialchars($member['staffNo']) ?></strong> mengesahkan bahawa Penyata Kewangan Koperasi Kakitangan KADA Kelantan Berhad bagi tarikh berakhir <?= htmlspecialchars($endOfMonth) ?> adalah benar.</div>
        </div>

        <div class="notes">
            <div>Nota:</div>
            <div>1. Sekiranya pihak Koperasi tidak menerima sebarang maklumbalas daripada tuan/puan sehingga <?= htmlspecialchars($formattedEndDate) ?>, maka pengesahan penyata ini dianggap betul dan tuan/puan bersetuju.</div>
        </div>
<?php
    } else {
        echo "Maklumat tidak dijumpai.";
    }
}
?>