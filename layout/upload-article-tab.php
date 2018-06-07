<div class="tab-pane fade float-left col-lg-9 " id="content-upload-article" role="tabpanel" aria-labelledby="upload-article">
	<?php require_once 'header.php';

if (isset($_SESSION['gebruikersnaam'])) {
	$categories = handlequery("SELECT rubrieknummer, rubrieknaam FROM laagsteRubrieken ORDER BY rubrieknaam");
  $username = $_SESSION['gebruikersnaam'];

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $filledin = array(
    'titel',
    'beschrijving',
    'looptijd',
    'startprijs',
    'betalingswijze'
  );

  if (isset($_POST['sellitem']) && fieldsFilledIn($filledin) && $_FILES["fileToUpload"]["size"] != 0) {
    if (empty($_POST['betalingsinstructie'])) {
      $_POST['betalingsinstructie'] = NULL;
    } if (empty($_POST['verzendkosten'])) {
      $_POST['verzendkosten'] = 0.00;
    } if (empty($_POST['verzendinstructies'])) {
      $_POST['verzendinstructies'] = NULL;
    }

    $voorwerpnummerUpload = handlequery("SELECT dbo.fnc_voorwerpnummer()");

    $plaats = handlequery("SELECT plaatsnaam, land FROM Gebruiker WHERE gebruikersnaam = '".$username."'");
    $uploadItem = array(
      ':voorwerpnummer' => $voorwerpnummerUpload[0][0],
      ':titel' => $_POST['titel'],
      ':beschrijving' => $_POST['beschrijving'],
      ':looptijd' => $_POST['looptijd'],
      ':startprijs' => (float)$_POST['startprijs'],
      ':betalingswijze' => $_POST['betalingswijze'],
      ':betalingsinstructie' => $_POST['betalingsinstructie'],
      ':verzendkosten' => (float)$_POST['verzendkosten'],
      ':verzendinstructies' => $_POST['verzendinstructies'],
      ':plaatsnaam' => $plaats[0]['plaatsnaam'],
      ':land' => $plaats[0]['land'],
      ':verkoper' => $_SESSION['gebruikersnaam']
    );
		$uploadRubriek = array(
			':voorwerpnummer' => $voorwerpnummerUpload[0][0],
			':rubriek' => $_POST['categorie']
		);

    handlequery('INSERT INTO Voorwerp (voorwerpnummer, titel, beschrijving, startprijs, betalingswijze, betalingsinstructie, plaatsnaam,
      land, looptijd, looptijdBeginDag, looptijdBeginTijdstip, verzendkosten, verzendinstructies, verkoper, veilingGesloten)
      VALUES (:voorwerpnummer, :titel, :beschrijving, :startprijs, :betalingswijze, :betalingsinstructie, :plaatsnaam, :land, :looptijd,
      GETDATE(), GETDATE(), :verzendkosten, :verzendinstructies, :verkoper, 0)', $uploadItem);
		handlequery('INSERT INTO VoorwerpInRubriek VALUES (:voorwerpnummer, :rubriek)', $uploadRubriek);

      $target_dir = "./uploads/" . $username . '/' . $voorwerpnummerUpload[0][0]. '/';
      if (!file_exists($target_dir)){
        mkdir('uploads/'. $username . '/' . $voorwerpnummerUpload[0][0] , 02202, true);
      }

      $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      if(checkIfImage($_POST["sellitem"]) && checkAllowedFileTypes($imageFileType) && checkSizeFile(1000000) && checkExistingFile($target_file)) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          $uploadFoto = array(':voorwerpnummer' => $voorwerpnummerUpload[0][0] , ':filenaam' => $target_file);
          handlequery("INSERT INTO Bestand (filenaam, voorwerpnummer) VALUES (:filenaam, :voorwerpnummer)", $uploadFoto);
        }
        else {
          echo "Sorry, Er is iets fout gegaan tijdens het uploaden van uw bestand.";
          die();
        }
      }
    redirectJS("productpage.php?product=" . $voorwerpnummerUpload[0][0]); // verwijzen naar nieuw
    die();
      } else {
        echo '<main><section><div class="container">
        Niet alle velden zijn ingevuld <br><br>
        <form action="upload-article.php" method="get">
        <input type="submit" value="Opnieuw proberen"
        name="Submit" id="frm1_submit"/></div>
        </form>
        </section></main>';
        die();
      }
    } ?>
<style>
.tt-query,
.tt-hint {
    width: 396px;
    height: 30px;
    padding: 8px 12px;
    font-size: 24px;
    line-height: 30px;
    border: 2px solid #ccc;
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    outline: none;
}

.tt-query {
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
    color: #999
}

.tt-dropdown-menu {
    width: 422px;
    margin-top: 12px;
    padding: 8px 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 8px;
    -moz-border-radius: 8px;
    border-radius: 8px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
    -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
    box-shadow: 0 5px 10px rgba(0,0,0,.2);
}

.tt-suggestion {
    padding: 3px 20px;
    font-size: 18px;
    line-height: 24px;
}

.tt-suggestion.tt-is-under-cursor {
    color: #fff;
    background-color: #0097cf;

}
</style>


        <div class="container">
            <form method="POST" enctype="multipart/form-data">
              <legend>Voorwerp veilen!</legend>
              <p>Velden met een * zijn verplicht</p>
              <div class="form-row">
                <div class="form-group col-md-8">
                  <label class="control-label" for="titel">Titel voor veiling*</label>
                  <input id="titel" name="titel" type="text" class="form-control input-md" required>
                </div>
                <div class="form-group col-md-8">
                  <label class="control-label" for="beschrijving">Beschrijving*</label>
                  <textarea class="form-control" id="beschrijving" name="beschrijving" required></textarea>
                </div>
								<div class="form-group col-md-8">
                  <label class="control-label" for="beschrijving">Categorie*</label>
                  <select id="categorie" name="categorie" type="select" class="form-control input-md" required>
                    <?php foreach ($categories as $key => $categorie) {
                      echo '<option value=' .$categorie['rubrieknummer'].'>'.$categorie['rubrieknaam'].'</option>';
                    } ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="control-label" for="looptijd">Looptijd in dagen*</label>
                  <select id="looptijd" name="looptijd" class="form-control">
                    <option value="1">1</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="7">7</option>
                    <option value="10">10</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label class="control-label" for="startprijs">Startprijs in euro&apos;s*</label>
                  <input id="startprijs" name="startprijs" type="number" step="0.01" min="0" class="form-control input-md" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="control-label" for="betalingswijze">Betalingswijze*</label>
                  <select id="betalingswijze" name="betalingswijze" class="form-control">
                    <option value="Credit Card">Credit Card</option>
                    <option value="iDeal">iDeal</option>
                    <option value="PayPal">PayPal</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label class="control-label" for="betalingsinstructie">Betalingsinstructie</label>
                  <input id="betalingsinstructie" name="betalingsinstructie" type="text" placeholder="Optioneel" class="form-control input-md">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="control-label" for="verzendkosten">Verzendkosten</label>
                  <input id="verzendkosten" name="verzendkosten" type="number" min="0" step="0.01" placeholder="Optioneel" class="form-control input-md">
                  </div>
                <div class="form-group col-md-4">
                  <label class="control-label" for="verzendinstructies">Verzendinstructies</label>
                  <input id="verzendinstructies" name="verzendinstructies" type="text" placeholder="Optioneel" class="form-control input-md">
                </div>
              </div>
              <p>Upload foto*</p>
              <div class="form-row">
                <div class="custom-file col-md-4" id="customFile">
                  <input id="fileToUpload" name="fileToUpload" class="custom-file-input" type="file" required>
                  <label class="custom-file-label" for="fileToUpload">Bestand kiezen</label>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="verzenden"></label>
                <button id="sellitem" name="sellitem" type="submit" class="cta-orange btn">Verkopen!</button>
              </div>
            </form>
        </div>


    <script>
        $('.custom-file-input').on('change',function(){
            var fileName = $(this).val();
            if (fileName) {
                var startIndex = (fileName.indexOf('\\') >= 0 ? fileName.lastIndexOf('\\') : fileName.lastIndexOf('/'));
                var filename = fileName.substring(startIndex);
                if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                    filename = filename.substring(1);
                }
                $(this).next('.custom-file-label').addClass("selected").html(filename);
            }
        })
    </script>
 <?php  } else {
    redirectJS("index.php"); // redirect naar index wanneer je niet ingelogd bent
  }
?>

</div>
