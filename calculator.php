<?php 
include 'crssession.php';
if(!session_id())
{
  session_start();
}

include 'headerapplicant.php';
include 'dbconnect.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.css">
    <style>
        :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #f567a1;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
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
            background-color: var(--primary);
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
        .container {
            max-width: 750px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .form-header {
                background-color: #007bff;
                color: white;
                text-align: center;
                padding: 10px 0;
                font-size: 20px;
                font-weight: bold;
                border-radius: 8px 8px 0 0;
                width: 100%;
                margin: 0;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            font-size: 14px;
            display: block;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .btn-primary {
            background-color: var(--light-blue);
            border-color: var(--light-blue);
        }
        .btn-success {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .modal-close {
            margin-top: 15px;
            padding: 10px;
            background-color: #0044cc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-close:hover {
            background-color: #0033aa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        table caption {
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }
        .footer-note {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page-header"><h2 class="page-title">KALKULATOR ANGGARAN BAYARAN BALIK</h2></div>
    <div class="form-container">
    <div class="container">
        <form id="loanForm" onsubmit="calculateLoan(event)">
            <div class="form-group">
                <label for="amount">Amaun Pembiayaan (RM)</label>
                <input type="number" id="amount" name="amount" placeholder="e.g. 1000" required>
            </div>
            <div class="form-group">
                <label for="duration">Tempoh Pembiayaan (Bulan)</label>
                <input type="number" id="duration" name="duration" placeholder="e.g. 1" required>
            </div>
            <div class="form-group">
                <label for="interest_rate">Kadar Keuntungan (%)</label>
                <input type="number" id="interest_rate" name="interest_rate" value="4.2" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Kira</button>
            <br><br>
            <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Jadual Pembayaran Balik Pembiayaan Skim Al-Bai'Ubithaman Aajil/Bai Al-Inah
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <body>
    <table>
        <thead>
            <tr>
                <th rowspan="1">Kadar Keuntungan</th>
                <th colspan="6">4.2%</th>
            </tr>
            <tr>
                
                <th rowspan="2">Jumlah Pembiayaan</th>
                <th colspan="6">Ansuran Bulanan</th>
            </tr>
            <tr>
                <th>1 Tahun (12 Bulan)</th>
                <th>2 Tahun (24 Bulan)</th>
                <th>3 Tahun (36 Bulan)</th>
                <th>4 Tahun (48 Bulan)</th>
                <th>5 Tahun (60 Bulan)</th>
                <th>6 Tahun (72 Bulan)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1,000.00</td>
                <td>86.83</td>
                <td>45.17</td>
                <td>31.28</td>
                <td>24.33</td>
                <td>20.17</td>
                <td>17.39</td>
            </tr>
            <tr>
                <td>2,000.00</td>
                <td>173.67</td>
                <td>90.33</td>
                <td>62.56</td>
                <td>48.67</td>
                <td>40.33</td>
                <td>34.78</td>
            </tr>
            <tr>
                <td>3,000.00</td>
                <td>260.50</td>
                <td>135.50</td>
                <td>93.83</td>
                <td>73.00</td>
                <td>60.50</td>
                <td>52.17</td>
            </tr>
            <tr>
                <td>4,000.00</td>
                <td>347.33</td>
                <td>180.67</td>
                <td>125.11</td>
                <td>97.33</td>
                <td>80.67</td>
                <td>69.56</td>
            </tr>
            <tr>
                <td>5,000.00</td>
                <td>434.17</td>
                <td>225.83</td>
                <td>156.39</td>
                <td>121.67</td>
                <td>100.83</td>
                <td>86.95</td>
            </tr>
            <tr>
                <td>6,000.00</td>
                <td>521.00</td>
                <td>271.00</td>
                <td>187.67</td>
                <td>146.00</td>
                <td>121.00</td>
                <td>104.34</td>
            </tr>
            <tr>
                <td>7,000.00</td>
                <td>607.83</td>
                <td>316.17</td>
                <td>218.94</td>
                <td>170.33</td>
                <td>141.17</td>
                <td>121.72</td>
            </tr>
            <tr>
                <td>8,000.00</td>
                <td>694.67</td>
                <td>361.33</td>
                <td>250.22</td>
                <td>194.67</td>
                <td>161.33</td>
                <td>139.11</td>
            </tr>
            <tr>
                <td>9,000.00</td>
                <td>781.50</td>
                <td>406.50</td>
                <td>281.50</td>
                <td>219.00</td>
                <td>181.50</td>
                <td>156.50</td>
            </tr>
            <tr>
                <td>10,000.00</td>
                <td>868.33</td>
                <td>451.67</td>
                <td>312.78</td>
                <td>243.33</td>
                <td>201.67</td>
                <td>173.89</td>
            </tr>
            <tr>
                <td>11,000.00</td>
                <td>955.17</td>
                <td>496.83</td>
                <td>344.06</td>
                <td>267.67</td>
                <td>221.83</td>
                <td>191.28</td>
            </tr>
            <tr>
                <td>12,000.00</td>
                <td>1,042.00</td>
                <td>542.00</td>
                <td>375.33</td>
                <td>292.00</td>
                <td>242.00</td>
                <td>208.67</td>
            </tr>
            <tr>
                <td>13,000.00</td>
                <td>1,128.83</td>
                <td>587.17</td>
                <td>406.61</td>
                <td>316.33</td>
                <td>262.17</td>
                <td>226.06</td>
            </tr>
            <tr>
                <td>14,000.00</td>
                <td>1,215.67</td>
                <td>632.33</td>
                <td>437.89</td>
                <td>340.67</td>
                <td>282.33</td>
                <td>243.44</td>
            </tr>
            <tr>
                <td>15,000.00</td>
                <td>1,302.50</td>
                <td>677.50</td>
                <td>469.17</td>
                <td>365.00</td>
                <td>302.50</td>
                <td>260.83</td>
            </tr>
            <tr>
                <td>16,000.00</td>
                <td>1,389.33</td>
                <td>722.67</td>
                <td>500.44</td>
                <td>389.33</td>
                <td>322.67</td>
                <td>278.22</td>
            </tr>
            <tr>
                <td>17,000.00</td>
                <td>1,476.17</td>
                <td>767.83</td>
                <td>531.72</td>
                <td>413.67</td>
                <td>342.83</td>
                <td>295.61</td>
            </tr>
            <tr>
                <td>18,000.00</td>
                <td>1,563.00</td>
                <td>813.00</td>
                <td>563.00</td>
                <td>438.00</td>
                <td>363.00</td>
                <td>313.00</td>
            </tr>
            <tr>
                <td>19,000.00</td>
                <td>1,649.83</td>
                <td>858.17</td>
                <td>594.28</td>
                <td>462.33</td>
                <td>383.17</td>
                <td>275.06</td>
            </tr>
            <tr>
                <td>20,000.00</td>
                <td>1,736.67</td>
                <td>903.33</td>
                <td>625.56</td>
                <td>486.67</td>
                <td>403.33</td>
                <td>347.78</td>
            </tr>
        </tbody>
    </table>
    <div class="footer-note">
        <p>Dikemaskini 10 Mei 2023</p>
        <p>Mesyuarat Lembaga Bil 3/2023 7 Mei 2023</p>
    </div>
              </div>
            </div>
          </div>
      </div>
      <br><br>
        </form>

        <div class="modal" id="resultModal">
            <div class="modal-content">
                <p id="resultText"></p>
                <button class="modal-close" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function calculateLoan(event) {
            // Prevent the form from reloading the page
            event.preventDefault();

            // Retrieve the input values
            const loanAmount = parseFloat(document.getElementById('amount').value);
            const loanDuration = parseInt(document.getElementById('duration').value);
            const interestRate = parseFloat(document.getElementById('interest_rate').value);

            // Calculate monthly repayment
            const monthlyInterestRate = interestRate / 100 / loanDuration;
            let monthlyPayment;

            if (monthlyInterestRate > 0) {
                monthlyPayment = (loanAmount * monthlyInterestRate) / 
                    (1 - Math.pow(1 + monthlyInterestRate, -loanDuration));
            } else {
                monthlyPayment = loanAmount / loanDuration; // No interest case
            }

            // Round the result to 2 decimal places
            monthlyPayment = monthlyPayment.toFixed(2);

            // Display the result in the modal
            document.getElementById('resultText').textContent = `Jumlah anggaran bulanan: RM ${monthlyPayment}`;
            document.getElementById('resultModal').style.display = 'flex';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('resultModal').style.display = 'none';
        }
    </script>
</body>
</html>
