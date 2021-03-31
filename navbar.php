<nav class="navtop">
  <h1><a href="home.php">AG-Manager</a></h1>
  <a href="workgroups.php">AGs</a>
  <?php
  try {
    $row = $account->getAccountData();
    if ($row['roll'] == 'admin') {
      ?>
      <a href="administration.php">Verwaltung</a>
      <?php
    }
  }
  catch (Exception $e) {
    echo htmlspecialchars($e->getMessage());
  }
   ?>
  <a href="profile.php">Profil</a>
  <a href="logout.php">Logout</a>
</nav>
