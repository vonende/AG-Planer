<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if (!$account->isTeacher() && $account->getRoll()!='viewer') {
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
  $query = 'SELECT DISTINCT class FROM students';
  try {
    $res = $pdo->prepare($query);
    $res->execute();
    $classes = $res->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (PDOExeption $e) {
    ?>
    <div class="alert">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong><?php echo $e->getMessage();?></strong>
    </div>
    <?php
  }
   ?>
  <body class="loggedin">
    <?php require 'navbar.php';?>
    <?php require 'navworkgroups.php';?>
    <div class="content">
      <h2>Klassenlisten</h2>
      <form method="post" action="classlist.php">
      	<div  class="flexbox">
          <div>
            <label for="classes">Bitte eine Klasse auswählen</label>
            <select id="classes" name="classes">
              <?php
              foreach ($classes as $class) {
                echo '<option value="'.$class['class'].'">'.$class['class'].'</option>';
              }
            ?>
            </select>
          </div>
        </div>
      </form>
    </div>
  </body>
</html>
