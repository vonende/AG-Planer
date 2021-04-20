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
		<script>

			function request(link) {
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("present_div").innerHTML = this.responseText;
					}
				};
				xhttp.open("GET", link, true);
				xhttp.send();
			}

		</script>

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
      <div class="flexbox" id="present_div">
        <p style="width: 100%">Klicke auf den Titel einer AG, um die AG-Beschreibung zu sehen.</p>
				<p>Klicke auf das Schuljahresdatum einer AG, um die Liste deiner Teilnahmen zu sehen.</p>
      </div>
			<div class="flexbox">
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
						?>
						<tr>
							<td onclick="request('present.php?id=<?php echo $row['wg_id'];?>&title=<?php echo $row['title'];?>')" style="cursor: pointer; font-weight: bold;"> <?php echo $row['schoolyear'];?></td>
						  <td onclick="document.getElementById('row<?php echo $count;?>').style.display='table-row';" style="cursor: pointer; font-weight: bold;"> <?php echo $row['title'];?> </td>
							<td> <?php echo $leiter;?> </td>
						  <td> <?php echo $row['day'];?> </td>
						  <td> <?php echo $row['time'];?> </td>
						  <td> <?php echo $row['duration'];?> min</td>
						  <td> <?php echo $max;?> </td>
						  <td> <?php echo $mul;?> </td>
					  </tr>
					  <tr id="row<?php echo $count;?>" style="display: none; cursor: pointer;" onclick="this.style.display='none';">
					    <td colspan="8"> <?php echo $row['description'];?> </td>
					  </tr>
<?php
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
