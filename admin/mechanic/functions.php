<?php

/* Deze functie zorgt voor de connectie met de Database */
function ConnectToDatabase(){
	$hostname = "(local)";
	$dbname = "iproject34";
	$dbusername = "sa";
	$dbpw = "12345";

	try { 
		$pdo = new PDO("sqlsrv:Server=$hostname;Database=$dbname;
			ConnectionPooling=0", "$dbusername", "$dbpw");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $pdo;
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
		die();
	}
}

/* Deze functie geeft een array terug met de SELECT RESULTATEN */
function FetchSelectData($sql, $parameters = false){

	global $pdo;
	$qry = $pdo->prepare("$sql");

	if($parameters){

		$qry->execute($parameters);
	}else{$qry->execute();}

	$result = $qry->fetchAll();

	return $result;
}

function FetchAssocSelectData($sql, $parameters = false){
	global $pdo;
	$qry = $pdo->prepare("$sql");	

	if($parameters){

		$qry->execute($parameters);
	}else{$qry->execute();}

	$result = $qry->fetch(PDO::FETCH_ASSOC);
	return $result;
}

/* Deze fucntie voert een sql query uit en geeft een resultaatmelding terug */
function executequery($sql, $parameters = false){

	global $pdo;

	$qry = $pdo->prepare("$sql");

	if($parameters){

		$data =	$qry->execute($parameters);
	}
	else{
		$data =$qry->execute();
	}

	if($data){
		return 'Opdracht volbracht!';
	}else {
		return 'Opdracht kon niet worden volbracht.';
	}
}

/* Deze functie handeld elke database query af |Voor elke functie kan voor elke functie gebruikt|*/
function handlequery($sql, $parameters = false){
	global $pdo;
	$first_word = strtok($sql, " ");
	$type = preg_replace('/\s+/', '', $first_word);

	if($type == 'SELECT'){ $data = FetchSelectData($sql,$parameters);}
	else{$data = executequery($sql,$parameters);}

	return $data;
}

/* Deze functie verzend een mail naar de aangegeven parameters */
function sendMail($to, $subject, $body, $message = "Fout"){
	$emailTo      = $to;
	$subjectEmail = $subject;
	$message_body = $body;


    //mail( $emailTo, $subjectEmail, $message_body ); moet uiteindelijk wel aan!
    echo '<script> alert("'.$body.'")</script>'; //geeft binnen een alert-box de body aan, wat eigenlijk binnen de mail staat

    $_SESSION['message'] = $message;
}

/* Deze functie geeft true of fasle terug, a.d.h.v. de POST informatie */
function checkIfFieldsFilledIn(){ // returnt true als de meegegeven velden gevuld zijn
	if (count(array_filter($_POST)) == count($_POST)) {
		return true;
	} else {
		return false;
	}
}

/* Deze functie checkt of de velden binnen de  meegegeven array
allemaal ingevult zijn */
function fieldsFilledIn($array){
	foreach ($array as $field) {
		if (!empty($_POST[$field])){
			$state = true;
		} else {
			$state = false;
			break;
		}
	}
	return $state;
}

/* Deze functie controleert of de meegegeven email nog niet bestaat
binnen het gebruikerstabel */
function checkEmailUnique($emailCheck){
	$emailControl = handleQuery("SELECT * FROM Gebruiker WHERE mailadres = :mailadres",array(':mailadres' => $emailCheck));

	if(count($emailControl) == 0) {
		$state = true;
	} else {
		$state = false;
	}
	return $state;
}

/* Deze functie checkt of de meegegeven invoer alleen uit karakters bestaat */
function is_Char_Only($Invoer){
	return (bool)(preg_match("/^[a-zA-Z ]*$/", $Invoer)) ;
}

/* Deze functie checkt of er in de meegegeven invoer een getal zit */
function contains_number($string){
	return 1 === preg_match('~[0-9]~', $string);
}

/* Deze functie checkt of er in de meegegeven invoer een hoofdletter zit */
function contains_capital($string){
	return preg_match('/[A-Z]/', $string);
}

/* Deze functie toont tekst en link wordt bepaalt a.d.h.v. of je ingelogt of uitlogt bent */
function showLoginAdminMenu(){
	$htmlLogin = ' ';
	if(isset($_SESSION['gebruikersnaam']) && !empty($_SESSION['gebruikersnaam'])){
		$htmlLogin = '<li class="nav-item">';
		$htmlLogin .= '<a class="nav-link" href="./change-article.php">Voorwerp aanpassen</a>';
		$htmlLogin .= '</li>';
		$htmlLogin .= '<li class="nav-item">';
		$htmlLogin .= '<a class="nav-link" href="./change-user.php">Gebruiker aanpassen</a>';
		$htmlLogin .= '</li>';
		$htmlLogin .= '<li class="nav-item">';
		$htmlLogin .= '<a class="nav-link" href="./logout.php">Uitloggen</a>';
		$htmlLogin .= '</li>';
		$htmlLogin .= '<li class="nav-item">';
		$htmlLogin .= '<a href="admin-pagina.php?cleanDatabase=true" class="btn btn-danger">Verschoon database</a>';
		if (isset($_GET['cleanDatabase'])) { cleanDB();}
		$htmlLogin .= '</li>';
	} else {
		$htmlLogin = '<li class="nav-item">';
		$htmlLogin .= '<a class="nav-link" href="./user.php">Inloggen</a>';
		$htmlLogin .= '</li>';
	}
	return $htmlLogin;
}
/* Deze functie blokkeerd de meegegeven gebruiker uit de database */
function blockUser($gebruiker){
	$deleteParam = array(':gebruikersnaam' => $gebruiker);
	handlequery("UPDATE Gebruiker SET soortGebruiker=3 WHERE gebruikersnaam=:gebruikersnaam",$deleteParam);
}

/* Deze functie verwijderd het meegegeven artikel uit de database */
function deleteArticle($artikel){
	$deleteParam = array(':artikel' => $artikel);
	handlequery("DELETE FROM Voorwerp WHERE voorwerpnummer = :artikel", $deleteParam);
	header("Location: ./change-article.php");
}

function changedFields($fieldsOld, $fieldsNew){
	$state = false;
	foreach ($fieldsOld as $indexOld) {
		foreach ($fieldsNew as $indexNew) {
			if ($indexNew == $indexOld) {
				$state = false;
				break;
			} else {
				$state = true;
			}
		}
	}
	return $state;
}

function cleanDB(){
	executequery("EXEC prc_verschoonDatabase");
	sendEmailClosedAuctions();
	executequery("EXEC prc_veilingSluiten");
	header("Location: ./admin-pagina.php");
}

function updateProductInfo($array){
	$dateLooptijdBegin = $array['looptijdbeginDag'];
	$myDateTimeBegin = DateTime::createFromFormat('Y-m-d', $dateLooptijdBegin);
	$datumLooptijdBegin = $myDateTimeBegin->format('Y-m-d');
	$parametersUpdate = array(':titel' => $array['titel'], ':beschrijving' => $array['beschrijving'], ':startprijs' => (float)$array['startprijs'], ':betalingswijze' => $array['betalingswijze'], ':betalingsinstructie' => $array['betalingsinstructie'], ':plaatsnaam' => $array['plaatsnaam'], ':land' => $array['land'], ':looptijd' => $array['looptijd'], ':looptijdbeginDag' => $datumLooptijdBegin, ':looptijdbeginTijdstip' => $array['looptijdbeginTijdstip'], ':verzendkosten' => (float)$array['verzendkosten'], ':verzendinstructies' => $array['verzendinstructies'], ':voorwerpnummer' => $array['voorwerpnummer']);

	
	handlequery("UPDATE Voorwerp SET
		titel = :titel,
		beschrijving = :beschrijving,
		startprijs = :startprijs,
		betalingswijze = :betalingswijze,
		betalingsinstructie = :betalingsinstructie,
		plaatsnaam = :plaatsnaam,
		land = :land,
		looptijd = :looptijd,
		looptijdbeginDag = :looptijdbeginDag,
		looptijdbeginTijdstip = :looptijdbeginTijdstip,
		verzendkosten = :verzendkosten,
		verzendinstructies = :verzendinstructies
		WHERE voorwerpnummer = :voorwerpnummer",
		$parametersUpdate);
	header("Location: ./change-article.php?&voorwerpInfo=".$parametersUpdate[':voorwerpnummer']);
}

function updateBit($array){
	$dateBitDate = $array['Datum'];
	$myDateTimeBegin = DateTime::createFromFormat('Y-m-d', $dateBitDate);
	$datumBoddag = $myDateTimeBegin->format('Y-m-d');
	$changeBitParam = array(':nummer' => $array['voorwerpnummer'],
		':bedrag' => (float)$array['bodbedrag'],
		':dag' => $datumBoddag,
		':tijdstip' => $array['Tijd'],
		':bedragOud' => $_GET['bodBedragOud']);
	$changeBitCheckParam = array(':nummer' => $array['voorwerpnummer'],
		':gebruikersnaam' => $array['gebruikersnaam'],
		':bedrag' => (float)$array['bodbedrag'],
		':dag' => $datumBoddag,
		':tijdstip' => $array['Tijd']);
	$changeBitQuery = "UPDATE Bod SET bodbedrag = :bedrag, bodDag = :dag, bodTijdstip = :tijdstip WHERE bodbedrag = :bedragOud AND voorwerpnummer = :nummer";
	handlequery($changeBitQuery, $changeBitParam);
	$queryChangedBitCheck = handlequery("SELECT * FROM Bod WHERE voorwerpnummer = :nummer AND gebruikersnaam = :gebruikersnaam AND bodbedrag = :bedrag AND bodDag = :dag AND bodTijdstip = :tijdstip", $changeBitCheckParam);
	header("Location: ./change-article.php?&voorwerpInfo=".$changeBitParam[':nummer']);
}

function returnSRCProduct($voorwernummer){
	$param = array(':nummer' => $voorwernummer);
	$query = FetchAssocSelectData("SELECT TOP 1 filenaam FROM bestand WHERE voorwerpnummer = :nummer", $param);
	$return = '.'.$query['filenaam'];
	return $return;
}


function sortProducts(){
	$sort = "desc";
	if (isset($_GET['filter-time-asc'])){
		$sort = "asc";
	}
	$return = "SELECT V.voorwerpnummer, V.verkoper, V.plaatsnaam, V.titel, V.startprijs, R.rubriekOpLaagsteNiveau, v.loopTijd FROM VoorwerpInRubriek R right outer join Voorwerp V 								        
		on V.voorwerpnummer = R.voorwerpnummer
		left outer join Rubriek Ru
		on R.rubriekOpLaagsteNiveau = Ru.rubrieknummer
		WHERE v.voorwerpnummer like :voorwerpnummer or V.verkoper like :verkoper or V.plaatsnaam like :plaats or V.titel like :titel or v.startprijs like :prijs
		or Ru.rubrieknaam like :categorie
		order by v.looptijd " . $sort;
	return $return;
}

function UpdateInfoUser($get, $gebruikersnaam,$gebruiker){
	$telefoonnummerPara = array(':telefoonnummer' => $get['telefoonnummer'] , ':gebruikersnaam' => $gebruikersnaam);
	$birthdate = $get['geboortedag'];
	$myDateTime = DateTime::createFromFormat('Y-m-d', $birthdate);
	$geboortedag = $myDateTime->format('Y-m-d');

	$infoParameters = array(':gebruikersnaam' => $gebruikersnaam ,
		':voornaam' => $get['voornaam'],
		':achternaam' => $get['achternaam'] ,
		':adresregel1' => $get['adresregel1'] ,
		':adresregel2' => $get['adresregel2'] ,
		':postcode' => $get['postcode'],
		':plaatsnaam' => $get['plaatsnaam'] ,
		':land' =>  $get['land'] ,
		':geboortedag' => $geboortedag ,
		':mailadres' => $get['mailadres']);
	handlequery("UPDATE Gebruiker
		SET voornaam = :voornaam ,
		achternaam = :achternaam,
		adresregel1 = :adresregel1 ,
		adresregel2 = :adresregel2 ,
		postcode = :postcode,
		plaatsnaam = :plaatsnaam ,
		land = :land,
		geboortedag = :geboortedag,
		mailadres = :mailadres
		WHERE gebruikersnaam = :gebruikersnaam",
		$infoParameters);
	if($gebruiker['telefoonnummer'] == null){
		handlequery("INSERT INTO Gebruikerstelefoon (telefoonnummer,gebruikersnaam) VALUES (:telefoonnummer,:gebruikersnaam )",$telefoonnummerPara);
		
	} else {
		handlequery("UPDATE Gebruikerstelefoon
		SET telefoonnummer = :telefoonnummer
		WHERE gebruikersnaam = :gebruikersnaam" , $telefoonnummerPara);
	}
	echo '<script>window.location.replace("./change-user.php?gebruikersnaam='.$gebruikersnaam.'")</script>';
}

function getUserData($user){
	$return = handlequery("SELECT * FROM voorwerp v join gebruiker G on v.".$user." = g.gebruikersnaam WHERE looptijdEindeDag <= CONVERT(date, GETDATE()) 
								 		  AND V.veilingGesloten = 0 AND looptijdEindeTijdstip < CONVERT(time, GETDATE());");
	return $return;
}

function sendEmailClosedAuctions(){
	foreach(getUserData("verkoper") as $gesloten){
		$email = $gesloten['mailadres'];
		$subject = 'veiling:' . $gesloten['titel'] . 'is gesloten';
		if(!empty ($gesloten['koper'])) {
			$message = 'De veiling' .' '. $gesloten['titel'] .' '. 'is Gesloten. de veiling is gewonnen door:' . $gesloten['koper'] . '.Bekijk de veiling op:http://iproject34.icasites.nl/productpage.php?product=' . $gesloten['voorwerpnummer'];
		} else {
			$message = 'De veiling' .' '. $gesloten['titel'] .' '. 'is Gesloten. Uw veiling heeft jammer genoeg geen koper. Bekijk de veiling op: http://iproject34.icasites.nl/productpage.php?product=' . $gesloten['voorwerpnummer'];
		}
		sendMail($email,$subject,$message);
	}
			
	foreach(getUserData("koper") as $gesloten){
		if (!empty($gesloten['koper'])){
			$subject = 'Gefeliciteerd u heeft veiling:' . $gesloten['titel'] . ' ' . 'Gewonnen!';
			$message = 'De veiling' .' '. $gesloten['titel'] .' '. 'is Gesloten. Gefeliciteerd! U bent de winnaar van deze Veiling. Bekijk de veiling op: http://iproject34.icasites.nl/productpage.php?product=' . $gesloten['voorwerpnummer'];
			$email = $gesloten['mailadres'];
			sendMail($email,$subject,$message);
		}
	}
}


?>
