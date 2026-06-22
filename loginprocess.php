<?php
session_start();
//Connect to DB
include('dbconnect.php');


//Retrieve data form
$femail = $_POST['femail'];
$fpwd = $_POST['fpwd'];

//SQL retrieve operation to get user data from DB
$sql = "SELECT * FROM users 
    WHERE email = '$femail'";

//Execute SQL
$result = mysqli_query($con, $sql);

//Retrieve data
$row = mysqli_fetch_array($result);

//count result to check
$count = mysqli_num_rows($result);

//Rule-baseed AI login
if($count == 1) //check user exist
{

  if(password_verify($fpwd, $row['password'])){
    //set session
  $_SESSION['email'] = $femail;

  if($row['userType'] == 1)
  {
    //applicant
    header('Location:applicantHome.php');
  }
  if($row['userType'] == 2)
  {
    //admin
    header('Location:admin.php');
  }
  if($row['userType'] == 3)
  {
    //alk
    header('Location:alk.php');
  }
  }else{
    header('Location: login.php?error=invalid_password');
  }

} else //user not found
{
  //Redirect to login error page (individual project)
  header('Location:login.php?error=user_not_found'); //temporary redirect page
}

//Close connection
mysqli_close($con);


//Confirmation registration successful or fail (your task in individual project)

?>