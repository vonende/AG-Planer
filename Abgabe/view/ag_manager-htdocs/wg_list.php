<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';
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
        <p>Ein Klick auf einen AG-Titel öffnet das zugehörige Infofeld. Ein Klick auf das geöffnete Infofeld schließt dieses wieder.</p>
        <p>Diese Tabelle ist rein informativ. Es können keine Plätze reserviert werden. Nähere Infos zur Teilnahme erhält man vom jeweiligen AG-Leiter.</p>
      </div>
      <div class="flexbox">
				<div class="flexbox">

<?php
$query = 'SELECT * FROM wgs
          NATURAL LEFT JOIN
				     (SELECT count(user_id),wg_id FROM participate
						  WHERE schoolyear = :sy GROUP BY wg_id) AS one
				  WHERE schoolyear=:sy
					ORDER BY day, time, title ASC';
$aglist = array();
try
{
  $res = $pdo->prepare($query);
	$res->bindValue(':sy',$schoolyear,PDO::PARAM_STR);
  $res->execute();
	$aglist = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e)
{
  echo 'Abfragefehler bei Ermittlung der AG-Liste des aktuellen Schuljahres.';
}

?>
<table>
  <thead>
  <tr>
    <th>Titel</th>
		<th>Leiter</th>
    <th>Wochentag</th>
    <th>Uhrzeit</th>
    <th>Dauer</th>
    <th>freie Plätze</th>
    <th>fortsetzbar</th>
  </tr>
</thead>
<tbody>
<?php
$count = 0;
foreach ($aglist as $row) {
	$leiter = getLeaders($row['wg_id']);
  $max = ($row['max_num']==0)?'unbekannt':(string)((int)$row['max_num']-(int)$row['count']);
  $mul = $row['multiple']?'ja':'nein';
  echo <<<EOF
  <tr>
  <td onclick="document.getElementById('row$count').style.display='table-row';" style="cursor: pointer; font-weight: bold;"> {$row['title']} </td>
	<td> $leiter </td>
  <td> {$row['day']} </td>
  <td> {$row['time']} </td>
  <td> {$row['duration']} min</td>
  <td> $max </td>
  <td> $mul </td>
  </tr>
  <tr id="row$count" style="display: none; cursor: pointer;" onclick="this.style.display='none';">
    <td colspan="7"> {$row['description']} </td>
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
