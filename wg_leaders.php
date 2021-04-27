<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if ($account->isStudent()) {
  echo 'Sie haben nicht die erforderliche Berechtigung AG-Leiter festzulegen.';
  exit;
}

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

// Eine Liste aller Personen erstellen, die noch keine AG-Leiter sind.
$query = "SELECT * FROM
           (SELECT user_id, firstname, lastname
            FROM users
            EXCEPT
            SELECT u.user_id, firstname, lastname
            FROM users u, lead l
            WHERE l.wg_id = :wid AND u.user_id=l.user_id) AS noleader
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

// Eine Liste aller AG-Leiter ermitteln
$query = "SELECT * FROM (
            SELECT user_id, firstname, lastname
            FROM users NATURAL JOIN lead l
            WHERE l.wg_id = :wid
            EXCEPT
            SELECT user_id, firstname, lastname
            FROM users WHERE user_id = :uid) AS one
          NATURAL LEFT JOIN students
          NATURAL LEFT JOIN teachers
          ORDER BY lastname, firstname, class, shorthand";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':wid',$_GET['wid'],PDO::PARAM_INT);
  $res->bindValue(':uid',$account->getId(),PDO::PARAM_INT);
  $res->execute();
  $leaders = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Laden der Benutzerliste auf:<br/>".$e;
  exit;
}?>

<p style='width: 100%'>
  <strong><?php echo $_GET['title'];?> - AG-Leiter festlegen</strong>
</p>
<p style='width: 100%'></p>


<form method="post" action="wg_edit.php" class="flexbox">
  <input type="hidden" name="wg_id" value="<?php echo $_GET['wid'] ?>">
  <input type="hidden" name="title" value="<?php echo $_GET['title'] ?>">
  <div>
    <label for="add_leader">Personenauswahl</label><br>
    <select id="add_leader" name="add_leader">
      <option value="">neuen AG-Leiter auswählen</option>
      <?php
      foreach ($users as $user) {
        echo '<option value="'.$user['user_id'].'">'.$user['lastname'].', '.$user['firstname'],' ('.$user['class'].$user['shorthand'].')'.'</option>';
      }
    ?>
    </select>
  </div>
  <div>
    <br>
    <input class="greenbutton" type="submit" value="als AG-Leiter festlegen">
  </div>
  <div>
    <br>
    <input class="redbutton" type="button" value="abbrechen" onclick="window.location.href='wg_edit.php'">
  </div>
</form>
<p>Sie selbst werden in der folgenden Tabelle nicht angezeigt.</p>
<div class="flexbox">
<table>
  <thead>
    <tr>
      <th colspan="4">Existierende AG-LeiterInnen:</th>
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
    foreach ($leaders as $l) {?>
      <tr>
        <td><?php echo $l['lastname'];?></td>
        <td><?php echo $l['firstname'];?></td>
        <td><?php echo $l['class'].$l['shorthand'];?></td>
        <td style="cursor: pointer" onclick="post('wg_edit.php', {del_leader: <?php echo $l['user_id'];?>, wg_id: <?php echo $_GET['wid'];?>, title: '<?php echo $_GET['title'];?>'})"><strong>&nbsp;&#10005;&nbsp;</strong></td>
      </tr><?php
    }
     ?>
  </tbody>
</table>
</div>
