<?php
try {
	$ok = $account->sessionLogin();
}
catch (Exception $e) {
	$ok = false;
}
if (!$ok) {
	header('Location: authenticate.php?error=Anmeldefehler'); // Keinerlei Ausgabe vor header() erlaubt!!!
	exit;
}
?>
