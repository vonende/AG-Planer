<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if ($account->isStudent()) {
  header('Location: wg_list.php');
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
    <script>

      function request(link) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("edit_div").innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", link, true);
        xhttp.send();
      }

  </script>
	</head>

<?php
try {
  $query = "SELECT title, day, time, schoolyear,wgs.wg_id FROM wgs, lead
            WHERE lead.user_id = :uid AND wgs.wg_id = lead.wg_id
            ORDER BY title ASC, schoolyear DESC, day ASC, time ASC";
  $res = $pdo->prepare($query);
  $res->bindValue(':uid',$account->getId(),PDO::PARAM_INT);
  $res->execute();
  $rows = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
  ?>
  <div class="alert">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong>Fehler beim Abfragen der AGs: <?php echo $e->getMessage();?></strong>
  </div>
  <?php
}

if (isset($_GET['error'])) {
  if ($_GET['error']=='no') {
    ?>
    <div class="confirm">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Die Daten wurden erfolgreich gespeichert.</strong>
    </div>
    <?php
  } else {
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong><?php echo htmlspecialchars($_GET['error'])?></strong>
    </div>
    <?php

  }
}
?>

  <?php require 'navbar.php'; ?>
  <?php require 'navworkgroups.php' ?>
	<body class="loggedin">
    <div class="content">
      <h2>Eigene AGs bearbeiten, Teilnehmer eintragen (&#9997;) und Anwesenheit notieren (&#10004;)</h2>
      <div class="flexbox" id="edit_div">
        Bitte eine AG zum Bearbeiten auswählen...
      </div>
      <div class="flexbox">
        <table>
          <thead>
            <tr>
              <th>Titel</th>
              <th>Wochentag</th>
              <th>Uhrzeit</th>
              <th>Schuljahr</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
                <?php
                foreach ($rows as $row) {?>
                      <tr>
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_edit_ajax.php?id='.$row['wg_id'].'")';?>'><strong><?php echo $row['title']?></strong></td>
                        <td><?php echo $row['day'];       ?></td>
                        <td><?php echo $row['time'];      ?></td>
                        <td><?php echo $row['schoolyear'];?></td>
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_participate.php?id='.$row['wg_id'].'")';?>'><strong>&nbsp;&#9997;&nbsp;</strong></td>
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_presence.php?id='.$row['wg_id'].'")';?>'><strong>&nbsp;&#10004;&nbsp;</strong></td>
                      </tr>
            <?php
                }
                 ?>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>
