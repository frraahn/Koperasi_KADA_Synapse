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
    if ($membershipStatus != 2 || $membershipStatus === 6 || $modahSyer<300) {
        echo "<script>
            alert('Keahlian anda perlu diluluskan atau jumlah Modah Syer perlu melebihi 300 sebelum dibenarkan memohon pembiayaan.');
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
    <title>Permohonan Pembiayaan</title>
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
            max-width: 900px;
            margin: 30px auto;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 5px;
            cursor: pointer;
            background: var(--accent);
            color: var(--primary);
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 5px;
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
        input:focus, select:focus {
            border-color: var(--blue); 
            outline: none; 
            box-shadow: 0 0 5px rgba(0, 0, 255, 0.5); 
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

        .list-group {
            position: absolute;
            width: 51%;
            background: white;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none; /* Ensures it's hidden by default */
        }

        .staff-suggestion {
            padding: 10px;
            cursor: pointer;
        }

        .staff-suggestion:hover {
            background: #f0f0f0;
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

        const loanMaxAmounts = {
            "1": 20000,
            "2": 20000,
            "3": 10000,
            "4": 10000,
            "5": 4500,
            "6": 4500,
            "8": 20000,
            "7": 20000,
        };

        function validateLoanAmount() {
            const typeSelect = document.getElementById('type'); // Dropdown for loan type
            const amountInput = document.getElementById('amount'); // Input for loan amount
            const selectedLoanType = typeSelect.value; // Get selected loan type

            console.log('Selected Loan Type:', selectedLoanType); // Debugging
            console.log('Entered Amount:', amountInput.value); // Debugging

            if (loanMaxAmounts[selectedLoanType]) {
                const maxAmount = loanMaxAmounts[selectedLoanType];

                // If amount exceeds maximum allowed, set it to the maximum
                if (parseInt(amountInput.value) > maxAmount) {
                    amountInput.value = maxAmount;
                    alert(`Amaun maksimum untuk jenis pembiayaan ini ialah RM${maxAmount}.`);
                }
            }

            // Recalculate monthly repayment after adjusting the amount
            calculateMonthlyRepayment();
        }

        function filterStaff(guarantorNumber) {
            const searchInput = document.getElementById(`gName${guarantorNumber}`).value.trim();
            const staffList = document.getElementById(`staffList${guarantorNumber}`);

            if (searchInput.length < 2) {
                staffList.innerHTML = ''; 
                staffList.style.display = 'none'; // Hide if input is too short
                return;
            }

            fetch('get_applicants.php?search=' + encodeURIComponent(searchInput))
                .then(response => response.json())
                .then(data => {
                    staffList.innerHTML = ''; // Clear previous results

                    if (!Array.isArray(data) || data.length === 0) {
                        staffList.style.display = 'none'; // Hide dropdown if no results
                        return;
                    }

                    data.forEach(applicant => {
                        const staffItem = document.createElement('div');
                        staffItem.className = 'list-group-item staff-suggestion';
                        staffItem.innerHTML = `${applicant.applicantName} (${applicant.applicantIC})`;

                        staffItem.onclick = function() {
                            selectStaff(guarantorNumber, applicant);
                        };

                        staffList.appendChild(staffItem);
                    });

                    staffList.style.display = 'block'; // Show results if available
                })
                .catch(error => {
                    console.error("Error fetching applicants:", error);
                    staffList.style.display = 'none'; // Hide dropdown if error
                });
        }

        function selectStaff(guarantorNumber, applicant) {
            document.getElementById(`gName${guarantorNumber}`).value = applicant.applicantName;
            document.getElementById(`gIC${guarantorNumber}`).value = applicant.applicantIC;
            document.getElementById(`gPF${guarantorNumber}`).value = applicant.applicantPF;
            document.getElementById(`gID${guarantorNumber}`).value = applicant.staffNo;
            document.getElementById(`gPhoneNumber${guarantorNumber}`).value = applicant.applicantPhoneNumber;

            // Hide dropdown after selection
            document.getElementById(`staffList${guarantorNumber}`).innerHTML = '';
            document.getElementById(`staffList${guarantorNumber}`).style.display = 'none';
        }



        // DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize the first tab
            showTab(0);

            const amountInput = document.getElementById('amount');
            const typeSelect = document.getElementById('type');

            typeSelect.addEventListener('change', validateLoanAmount);

            amountInput.addEventListener('input', validateLoanAmount);
        });

    </script>
</head>
<body>
      <div class="page-header"><h2 class="page-title">PERMOHONAN PEMBIAYAAN</h2></div>

      <div class="tabs">
            <div class="tab active" onclick="showTab(0)">Jenis Pembiayaan</div>
            <div class="tab" onclick="showTab(1)">Maklumat Pemohon</div>
            <div class="tab" onclick="showTab(2)">Pengakuan Pemohon</div>
            <div class="tab" onclick="showTab(3)">Maklumat Penjamin 1</div>
            <div class="tab" onclick="showTab(4)">Maklumat Penjamin 2</div>
            <div class="tab" onclick="showTab(5)">Pengesahan Majikan</div>
        </div>
    <div class="form-container">

        <form action="pinjamanprocess.php" method="post" enctype="multipart/form-data">

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
          <div class="form-section-title">JENIS PEMBIAYAAN</div>
          <br>
                <!-- Loan Type Selection -->
                <div class="form-group">
                    <label for="type">Jenis Pembiayaan:</label>
                    <select name="type" id="type" required>
                        <option value="">-- Pilih Jenis Pembiayaan --</option>
                        <option value="1">al-bai</option>
                        <option value="2">al-innah</option>
                        <option value="3">skim khas</option>
                        <option value="4">karnival musim istimewa</option>
                        <option value="5">baik pulih kenderaan</option>
                        <option value="6">cukai jalan</option>
                        <option value="7">al-qadrul hassan</option>
                        <option value="8">lain-lain</option>
                        
                    </select>

                </div>

                <!-- If Loan Type is "Lain-lain" -->
                <div class="form-group">
                    <br>
                    <label for="other">Jika "Lain-lain," Nyatakan:</label>
                    <input type="text" name="other" id="other">
                </div>

                <!-- Loan Amount -->
                <div class="form-group">
                    <label for="amount">Amaun Dipohon (RM):</label>
                    <input type="number" name="amount" id="amount" required oninput="validateLoanAmount();calculateMonthlyRepayment()">
                </div>

                <!-- Loan Duration -->
                <div class="form-group">
                    <label for="duration">Tempoh Pembiayaan (months):</label>
                    <input type="number" name="duration" id="duration" required oninput="calculateMonthlyRepayment()">
                </div>

                <div class="form-group">
                    <label for="profitRate">Kadar Keuntungan Koperasi (%):</label>
                    <input type="number" name="profitRate" id="profitRate" value="4.2" readonly>
                </div>

                <!-- Monthly Repayment -->
                <div class="form-group">
                    <label for="mpayment">Ansuran Bulanan (RM):</label>
                    <input type="number" name="mpayment" id="mpayment" readonly>
                </div><br>
                <!-- <label for="file">Upload PDF/Image:</label>
    <input type="file" id="file" name="file" accept=".pdf,.jpg,.png,.jpeg,.gif" required><br> -->
        </div>

                      <!-- Applicant Details Section -->
        <div class="tab-content">
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
                      <label for="office_postcode">No. Poskod Pejabat:</label>
                      <input type="text" name="office_postcode" id="office_postcode" value="<?php echo $applicant['officePostcode']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="phoneNumber">No. Telefon Bimbit:</label>
                      <input type="text" name="phoneNumber" id="phoneNumber" value="<?php echo $applicant['applicantPhoneNumber']; ?>" readonly>
                  </div>
                  <div class="form-group">
                      <label for="bank">Nama Bank/Cawangan</label>
                        <select id="bank" name="bank" class="form-select">
                            <option value="" disabled selected>Pilih Bank</option>
                            <option value="Malayan Banking Berhad (Maybank)">Malayan Banking Berhad (Maybank)</option>
                            <option value="CIMB Group Holdings">CIMB Group Holdings</option>
                            <option value="Public Bank Berhad">Public Bank Berhad</option>
                            <option value="RHB Bank">RHB Bank</option>
                            <option value="Hong Leong Bank">Hong Leong Bank</option>
                            <option value="AmBank">AmBank</option>
                            <option value="UOB Malaysia">UOB Malaysia</option>
                            <option value="Bank Rakyat">Bank Rakyat</option>
                            <option value="OCBC Bank Malaysia">OCBC Bank Malaysia</option>
                            <option value="HSBC Bank Malaysia">HSBC Bank Malaysia</option>
                            <option value="Bank Islam Malaysia">Bank Islam Malaysia</option>
                            <option value="Affin Bank">Affin Bank</option>
                             <option value="Alliance Bank Malaysia Berhad">Alliance Bank Malaysia Berhad</option>
                            <option value="Standard Chartered Bank Malaysia">Standard Chartered Bank Malaysia</option>
                            <option value="MBSB Bank">MBSB Bank</option>
                            <option value="Bank Rakyat">Bank Rakyat</option>
                            <option value="Bank Simpanan Nasional (BSN)">Bank Simpanan Nasional (BSN)</option>
                            <option value="Bank Muamalat Malaysia Berhad">Bank Muamalat Malaysia Berhad</option>
                            <option value="Agrobank">Agrobank</option>
                            <option value="Co-op Bank Pertama">Co-op Bank Pertama</option>
                        </select>
                  </div>
                  <div class="form-group">
                      <label for="accNum">No. Akaun Bank:</label>
                      <input type="text" name="accNum" id="accNum"required>
                  </div>
                  <br>
                </div>
              </div>

                  <div class="tab-content">
                    <br>
                  <div class="form-section-title">Pengakuan Pemohon</div>
                  <br>
            <p>
              Saya, <span id="applicant_name"><?php echo $applicant['applicantName']; ?></span>, No. K/P: 
              <span id="applicant_ic"><?php echo $applicant['applicantIC']; ?></span>, dengan ini memberi kuasa kepada 
              KOPERASI KAKITANGAN KADA KELANTAN BHD atau waklinya yang sah untuk mendapat apa-apa maklumat yang 
              diperlukan dan juga mendapatkan bayaran balik dari potongan gaji dan emolumen saya sebagaimana amaun 
              yang dipinjamkan. Saya juga bersetuju menerima sebarang keputusan dari KOPERASI ini untuk menolak 
              permohonan tanpa memberi sebarang alasan.
          </p>
          <div class="checkbox-container">
            <input type="checkbox" name="agree_terms" id="agree_terms" required>
            <label for="agree_terms">Saya bersetuju dengan terma dan syarat yang dinyatakan di atas.</label>
        </div>
        </div>

            <div class="tab-content">
                <br>
                <div class="form-section-title">Maklumat Penjamin 1</div>
                    <br>
                    <div class="form-group">
                        <label for="gName1">Nama:</label>
                        <input type="text" name="gName1" id="gName1" onkeyup="filterStaff(1)">
                        <div id="staffList1" class="list-group"></div> <!-- Dropdown List -->
                    </div>

                    <div class="form-group">
                        <label for="gIC1">No K/P:</label>
                        <input type="text" name="gIC1" id="gIC1">
                    </div>

                    <div class="form-group">
                        <label for="gPF1">No PF:</label>
                        <input type="text" name="gPF1" id="gPF1">
                    </div>

                    <div class="form-group">
                        <label for="gID1">No Anggota:</label>
                        <input type="text" name="gID1" id="gID1">
                    </div>

                    <div class="form-group">
                        <label for="gPhoneNumber1">No. Telefon Bimbit:</label>
                        <input type="text" name="gPhoneNumber1" id="gPhoneNumber1">
                    </div>

                    <div class="form-group">
                        <label for="gphoto1">Sila muat naik slip gaji yang telah disahkan (PDF):</label>
                        <input type="file" name="gphoto1" id="gphoto1" accept="application/pdf">
                    </div>
        </div>

            <div class="tab-content">
                <br>
                <div class="form-section-title">Maklumat Penjamin 2</div>
                    <br>
                    <div class="form-group">
                        <label for="gName2">Nama:</label>
                        <input type="text" name="gName2" id="gName2" onkeyup="filterStaff(2)">
                        <div id="staffList2" class="list-group"></div> <!-- Dropdown List -->
                    </div>

                    <div class="form-group">
                        <label for="gIC2">No K/P:</label>
                        <input type="text" name="gIC2" id="gIC2">
                    </div>

                    <div class="form-group">
                        <label for="gPF2">No PF:</label>
                        <input type="text" name="gPF2" id="gPF2">
                    </div>

                    <div class="form-group">
                        <label for="gID2">No Anggota:</label>
                        <input type="text" name="gID2" id="gID2">
                    </div>

                    <div class="form-group">
                        <label for="gPhoneNumber2">No. Telefon Bimbit:</label>
                        <input type="text" name="gPhoneNumber2" id="gPhoneNumber2">
                    </div>

                    <div class="form-group">
                        <label for="gphoto2">Sila muat naik slip gaji yang telah disahkan (PDF):</label>
                        <input type="file" name="gphoto2" id="gphoto2" accept="application/pdf">
                    </div>
                </div>

                <div class="tab-content">
                <br>
                <div class="form-section-title">Pengesahan Majikan</div>
                <br>
                    <div class="form-group">
                        <div class="form-group">
                          <label for="netSalary">Gaji Bersih Bulanan:</label>
                          <input type="text" name="netSalary" id="netSalary"required>
                        </div>
                        <div class="form-group">
                          <label for="basicSalary">Gaji Pokok Bulanan:</label>
                          <input type="text" name="basicSalary" id="basicSalary"required>
                        </div>
                        <div class="form-group">
                            <label for="pengesahanMajikan">Sila muat naik surat pengesahan majikan (PDF):</label>
                            <input type="file" name="pengesahanMajikan" id="pengesahanMajikan" accept="application/pdf">
                        </div>
                    </div>
                </div>


          <div class="button-group">
            <button type="button" class="btn btn-secondary" id="prevButton" onclick="prevTab()" style="display: none;">Kembali</button>
            <button type="button" class="btn btn-primary" id="nextButton" onclick="validateAndNextTab()">SIMPAN</button>
            <button type="submit" class="btn btn-success" id="submitButton" style="display: none;">HANTAR</button>
            <button class="btn btn-danger" type="button" id="clearButton" onclick="clearForm()">SET SEMULA</button>
        </div>

  </form>
</div>
</body>
</html>
