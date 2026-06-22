<?php
include('crssession.php');
if (!session_id()) {
    session_start();
}

// Connect to DB
include('dbconnect.php');


// Retrieve data from form
$sNo = $_SESSION['staffNo'];
$fMasuk = $_POST['fMasuk'];
$mSyer = $_POST['mSyer'];
$mYuran = $_POST['mYuran'];
$wangDepo = $_POST['wangDepo'];
$sumTabung = $_POST['sumTabung'];
$simTetap = $_POST['simTetap'];
$dll = $_POST['dll'];
 // Get staffNo from session

///SQL Insert Operation
$sql = "INSERT INTO savingtype (staffNo, feeMasuk, modahSyer, modalYuran, wangDepositAnggota, sumbanganTabung, simpananTetap, lainLain)
        VALUES ('$sNo', '$fMasuk', '$mSyer', '$mYuran', '$wangDepo', '$sumTabung', '$simTetap', '$dll')";


    if (mysqli_query($con, $sql)) {
         $_SESSION['feeMasuk'] = $fMasuk;
         $_SESSION['modahSyer'] = $mSyer;
         $_SESSION['modalYuran'] = $mYuran;
         $_SESSION['wangDepositAnggota'] = $wangDepo;
         $_SESSION['sumbanganTabung'] = $sumTabung;
         $_SESSION['simpananTetap'] = $simTetap;
         $_SESSION['lainLain'] = $dll;
        header('Location: pengakuanAnggota.php');
        exit;
    } else {
        // Handle SQL insertion error
        echo "Error: " . mysqli_error($con);
    }


// Close connection
mysqli_close($con);
?>
