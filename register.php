<?php include 'headermain.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <style>
    /* Background and body styling */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url('img/img.png'); /* Replace with the correct path to your background image */
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
    }

    /* Styling the container */
    .container {
      background-color: rgba(255, 255, 255, 0.8); /* White with slight transparency */
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for the container */
      max-width: 600px;
      margin: 50px auto;
    }

    /* Heading styles */
    h5 {
      color: black; /* Green heading color */
      text-align: center;
      font-weight: bold;
    }

    /* Label styles */
    label {
      color: #333; /* Dark label color */
      font-weight: bold;
    }

    .btn {
            border-radius: 25px;
            padding: 0.25rem 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.2rem;
            font-size: 0.875rem;
        }

    /* Button primary (Submit) styles */
    .btn-primary {
      background-color: #4CAF50;
      border-color: #4CAF50;
    }

    .btn-primary:hover {
      background-color: #45a049;
      border-color: #45a049;
    }

    /* Button warning (Reset) styles */
    .btn-warning {
      background-color: #f0ad4e;
      border-color: #f0ad4e;
    }

    .btn-warning:hover {
      background-color: #ec971f;
      border-color: #ec971f;
    }

    /* Add spacing to buttons */
    button {
      margin-right: 10px;
    }
  </style>
</head>
<body>
<div class="container">

<br><br><h5>Sila isi maklumat anda</h5>
<form method="POST" action="registerprocess.php" onsubmit="return validatePasswords()">
  <fieldset>
    <div>
      <label for="exampleInputEmail1" class="form-label mt-4">Nama pertama</label>
      <input type="text" name="fName" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Sila isi nama pertama anda" required>
    </div>

     <div>
      <label for="exampleInputEmail1" class="form-label mt-4">Nama terakhir</label>
      <input type="text" name="lName" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Sila isi nama terakhir anda" required>
    </div>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Alamat e-mel</label>
      <input type="text" name="femail" class="form-control" id="exampleInputPassword1" placeholder="Sila isi alamat e-mel anda" autocomplete="off" required>
    </div>

    <div>
      <label for="exampleInputPassword1" class="form-label mt-4">Kata laluan</label>
      <input type="password" name="fpwd" class="form-control" id="exampleInputPassword1" placeholder="Cipta kata laluan anda" autocomplete="off" required>
    </div>

    <div>
      <label for="confirmPassword" class="form-label mt-4"> Sahkan Kata laluan</label>
      <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Sahkan kata laluan anda" autocomplete="off" required>
      <?php if(isset($_GET['error']) && $_GET['error'] == 'password_mismatch'): ?>
        <div class="text-danger mt-2">Kata laluan tidak sepadan. Sila masukkan semula kata laluan.</div>
      <?php endif; ?>
    </div>

<div>
  <label for="exampleSelect1" class="form-label mt-4">Pilih posisi</label>
  <select class="form-select" name="uType" id="exampleSelect1" required>
    <option value="1">Pemohon</option>
    <option value="2">Kerani</option>
    <option value="3">ALK</option>
  </select>
</div><br>

   <div class="d-flex justify-content-between mt-4">
    <button type="submit" class="btn btn-primary">Hantar</button>
    <button type="reset" class="btn btn-warning">Kosongkan Borang</button>
  </div>
  </fieldset>
</form>

</div>
</body>
</html>
<script>
  function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
      alert('Passwords do not match! Please try again.');
      return false;
    }
    return true;
  }
</script>

