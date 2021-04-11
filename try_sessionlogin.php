<?php
try {
	$ok = $account->sessionLogin();
}
catch (Exception $e) {
	$ok = false;
/*
	?>
	<div class="alert">
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
		<strong><?php echo $e->getMessage();?></strong>
	</div>
	<?php
*/
}

if (!$ok) {
	header('Location: authenticate.php'); // Keinerlei Ausgabe vor header() erlaubt!!!
	exit;
}
?>
