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

      // https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit
      function post(path, params, method='post') {
        // The rest of this code assumes you are not using a library.
        // It can be made less verbose if you use one.
        const form = document.createElement('form');
        form.method = method;
        form.action = path;
        for (const key in params) {
          if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
          }
        }
        document.body.appendChild(form);
        form.submit();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['del_user'])) {
    try {
      $query = "DELETE FROM participate WHERE user_id=:uid AND wg_id=:wid AND schoolyear=:sy";
      $res = $pdo->prepare($query);
      $res->bindValue(':uid',$_POST['del_user'],PDO::PARAM_INT);
      $res->bindValue(':wid',$_POST['wg_id'],PDO::PARAM_INT);
      $res->bindValue(':sy',$schoolyear,PDO::PARAM_STR);
      $res->execute();
    }
    catch (PDOException $e) {
      ?>
      <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Fehler beim Löschen des Benutzers: <br/> <?php echo $e->getMessage();?></strong>
      </div>
      <?php
    }
    ?>
    <script>
      request('wg_participate.php?id=<?php echo $_POST['wg_id'];?>&title=<?php echo $_POST['title'];?>');
    </script><?php
  }

  if (isset($_POST['add_event'])) {
    try {
      $pdo->beginTransaction();
      $query = "INSERT INTO events (time, date, duration, annotation, wg_id)
                VALUES (:ti, :da, :du, :an, :id)";
      $res = $pdo->prepare($query);
      $res->bindValue(':du',$_POST['duration'],PDO::PARAM_INT);
      $res->bindValue(':id',$_POST['wg_id'],PDO::PARAM_INT);
      $res->bindValue(':ti',$_POST['time'],PDO::PARAM_STR);
      $res->bindValue(':da',$_POST['date'],PDO::PARAM_STR);
      $res->bindValue(':an',$_POST['annotation'],PDO::PARAM_STR);
      $res->execute();

      $query = "SELECT currval('events_event_id_seq') AS eid";
      $res = $pdo->prepare($query);
      $res->execute();
      $eid = $res->fetch(PDO::FETCH_ASSOC)['eid'];

      $query = "INSERT INTO present (user_id, event_id)
                VALUES ";
      $s="";
      foreach ($_POST['userlist'] as $uid) {
        $uid = intval($uid); // Nur zur Sicherheit gegen SQL-Injection
        if (strlen($s)>0) {
          $s = $s.",";
        }
        $s = $s.'('.$uid.','.$eid.')';
      }
      $query=$query.$s;
      $res=$pdo->prepare($query);
      $res->execute();
      $pdo->commit();
      ?>
      <div class="confirm">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Der Termin wurde erfolgreich gespeichert.</strong>
      </div>
      <?php
    }
    catch (PDOException $e) {
      $pdo->rollBack();
      ?>
      <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Fehler beim Hinzufügen des Termins: <br/> <?php echo $e->getMessage();?></strong>
      </div>
      <?php
    }
    ?>
    <script>
      request('wg_presence.php?id=<?php echo $_POST['wg_id'];?>&title=<?php echo $_POST['title'];?>');
    </script><?php
  }

  if (isset($_POST['add_user'])){
    $_POST['free']=$_POST['free'] ?? 0;
    if ($_POST['free']>0) {
    try {
      $query = "INSERT INTO participate (user_id, wg_id, schoolyear)
                VALUES (:uid, :wid, :sy)";
      $res = $pdo->prepare($query);
      $res->bindValue(':uid',$_POST['add_user'],PDO::PARAM_INT);
      $res->bindValue(':wid',$_POST['wg_id'],PDO::PARAM_INT);
      $res->bindValue(':sy',$schoolyear,PDO::PARAM_STR);
      $res->execute();
    }
    catch (PDOException $e) {
      ?>
      <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Fehler beim Eintragen des Benutzers: <br/> <?php echo $e->getMessage();?></strong>
      </div>
      <?php
    }
  } else {
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Die AG ist bereits voll!!!</strong>
    </div>
    <?php

  }
  ?>
  <script>
    request('wg_participate.php?id=<?php echo $_POST['wg_id'];?>&title=<?php echo $_POST['title'];?>');
  </script><?php
  }
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
      <h2>Eigene AGs bearbeiten</h2>
      <div id="edit_div" class="flexbox">
        <p>Bitte eine AG zum Bearbeiten auswählen.</p>
        <p>Sie können Teilnehmer ein- und austragen (&#9997;) und die Anwesenheit notieren (&#10004;).</p>
      </div>

      <div class="flexbox">
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
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_edit_ajax.php?id='.$row['wg_id'].'")';?>'><strong><?php echo htmlspecialchars($row['title'])?></strong></td>
                        <td><?php echo $row['day'];       ?></td>
                        <td><?php echo $row['time'];      ?></td>
                        <td><?php echo $row['schoolyear'];?></td>
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_participate.php?id='.$row['wg_id'].'&title='.htmlspecialchars($row['title']).'")';?>'><strong>&nbsp;&#9997;&nbsp;</strong></td>
                        <td style="cursor: pointer" onclick='request(<?php echo '"wg_presence.php?id='.$row['wg_id'].'&title='.htmlspecialchars($row['title']).'")';?>'><strong>&nbsp;&#10004;&nbsp;</strong></td>
                      </tr>
            <?php
                }
                 ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </body>
</html>
