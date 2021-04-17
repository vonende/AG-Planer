<?php
try {
	$ok = $account->sessionLogin();
}
catch (Exception $e) {
	$ok = false;
}
if (!$ok) {
	header('Location: login.php'); // Keinerlei Ausgabe vor header() erlaubt!!!
	exit;
}
?>
