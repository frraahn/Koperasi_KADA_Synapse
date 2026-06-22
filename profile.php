<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include 'headerapplicant.php';
include 'dbconnect.php';

// Assuming the logged-in user's email is stored in the session
$userEmail = $_SESSION['email'];

// Fetch applicant's details from the database
$sql = "SELECT * FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $applicant = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching profile data.";
    exit();
}

// Handle save functionality
if (isset($_GET['save'])) {
    $applicantName = $_GET['applicantName'];
    $applicantIC = $_GET['applicantIC'];
    $applicantReligion = $_GET['applicantReligion'];
    $applicantRace = $_GET['applicantRace'];
    $applicantPosition = $_GET['applicantPosition'];
    $applicantAge = $_GET['applicantAge'];
    $applicantStreet = $_GET['applicantStreet'];
    $applicantPostcode = $_GET['applicantPostcode'];
    $applicantCity = $_GET['applicantCity'];
    $applicantState = $_GET['applicantState'];
    $applicantPhoneNumber = $_GET['applicantPhoneNumber'];

    // Update query
    $updateSql = "UPDATE applicant SET 
                    applicantReligion = '$applicantReligion',
                    applicantRace = '$applicantRace',
                    applicantPosition = '$applicantPosition',
                    applicantAge = '$applicantAge',  
                    applicantStreet = '$applicantStreet', 
                    applicantPostcode = '$applicantPostcode', 
                    applicantCity = '$applicantCity', 
                    applicantState = '$applicantState', 
                    applicantPhoneNumber = '$applicantPhoneNumber' 
                  WHERE email = '$userEmail'";

    if (mysqli_query($con, $updateSql)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    exit();
        exit();
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-image: url('img/img.png'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8); 
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
            max-width: 600px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
            font-weight: bold;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Profile</h2>
        <form method="GET" id="profileForm">
            <div class="mb-3">
                <label for="staffNo" class="form-label">No. Pekerja</label>
                <input type="text" class="form-control" id="staffNo" name="staffNo" value="<?= ($applicant['staffNo']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="applicantName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="applicantName" name="applicantName" value="<?= ($applicant['applicantName']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantIC" class="form-label">No. Kad Pengenalan</label>
                <input type="text" class="form-control" id="applicantIC" name="applicantIC" value="<?= ($applicant['applicantIC']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="applicantDOB" class="form-label">Tarikh Lahir</label>
                <input type="text" class="form-control" id="applicantDOB" name="applicantDOB" value="<?= ($applicant['applicantDOB']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="applicantAge" class="form-label">Umur</label>
                <input type="text" class="form-control" id="applicantAge" name="applicantAge" value="<?= ($applicant['applicantAge']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantPF" class="form-label">No. PF</label>
                <input type="text" class="form-control" id="applicantPF" name="applicantPF" value="<?= ($applicant['applicantPF']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="applicantStreet" class="form-label">Street</label>
                <input type="text" class="form-control" id="applicantStreet" name="applicantStreet" value="<?= ($applicant['applicantStreet']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantPostcode" class="form-label">Postcode</label>
                <input type="number" class="form-control" id="applicantPostcode" name="applicantPostcode" value="<?= ($applicant['applicantPostcode']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantCity" class="form-label">City</label>
                <input type="text" class="form-control" id="applicantCity" name="applicantCity" value="<?= ($applicant['applicantCity']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantState" class="form-label">State</label>
                <input type="text" class="form-control" id="applicantState" name="applicantState" value="<?= ($applicant['applicantState']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantGender" class="form-label">Jantina</label>
                <input type="text" class="form-control" id="applicantGender" name="applicantGender" value="<?= ($applicant['applicantGender']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="applicantReligion" class="form-label">Agama</label>
                <input type="text" class="form-control" id="applicantReligion" name="applicantReligion" value="<?= ($applicant['applicantReligion']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantRace" class="form-label">Bangsa</label>
                <input type="text" class="form-control" id="applicantRace" name="applicantRace" value="<?= ($applicant['applicantRace']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantPosition" class="form-label">Jawatan</label>
                <input type="text" class="form-control" id="applicantPosition" name="applicantPosition" value="<?= ($applicant['applicantPosition']); ?>" disabled required>
            </div>
            <div class="mb-3">
                <label for="applicantPhoneNumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="applicantPhoneNumber" name="applicantPhoneNumber" value="<?= ($applicant['applicantPhoneNumber']); ?>" disabled required>
            </div>
            <button type="button" class="btn btn-primary" id="editButton">Update Profile</button>
            <button type="submit" class="btn btn-success d-none" id="saveButton" name="save">Save Changes</button>
        </form>
    </div>

    <script>
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const inputs = document.querySelectorAll('#profileForm input');

        editButton.addEventListener('click', () => {
            inputs.forEach(input => input.removeAttribute('disabled'));
            editButton.classList.add('d-none');
            saveButton.classList.remove('d-none');
        });
    </script>
</body>
</html>
