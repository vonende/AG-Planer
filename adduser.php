<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

// Wenn ein Nicht-Admin versucht, diese Seite aufzurufen, wird er weggeschickt.
if ($account->getRoll()!='admin') {
  header('Location: home.php');
  exit;
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
<?php
if ($_SERVER["REQUEST_METHOD"]=="POST") {

  try {
		$pdo->beginTransaction();
    $id = $account->addAccount($_POST['uname'],$_POST['pwd'], isset($_POST['enabled'])?true:false, $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['roll']);
		if ($_POST["usertypes"]=="teacher"){
			$account->setTeacher($id,$_POST["shorthand"]);
		} else if ($_POST["usertypes"]=="student"){
			$account->setStudent($id,$_POST["class"],$_POST["number"]);
		};
		$pdo->commit();
?>
    <div class="confirm">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Der Account wurde erfolgreich hinzugefügt.</strong>
    </div>
    <?php
  }
  catch (Exception $e){
		$pdo->rollBack();
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong><?php echo $e->getMessage();?></strong>
    </div>
    <?php
  }
}

?>
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
          	<input list="roll" name="roll" placeholder="admin, editor, viewer oder user" required>
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
						<input type="radio" id="teacher" name="usertypes" value="teacher" onclick="document.getElementById('teachersettings').style.display='flex'; document.getElementById('studentsettings').style.display='none';" checked>
  					<label for="teacher">LehrerIn</label>
  					<input type="radio" id="student" name="usertypes" value="student" onclick="document.getElementById('teachersettings').style.display='none'; document.getElementById('studentsettings').style.display='flex';">
  					<label for="student">SchülerIn</label>
  					<input type="radio" id="other" name="usertypes" value="other" onclick="document.getElementById('teachersettings').style.display='none'; document.getElementById('studentsettings').style.display='none';">
  					<label for="other">Andere(r)</label>
					</div>

					<div class="fullwidth" id="teachersettings">
						<div>
						<label for="shorthand">Lehrerkürzel</label><br>
						<input type="text" name="shorthand" placeholder="Lehrerkürzel...">
					</div>
					</div>

					<div class="fullwidth" style="display: none; flex-wrap: wrap;" id="studentsettings">
						<div style="margin-right: 60px;">
							<label for="class">Klasse</label><br>
							<input type="text" name="class" placeholder="Klasse...">
						</div>

						<div>
							<label for="number">Schülernummer</label><br>
							<input type="text" name="number" placeholder="Schülernummer...">
						</div>
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
