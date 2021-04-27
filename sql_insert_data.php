<?php

try {
  $pdo = new PDO ('pgsql:host=localhost; dbname=ag_manager',"ag_admin",'kq9Ba8kf61;6]f');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
  echo "Fehler: Verbindung mit der Datenbank schlug fehl.\n";
  echo "Fehlermeldung: " . htmlspecialchars ($e->getMessage ());
  die();
}

require 'config.php';

$query = "SELECT * FROM users ORDER BY user_id";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO users (username,password,firstname,lastname,enabled,roll) <br/>
VALUES
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  if ($r['enabled']==1) {$en="TRUE";} else {$en="FALSE";}
  echo "('".$r['username']."','".$r['password']."','".$r['firstname']."','".$r['lastname']."',".$en;
  echo ",'".$r['roll']."')";
}
echo ";<br/><br/>";

$query = "SELECT * FROM wgs ORDER BY wg_id";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO wgs (title, day, time, duration, max_num, multiple, schoolyear, description) <br/>
VALUES
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  if ($r['multiple']==1) {$mu = "TRUE";} else {$mu="FALSE";}
  echo "('".$r['title']."','".$r['day']."','".$r['time']."',";
  echo $r['duration'].",".$r['max_num'].",".$mu.",'".$r['schoolyear']."','";
  echo $r['description']."')";
}
echo ";<br/><br/>";


$query = "SELECT * FROM teachers";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO teachers (user_id, shorthand) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "(".$r['user_id'].",'".$r['shorthand']."')";
}
echo ";<br/><br/>";


$query = "SELECT * FROM students";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO students (class, studentnumber, user_id) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "('".$r['class']."','".$r['studentnumber']."',".$r['user_id'].")";
}
echo ";<br/><br/>";

$query = "SELECT * FROM lead";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO lead (user_id, wg_id) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "(".$r['user_id'].",".$r['wg_id'].")";
}
echo ";<br/><br/>";


$query = "SELECT * FROM events ORDER BY event_id";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO events (date, time, duration, annotation, wg_id) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "('".$r['date']."','".$r['time']."',".$r['duration'].",'";
  echo $r['annotation']."',".$r['wg_id'].")";
}
echo ";<br/><br/>";

$query = "SELECT * FROM participate";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO participate (user_id,wg_id,schoolyear) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "(".$r['user_id'].",".$r['wg_id'].",'".$r['schoolyear']."')";
}
echo ";<br/><br/>";


$query = "SELECT * FROM present";
$res=$pdo->prepare($query);
$res->execute();
$rows = $res->fetchAll(PDO::FETCH_ASSOC);
?>
INSERT INTO present (event_id,user_id) VALUES <br/>
<?php
$first = true;
foreach ($rows as $r) {
  if ($first) {$first=false;} else {echo ",<br/>"; }
  echo "(".$r['event_id'].",".$r['user_id'].")";
}
?>
;
