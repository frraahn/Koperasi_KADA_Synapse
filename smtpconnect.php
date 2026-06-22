<?php
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'auninawwarah563@gmail.com'; // Replace with your email
    $mail->Password = 'xjizbbhutvbugfjs';   // Replace with your app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email details
    $mail->setFrom('admin@kada.com', 'Koperasi Kakitangan KADA Kelantan');
    $mail->addAddress($email, $name);
?>