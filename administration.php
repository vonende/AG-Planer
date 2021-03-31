<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
if (!($account->sessionLogin())) {
	header('Location: authenticate.php');
	exit;
}
try {
  $row = $account->getAccountData();
}
catch (Exception $e){
  echo $e->getMessage();
  exit;
}

// Wenn ein Nicht-Admin versucht, diese Seite aufzurufen, wird er weggeschickt.
if ($row['roll']!='admin') {
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

  <body class="loggedin">
    <?php require 'navbar.php'; ?>
    <div class="content">
      <h2>Verwaltungsbereich für Administratoren</h2>
      <div>
        Wer kein Admin ist kommt hier nicht rein.
      </div>
    </div>
  </body>
</html>
