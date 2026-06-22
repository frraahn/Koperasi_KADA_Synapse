<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

include('dbconnect.php');

// Retrieve data from form
$sNo = isset($_POST['sNo']) ? $_POST['sNo'] : '';
$aName = isset($_POST['aName']) ? $_POST['aName'] : '';
$aIC = isset($_POST['aIC']) ? $_POST['aIC'] : '';
$aPF = isset($_POST['aPF']) ? $_POST['aPF'] : '';
$aStreet = isset($_POST['aStreet']) ? $_POST['aStreet'] : '';
$aPostcode = isset($_POST['aPostcode']) ? $_POST['aPostcode'] : '';
$aCity = isset($_POST['aCity']) ? $_POST['aCity'] : '';
$aState = isset($_POST['aState']) ? $_POST['aState'] : '';
$aGender = isset($_POST['aGender']) ? $_POST['aGender'] : '';
$aReligion = isset($_POST['aReligion']) ? $_POST['aReligion'] : '';
$aRace = isset($_POST['aRace']) ? $_POST['aRace'] : '';
$aPosition = isset($_POST['aPosition']) ? $_POST['aPosition'] : '';
$oStreet = isset($_POST['oStreet']) ? $_POST['oStreet'] : '';
$oPostcode = isset($_POST['oPostcode']) ? $_POST['oPostcode'] : '';
$oCity = isset($_POST['oCity']) ? $_POST['oCity'] : '';
$aPhoneNo = isset($_POST['aPhoneNo']) ? $_POST['aPhoneNo'] : '';
$aDOB = isset($_POST['aDOB']) ? $_POST['aDOB'] : '';
$aAge = isset($_POST['aAge']) ? $_POST['aAge'] : '';
$maritalStat = isset($_POST['maritalStat']) ? $_POST['maritalStat'] : '';
$aGrade = isset($_POST['aGrade']) ? $_POST['aGrade'] : '';
$oFax = isset($_POST['oFax']) ? $_POST['oFax'] : '';
$aPhoneHome = isset($_POST['aPhoneHome']) ? $_POST['aPhoneHome'] : '';
$aSalary = isset($_POST['aSalary']) ? $_POST['aSalary'] : '';
$femail = $_SESSION['email'];

$uploadDir = "uploads/"; // Set your upload directory

// Ensure the upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$photoPath = ""; // Initialize variable
$salaryStatementPath = ""; // Initialize to prevent undefined variable issues

// Handle photo upload (PDF file)
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $photo = $_FILES['photo'];

    // Validate file type
    $fileType = mime_content_type($photo['tmp_name']);
    $fileExtension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));

    if ($fileType !== 'application/pdf' || $fileExtension !== 'pdf') {
        die("Photo must be in PDF format.");
    }

    // Generate unique file path
    $photoPath = $uploadDir . 'photo_' . uniqid() . '.pdf';

    // Move uploaded file
    if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
        die("Error uploading photo. Please try again.");
    }
}

// Handle salary statement upload
if (isset($_FILES['salaryStatement']) && $_FILES['salaryStatement']['error'] == 0) {
    $salaryStatement = $_FILES['salaryStatement'];

    // Validate file type
    $fileType = mime_content_type($salaryStatement['tmp_name']);
    $fileExtension = strtolower(pathinfo($salaryStatement['name'], PATHINFO_EXTENSION));

    if ($fileType !== 'application/pdf' || $fileExtension !== 'pdf') {
        die("Salary statement must be in PDF format.");
    }

    // Generate unique file path
    $salaryStatementPath = $uploadDir . 'salary_' . uniqid() . '.pdf';

    // Move uploaded file
    if (!move_uploaded_file($salaryStatement['tmp_name'], $salaryStatementPath)) {
        die("Error uploading salary statement. Please try again.");
    }
}

// Insert into database
$sql = "INSERT INTO applicant (staffNo, applicantName, applicantIC, applicantPF, applicantStreet, applicantPostcode, applicantCity, applicantState, applicantGender, applicantReligion, applicantRace, applicantPosition, officeStreet, officePostcode, officeCity, applicantPhoneNumber, applicantDOB, applicantAge, maritalStatus, applicantGrade, officeFax, applicantPhoneHome, applicantSalary, email, photo, salaryStatement) 
VALUES ('$sNo', '$aName', '$aIC', '$aPF', '$aStreet', '$aPostcode', '$aCity', '$aState', '$aGender', '$aReligion', '$aRace', '$aPosition', '$oStreet', '$oPostcode', '$oCity', '$aPhoneNo', '$aDOB', '$aAge', '$maritalStat', '$aGrade', '$oFax', '$aPhoneHome', '$aSalary', '$femail', '$photoPath', '$salaryStatementPath')";

if (mysqli_query($con, $sql)) {
    $_SESSION['staffNo'] = $sNo;
    $_SESSION['applicantName'] = $aName;
    $_SESSION['applicantIC'] = $aIC;
    $_SESSION['applicantDOB'] = $aDOB;
    $_SESSION['applicantAge'] = $aAge;
    $_SESSION['applicantStreet'] = $aStreet;
    $_SESSION['applicantPostcode'] = $aPostcode;
    $_SESSION['applicantGender'] = $aGender;
    $_SESSION['applicantReligion'] = $aReligion;
    $_SESSION['applicantRace'] = $aRace;
    $_SESSION['applicantPF'] = $aPF;
    $_SESSION['applicantPosition'] = $aPosition;
    $_SESSION['officeStreet'] = $oStreet;
    $_SESSION['officePostcode'] = $oPostcode;
    $_SESSION['applicantPhoneNumber'] = $aPhoneNo;
    
    // Redirect after successful insertion
    header('Location: family.php');
    exit;
} else {
    echo "Error: " . mysqli_error($con);
}
?>
