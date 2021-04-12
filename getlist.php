<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if (!$account->isTeacher() && $account->getRoll()!='viewer') {
  echo "Fehlende Berechtigung!";
  exit;
}

if (!isset($_GET['class'])) {
  echo "Es wurde keine Klasse ausgewählt.";
  exit;
}

$cl = sanitize($_GET['class']);

$query = "SELECT u.firstname, u.lastname, w.title, w.wg_id AS id, COUNT(e.event_id) AS eventcount
          FROM students AS s, users AS u, wgs AS w, present AS p, events AS e
          WHERE s.class=:cl AND w.schoolyear=:sy AND u.user_id=s.user_id AND s.user_id=p.user_id AND p.event_id=e.event_id AND e.wg_id=w.wg_id
          GROUP BY u.user_id, w.wg_id
          ORDER BY lastname,firstname,title ASC";
$values = array(':cl'=>$cl, ':sy'=>$schoolyear);

try {
  $res = $pdo->prepare($query);
  $res->execute($values);
  $list = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
  echo "Datenbankfehler: ".$e->getMessage();
  exit;
}

if (empty($list)) {
  echo "Keine AG-Teilnehmer gefunden.";
}
?>
<table>
  <thead>
  <tr>
    <th>Nachname</th>
    <th>Vorname</th>
    <th>AG</th>
    <th>Teilnahmen</th>
  </tr>
</thead>
<tbody>
<?php
foreach ($list as $row) {
  echo "<tr>";
  echo '<td>'.$row['lastname'].'</td>';
  echo '<td>'.$row['firstname'].'</td>';
  echo '<td>'.$row['title'].'</td>';
  echo '<td>'.$row['eventcount'].'</td>';
  echo "</tr>";
}

?>
</tbody>
</table>