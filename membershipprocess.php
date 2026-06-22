<?php
//Connect to DB
include('dbconnect.php');

//Retrieve data from form
$mStatus = $_POST['mStatus'];
$mApplyDate = $_POST['mApplyDate'];
$sNo = $_SESSION['staffNo'];
$fMasuk = $_SESSION['feeMasuk'];
$mSyer = $_SESSION['modahSyer'];
$mYuran = $_SESSION['modalYuran'];
$wangDepo = $_SESSION['wangDepositAnggota'];
$sumTabung = $_SESSION['sumbanganTabung'];
$simTetap = $_SESSION['simpananTetap'];
$dll = $_SESSION['lainLain'];

//SQL Insert Operation
$sql = "INSERT INTO membership (membershipStatus, membershipApplyDate, staffNo, feeMasuk, modahSyer, modalYuran, wangDepositAnggota, sumbanganTabung, simpananTetap, lainLain)
        VALUES ('1',CURRENT_TIMESTAMP(),'$sNo','$fMasuk', '$mSyer', '$mYuran', '$wangDepo', '$sumTabung', '$simTetap', '$dll')";

//Execute SQL
mysqli_query($con, $sql);

//Close connection
mysqli_close($con);

//Confirmation registration successful or fail (your task in individual project)

//Redirect user to login page
header('Location: membership.php');

?>