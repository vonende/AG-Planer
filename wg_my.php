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

<?php
$query = 'SELECT * FROM wgs,participate AS p WHERE p.user_id = :id AND p.wg_id = wgs.wg_id ORDER BY p.schoolyear DESC, wgs.title ASC';
$values = array(':id' => $account->getId());
$ags = array();
try {
	$res = $pdo->prepare($query);
	$res->execute($values);
	$ags = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
	?>
	<div class="alert">
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
		<strong><?php echo $e->getMessage();?></strong>

	</div>
	<?php
}

?>

<?php require 'navbar.php'; ?>
<?php require 'navworkgroups.php' ?>
		<div class="content">
			<h2>Meine Arbeitsgemeinschaften</h2>
      <div>
        <p>Klicke auf den Titel einer AG, um die AG-Beschreibung zu sehen.</p><br>
				<p>Klicke auf das Schuljahresdatum einer AG, um die Liste deiner Teilnahmen zu sehen.</p>
      </div>
			<div class="flexbox">
				<table>
				  <thead>
				  	<tr>
							<th>Schuljahr</th>
				    	<th>Titel</th>
							<th>Leiter</th>
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
					foreach ($ags as $row) {
						$leiter = getLeaders($row['wg_id']);
					  $max = ($row['max_num']==0)?'keins':(string)$row['max_num'];
					  $mul = $row['multiple']?'ja':'nein';
						echo <<<EOF
					  <tr>
						<td onclick="window.location.href='present.php?id={$row['wg_id']}&title={$row['title']}'" style="cursor: pointer; font-weight: bold;"> {$row['schoolyear']}</td>
					  <td onclick="document.getElementById('row$count').style.display='table-row';" style="cursor: pointer; font-weight: bold;"> {$row['title']} </td>
						<td> $leiter </td>
					  <td> {$row['day']} </td>
					  <td> {$row['time']} </td>
					  <td> {$row['duration']} min</td>
					  <td> $max </td>
					  <td> $mul </td>
					  </tr>
					  <tr id="row$count" style="display: none; cursor: pointer;" onclick="this.style.display='none';">
					    <td colspan="8"> {$row['description']} </td>
					  </tr>
EOF;
					$count++;
					}

					?>
					</tbody>
				</table>
			</div>
    </div>
  </body>
</html>
