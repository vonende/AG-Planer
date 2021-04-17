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
<body>
<?php
try {
  $account->logout();
  ?>
  <div class="confirm">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong>Logout war erfolgreich.</strong>
  </div>
  <?php
}
catch (Exception $e) {
  ?>
  <div class="alert">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><?php echo $e->getMessage();?></strong>
  </div>
  <?php
}
?>
<div class="content">
  <div>
    <a href="login.php">Zur Loginseite wechseln...</a>
  </div>
</div>
</body>
</html>
