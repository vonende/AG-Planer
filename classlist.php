<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

// Nur Lehrer und Viewer dürfen die Teilnahmen aller Schüler einer Klasse einsehen.
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
    <script>
      // Die Funktion changeClass() lädt die Teilnahmentabelle mittels AJAX-Request nach.
      function changeClass() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("ajaxresult").innerHTML = this.responseText;
          }
        };
        xhttp.open("GET", "getlist.php?class="+document.getElementById("classes").value, true);
        xhttp.send();
      }
    </script>
	</head>
  <?php

  // Eine Liste aller eingetragenen Klassen wird erstellt
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
      <form method="post">
      	<div  class="flexbox">
          <div>
            <label for="classes">Klassenauswahl</label><br>
            <select id="classes" name="classes" onchange="changeClass()">
              <option value="">Bitte eine Klasse auswählen</option>
              <?php
              foreach ($classes as $class) {
                echo '<option value="'.$class['class'].'">'.$class['class'].'</option>';
              }
            ?>
            </select>
          </div>
        </div>
      </form>
      <div>
        <div id="ajaxresult">
          Es wurde noch keine Klasse ausgewählt.
        </div>
      </div>
    </div>
  </body>
</html>
