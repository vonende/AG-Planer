<nav class="navsub">
  <a href="wg_list.php">alle AGs</a>
  <?php
    // Ein Viewer nimmt selbst an keinen AGs teil
    if ($account->getRoll() != 'viewer') {
      echo '<a href="wg_my.php">meine AGs</a>';
    }

    // Viewer dürfen keine AGs editieren
    if ($account->getRoll()!='viewer') { ?>
      <a href="wg_edit.php">verwalten</a>
      <?php
    }

    // Schüler und Viewer dürfen keine eigenen AGs anlegen
    if (!$account->isStudent() && $account->getRoll()!='viewer') { ?>
      <a href="wg_add.php">anlegen</a>
      <?php
    }

    // Nur Lehrer und Viewer dürfen AG-Teilnahmen aller Schüler einer Klasse einsehen (Datenschutz!!!)
    if ($account->getRoll()=='viewer' || $account->isTeacher()) {
      echo '<a href="classlist.php">Klassenlisten</a>';
    }
   ?>
</nav>
