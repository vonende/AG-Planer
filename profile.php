<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>AG-Manager</title>
	<link href="style.css" rel="stylesheet" type="text/css">
</head>
<?php
require 'account_class.php';

// If the user is not logged in redirect to the login page...
if (!($account->sessionLogin())) {
	header('Location: authenticate.php');
	exit;
}

// Benutzerdaten aus der Datenbank laden
try {
	$row = $account->getAccountData();
	$username  = $row['username'];
	$firstname = $row['firstname'];
	$lastname  = $row['lastname'];
	$email     = $row['email'];
	$roll      = is_string($row['roll']?$row['roll']:'');
	$member    = is_string($row['member'])?$row['member']:'';
}
catch (Exception $e) {
	echo '<div class="alert">';
	echo 'Fehler beim Holen der Accountinfos. <br/>';
	echo $e->getMessage();
	echo '</div>';
}

// Falls das Formular sich selbst aufgerufen hat werden nun einige Dinge überprüft:
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$pw 				= $_POST["password"];
	$pw2 				= $_POST["password2"];
	$username 	= $_POST['username'];
	$firstname 	= $_POST['firstname'];
	$lastname 	= $_POST['lastname'];
	$email 			= $_POST['email'];

	if ($pw!=$pw2) {?>
		<div class="alert">
			<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
			<strong>Die Passwörter stimmen nicht überein!</strong>
		</div>
		<?php
	} elseif ($username=="") { // Dieser Fall dürfte wegen "required" nie auftreten.
?>
		<div class="alert">
			<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
			<strong>Es muss ein Benutzername eingetragen werden.</strong>
		</div>
			<?php
	} elseif ($pw=="") { // Dieser Fall dürfte wegen "required" nie auftreten.
		echo '<div class="alert">';
		echo "Es muss ein Passwort eingetragen werden. <br/>";
		echo '</div>';
	} elseif (!$account->isPasswdValid($pw)) {
		?>
				<div class="alert">
					<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
					<strong>Bitte ein längeres Passwort wählen. Mindestens 8 Zeichen.</strong>
				</div>
		<?php
	} else {
			try {
				$account->editAccount($account->getId(), $username, $pw, true, $firstname, $lastname, $email, $roll);
				?>
				<div class="confirm">
					<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
					<strong>Die Daten wurden erfolgreich gespeichert.</strong>
				</div>
				<?php
			}
			catch (Exception $e){
				echo '<div class="alert">Ein Fehler ist aufgetreten.<br/>';
				echo $e->getMessage();
				echo '</div>';
			}
	}
}
?>

	<body class="loggedin">
		<?php require 'navbar.php'; ?>
		<div class="content">
			<h2><?php echo $member=='student'?"Dein":"Ihr"?> Profil:</h2>
			<?php
			if ($member=="student") {echo '<p>Du bist als Schüler registriert.</p>';};
			if ($member=="teacher") {echo '<p>Sie sind als Lehrer registriert.</p>';};
			if ($member=="other")   {echo '<p>Sie sind als Mitarbeiter registriert.</p>';};
			 ?>
      <form method="post" action="profile.php" class="flexbox">
				<div>
        	<label for="username">Benutzername</label><br>
        	<input type="text" id="username" name="username" value="<?php echo $username ?>" required>
			  </div>

				<div>
        	<label for="password">Passwort</label><br>
        	<input type="password" id="password" name="password" placeholder="Passwort..." required>
			  </div>

				<div>
        	<label for="password">Passwort (Wiederholung)</label><br>
        	<input type="password" id="password2" name="password2" placeholder="Passwort..." required>
				</div>

				<div>
        	<label for="firstname">Vorname</label><br>
        	<input type="text" id="firstname" name="firstname" value="<?php echo $firstname ?>" placeholder="Vorname...">
				</div>

				<div>
        	<label for="lastname">Nachname</label><br>
        	<input type="text" id="lastname" name="lastname" value="<?php echo $lastname ?>" placeholder="Nachname...">
				</div>

				<div>
        	<label for="email">E-Mail</label><br>
        	<input type="text" id="email" name="email" value="<?php echo $email ?>"placeholder="mailadresse@irgendwo.de">
				</div>

				<div class="fullwidth">
        	<input class="onebutton" type="submit" value="Speichern">
				</div>
      </form>
		</div>
	</body>
</html>
