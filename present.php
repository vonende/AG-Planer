<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

$query = 'SELECT date, time, duration, annotation FROM events AS e, present AS p
          WHERE e.wg_id = :wid AND p.user_id = :uid AND e.event_id = p.event_id
					ORDER BY e.date DESC, e.time DESC';
$takeparts = array();
try {
	$res = $pdo->prepare($query);
  $res->bindValue(':uid', $account->getId(), PDO::PARAM_INT);
  $res->bindValue(':wid', $_GET['id'], PDO::PARAM_INT);
	$res->execute();
	$takeparts = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
	?>
		<strong>Bei der Abfrage der Teilnahmen trat ein Fehler auf: <br/><?php echo $e->getMessage();?></strong>
	<?php
}
?>
<form>
	<input class="greenbutton" type="button" value="Zurück" onclick="window.location.href='wg_my.php'">
</form>
<table>
	<thead>
		<tr>
			<th colspan="4"><?php echo $_GET['title'];?> (Terminliste)</th>
		</tr>
		<tr>
			<th>Datum</th>
			<th>Uhrzeit</th>
			<th>Dauer</th>
			<th>Bemerkung</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($takeparts as $row) {
			echo <<<EOF
			<tr>
			<td> {$row['date']} </td>
			<td> {$row['time']} </td>
			<td> {$row['duration']} </td>
			<td> {$row['annotation']} </td>
			</tr>
EOF;
		}

		?>
	</tbody>
</table>
