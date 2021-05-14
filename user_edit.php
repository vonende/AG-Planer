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

if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else if (isset($_POST['id'])) {
	$id = $_POST['id'];
} else {
  header('Location: administration.php');
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
	if ($_POST['password']!=$_POST['password2']) {
		throw new Exception('Die Passwörter stimmen nicht überein.');
	}
    $account->editAccount($_POST['id'], $_POST['username'],$_POST['password'], isset($_POST['enabled'])?true:false, $_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['roll']);

	if ($_POST['usertypes']=='teacher') {
		try {
			$query="SELECT * FROM teachers WHERE user_id = :uid";
			$res=$pdo->prepare($query);
			$res->bindValue(':uid',$id,PDO::PARAM_INT);
			$res->execute();
			$row=$res->fetch(PDO::FETCH_ASSOC);
			if (is_array($row)) {
				$query="UPDATE teachers SET shorthand = :sh WHERE user_id = :uid";
				$res=$pdo->prepare($query);
				$res->bindValue(':sh',htmlspecialchars($_POST['shorthand']),PDO::PARAM_STR);
				$res->bindValue(':uid',$id,PDO::PARAM_INT);
				$res->execute();				
			} else {
				$account->setTeacher($id,$_POST['shorthand']);
				$query="DELETE FROM students WHERE user_id = :uid";
				$res=$pdo->prepare($query);
				$res->bindValue(':uid',$id,PDO::PARAM_INT);
				$res->execute();				
			}
		}
		catch(PDOException $e) {
			throw new Exception("Update der Lehrertabelle ist nicht geglückt. Das Kürzel existiert wahrscheinlich bereits.");
		}
	}

	if ($_POST['usertypes']=='student') {
		try {
			$query="SELECT * FROM students WHERE user_id = :uid";
			$res=$pdo->prepare($query);
			$res->bindValue(':uid',$id,PDO::PARAM_INT);
			$res->execute();
			$row=$res->fetch(PDO::FETCH_ASSOC);
			if (is_array($row)) {
				$query="UPDATE students SET class = :cl, studentnumber = :sn WHERE user_id = :uid";
				$res=$pdo->prepare($query);
				$res->bindValue(':cl',htmlspecialchars($_POST['class']),PDO::PARAM_STR);
				$res->bindValue(':sn',htmlspecialchars($_POST['studentnumber']),PDO::PARAM_STR);
				$res->bindValue(':uid',$id,PDO::PARAM_INT);
				$res->execute();				
			} else {
				$account->setStudent($id,$_POST['class'],$_POST['studentnumber']);
				$query="DELETE FROM teachers WHERE user_id = :uid";
				$res=$pdo->prepare($query);
				$res->bindValue(':uid',$id,PDO::PARAM_INT);
				$res->execute();				
			}
		}
		catch(PDOException $e) {
			throw new Exception("Update der Schülertabelle ist nicht geglückt. Die Schülernummer existiert wahrscheinlich bereits.");
		}
	}

	if ($_POST['usertypes']=='other') {
		try {
			$query="DELETE FROM teachers WHERE user_id = :uid";
			$res=$pdo->prepare($query);
			$res->bindValue(':uid',$id,PDO::PARAM_INT);
			$res->execute();				
			$query="DELETE FROM students WHERE user_id = :uid";
			$res=$pdo->prepare($query);
			$res->bindValue(':uid',$id,PDO::PARAM_INT);
			$res->execute();				
		}
		catch(PDOException $e) {
			throw new Exception("Update der Lehrer- oder Schülertabelle ist nicht geglückt.");
		}
	}
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
      <strong>Fehler beim Editieren des Accounts: <?php echo $e->getMessage();?></strong>
    </div>
    <?php
  }
}

try{
	$row = $account->getAccountData($id);
}
catch (Exception $e) {
	?>
	<div class="alert">
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
		<strong><?php echo $e->getMessage();?></strong>

	</div>
	<?php
}

?>

	<body class="loggedin">
    <?php require 'navbar.php'; require 'navadministration.php';?>
		<div class="content">
			<h2>Benutzerdaten editieren</h2>

			<form method="post">
				<div  class="flexbox">
					<input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
					<div>
						<label for="username">Benutzername</label><br>
						<input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
					</div>

					<div>
						<label for="password">Passwort</label><br>
						<input type="password" id="password" name="password" placeholder="Passwort...">
					</div>

					<div>
						<label for="password">Passwort (Wiederholung)</label><br>
						<input type="password" id="password2" name="password2" placeholder="Passwort...">
					</div>

					<div>
						<label for="firstname">Vorname</label><br>
						<input type="text" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" placeholder="Vorname..." required>
					</div>

					<div>
						<label for="lastname">Nachname</label><br>
						<input type="text" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" placeholder="Nachname..." required>
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
						<input type="radio" id="teacher" name="usertypes" value="teacher" onclick="document.getElementById('teachersettings').style.display='flex'; document.getElementById('studentsettings').style.display='none';" <?php if ($row['member']=='teacher') {echo "checked";}?>>
						<label for="teacher">LehrerIn</label>
						<input type="radio" id="student" name="usertypes" value="student" onclick="document.getElementById('teachersettings').style.display='none'; document.getElementById('studentsettings').style.display='flex';"<?php if ($row['member']=='student') {echo "checked";}?>>
						<label for="student">SchülerIn</label>
						<input type="radio" id="other" name="usertypes" value="other" onclick="document.getElementById('teachersettings').style.display='none'; document.getElementById('studentsettings').style.display='none';"<?php if ($row['member']=='other') {echo "checked";}?>>
						<label for="other">Andere(r)</label>
					</div>

					<div class="fullwidth" id="teachersettings" <?php if ($row['member']!='teacher') {echo 'style="display: none; flex-wrap: wrap;"';}?>>
						<div>
							<label for="shorthand">Lehrerkürzel</label><br>
							<input type="text" name="shorthand" placeholder="Lehrerkürzel..." value="<?php if ($row['member']=='teacher') {echo $row['shorthand'];}?>">
						</div>
					</div>

					<div class="fullwidth" <?php if ($row['member']!='student') {echo 'style="display: none; flex-wrap: wrap;"';}?> id="studentsettings">
						<div style="margin-right: 20px;">
							<label for="class">Klasse</label><br>
							<input type="text" name="class" placeholder="Klasse..."value="<?php if ($row['member']=='student') {echo $row['class'];}?>">
						</div>

						<div>
							<label for="studentnumber">Schülernummer</label><br>
							<input type="text" name="studentnumber" placeholder="Schülernummer..." value="<?php if ($row['member']=='student') {echo $row['studentnumber'];}?>">
						</div>
					</div>
				</div>

			
				<div  class="flexbox">
					<div>
						<input class="greenbutton" type="submit" value="Speichern">
					</div>
					<div>
						<input class="redbutton" type="button" value="Zurück" onclick="window.location.href='user_search.php'">
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
