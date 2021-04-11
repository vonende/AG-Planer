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

	<body class="loggedin">
		<?php require 'navbar.php'; ?>
		<div class="content">
			<h2>Willkommen beim AG-Manager</h2>
      <div>
				<div>
				Mit dieser App können
				<ul>
						<li>SchülerInnen sich über die aktuell angebotenen AGs informieren und einsehen, an welchen Terminen sie teilgenommen haben,</li>
						<li>AG-LeiterInnen Informationen zu ihrer AG veröffentlichen, Teilnehmer verwalten und deren Anwesenheit dokumentieren,</li>
						<li>Klassenlehrer sich über AG-Teilnahmen ihrer Schüler informieren zwecks Zeugnisvermerk.</li>
				</ul>
			</div>
			</div>
		</div>
	</body>
</html>
