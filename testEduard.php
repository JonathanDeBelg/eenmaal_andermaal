<?php require_once 'header.php'; 

$geslotenVeilingen = handlequery("SELECT * FROM voorwerp v join gebruiker G on v.verkoper = g.gebruikersnaam WHERE looptijdEindeDag <= CONVERT(date, GETDATE()) 
								 AND V.veilingGesloten = 0 AND looptijdEindeTijdstip < CONVERT(time, GETDATE());");
// hier worden de mailtjes gestuurd voor de veilingen die nog op 0 staan en afgelopen zijn
if (isset($_POST['sendmail'])){

		foreach($geslotenVeilingen as $gesloten){
		
		$geslotenParameters = array(':voorwerpnummer' => $gesloten['voorwerpnummer']);
		$subject = 'veiling:' . $gesloten['titel'] . 'is gesloten';
		$message = 'De veiling' .' '. $gesloten['titel'] .' '. 'is Gesloten. De winnaar van deze veiling is:' .' '. $gesloten['koper'];
		$email = $gesloten['mailadres'];
		sendMail($email,$subject,$message);
			
		}
	}
?>

<main class="geslotenVeilingen">
	<section>

		<form class="form-group" method="POST">
			<input type="submit" class="cta-orange btn" name="sendmail" value="Check veilingen">
		</form>
	</section>
</main>