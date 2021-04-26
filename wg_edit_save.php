<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if ($account->isStudent()) {
  header('Location: wg_list.php');
  exit;
}

if (!checkOwner($account->getId(),$_POST['wg_id'])) {
  echo 'Sie leiten diese AG nicht.';
  exit;
}

$query = "UPDATE wgs SET title=:title, day=:day, time=:time, duration=:dur, max_num=:max, multiple=:mul, schoolyear=:year, description=:des
          WHERE wg_id=:wid";
// bindValue(), da es sonst beim Einfügen von Bools Probleme mit dem PDO gibt.
try{
  $res = $pdo->prepare($query);
  $res->bindValue(':wid', $_POST['wg_id'], PDO::PARAM_INT);
  $res->bindValue(':title', $_POST['title'], PDO::PARAM_STR);
  $res->bindValue(':day', $_POST['day'], PDO::PARAM_STR);
  $res->bindValue(':time', $_POST['time'], PDO::PARAM_STR);
  $res->bindValue(':dur', $_POST['duration'], PDO::PARAM_INT);
  $res->bindValue(':max', $_POST['max_num'], PDO::PARAM_INT);
  $res->bindValue(':mul', isset($_POST['multiple']), PDO::PARAM_BOOL); // !!!!
  $res->bindValue(':year', $_POST['schoolyear'], PDO::PARAM_STR);
  $res->bindValue(':des', $_POST['description'], PDO::PARAM_STR);
  $res->execute();
  header('Location: wg_edit.php?error=no');
  exit;
}
catch (PDOException $e) {
  header('Location: wg_edit.php?error='.$e->getMessage());
  exit;
}
?>
