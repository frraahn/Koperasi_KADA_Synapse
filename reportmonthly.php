<!-- Monthly Report -->
<div class="report-card">
    <div class="card-header">
        <h3 style="margin: 0;">Laporan Bulanan</h3>
    </div>

    <form method="POST" class="report-form">
        <select name="month">
            <?php
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
                5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
            ];
            foreach ($months as $num => $name) {
                $selected = $selectedMonth == $num ? 'selected' : '';
                echo "<option value='$num' $selected>$name</option>";
            }
            ?>
        </select>
        <select name="year">
            <?php
            mysqli_data_seek($resultYears, 0);
            while ($year = mysqli_fetch_assoc($resultYears)) {
                $selected = $selectedYear == $year['year'] ? 'selected' : '';
                echo "<option value='{$year['year']}' $selected>{$year['year']}</option>";
            }
            ?>
        </select>
        <button type="submit">Papar</button>
    </form>

    <div class="stats-grid">
        <!-- Membership Statistics -->
        <div class="stats-section">
            <h4>Statistik Keahlian</h4>
            <div class="stats-container">
                <div class="stat-item blue-card">
                    <div class="stat-number"><?php echo $membershipStats['total_applications'] ?? 0;?></div>
                    <div class="stat-label">Jumlah Permohonan</div>
                </div>
                <div class="stat-item green-card">
                    <div class="stat-number"><?php echo $membershipStats['approved'] ?? 0;?></div>
                    <div class="stat-label">Diluluskan</div>
                </div>
                <div class="stat-item red-card">
                    <div class="stat-number"><?php echo $membershipStats['rejected'] ?? 0;?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
                <div class="stat-item orange-card">
                    <div class="stat-number"><?php echo $membershipStats['pending'] ?? 0;?></div>
                    <div class="stat-label">Dalam Proses</div>
                </div>
            </div>
        </div>

        <!-- Loan Statistics -->
        <div class="stats-section">
            <h4>Statistik Pinjaman</h4>
            <div class="stats-container">
                <div class="stat-item blue-card">
                    <div class="stat-number"><?php echo $loanStats['total_applications'] ?? 0; ?></div>
                    <div class="stat-label">Jumlah Permohonan</div>
                </div>
                <div class="stat-item green-card">
                    <div class="stat-number"><?php echo $loanStats['approved'] ?? 0; ?></div>
                    <div class="stat-label">Diluluskan</div>
                </div>
                <div class="stat-item red-card">
                    <div class="stat-number"><?php echo $loanStats['rejected'] ?? 0; ?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
                <div class="stat-item orange-card">
                    <div class="stat-number"><?php echo $loanStats['pending'] ?? 0; ?></div>
                    <div class="stat-label">Dalam Proses</div>
                </div>
            </div>
        </div>
    </div>
</div>