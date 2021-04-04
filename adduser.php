<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
if (!($account->sessionLogin())) {
	header('Location: authenticate.php');
	exit;
}

// Wenn ein Nicht-Admin versucht, diese Seite aufzurufen, wird er weggeschickt.
if ($account->getRoll()!='admin') {
  header('Location: home.php');
  exit;
}

if ($_SERVER["REQUEST_METHOD"]=="POST") {

  try {
    $id = $account->addAccount($_POST['uname'],$_POST['pwd'], isset($_POST['enabled'])?true:false, $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['roll']);
?>
    <div class="confirm">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Der Account wurde erfolgreich hinzugefügt.</strong>
    </div>
    <?php
  }
  catch (Exception $e){
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong><?php echo $e->getMessage();?></strong>

    </div>
    <?php
  }
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>AG-Manager</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>

  <body class="loggedin">
    <?php require 'navbar.php'; require 'navadministration.php';?>
    <div class="content">
      <h2>Benutzer hinzufügen</h2>

			<form method="post" action="adduser.php">
        <div  class="flexbox">
      	<div>
        	<label for="uname">Benutzername</label><br>
        	<input type="text" name="uname" placeholder="Benutzername..." required>
			  </div>

				<div>
        	<label for="pwd">Passwort</label><br>
        	<input type="text" name="pwd" placeholder="Passwort..." required>
			  </div>

				<div>
        	<label for="fname">Vorname</label><br>
        	<input type="text" name="fname" placeholder="Vorname..." required>
				</div>

				<div>
        	<label for="lname">Nachname</label><br>
        	<input type="text" name="lname" placeholder="Nachname..." required>
				</div>

				<div>
        	<label for="email">E-Mail</label><br>
        	<input type="text" name="email" placeholder="mailadresse@irgendwo.de">
				</div>

				<div>
        	<label for="roll">Rolle</label><br>
          <input list="roll" name="roll" placeholder="admin, editor, viewer oder user">
          <datalist id="roll">
            <option value="admin">
            <option value="editor">
            <option value="viewer">
            <option value="user">
          </datalist>
        </div>

				<div>
        	<label for="enabled">Konto aktiv</label><br>
        	<input type="checkbox" name="enabled" checked >
				</div>
      </div>
      <div  class="flexbox">
        <div>
        	<input type="submit" value="Speichern">
        </div>
      </div>
      </form>
    </div>
  </body>
</html>
