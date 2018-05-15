<?php 
require_once 'header.php'; 

if($_SESSION['gebruikersnaam'] == "admin") { 

	if (isset($_GET['zoekenVoorwerp'])){

		$voorwerpNummer = $_GET['voorwerp'];
		$parametersVoorwerp = array(':voorwerpnummer' => $voorwerpNummer);
		$voorwerpen = handlequery("SELECT * FROM Voorwerp WHERE voorwerpnummer = :voorwerpnummer",$parametersVoorwerp);
		echo print_r($voorwerpen);
		$artikelResultaten = '<table class="table"><tr><th scope="col">Voorwerp</th></tr><tr>';
		foreach($voorwerpen as $voorwerp){

			$getpath = "$_SERVER[QUERY_STRING]";
			$artikelResultaten .= "<tr>" . "<td>" . "<a href='?".$getpath."&voorwerpForm=" . $voorwerp['titel'] . "'>" .  $voorwerp['titel'] ."</a>". "</td>" . "</tr>"; 

		
		} 
		$artikelResultaten .= '</tr></table>';
	} else if (isset($_GET['zoekenGebruiker'])) {
		$gebruikersnaam = $_GET['gebruiker'];
		$parametersGebruiker = array(':gebruiker' => "%". $gebruikersnaam ."%");
		$gebruikers = handlequery("SELECT * FROM Gebruiker WHERE gebruikersnaam like :gebruiker",$parametersGebruiker);
		echo print_r($gebruikers);
		$gebruikerResultaten = '<table class="table"><tr><th scope="col">Gebruikersnaam</th></tr><tr>';
		foreach($gebruikers as $gebruiker){
			$gebruikerResultaten .= "<tr>" . "<td>" . "<a href='?gebruikersnaamForm=" . $gebruiker['gebruikersnaam'] . "'>" .  $gebruiker['gebruikersnaam'] ."</a>". "</td>" . "</tr>";
		} 
		$gebruikerResultaten .= '</tr></table>';
	}

	?>

	<main>
		<div class="container">
			<div class="row">
				<div class="artikelnummer">
					<!-- form om te zoeken op artikelnummer -->
					<div class="col-lg-4">
						<form class="form-group" method="GET" action=""> 
							<input type="number" name="voorwerp" placeholder="Voorwerpnummer"> <br>
							<input class="cta-orange" name="zoekenVoorwerp" type="submit" value="Zoeken">
						</form>
					</div>
				</div>

				<div class="gebruiker">
					<!-- for om te zoeken op gebruiker -->
					<div class="col-lg-4">
						<form class="form-group" method="GET" action=""> 
							<input type="text" name="gebruiker" placeholder="Gebruikersnaam"> <br>
							<input class="cta-orange" name="zoekenGebruiker" type="submit" value="Zoeken">
						</form>
					</div>
				</div>
				<?php if(isset($artikelResultaten)) { 
					echo $artikelResultaten; 
				} else if(isset($gebruikerResultaten)) { 
					echo $gebruikerResultaten; 
				}?>
			</div>

		<?php }
		if (isset($_GET['voorwerpForm'])) {?>


			<!-- Voor nu op deze manier gedaan, kan met een foreach. Later nog naar kijken maar aangezien de tijdslimiet zo gedaan om geen tijd te verspillen. -->

			<form class="form-group" method="GET" action=""> 
				<label>Titel</label>
				<input type="text" name="titel" value="<?=$voorwerpen[0]['titel']?>"> <br>
				<label>Beschrijving</label>
				<input type="text" name="beschrijving" value="<?=$voorwerpen[0]['beschrijving']?>"><br>
				<label>Startprijs</label>
				<input type="number" name="startprijs" value="<?=$voorwerpen[0]['startprijs']?>"><br>
				<label>Betalingswijze</label>
				<input type="number" name="betalingswijze" value="<?=$voorwerpen[0]['betalingswijze']?>"><br>
				<label>Betalingsinstructie</label>
				<input type="text" name="betalingsinstructie" value="<?=$voorwerpen[0]['betalingsinstructie']?>"><br>
				<label>Plaatsnaam</label>
				<input type="text" name="plaatsnaam" value="<?=$voorwerpen[0]['plaatsnaam']?>"><br>
				<label>Land</label>
				<input type="text" name="land" value="<?=$voorwerpen[0]['land']?>"><br>
				<label>Looptijd</label>
				<input type="text" name="looptijd" value="<?=$voorwerpen[0]['looptijd']?>"><br>
				<label>looptijdbeginDag</label>
				<input type="text" name="looptijdbeginDag" value="<?=$voorwerpen[0]['looptijdbeginDag']?>"><br>
				<label>looptijdbeginTijdstip</label>
				<input type="text" name="looptijdbeginTijdstip" value="<?=$voorwerpen[0]['looptijdbeginTijdstip']?>"><br>
				<label>verzendkosten</label>
				<input type="text" name="verzendkosten" value="<?=$voorwerpen[0]['verzendkosten']?>"><br>
				<label>verzendinstructies</label>
				<input type="text" name="verzendinstructies" value="<?=$voorwerpen[0]['verzendinstructies']?>"><br>
				<label>verkoper</label>
				<input type="text" name="verkoper" value="<?=$voorwerpen[0]['verkoper']?>"><br>
				<label>koper</label>
				<input type="text" name="koper" value="<?=$voorwerpen[0]['koper']?>"><br>
				<label>looptijdeindeDag</label>
				<input type="text" name="looptijdeindeDag" value="<?=$voorwerpen[0]['looptijdeindeDag']?>"><br>
				<label>looptijdeindeTijdstip</label>
				<input type="text" name="looptijdeindeTijdstip" value="<?=$voorwerpen[0]['looptijdeindeTijdstip']?>"><br>
				<label>veilingGesloten</label>
				<input type="text" name="veilingGesloten" value="<?=$voorwerpen[0]['veilingGesloten']?>"><br>
				<label>verkoopPrijs</label>
				<input type="number" name="verkoopPrijs" value="<?=$voorwerpen[0]['verkoopPrijs']?>"><br>
				<input class="cta-orange" type="submit" name="verzenden" value="verzenden">

			</form>


	
		<?php }
		// nog niet helemaal werkend. dit is de query om de tabel te updaten.
		if (isset($_GET['verzenden'])) {
			$parametersUpdate = array(
				':titel' => $_GET['titel'], 
				':beschrijving' => $_GET['beschrijving'],
				':startprijs' => $_GET['startprijs'],
				':betalingswijze' => $_GET['betalingswijze'],
				':betalingsinstructie' => $_GET['betalingsinstructie'],
				':plaatsnaam' => $_GET['plaatsnaam'],
				':land' => $_GET['land'],
				':looptijd' => $_GET['looptijd'],
				':looptijdbeginDag' => $_GET['looptijdbeginDag'],
				':looptijdbeginTijdstip' => $_GET['looptijdbeginTijdstip'],
				':verzendkosten' => $_GET['verzendkosten'],
				':verzendinstructies' => $_GET['verzendinstructies'],
				':verkoper' => $_GET['verkoper'],
				':koper' => $_GET['koper'],
				':looptijdeindeDag' => $_GET['looptijdeindeDag'],
				':looptijdeindeTijdstip' => $_GET['looptijdeindeTijdstip'],
				':veilingGesloten' => $_GET['veilingGesloten'],
				':verkoopPrijs' => $_GET['verkoopPrijs']);

			handlequery("UPDATE Voorwerp SET
				titel = :titel,
				beschrijving = :beschrijving,
				startprijs = CONVERT(NUMERIC(8,2), :startprijs),
				betalingswijze = CONVERT(INT, :betalingswijze),
				betalingsinstructie = :betalingsinstructie,
				plaatsnaam = :plaatsnaam,
				land = :land,
				looptijd = :looptijd,
				looptijdbeginDag = :looptijdbeginDag,
				looptijdbeginTijdstip = :looptijdbeginTijdstip,
				verzendkosten = CONVERT(NUMERIC(5,2), :verzendkosten),
				verzendinstructies = :verzendinstructies,
				verkoper = :verkoper,
				koper = :koper,
				looptijdeindeDag = :looptijdeindeDag,
				looptijdeindeTijdstip = :looptijdeindeTijdstip,
				veilingGesloten = :veilingGesloten,
				verkoopPrijs = CONVERT(NUMERIC(8,2), :verkoopPrijs)",
				$parametersUpdate);
			}?>
		</div>
	</main>
	
<?php require_once './footer.php'; ?>

