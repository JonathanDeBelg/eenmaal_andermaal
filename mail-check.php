<?php
require_once './head.php';
require_once './db.php';

$emailCheck = ($_SESSION['emailadresControle1']);

$_SESSION['randomVerificationCode'] = rand(100000,900000);
$randomCode = ($_SESSION['randomVerificationCode']);

$emailParameters = array(':mailadres' => "$emailCheck");
$emailUnique = handleQuery("SELECT * FROM ActivatieCode WHERE mailadres = :mailadres",$emailParameters);

if(strlen($emailCheck) != 0){
    echo '<script type="text/javascript">alert("invoer minder dan nul");</script>';
    if(empty($emailUnique)) {
        echo '<script type="text/javascript">alert("bestaat al");</script>';
        if (filter_var($emailCheck, FILTER_VALIDATE_EMAIL)) {
            handleQuery("INSERT INTO ActivatieCode(code, mailadres) VALUES ($randomCode ,:mailadres)",$emailParameters);
            sendCode($randomCode, $emailCheck);
        } else {
            $_SESSION['error_registatrion'] = 'Geen gelding e-mailadres.';
            header("location: ./registratieScherm.php");
        }
    } else {
        $_SESSION['error_registatrion'] = 'Er bestaat al een account met dit emailadres.';
        header("location: ./registratieScherm.php");
    }
} else{
    $_SESSION['error_registatrion'] = 'Geen invoer.';
    header("location: ./registratieScherm.php");
}
?>