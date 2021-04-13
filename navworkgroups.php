<nav class="navsub">
  <a href="workgroups.php">alle AGs</a>
  <?php
    // Ein Viewer nimmt selbst an keinen AGs teil
    if ($account->getRoll() != 'viewer') {
      echo '<a href="my_workgroups.php">meine AGs</a>';
    }
    // Nur Lehrer und Externe dürfen AGs anlegen und ihre AGs verwalten
    if (!$account->isStudent()) {
      echo '<a href="add_wg.php">anlegen</a>';
      echo '<a href="edit_wgs.php">verwalten</a>';
    }
    ?>
    <a href='presence.php'>Anwesenheit</a>
    <?php
    // Nur Lehrer und Viewer dürfen AG-Teilnahmen aller Schüler einer Klasse einsehen (Datenschutz!!!)
    if ($account->getRoll()=='viewer' || $account->isTeacher()) {
      echo '<a href="classlist.php">Klassenlisten</a>';
    }
   ?>
</nav>
