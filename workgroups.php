<?php
session_start();
require 'account_class.php';
// If the user is not logged in redirect to the login page...
if (!($account->sessionLogin())) {
	header('Location: authenticate.php');
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
	<body class="loggedin">
		<?php require 'navbar.php'; ?>
    <?php require 'navworkgroups.php' ?>
		<div class="content">
			<h2>Alle Arbeitsgemeinschaften auf einen Blick</h2>
      <div>
        Ein Klick auf einen AG-Titel öffnet das zugehörige Infofeld. Ein Klick auf das geöffnete Infofeld schließt dieses wieder.
      <br>
        Diese Tabelle ist rein informativ. Es können keine Plätze reserviert werden. Nähere Infos zur Teilnahme erhält man vom jeweiligen AG-Leiter.
      </div>
      <div>

<?php
$query = 'SELECT * FROM wgs WHERE schoolyear = :sy ORDER BY day, time, title ASC';
$values = array(':sy' => $schoolyear);

try
{
  $res = $pdo->prepare($query);
  $res->execute($values);
}
catch (PDOException $e)
{
  echo 'Abfragefehler bei Ermittlung der AG-Liste des aktuellen Schuljahres.';
}

$aglist = $res->fetchAll(PDO::FETCH_ASSOC);
?>
<div>
<table>
  <thead>
  <tr>
    <th>Titel</th>
    <th>Wochentag</th>
    <th>Uhrzeit</th>
    <th>Dauer</th>
    <th>Maximum</th>
    <th>fortsetzbar</th>
  </tr>
</thead>
<tbody>
<?php
$count = 0;
foreach ($aglist as $row) {
  $max = ($row['max_num']==0)?'keins':(string)$row['max_num'];
  $mul = $row['multiple']?'ja':'nein';
  echo <<<EOF
  <tr>
  <td onclick="document.getElementById('row$count').style.display='table-row';" style="cursor: pointer; font-weight: bold;"> {$row['title']} </td>
  <td> {$row['day']} </td>
  <td> {$row['time']} </td>
  <td> {$row['duration']} min</td>
  <td> $max </td>
  <td> $mul </td>
  </tr>
  <tr id="row$count" style="display: none; cursor: pointer;" onclick="this.style.display='none';">
    <td colspan="6"> {$row['description']} </td>
  </tr>
EOF;
$count++;
}
 ?>
</tbody>
</table>
</div>
			</div>
		</div>
	</body>
</html>
