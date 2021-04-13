<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

// Schüler dürfen keine AG anlegen.
if ($account->isStudent()) {
  header('Location: home.php');
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
<?php

$title="";
$day  = "";
$time = "15:20";
$duration = 90;
$max_num = 0;
$year = $schoolyear;
$desc = "";
$multi = true;

if ($_SERVER["REQUEST_METHOD"]=="POST") {
  $title    = htmlspecialchars($_POST['title']);
  $day      = trim($_POST['day']);
  $time     = htmlspecialchars($_POST['time']);
  $duration = (int)$_POST['duration'];
  $max_num  = (int)$_POST['max_num'];
  $year     = $_POST['schoolyear'];
  $desc     = htmlspecialchars($_POST['description']);
  $desc     = str_replace("\r\n", " ", $desc); // Newline Windows
  $desc     = str_replace("\n", " ", $desc);   // Newline Linux
  $desc     = str_replace("\r", "", $desc);    // verbleibende \r löschen
  $multi    = isset($_POST['multiple']);

  try {
    if (strlen($title)<3) {
      throw new Exception("Es muss ein AG-Titel eingegeben werden. (mind. 3 Zeichen lang)");
    }
    if ($day!="Montag" && $day!="Dienstag" && $day!="Mittwoch" && $day!="Donnerstag" && $day!="Freitag" && $day!="Samstag" && $day!="Sonntag") {
      throw new Exception("Bitte einen gültigen Wochentag eingeben.");
    }
    if ($duration<0) {
      throw new Exception("Keine negativen Werte für die Dauer eingeben.");
    }
    if ($max_num<0) {
      throw new Exception("Keine negativen Werte für die maximale Teilnehmerzahl eingeben.");
    }
    if (preg_match("/[0-9]{4}\/[0-9]{2}/", $year)!=1) {
      throw new Exception("Bitte ein gültiges Schuljahresformat eingeben, z.B. 2020/21");
    }
    try {
      $pdo->beginTransaction();
      $query = "INSERT INTO wgs(title,day,time,duration,max_num,multiple,schoolyear,description)
                VALUES (:title,:day,:time,:dur,:max,:mul,:year,:des)";
      // bindValue(), da es sonst beim Einfügen von Bools Probleme mit dem PDO gibt.
      $res = $pdo->prepare($query);
      $res->bindValue(':title', $title, PDO::PARAM_STR);
      $res->bindValue(':day', $day, PDO::PARAM_STR);
      $res->bindValue(':time', $time, PDO::PARAM_STR);
      $res->bindValue(':dur', $duration, PDO::PARAM_INT);
      $res->bindValue(':max', $max_num, PDO::PARAM_INT);
      $res->bindValue(':mul', $multi, PDO::PARAM_BOOL); // !!!!
      $res->bindValue(':year', $year, PDO::PARAM_STR);
      $res->bindValue(':des', $desc, PDO::PARAM_STR);
      $res->execute();
      $query = "INSERT INTO lead (user_id, wg_id) VALUES (:uid,(SELECT currval('wgs_wg_id_seq')))";
      $res = $pdo->prepare($query);
      $res->bindValue(':uid',$account->getId(),PDO::PARAM_INT);
      $res->execute();
      $pdo->commit();
      ?>
      <div class="confirm">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <strong>Die AG wurde erfolgreich hinzugefügt.</strong>
      </div>
      <?php

    }
    catch (PDOException $e) {
      $pdo->rollBack();
      throw new Exception("Datenbankfehler beim Anlegen der AG: ".$e->getMessage());
    }
  }
  catch (Exception $e) {
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong><?php echo $e->getMessage();?></strong>
    </div>
    <?php
  }
}
?>

  <?php require 'navbar.php'; ?>
  <?php require 'navworkgroups.php' ?>
	<body class="loggedin">
    <div class="content">
      <h2>Arbeitsgemeinschaft erstellen</h2>
      <form method="post">
        <div class="flexbox">
          <div>
            <label for="title">Titel der Arbeitsgemeinschaft</label><br/>
            <input type="text" id="title" name="title" placeholder="AG-Titel eingeben..." value="<?php echo $title; ?>" required>
          </div>
          <div>
            <label for="day">Wochentag</label><br>
            <input list="day" name="day" value="<?php echo $day; ?>"  placeholder="Montag oder Dienstag oder ..." required>
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
            <input type="time" id="time" name="time" value="<?php echo $time; ?>" required>
          </div>
          <div>
            <label for="duration">Dauer in Minuten</label><br/>
            <input type="text" id="duration" name="duration" value="<?php echo $duration; ?>" required>
          </div>
          <div>
            <label for="max_num">Maximale Teilnehmeranzahl (0 = beliebig)</label><br/>
            <input type="text" id="max_num" name="max_num" value="<?php echo $max_num;?>">
          </div>
          <div>
            <label for="schoolyear">Schuljahr</label><br/>
            <input type="text" id="schoolyear" name="schoolyear" value="<?php echo $year;?>">
          </div>
          <div>
            <label for="multiple">Fortsetzbar?</label><br>
            <input type="checkbox" id="multiple" name="multiple" <?php echo $multi?'checked':'';?>>
          </div>
          <div class="fullwidth">
            <label for="description">Beschreibung</label><br>
            <textarea name="description" id="description"><?php echo $desc; ?></textarea>
          </div>
          <div class="fullwidth">
            <input type="submit" value="speichern">
          </div>
        </div>
      </form>
    </div>
  </body>
</html>
