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

$un = '%';
$fn = '%';
$ln = '%';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$un = $_POST['usersearch'];
	$fn = $_POST['firstnamesearch'];
	$ln = $_POST['lastnamesearch'];
	$query = 'SELECT * FROM users WHERE username LIKE :user AND firstname LIKE :fn AND lastname LIKE :ln';
	$values = array(':user'=>$un, ':fn'=>$fn, ':ln'=>$ln);
	$userlist = array();
	try{
		$res = $pdo->prepare($query);
		$res->execute($values);
		$userlist = $res->fetchAll(PDO::FETCH_ASSOC);
	}
	catch (PDOException $e)
	{
	  echo 'Abfragefehler bei Ermittlung aller zum Suchstring passenden Benutzer.';
		die();
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
      <h2>Verwaltungsbereich für Administratoren</h2>
			<div>
				<div>Wildcards:</div>
				<ul>
					<li>beliebig viele beliebige Zeichen: %</li>
					<li>ein beliebiges Zeichen: _ </li>
				</ul>
			</div>
      <form method="post" action="administration.php" class="flexbox">
				<div>
					<label for="usersearch">Benutzer</label><br>
					<input type="text" name="usersearch" id="usersearch" placeholder="Suchstring" value=<?php echo $un ?>>
				</div>
				<div>
					<label for="firstnamesearch">Vorname</label><br>
					<input type="text" name="firstnamesearch" id="firstnamesearch" placeholder="Suchstring" value=<?php echo $fn ?>>
				</div>
				<div>
					<label for="lastnamesearch">Nachname</label><br>
					<input type="text" name="lastnamesearch" id="lastnamesearch" placeholder="Suchstring" value=<?php echo $ln ?>>
				</div>
				<div>
					<br>
					<input type="submit" value="suchen">
				</div>
			</form>

<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
			<div>
				<div>
					<table>
					  <thead>
					  <tr>
					    <th>ID</th>
					    <th>Benutzer</th>
					    <th>Vorname</th>
					    <th>Nachname</th>
					    <th>E-Mail</th>
					    <th>Rolle</th>
							<th>aktiv</th>
							<th>Registrierungsdatum</th>
							<th>Letztes Update</th>
					  </tr>
					</thead>
					<tbody>
					<?php
					$count = 0;
					foreach ($userlist as $row) {
					  $aktiv = $row['enabled']?'ja':'nein';
					  echo <<<EOF
					  <tr>
					  <td style="cursor: pointer; font-weight: bold;"> <a href="edit_account.php?id={$row['user_id']}">{$row['user_id']} </a></td>
					  <td> {$row['username']} </td>
					  <td> {$row['firstname']} </td>
					  <td> {$row['lastname']} </td>
						<td> {$row['email']} </td>
						<td> {$row['roll']} </td>
					  <td> $aktiv </td>
						<td> {$row['registrationtime']} </td>
						<td> {$row['last_update']} </td>
					  </tr>
					EOF;
					$count++;
					}
					 ?>
					</tbody>
					</table>
				</div>
			</div>
<?php } ?>
    </div>
  </body>
</html>
