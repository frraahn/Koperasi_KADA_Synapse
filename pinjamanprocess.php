<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}
// Connect to DB
include('dbconnect.php');

// Retrieve data from form
$amount = $_POST['amount'];
$duration = $_POST['duration'];
$mpayment = $_POST['mpayment'];
$bank = $_POST['bank'];
$accNum = $_POST['accNum'];
$type = $_POST['type'];
$netSalary = $_POST['netSalary'];
$basicSalary = $_POST['basicSalary'];
$other = isset($_POST['other']) ? $_POST['other'] : NULL;

$otherLoan = ($type == 7 && isset($_POST['other'])) ? $_POST['other'] : NULL;
$adminStaffNo = isset($adminStaffNo) ? $adminStaffNo : 'NULL';
$loanReviewDate = isset($loanReviewDate) ? $loanReviewDate : 'NULL';
$loanApproveDate = isset($loanApproveDate) ? $loanApproveDate : 'NULL';
$pengesahanMajikan = $_FILES['pengesahanMajikan'];

        // Handle Guarantor 1 file upload
if (isset($_FILES['pengesahanMajikan']) && $_FILES['pengesahanMajikan']['error'] === UPLOAD_ERR_OK) {
    $fileTmpName3 = $_FILES['pengesahanMajikan']['tmp_name'];
    $fileContent3 = addslashes(file_get_contents($fileTmpName3)); }


// Retrieve user email from session
$userEmail = $_SESSION['email'];

// Retrieve staffNo from the applicant table
$sqlStaffNo = "SELECT staffNo FROM applicant WHERE email = '$userEmail'";
$result = mysqli_query($con, $sqlStaffNo);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $staffNo = $row['staffNo'];

    // SQL Insert operation for the loan
    $sqlInsertLoan = "INSERT INTO loan 
                      (loanAmount, loanDuration, loanStatus, monthlyPayment, bankName, accountBankNumber, loanType, otherLoan, staffNo, adminStaffNo, loanApplyDate, loanReviewDate, loanApproveDate, applicantNetSalary, applicantBasicSalary, pengesahanMajikan)
                      VALUES ('$amount', '$duration', '1', '$mpayment', '$bank', '$accNum', '$type', '$other', '$staffNo', $adminStaffNo, CURRENT_TIMESTAMP(), $loanReviewDate, $loanApproveDate, '$netSalary', '$basicSalary', '$fileContent3')";

    if (mysqli_query($con, $sqlInsertLoan)) {
        $loanID = mysqli_insert_id($con);

        // Guarantor 1 data
        $gIC1 = $_POST['gIC1'];
        $gName1 = $_POST['gName1'];
        $gID1 = $_POST['gID1'];
        $gPF1 = $_POST['gPF1'];
        $gPhoneNumber1 = $_POST['gPhoneNumber1'];
        $gPhoto1 = $_FILES['gphoto1'];

        // Handle Guarantor 1 file upload
        if (isset($_FILES['gphoto1']) && $_FILES['gphoto1']['error'] === UPLOAD_ERR_OK) {
    $fileTmpName1 = $_FILES['gphoto1']['tmp_name'];
    $fileContent1 = addslashes(file_get_contents($fileTmpName1));
    
    $sqlInsertGuarantor1 = "INSERT INTO guarantorInfo 
                            (guarantorIC, loanID, guarantorName, guarantorID, guarantorPF, guarantorPhoneNumber, photo)
                            VALUES ('$gIC1', '$loanID', '$gName1', '$gID1', '$gPF1', '$gPhoneNumber1', '$fileContent1')";

    if (!mysqli_query($con, $sqlInsertGuarantor1)) {
        echo "Error inserting Guarantor 1: " . mysqli_error($con);
        exit();
    }
} else {
    echo "Error uploading file for Guarantor 1. ";
    echo "File Upload Error Code: " . ($_FILES['gphoto1']['error'] ?? 'No file uploaded');
    exit();
}
        // Guarantor 2 data
        $gIC2 = $_POST['gIC2'];
        $gName2 = $_POST['gName2'];
        $gID2 = $_POST['gID2'];
        $gPF2 = $_POST['gPF2'];
        $gPhoneNumber2 = $_POST['gPhoneNumber2'];
        $gPhoto2 = $_FILES['gphoto2'];

        // Handle Guarantor 2 file upload
        if ($gPhoto2['error'] === UPLOAD_ERR_OK) {
            $fileTmpName2 = $gPhoto2['tmp_name'];
            $fileContent2 = addslashes(file_get_contents($fileTmpName2));

            $sqlInsertGuarantor2 = "INSERT INTO guarantorInfo 
                                    (guarantorIC, loanID, guarantorName, guarantorID, guarantorPF, guarantorPhoneNumber, photo)
                                    VALUES ('$gIC2', '$loanID', '$gName2', '$gID2', '$gPF2', '$gPhoneNumber2', '$fileContent2')";

            if (!mysqli_query($con, $sqlInsertGuarantor2)) {
                echo "Error inserting Guarantor 2: " . mysqli_error($con);
                exit();
            }
        } else {
            echo "Error uploading file for Guarantor 2. Error Code: " . $gPhoto2['error'];
            exit();
        }

        // Redirect to status page on success
        header('Location: statuspinjaman.php');
        exit();
    } else {
        echo "Error inserting loan: " . mysqli_error($con);
        exit();
    }
} else {
    echo "Error retrieving staffNo for the logged-in user.";
    exit();
}

// Close connection
mysqli_close($con);
?>
