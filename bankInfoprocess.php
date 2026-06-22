<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include('dbconnect.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and retrieve form data
    $aBankNo = mysqli_real_escape_string($con, $_POST['aBankNo']);
    $bName = mysqli_real_escape_string($con, $_POST['bName']);
    $sNo = $_SESSION['staffNo']; // Assuming staffNo is stored in session

    // Check if file was uploaded
    if (isset($_FILES['bankStatement']) && $_FILES['bankStatement']['error'] === UPLOAD_ERR_OK) {
        $fileTmpName = $_FILES['bankStatement']['tmp_name'];
        $fileContent = addslashes(file_get_contents($fileTmpName)); // Read and escape the file content

        // Insert into database
        $sql = "INSERT INTO bankinfo (accountBankNumber, bankAccountName, staffNo, bankStatement)
                VALUES ('$aBankNo', '$bName', '$sNo', '$fileContent')";

        if (mysqli_query($con, $sql)) {
            $_SESSION['accountBankNumber'] = $aBankNo;
            $_SESSION['bankAccountName'] = $bName;
            header('Location: saving.php'); // Redirect to the next page
            exit();
        } else {
            echo "Error: " . mysqli_error($con); // Handle SQL error
        }
    } else {
        // Handle file upload error
        echo "Error uploading file. Code: " . $_FILES['bankStatement']['error'];
    }
}

// Close the database connection
mysqli_close($con);
?>
