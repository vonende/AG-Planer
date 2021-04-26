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

// Eine Liste aller noch nicht eingeschriebenen Benutzer erstellen
$query = "SELECT * FROM
           (SELECT user_id, firstname, lastname
            FROM users
            EXCEPT
           (SELECT u.user_id, firstname, lastname
            FROM users u, participate p WHERE p.wg_id = :wid AND u.user_id=p.user_id)) AS noparticipator
          NATURAL LEFT JOIN (SELECT user_id, class FROM students) AS s
          NATURAL LEFT JOIN (SELECT user_id, shorthand FROM teachers) AS t
          ORDER BY lastname, firstname, class, shorthand";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->execute();
  $users = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Laden der Benutzerliste auf:<br/>".$e;
  exit;
}

// Eine Liste aller AG-Teilnehmer ermitteln
$query = "SELECT * FROM (
            SELECT user_id, firstname, lastname
            FROM users NATURAL JOIN participate p
            WHERE p.wg_id = :wid AND p.schoolyear='$schoolyear') AS one
          NATURAL LEFT JOIN students
          NATURAL LEFT JOIN teachers
          ORDER BY lastname, firstname, class, shorthand";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->execute();
  $participants = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Laden der Benutzerliste auf:<br/>".$e;
  exit;
}

// Die Anzahl der bereits eingeschriebenen Teilnehmer ermitteln
$query = "SELECT count(user_id) AS num
          FROM participate
          WHERE wg_id = :wid AND schoolyear = :sy";

try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->bindValue(':sy',$schoolyear,PDO::PARAM_STR);
  $res->execute();
  $num = $res->fetch(PDO::FETCH_ASSOC)['num'] ?? 0;
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Laden der Teilnehmerzahlen auf:<br/>".$e;
  exit;
}

// Die maximal zulässige Anzahl an Teilnehmern der AG ermitteln
$query = "SELECT max_num, schoolyear FROM wgs WHERE wg_id = :wid";

try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->execute();
  $wg =  $res->fetch(PDO::FETCH_ASSOC);
  $max_num = $wg['max_num'] ?? 0;
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Laden der maximalen Teilnehmerzahl auf:<br/>".$e;
  exit;
}

echo "<p style='width: 100%'><strong>".$_GET['title']." - Teilnehmer einschreiben und entfernen</strong></p>";

if ($wg['schoolyear'] == $schoolyear) {
  $free = 1;
  if ($max_num==0) {
    echo "<p class='fullwidth'>Es können noch beliebig viele Teilnehmer hinzugefügt werden.<br><br></p>";
  } else if ($max_num==$num){
    echo "<p class='fullwidth'>Die AG ist bereits voll belegt.<br/><br/></p>";
    $free=0;
  } else if ($max_num-$num == 1) {
    echo "<p class='fullwidth'>Es kann noch ein Teilnehmer hinzugefügt werden.<br/><br/></p>";
  } else {
    echo "<p class='fullwidth'>Es können noch ".(string)((int)$max_num-(int)$num)." Teilnehmer hinzugefügt werden.<br/><br/></p>";
  }

  ?>
  <form method="post" action="wg_edit.php" class="flexbox">
    <input type="hidden" name="free" value="<?php echo $free;?>">
    <input type="hidden" name="wg_id" value="<?php echo $_GET['wid'] ?>">
    <input type="hidden" name="title" value="<?php echo $_GET['title'] ?>">
    <div>
      <label for="add_user">Teilnehmerauswahl</label><br>
      <select id="add_user" name="add_user">
        <option value="">neuen Teilnehmer auswählen</option>
        <?php
        foreach ($users as $user) {
          echo '<option value="'.$user['user_id'].'">'.$user['lastname'].', '.$user['firstname'],' ('.$user['class'].$user['shorthand'].')'.'</option>';
        }
      ?>
      </select>
    </div>
    <div>
      <br>
      <input class="greenbutton" type="submit" value="Zur AG hinzufügen">
    </div>
    <div>
      <br>
      <input class="redbutton" type="button" value="abbrechen" onclick="window.location.href='wg_edit.php'">
    </div>
  </form>
<?php
}
 ?>
 <div class="flexbox">
<table>
  <thead>
    <tr>
      <th colspan="4">Eingeschriebene Personen:</th>
    </tr>
    <tr>
      <th>Nachname</th>
      <th>Vorname</th>
      <th>Klasse/Kürzel</th>
      <th>entfernen</th>
    </tr>
  </thead>
  <tbody>
    <?php
    foreach ($participants as $p) {?>
      <tr>
        <td><?php echo $p['lastname'];?></td>
        <td><?php echo $p['firstname'];?></td>
        <td><?php echo $p['class'].$p['shorthand'];?></td>
        <td style="cursor: pointer" onclick="post('wg_edit.php', {del_user: <?php echo $p['user_id'];?>, wg_id: <?php echo $_GET['wid'];?>, title: '<?php echo $_GET['title'];?>'})"><strong>&nbsp;&#10005;&nbsp;</strong></td>
      </tr><?php
    }
     ?>
  </tbody>
</table>
</div>
