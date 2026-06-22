<?php include 'headermain.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <style>
    :root {
            --primary: #6686f6;
            --secondary: #318166;
            --accent: #f567a1;
            --dark-blue: #1255b8;
            --light-blue: #258de4;
            --purple: #5954bb;
      }
    /* Background and body styling */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url('img/img.png'); /* Replace with the correct path to your background image */
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-position: center;
    }

/*    background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('img/img.png');*/
    
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

        .btn-success {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-danger {
            background-color: var(--accent);
            border-color: var(--accent);
        }

  </style>
</head>
<body>

<div class="container mt-5">
    <br><br><h5>Sila isi maklumat anda</h5><br>

    <form method="POST" action="loginprocess.php">
        <fieldset>
            <!-- Email Input -->
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Sila isi alamat emel anda</label>
                <input type="email" name="femail" class="form-control" id="exampleInputEmail1" placeholder="Sila isi alamat emel" required>
                <small id="emailHelp" class="form-text text-muted">Kami tidak akan mengongsikan e-mel anda dengan orang lain.</small>
            </div>
            <!-- Password Input -->
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Sila isi kata laluan</label>
                <input type="password" name="fpwd" class="form-control" id="exampleInputPassword1" placeholder="Sila isi kata laluan" autocomplete="off" required>
            </div>
            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">Log Masuk</button>
                <button type="reset" class="btn btn-warning">Kosongkan Borang</button>
            </div>
        </fieldset>
    </form>
</div>
</body>
</html>