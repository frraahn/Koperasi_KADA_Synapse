<?php 
include 'crssession.php';

if(!session_id())
{
  session_start();
}

include 'headerapplicant.php';
include 'dbconnect.php';

// Get user email from session
$userEmail = $_SESSION['email'];

$sqlMembership = "SELECT membership.membershipStatus, savingtype.modahSyer
                  FROM membership 
                  JOIN applicant ON membership.staffNo = applicant.staffNo 
                  JOIN savingtype ON membership.staffNo = savingType.staffNo
                  WHERE applicant.email = '$userEmail'";
$resultMembership = mysqli_query($con, $sqlMembership);

if ($resultMembership && mysqli_num_rows($resultMembership) > 0) {
    $rowMembership = mysqli_fetch_assoc($resultMembership);
    $membershipStatus = $rowMembership['membershipStatus'];
    $modahSyer = $rowMembership['modahSyer'];
    // Check if membership status is not 2
    if ($membershipStatus != 2 || $modahSyer<300) {
        echo "<script>
            alert('Anda perlu mempunyai status aktif keahlian sebelum memohon.');
            window.location.href = 'applicantHome.php'; // Replace with an appropriate page
        </script>";
        exit();
    }
} else {
    echo "<script>
        alert('Keahlian anda perlu diluluskan atau jumlah Modah Syer perlu melebihi 300 sebelum dibenarkan memohon pembiayaan.');
        window.location.href = 'applicantHome.php'; 
    </script>";
    exit();
}

// Retrieve applicant details
$sql = "SELECT applicant.*, bankinfo.bankAccountName, bankinfo.accountBankNumber
        FROM applicant 
        LEFT JOIN users ON users.email = applicant.email
        LEFT JOIN bankinfo ON bankinfo.staffNo = applicant.staffNo 
        LEFT JOIN membership ON membership.staffNo = applicant.staffNo
        WHERE users.email = '$userEmail'";

// Execute SQL statement
$result = mysqli_query($con, $sql); 
$applicant = mysqli_fetch_array($result);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Berhenti Menjadi Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #D6536D;
            --secondary: #E43D12;
            --accent: #EBE9E1;
            --yellow: #EFB11D;
            --light-pink: #FFA2B6;
            --blue: #6686F6;
            --green: #CFFFDC;
        }
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.25)), url('img/img.png'); /* Replace with the correct path to your background image */
          background-size: cover;
          background-repeat: no-repeat;
          background-attachment: fixed;
          background-position: center;
        }

        .page-header {
            background-color: var(--blue);
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .page-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            text-align: left;
            margin: 0;
            padding-left: 4.5rem;
        }

        .form-container {
                max-width: 900px;
                margin: 30px auto;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                padding: 20px;
        }
        .form-section-title {
                background-color: var(--blue);
                color: #FFFFFF;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 5px;
        }
        .form-group {
                margin-bottom: 15px;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
            max-width: 600px;
            margin: 30px auto;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            background: var(--accent);
            color: var(--primary);
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 5px;
            margin-top: auto;
        }
        .tab.active {
            background: var(--secondary);
            color: #fff;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        button {
            width: 20%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.2rem;
            font-size: 0.875rem;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .btn-danger {
            background-color: var(--yellow);
            border-color: var(--yellow);
        }
        .btn-primary {
            background-color: var(--light-pink);
            border-color: var(--light-pink);
        }
        .btn-success {
            background-color: green;
            border-color: green;
        }
        #submitButton:hover {
            background-color: var(--green);
            color: #000000;
            border-color: var(--green);
        }
        #clearButton {
            background-color: var(--yellow);
            color: white;
        }

        #clearButton:hover {
            background-color: var(--secondary); /* Hover in secondary color */
            border-color: var(--secondary); /* Border in secondary color */
        }

        #prevButton {
            background-color: slategrey;
            color: white;
        }

        #nextButton {
            background-color: var(--primary);
            color: white;
        }

        #prevButton:hover {
            background-color: darkslategrey;
        }

        #nextButton:hover {
            background-color: var(--light-pink);
            border-color: var(--light-pink); 
        }

        .button-group {
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Adds consistent gaps between buttons */
        }
        .modal {
            z-index: 1200; 
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 5px; /* Adjust spacing between checkbox and text */
        }
        .checkbox-container input[type="checkbox"] {
            width: auto; /* Override input's global width */
            margin: 8;
        }
    </style>
    <script>
        let currentTab = 0;

        function showTab(tabIndex) {
            const tabs = document.querySelectorAll('.tab');
            const contents = document.querySelectorAll('.tab-content');
            tabs.forEach((tab, index) => {
                tab.classList.toggle('active', index === tabIndex);
                contents[index].classList.toggle('active', index === tabIndex);
            });
            currentTab = tabIndex;
            updateNavigationButtons();
        }

        function nextTab() {
            const tabs = document.querySelectorAll('.tab');
            if (currentTab < tabs.length - 1) {
                showTab(currentTab + 1);
            }
        }

        function prevTab() {
            if (currentTab > 0) {
                showTab(currentTab - 1);
            }
        }

        function clearForm() {
            document.querySelector('form').reset();
        }

        function updateNavigationButtons() {
            document.getElementById('prevButton').style.display = currentTab === 0 ? 'none' : 'inline-block';
            document.getElementById('nextButton').style.display = currentTab === document.querySelectorAll('.tab').length - 1 ? 'none' : 'inline-block';
            document.getElementById('submitButton').style.display = currentTab === document.querySelectorAll('.tab').length - 1 ? 'inline-block' : 'none';
        }

        function validateFields() {
        let isValid = true;

        // Check all required fields
        document.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
            }
        });

        return isValid;
        }

        function validateAndNextTab() {
            const requiredFields = document.querySelectorAll('form .tab-content.active [required]');
            let isValid = true;

            requiredFields.forEach(field => {
            if (field.type === 'checkbox' && !field.checked) {
                isValid = false;
                field.classList.add('error'); // Optional: visually highlight the checkbox
            } else if (field.type !== 'checkbox' && !field.value.trim()) {
                isValid = false;
                field.classList.add('error'); // Highlight error fields (optional).
            } else {
                field.classList.remove('error');
            }
            });

            if (isValid) {
                nextTab(); // Call your existing function for next tab.
            } else {
                // Show the error modal
                const modal = new bootstrap.Modal(document.getElementById('errorModal')); // Ensure you have the error modal element.
                modal.show();
            }
        }


        function calculateMonthlyRepayment() {
            const loanAmount = parseFloat(document.getElementById("amount").value);
            const loanDuration = parseInt(document.getElementById("duration").value);
            const profitRate = parseFloat(document.getElementById("profitRate").value) / 100;
            if (!isNaN(loanAmount) && !isNaN(loanDuration) && loanDuration > 0) {
                const monthlyRepayment = (loanAmount + loanAmount * profitRate) / loanDuration;
                document.getElementById("mpayment").value = monthlyRepayment.toFixed(2);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showTab(0);
        });
    </script>
</head>
<body>
      <div class="page-header"><h2 class="page-title">PERMOHONAN BERHENTI MENJADI ANGGOTA</h2></div>
        <div class="tabs">
            <div class="tab active" onclick="showTab(0)">Maklumat Pemohon</div>
            <div class="tab" onclick="showTab(1)">Sebab Berhenti</div>
            <div class="tab" onclick="showTab(2)">Pengakuan Pemohon</div>
        </div>

        <div class="form-container">

        <form action="berhentiprocess.php" method="post" enctype="multipart/form-data">

        <!-- Error Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Maklumat Tidak Lengkap</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Sila lengkapkan maklumat diperlukan sebelum simpan.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Loan Details Section -->
        <div class="tab-content active">
            <br>
          <div class="form-section-title">Maklumat Pemohon</div>
            <br>
                      <div class="form-group">
                          <label for="name">Nama:</label>
                          <input type="text" name="name" id="name" value="<?php echo $applicant['applicantName']; ?>" readonly>
                      </div>
                      <div class="form-group">
                          <label for="ic_number">No. KP:</label>
                          <input type="text" name="ic_number" id="ic_number" value="<?php echo $applicant['applicantIC']; ?>" readonly>
                      </div>
                      <div class="form-group">
                        <label for="dob">Tarikh Lahir:</label>
                        <input type="date" name="dob" id="dob" value="<?php echo $applicant['applicantDOB']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="number" name="age" id="age" value="<?php echo $applicant['applicantAge']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="address">Alamat Rumah:</label>
                      <input type="text" name="address" id="address" value="<?php echo $applicant['applicantStreet']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="postcode">Poskod:</label>
                      <input type="text" name="postcode" id="postcode" value="<?php echo $applicant['applicantPostcode']; ?>" readonly>
                    </div>
                <div class="form-group">
                      <label for="postcode">Negeri:</label>
                      <input type="text" name="postcode" id="postcode" value="<?php echo $applicant['applicantState']; ?>" readonly>
                </div>
                      <div class="form-group">
                        <label for="sex">Jantina:</label>
                        <input type="text" name="sex" id="sex" value="<?php echo $applicant['applicantGender']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="religion">Agama:</label>
                        <input type="text" name="religion" id="religion" value="<?php echo $applicant['applicantReligion']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label for="nation">Bangsa:</label>
                      <input type="text" name="nation" id="nation" value="<?php echo $applicant['applicantRace']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="pf">No. PF:</label>
                      <input type="text" name="pf" id="pf" value="<?php echo $applicant['applicantPF']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="position">Jawatan:</label>
                      <input type="text" name="position" id="position" value="<?php echo $applicant['applicantPosition']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="office_street"> Alamat Pejabat:</label>
                      <input type="text" name="office_street" id="office_street" value="<?php echo $applicant['officeStreet']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="office_postcode">Poskod Pejabat:</label>
                      <input type="text" name="office_postcode" id="office_postcode" value="<?php echo $applicant['officePostcode']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="office_postcode">No. Telefon Pejabat:</label>
                      <input type="text" name="office_postcode" id="office_postcode" value="<?php echo $applicant['officeFax']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="phoneNumber">No. Telefon Bimbit:</label>
                      <input type="text" name="phoneNumber" id="phoneNumber" value="<?php echo $applicant['applicantPhoneNumber']; ?>" readonly>
                  </div>
                  <br>
                </div>

                <div class="tab-content">
                    <br>
                  <div class="form-section-title">Sebab Berhenti Menjadi Anggota</div>
                  <br>
                  <div class="form-group">
                  <label for="reason">Sila nyatakan sebab-sebab:</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Nyatakan sebab anda..." required></textarea>
                </div>
                <br>
              </div>

                  <div class="tab-content">
                    <br>
                  <div class="form-section-title">Pengakuan Pemohon</div>
                  <br>
            <p>
              Saya, <span id="applicant_name"><?php echo $applicant['applicantName']; ?></span>, No. K/P: 
              <span id="applicant_ic"><?php echo $applicant['applicantIC']; ?></span>, dengan ini, saya memberi kuasa kepada KOPERASI KAKITANGAN KADA KELANTAN BHD atau wakilnya yang sah untuk memproses permohonan penamatan keahlian saya. Saya memahami bahawa sebarang tunggakan, yuran, atau bayaran yang masih belum dijelaskan perlu diselesaikan sebelum permohonan ini dapat diluluskan. Saya juga bersetuju bahawa pihak KOPERASI KAKITANGAN KADA KELANTAN BHD berhak untuk menolak permohonan ini sekiranya terdapat sebarang keperluan atau syarat yang tidak dipenuhi tanpa perlu memberikan sebarang alasan.
          </p>
          <div class="checkbox-container">
            <input type="checkbox" name="agree_terms" id="agree_terms" required>
            <label for="agree_terms">Saya bersetuju dengan terma dan syarat yang dinyatakan di atas.</label>
        </div>
        </div>

          <div class="button-group">
                <button type="button" class="btn btn-secondary" id="prevButton" onclick="prevTab()" style="display: none;">Kembali</button>
                <button type="button" class="btn btn-primary" id="nextButton" onclick="validateAndNextTab()">Simpan</button>
                <button type="submit" class="btn btn-success"id="submitButton" style="display: none;">Hantar</button>
                <button class="btn btn-danger" type="clearButton" onclick="clearForm()">Kosongkan Borang</button>
            </div>
  </form>
</div>
</body>
</html>