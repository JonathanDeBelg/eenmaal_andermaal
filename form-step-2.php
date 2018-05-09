<?php
require_once './db.php';


if (isset($_POST['submit-code-registration'])){
	if (checkIfFieldsFilledIn()) {
    	$_SESSION['hashedcode'] = md5($_POST['code']);
        if (validateCode($_POST['code'], $_SESSION['email-registration']) {
        	header("Location: ./".$headerLocationIf);
        } else {
        	header("Location: ./".$headerLocationElse);
        }
    }
}

echo '
<form method="post" class="form-steps">
    <div class="form-group">
        <label for="code">uw code</label>
        <input type="textarea" class="form-control" name="code" id="code">
    </div>

     <button type="submit" name="submit-code-registration" value="Register" class="btn btn-primary btn-sm">Code invoeren</button>
</form>
';
?>