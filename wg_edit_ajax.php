<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if ($account->isStudent()) {
  header('Location: wg_list.php');
  exit;
}

if (!isset($_GET['id'])) {
  echo 'Es wurde keine Id angegeben.';
  exit;
}

$query = "SELECT * FROM wgs WHERE wg_id=:id";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':id',$_GET['id'],PDO::PARAM_INT);
  $res->execute();
  $row = $res->fetch(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Abrufen der AG auf:<br/>".$e;
  exit;
}
?>

<form action="wg_edit_save.php" method="post">
  <div  class="flexbox">
    <input type="hidden" name="wg_id" value="<?php echo $_GET['id']; ?>">
    <div>
      <label for="title">Titel</label><br>
      <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
    </div>
      <div>
        <label for="day">Wochentag</label><br>
        <input list="day" name="day" value="<?php echo $row['day']; ?>"  placeholder="Montag oder Dienstag oder ..." required>
        <datalist id="day">
          <option value="Montag">
          <option value="Dienstag">
          <option value="Mittwoch">
          <option value="Donnerstag">
          <option value="Freitag">
          <option value="Samstag">
          <option value="Sonntag">
        </datalist>
      </div>
      <div>
        <label for="time">Uhrzeit</label><br/>
        <input type="time" id="time" name="time" value="<?php echo $row['time']; ?>" required>
      </div>
      <div>
        <label for="duration">Dauer in Minuten</label><br/>
        <input type="text" id="duration" name="duration" value="<?php echo $row['duration']; ?>" required>
      </div>
      <div>
        <label for="max_num">Maximale Teilnehmeranzahl (0 = beliebig)</label><br/>
        <input type="text" id="max_num" name="max_num" value="<?php echo $row['max_num'];?>">
      </div>
      <div>
        <label for="schoolyear">Schuljahr</label><br/>
        <input type="text" id="schoolyear" name="schoolyear" value="<?php echo $row['schoolyear'];?>">
      </div>
      <div>
        <label for="multiple">Fortsetzbar?</label><br>
        <input type="checkbox" id="multiple" name="multiple" <?php echo (bool)$row['multiple']?'checked':'';?>>
      </div>
      <div class="fullwidth">
        <label for="description">Beschreibung</label><br>
        <textarea name="description" id="description"><?php echo $row['description']; ?></textarea>
      </div>
      <div class="fullwidth">
        <input class="greenbutton" type="submit" value="speichern">
        <input class="redbutton" type="button" value="abbrechen" onclick="window.location.href='wg_edit.php'">
      </div>
    </div>
</form>
