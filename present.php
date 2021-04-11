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
<?php
$query = 'SELECT * FROM events AS e, present AS p WHERE e.wg_id = :wid AND p.user_id = :uid AND e.event_id = p.event_id ORDER BY e.date DESC, e.time DESC';
$values = array(':uid' => $account->getId(), ':wid' => $_GET['id']);
$takeparts = array();
try {
	$res = $pdo->prepare($query);
	$res->execute($values);
	$takeparts = $res->fetchAll(PDO::FETCH_ASSOC);
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
	<body class="loggedin">
    <div class="content">
    <h2>Teilnahmen an der AG <?php echo $_GET['title']; ?></h2>
    <div>
      <div>
        <table>
				  <thead>
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
      </div>
    </div>
    <form method="get">
      <div>
        <input type="button" value="Zurück" onclick="window.location.href='my_workgroups.php'">
      </div>
    </form>
    </div>
  </body>
</html>
