<nav class="navtop">
  <h1><a href="home.php">AG-Manager</a></h1>
  <a href="wg_list.php">AGs</a>
  <?php
    if ($account->getRoll() == 'admin') {
      ?>
      <a href="user_search.php">Verwaltung</a>
      <?php
    }
   ?>
  <a href="profile.php">Profil</a>
  <a href="logout.php">Logout</a>
</nav>
