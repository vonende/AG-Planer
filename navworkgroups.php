<nav class="navsub">
  <a href="workgroups.php">alle AGs</a>
  <?php
    if ($account->getRoll() != 'viewer') {
      ?>
      <a href="my_workgroups.php">meine AGs</a>
      <?php
    }
   ?>
  <?php
    if ($account->getRoll()=='viewer' || $account->isTeacher()) {
      ?>
      <a href="classlist.php">Klassenlisten</a>
      <?php
    }
   ?>
</nav>
