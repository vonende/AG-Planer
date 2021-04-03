<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
if (!($account->sessionLogin())) {
	header('Location: authenticate.php');
	exit;
}

// Im Array $row werden die aktuellen Accountdaten hinterlegt.
try {
  $row = $account->getAccountData();
}
catch (Exception $e){
  echo $e->getMessage();
  exit;
}

// Wenn ein Nicht-Admin versucht, diese Seite aufzurufen, wird er weggeschickt.
if ($row['roll']!='admin') {
  header('Location: home.php');
  exit;
}

if ($_SERVER["REQUEST_METHOD"]=="POST") {
  try {
    $account->editAccount($_POST['id'], $_POST['username'],$_POST['password'], isset($_POST['enabled'])?true:false, $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['roll']);
?>
    <div class="confirm">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Die Benutzerdaten wurden erfolgreich geändert.</strong>
    </div>
    <?php
  }
  catch (Exception $e){
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Fehler beim Editieren des Accounts. <br/></strong>
      <?php echo $e->getMessage();?>
    </div>
    <?php
  }
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
  header('Location: administration.php');
  exit;
}

$row = $account->getAccountData();


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
      <h2>Benutzerdaten editieren</h2>

			<form method="post" action="edit_account.php">
        <div  class="flexbox">
        <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
      	<div>
        	<label for="username">Benutzername</label><br>
        	<input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
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
        	<input type="text" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" placeholder="Vorname...">
				</div>

				<div>
        	<label for="lastname">Nachname</label><br>
        	<input type="text" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" placeholder="Nachname...">
				</div>

				<div>
        	<label for="email">E-Mail</label><br>
        	<input type="text" id="email" name="email" value="<?php echo $row['email']; ?>" placeholder="mailadresse@irgendwo.de">
				</div>

				<div>
        	<label for="roll">Rolle</label><br>
          <input list="roll" name="roll" value="<?php echo $row['roll']; ?>" placeholder="admin, editor, viewer oder user">
          <datalist id="roll">
            <option value="admin">
            <option value="editor">
            <option value="viewer">
            <option value="user">
          </datalist>
        </div>

				<div>
        	<label for="enabled">Konto aktiv</label><br>
        	<input type="checkbox" id="enabled" name="enabled" <?php echo $row['enabled']?'checked':''; ?>>
				</div>
      </div>
      <div  class="flexbox">
        <div>
        	<input type="submit" value="Speichern">
        </div>
        <div>
          <input type="button" value="Zurück">
        </div>
      </div>
      </form>
    </div>
  </body>
</html>
