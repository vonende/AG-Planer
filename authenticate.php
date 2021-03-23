<?php
session_start();

require 'account_class.php';
if ( isset($_POST['user'], $_POST['pwd']) ) {
  if ($account->login($_POST["user"], $_POST["pwd"]))
  {
    header("Location: home.php");
    exit;
  }
}
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
 		<link rel="stylesheet" href="style.css" type="text/css">
     <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
   </head>

   <body>
     <?php
       if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
         echo '<div class="alert">';
         echo "Fehlerhafte Eingabedaten <br/>";
         echo '</div>';
       }
      ?>

       <div class="login">
       <h1>AG-Manager (Anmeldung) </h1>

       <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" >
         <label for="username">Benutzername</label>
         <input type="text" name="user" id="username" placeholder="Benutzername" required>

         <label for="password">Passwort</label>
         <input type="password" name="pwd" id="password" placeholder="Passwort" required>

         <input type="submit" value="anmelden">
       </form>
     </div>

   </body>
 </html>
