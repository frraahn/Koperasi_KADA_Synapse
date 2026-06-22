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
    <style>
      body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 75%;
            margin: auto;
        }
        .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }
        .tab.active {
            background: #007bff;
            color: #fff;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .header {
            background-color: red;
            height: 50px;
        }

        .sub-header {
            background-color: blue;
            height: 50px;
            text-align: center; 
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
        }
      label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 5px 15px; /* Smaller padding for smaller buttons */
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3; /* Change to a darker shade or a desired hover color */
            color: #fff; /* Change text color on hover */
        }
        #submitButton {
            background-color: green;
            color: white;
        }
        #clearButton {
            background-color: red;
            color: white;
        }
        #clearButton {
        background-color: red;
        color: white;
        }

        #clearButton:hover {
            background-color: darkred;
        }

        #prevButton, #nextButton {
            background-color: #007bff;
            color: white;
        }

        #prevButton:hover, #nextButton:hover {
            background-color: #0056b3;
        }

        .button-group {
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Adds consistent gaps between buttons */
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

        document.addEventListener('DOMContentLoaded', () => {
            showTab(0);
        });
    </script>
</head>
<body>
<div class="container">
        <br>
        <div class="tabs">
            <div class="tab active" onclick="showTab(0)">Maklumat Penjamin 1</div>
            <div class="tab" onclick="showTab(1)">Maklumat Penjamin 2</div>
        </div>

<form action="guarantorprocess.php" method="post">

              <!-- Guarantor Details Section -->
              <div class="tab-content active">
                <br>
                <h2>Maklumat Penjamin 1</h2><br>

                  <div class="form-group">
                    <label for="gName1">Nama:</label>
                    <input type="text" name="gName1" id="gName1">
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
                </div>


                  <div class="tab-content">
                <br>
                <h2>Maklumat Penjamin 2</h2><br>

                  <div class="form-group">
                    <label for="gName2">Nama:</label>
                    <input type="text" name="gName2" id="gName2">
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
                </div>

                <div class="button-group">
                <button type="button" id="prevButton" onclick="prevTab()" style="display: none;">Back</button>
                <button type="button" id="nextButton" onclick="nextTab()">Next</button>
                <button type="submit" id="submitButton" style="display: none;">Submit</button>
                <button type="button" id="clearButton" onclick="clearForm()">Clear Form</button>
                </div>

              </form>

</div>
</body>
</html>

