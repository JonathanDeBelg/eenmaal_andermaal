<?php
require_once './head.php';
require_once './db.php';

$emailCheck = ($_SESSION['email']);

$_SESSION['randomVerificationCode'] = rand(100000,900000);
$randomCode = ($_SESSION['randomVerificationCode']);

$emailParameters = array(':mailadres' => "$emailCheck");
$emailUnique = handleQuery("SELECT * FROM ActivatieCode WHERE mailadres = :mailadres",$emailParameters);

$to      = $email;
$subject = 'Account activatie';
$message_body = '
Beste,

Bedankt voor het registreren!

Voer deze code in op de site:
' .$randomNumber.'.';


if(strlen($emailCheck) != 0){
//    if(empty($emailUnique)) {
    if (filter_var($emailCheck, FILTER_VALIDATE_EMAIL)) {
        handleQuery("INSERT INTO ActivatieCode(code, mailadres) VALUES ($randomCode ,:mailadres)",$emailParameters);
        sendCode($randomCode, $emailCheck);
        $_SESSION["step1"] = false;
        $_SESSION["step2"] = true;
        header("location: ./registratieScherm.php");

    } else {
        $_SESSION['error_registatrion'] = 'Geen gelding e-mailadres.';
        header("location: ./registratieScherm.php");
    }
//    } else {
//        $_SESSION['error_registatrion'] = 'Er bestaat al een account met dit emailadres.';
//        header("location: ./registratieScherm.php");
//    }
} else{
    $_SESSION['error_registatrion'] = 'Geen invoer.';
    header("location: ./registratieScherm.php");
}
?>