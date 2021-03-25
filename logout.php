<?php
session_start();
?>
<!DOCTYPE html>
<html>
<?php
require 'account_class.php';

$account->logout();

header("Location: authenticate.php");
exit;
?>
</html>
