<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if (!isset($_GET['wid'])) {
  echo 'Es wurde keine Id angegeben.';
  exit;
}

if (!isset($_GET['title'])) {
  echo 'Der AG-Titel fehlt.';
  exit;
} else {
  $_GET['title'] = htmlspecialchars($_GET['title']);
}

if (!checkOwner($account->getId(),$_GET['wid'])) {
  echo 'Sie leiten diese AG nicht.';
  exit;
}

// Abfrage der eingeschriebenen oder bereits anwesend markierten Schüler der AG für die Teilnahme-Checkboxen
// Ein ehemals anwesender Schüler kann zwischenzeitlich aus der AG ausgetreten sein, daher
// ist die Abfrage so kompliziert und erstreckt sich über "present" und "participate".
$query = "SELECT * FROM (
            SELECT user_id, firstname, lastname FROM present pr NATURAL JOIN users
            WHERE pr.event_id=:eid
          UNION
            SELECT user_id, firstname, lastname FROM participate pa NATURAL JOIN users
            WHERE pa.wg_id=:wid AND pa.schoolyear=:sy) as one
          NATURAL LEFT JOIN (
            SELECT user_id, 'y' AS waspresent FROM present WHERE event_id=:eid) AS two
          NATURAL LEFT JOIN (
            SELECT students.user_id, class FROM students) AS three
          NATURAL LEFT JOIN
            teachers
          ORDER BY lastname, firstname, class";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->bindValue(':eid',$_GET['eid'],PDO::PARAM_INT);
  $res->bindValue(':sy',$schoolyear,PDO::PARAM_STR);
  $res->execute();
  $rows = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Abrufen der AG auf:<br/>".$e->getMessage();
  exit;
}

try {
  $query = "SELECT * FROM events WHERE event_id = :eid";
  $res = $pdo->prepare($query);
  $res->bindValue(':eid', $_GET['eid'],PDO::PARAM_INT);
  $res->execute();
  $event = $res->fetch(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
  echo "Konnte den Termin nicht abrufen: <br/>".$e->getMessage();
}

?>
<form class="flexbox" method="post" action="wg_edit.php">
  <p><strong><?php echo $_GET['title'];?> - einen AG-Termin bearbeiten</strong><br><br></p>
  <input type="hidden" name="edit_event" value="edit_event">
  <input type="hidden" name="title" value="<?php echo $_GET['title']?>">
  <input type="hidden" name="wg_id" value="<?php echo $_GET['wid']?>">
  <input type="hidden" name="event_id" value="<?php echo $_GET['eid']?>">
  <div>
    <label for="date">Datum</label><br>
    <input type="date" name="date" id="date" value="<?php echo $event['date'];?>" required>
  </div>

  <div>
    <label for="time">Zeit</label><br>
    <input type="time" name="time" id="time" value="<?php echo $event['time'];?>" required>
  </div>

  <div>
    <label for="duration">Dauer in Minuten</label><br>
    <input type="text" name="duration" id="duration" value="<?php echo $event['duration'];?>" required>
  </div>

  <div>
    <label for="annotation">Anmerkung</label><br>
    <textarea type="text" id="annotation" name="annotation" required><?php echo $event['annotation'];?></textarea>
  </div>


<div class="flexbox">
<?php
foreach ($rows as $row) {
  $s=$row['class'].$row['shorthand'];
?>
  <div style="width: 200px;margin: 10px 0;">
    <input type="checkbox" id="user<?php echo $row['user_id'];?>" name="userlist[]" value="<?php echo $row['user_id'];?>" <?php if ($row['waspresent']=="y") echo " checked"; ?>><br/>
    <label for="user<?php echo $row['user_id'];?>"><?php echo $row['firstname'].' '.$row['lastname'].' ('.$s.')'; ?></label>
  </div>
    <?php
}
?>
</div>

<?php
$events = array();
try {
  $query = "SELECT * FROM events WHERE wg_id = :wid ORDER BY date DESC, time ASC";
  $res = $pdo->prepare($query);
  $res->bindValue(':wid', $_GET['wid'],PDO::PARAM_INT);
  $res->execute();
  $events = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
  echo "Konnte die Terminliste nicht abrufen: <br/>".$e->getMessage();
}
 ?>

<div>
  <br>
  <input class="greenbutton" type="submit" value="speichern">
</div>

<div>
  <br>
  <input class="redbutton" type="button" value="abbrechen" onclick="window.location.href='wg_edit.php'">
</div>

</form>

<div class="flexbox">
  <table>
    <thead>
      <tr>
        <th colspan="4">Bisherige Termine:</th>
      </tr>
      <tr>
        <th>Datum</th>
        <th>Uhrzeit</th>
        <th>Dauer</th>
        <th>Anmerkung</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($events as $event) {?>
        <tr>
          <td style="cursor: pointer" onclick='request("<?php echo 'wg_presence_edit.php?eid='.$event['event_id'].'&wid='.$_GET['wid'].'&title='.$_GET['title']?>")'> <strong><?php echo $event['date']; ?></strong></td>
          <td> <?php echo $event['time']; ?></td>
          <td> <?php echo $event['duration']; ?></td>
          <td> <?php echo $event['annotation']; ?></td>
        </tr>
      <?php
      } ?>
    </tbody>
  </table>
</div>
