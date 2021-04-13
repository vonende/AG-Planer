<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>AG-Manager</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
  <?php require 'navbar.php'; ?>
  <?php require 'navworkgroups.php' ?>
	<body class="loggedin">
    <div class="content">
      <h2>Überschrift</h2>
      <form method="post">
        <div class="flexbox">
          <div>
            <label for="idname">Labeltext</label><br/>
            <input type="text" id="idname" placeholder="Text eingeben...">
          </div>
        </div>
      </form>
    </div>
  </body>
</html>
