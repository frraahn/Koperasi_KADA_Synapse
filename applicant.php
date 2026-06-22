<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headerapplicant.php';
include 'dbconnect.php';

// Retrieve the applicant's data from the database
$femail = $_SESSION['email'];
$query = "SELECT * FROM applicant WHERE email = '$femail'";
$result = mysqli_query($con, $query);
$applicantData = mysqli_fetch_assoc($result);
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
             .nav-tabs {
            border-bottom: none;
            justify-content: center;
            display: flex;
            margin-bottom: 30px;
            margin-top: 20px; /* Add gap from the header */
        }
        .nav-tabs .nav-item {
            margin: 0 5px;
        }
        .nav-tabs .nav-link {
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
        .nav-tabs .nav-link.active {
            background: var(--secondary);
            color: #fff;
        }
    </style>
</head>
<body>
   <div class="page-header"><h2 class="page-title">PERMOHONAN ANGGOTA</h2></div>

    <!-- Navigation Tabs -->
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="applicant.php">Maklumat Pemohon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="family.php">Maklumat Keluarga</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="bankInfo.php">Maklumat Bank</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="saving.php">Maklumat Simpanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pengakuanAnggota.php">Pengakuan Pemohon</a>
            </li>
        </ul>
    </div>

    <div class="form-container">
        <form method="POST" action="applicantprocess.php" enctype="multipart/form-data">
            <!-- Applicant Information -->
            <div class="form-section-title">MAKLUMAT PEMOHON</div>
            <div class="form-group">
        <label for="photo">Sila muat naik gambar dalam format PDF</label>
        <input type="file" id="photo" name="photo" class="form-control" accept="application/pdf" required>
    </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Nama</label>
                        <input type="text" id="aName" name="aName" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="ic_number">No. KP</label>
                        <input type="text" id="aIC" name="aIC" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="DOB">Tarikh Lahir</label>
                        <input type="date" id="aDOB" name="aDOB" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="age">Umur</label>
                        <input type="text" id="aAge" name="aAge" class="form-control" required>
                    </div>                    
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="marital_status">Taraf Perkahwinan</label>
                        <select id="maritalStat" name="maritalStat" class="form-select">
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="Bujang">Bujang</option>
                            <option value="Berkahwin">Berkahwin</option>
                            <option value="Bercerai">Bercerai</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="gender">Jantina</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="male" name="aGender" value="LELAKI" class="form-check-input" required>
                            <label for="male" class="form-check-label">Lelaki</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="female" name="aGender" value="PEREMPUAN" class="form-check-input">
                            <label for="female" class="form-check-label">Perempuan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="religion">Agama</label>
                        <select id="aReligion" name="aReligion" class="form-select">
                            <option value="" disabled selected>Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristian">Kristian</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Lain-Lain">Lain-Lain</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="race">Bangsa</label>
                        <select id="aRace" name="aRace" class="form-select">
                            <option value="" disabled selected>Pilih Bangsa</option>
                            <option value="Melayu">Melayu</option>
                            <option value="Cina">Cina</option>
                            <option value="India">India</option>
                            <option value="Lain-Lain">Lain-Lain</option>
                        </select>
                    </div>
                </div>

        <div class="row">
                <div class="col-md-6 form-group">
                    <label for="aStreet">Alamat Rumah</label>
                    <input type="text" id="aStreet" name="aStreet" class="form-control mb-2" placeholder="Baris">
                </div>
                <div class="col-md-3 form-group">
                    <label for="aCity">Bandar</label>
                    <input type="text" id="aCity" name="aCity" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="aPostcode">Poskod</label>
                    <input type="text" id="aPostcode" name="aPostcode" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="aState">Negeri</label>
                    <select id="aState" name="aState" class="form-select" required>
                        <option value="" disabled selected>Pilih Negeri</option>
                        <option value="Johor">Johor</option>
                        <option value="Kedah">Kedah</option>
                        <option value="Kelantan">Kelantan</option>
                        <option value="Melaka">Melaka</option>
                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                        <option value="Pahang">Pahang</option>
                        <option value="Perak">Perak</option>
                        <option value="Perlis">Perlis</option>
                        <option value="Pulau Pinang">Pulau Pinang</option>
                        <option value="Sabah">Sabah</option>
                        <option value="Sarawak">Sarawak</option>
                        <option value="Selangor">Selangor</option>
                        <option value="Terengganu">Terengganu</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Kuala Lumpur</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Labuan</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Putrajaya</option>
                    </select>
                </div>
            </div>

                    <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="staff_number">No. Anggota</label>
                        <input type="text" id="staff_number" name="sNo" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="no_pf">No. PF</label>
                        <input type="text" id="no_pf" name="aPF" class="form-control" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="position">Jawatan</label>
                        <input type="text" id="position" name="aPosition" class="form-control" required>
                    </div>
                     <div class="col-md-6 form-group">
                        <label for="position">Gred</label>
                        <input type="text" id="position" name="aGrade" class="form-control" required>
                    </div>
                </div>


   <div class="row">
                <div class="col-md-6 form-group">
                    <label for="oStreet">Alamat Pejabat</label>
                    <input type="text" id="oStreet" name="oStreet" class="form-control mb-2" placeholder="Baris">
                </div>
                <div class="col-md-3 form-group">
                    <label for="oCity">Bandar</label>
                    <input type="text" id="oCity" name="oCity" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label for="oPostcode">Poskod</label>
                    <input type="text" id="oPostcode" name="oPostcode" class="form-control">
                </div>
                <div class="col-md-6 form-group">
                    <label for="oState">Negeri</label>
                    <select id="oState" name="oState" class="form-select" required>
                        <option value="" disabled selected>Pilih Negeri</option>
                        <option value="Johor">Johor</option>
                        <option value="Kedah">Kedah</option>
                        <option value="Kelantan">Kelantan</option>
                        <option value="Melaka">Melaka</option>
                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                        <option value="Pahang">Pahang</option>
                        <option value="Perak">Perak</option>
                        <option value="Perlis">Perlis</option>
                        <option value="Pulau Pinang">Pulau Pinang</option>
                        <option value="Sabah">Sabah</option>
                        <option value="Sarawak">Sarawak</option>
                        <option value="Selangor">Selangor</option>
                        <option value="Terengganu">Terengganu</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Kuala Lumpur</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Labuan</option>
                        <option value="Wilayah Persekutuan">Wilayah Persekutuan Putrajaya</option>
                    </select>
                </div>
            </div>

            <div class="row">
                    <div class="col-md-6 form-group">
                <label for="office_phone">No. Tel/Fax Pejabat:</label>
                <input type="text" id="office_phone" name="oFax"  class="form-control">
                   </div>

                     <div class="col-md-6 form-group">
                <label for="mobile_number">No. Tel Bimbit:</label>
                <input type="text" id="mobile_number" name="aPhoneNo"  class="form-control">
                     </div>

                     <div class="col-md-6 form-group">
                <label for="home_phone">No. Tel Rumah:</label>
                <input type="text" id="home_phone" name="aPhoneHome"  class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="monthly_income">Gaji Bulanan (RM)</label>
                        <input type="number" id="monthly_income" name="aSalary" class="form-control">
                    </div>
                   <div class="form-group">
        <label for="salaryStatement">Sila muat naik penyata gaji dalam format PDF</label>
        <input type="file" id="salaryStatement" name="salaryStatement" class="form-control" accept="application/pdf" required>
    </div>
                </div>
                  
         
                <button type="reset" class="btn btn-danger">SET SEMULA</button>
                <button type="submit" class="btn btn-primary">SIMPAN</button>
            </div>
            </form>
        </div>

    </body>
    </html>

