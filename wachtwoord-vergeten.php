<?php require_once 'header.php'; 

if (isset($_POST['check'])) {
	$emailAdres = $_POST['mailadres'];
    $_SESSION['mailAdres'] = $emailAdres;
}
$emailParameters = array(':mailadres' => "$emailAdres");
$gebruiker = handlequery("SELECT * FROM Gebruiker JOIN Vraag ON Gebruiker.vraag = Vraag.vraagnummer WHERE mailadres = :mailadres 
   AND Gebruiker.vraag = Vraag.vraagnummer", $emailParameters);

// $_SESSION['mailadres'] = $gebruiker['mailadres']; 


$subject = 'Wachtwoord wijzigen';
$message = 'U heeft aangegeven dat u het wachtwoord wilt wijzigen. Uw nieuwe wachtwoord is = ';

$randomPassword = createRandomPassword(); 
$messageCode = $message . $randomPassword;
?>

<main>
 <section class="forgotpass">
  <div class="container">
    <div class="inputMailadres col-md-6">
     <!--  de form om te checken of de e-mail adres klopt -->
    <form id="formMail" method="POST" class="form-group">
        <label for="E-mailadres">Voer hier uw E-mailadres in: </label>
        <input type="text" name="mailadres" class="form-control" id="mailadres" placeholder="E-mailadres" value=<?= '"' .$_SESSION['mailAdres'].'"' ?> >
        <input class="cta-orange btn" type="submit" name="check" value="Controlleer">
 </div>
        <?php 
        if( isset($_POST['mailadres']) && count($gebruiker) == 1 ) { 
            ?>
             <div class="inputAntwoord col-md-6">
          <!--     de form om de vraag te tonen met een antwoordveld -->
            <form id="formAntwoord" method="POST" class="form-group">
                <label for="testvoorvraag">  <?= $gebruiker[0]['vraag']?> </label>
                <input type="text" name="antwoord" class="form-control" id="testAntwoordvakje" placeholder="Antwoord">
                <input class="cta-orange btn" type="submit" name="verzenden" value="Verzenden">
            </form>
          </div>
            <?php 
        }  
        // als op de knop verzenden word geklikt dan word de onderstaande query uitgevoerd
        if (isset ($_POST['verzenden'])){
            $antwoordtekst = $_POST['antwoord'];
            $emailAdres = $_POST['mailadres'];
            $answerParameters = array(':antwoord' => "$antwoordtekst", ':mailadres' => "$emailAdres" );
            $antwoord = handlequery("SELECT antwoordtekst FROM Gebruiker WHERE antwoordtekst = :antwoord AND mailadres = :mailadres", $answerParameters);
        // hier word gecheckt of het antwoord klopt
            if (count($antwoord) == 1) {
            $randomPassword = trim($randomPassword);
                sendMail($emailAdres,$subject,$messageCode);
                $updatePasswordParameters = array(':mailadres' => $emailAdres,':wachtwoord' => $randomPassword);
                handlequery("UPDATE Gebruiker SET wachtwoord = :wachtwoord WHERE mailadres = :mailAdres",$updatePasswordParameters);

            } else {
                echo 'Er is iets fout gegaan bij het wijzigen van uw wachtwoord!';
            }
        }
        ?>
    </form>
  </div>
 </section>
</main>
<?php require_once 'footer.php'; ?>