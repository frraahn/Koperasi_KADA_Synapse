<?php 
include('crssession.php');
if(!session_id()) {
    session_start();
}

// Connect to DB
include('dbconnect.php');

// Retrieve the logged-in user's email
$userEmail = $_SESSION['email'];

// Retrieve loanID from the loan table based on the email
$sqlLoanID = "SELECT loan.loanID 
              FROM loan 
              INNER JOIN applicant ON loan.staffNo = applicant.staffNo
              WHERE applicant.email = '$userEmail' 
              ORDER BY loan.loanID DESC LIMIT 1"; // Assuming the most recent loan application
$resultLoanID = mysqli_query($con, $sqlLoanID);

if ($resultLoanID && mysqli_num_rows($resultLoanID) > 0) {
    $row = mysqli_fetch_assoc($resultLoanID);
    $loanID = $row['loanID'];

    // Retrieve data for the first guarantor from the form
    $gIC1 = $_POST['gIC1'];
    $gName1 = $_POST['gName1'];
    $gID1 = $_POST['gID1'];
    $gPF1 = $_POST['gPF1'];
    $gPhoneNumber1 = $_POST['gPhoneNumber1'];

    // SQL Insert for the first guarantor
    $sqlInsertGuarantor1 = "INSERT INTO guarantorInfo 
                            (guarantorIC, loanID, guarantorName, guarantorID, guarantorPF, guarantorPhoneNumber)
                            VALUES 
                            ('$gIC1', '$loanID', '$gName1', '$gID1', '$gPF1', '$gPhoneNumber1')";

    // Retrieve data for the second guarantor from the form
    $gIC2 = $_POST['gIC2'];
    $gName2 = $_POST['gName2'];
    $gID2 = $_POST['gID2'];
    $gPF2 = $_POST['gPF2'];
    $gPhoneNumber2 = $_POST['gPhoneNumber2'];

    // SQL Insert for the second guarantor
    $sqlInsertGuarantor2 = "INSERT INTO guarantorInfo 
                            (guarantorIC, loanID, guarantorName, guarantorID, guarantorPF, guarantorPhoneNumber)
                            VALUES 
                            ('$gIC2', '$loanID', '$gName2', '$gID2', '$gPF2', '$gPhoneNumber2')";

    // Execute the insert operations
    if (mysqli_query($con, $sqlInsertGuarantor1) && mysqli_query($con, $sqlInsertGuarantor2)) {

    	$loanID = $_SESSION['loanID'];
        // Redirect to status page on success
        header('Location: statuspinjaman.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    echo "Error: Unable to retrieve loanID for the logged-in user.";
}

// Close connection
mysqli_close($con);

?>
