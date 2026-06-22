<?php
include('crssession.php');
if(!session_id())
{
  session_start();
}
  
include 'headerapplicant.php';
include 'dbconnect.php';?>

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
            <form method="POST" action="savingprocess.php">

   <!-- Fees and Donations Section -->
                <div class="form-section-title">YURAN DAN SUMBANGAN</div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>BIL</th>
                                <th>PERKARA</th>
                                <th>RM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>FEE MASUK</td>
                                <td><input type="number" name="fMasuk" class="form-control"></td>
                            </tr>
                            <tr>
    <td>2</td>
    <td>MODAH SYER*</td>
    <td><input type="number" name="mSyer" class="form-control" min="300" required></td>
</tr>
                            <tr>
                                <td>3</td>
                                <td>MODAL YURAN</td>
                                <td><input type="number" name="mYuran" class="form-control"></td>
                            </tr>
                            <tr>
    <td>4</td>
    <td>WANG DEPOSIT ANGGOTA</td>
    <td><input type="number" name="wangDepo" class="form-control" required></td>
</tr>
                            <tr>
                                <td>5</td>
                                <td>SUMBANGAN TABUNG KEBAJIKAN (AL-ABRAR)</td>
                                <td><input type="number" name="sumTabung" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>SIMPANAN TETAP</td>
                                <td><input type="number" name="simTetap" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>LAIN-LAIN</td>
                                <td><input type="number" name="dll" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-note">
                    *MINIMA MODAL SYER ADALAH SEBANYAK RM 300.00 DAN TIDAK MELEBIHI 1/5 DARIPADA MODAL SYER KOPERASI DAN HENDAKLAH DIJELASKAN DALAM TEMPOH 6 BULAN DARI TARIKH KELULUSAN MENJADI ANGGOTA.
                    <br><br>
                    *JIKA ANDA PERNAH MENAMATKAN KEANGGOTAAN KOPERASI, MODAL YURAN ADALAH SEKURANG-KURANGNYA RM 100.00. <br>
                </div>



          <div class="btn-container">
            <br>
      <!-- Go back to the previous page -->
    <a href="bankinfo.php" class="btn btn-secondary">KEMBALI</a><!-- Reset the form -->
    <button type="reset" class="btn btn-danger">SET SEMULA</button>

    <!-- Save data and proceed to the next page -->
    <button type="submit" class="btn btn-primary">SIMPAN</button>
            </form>
        </div>
</form>

