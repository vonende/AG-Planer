<?php
session_start();
require 'account_class.php';

// Wer nicht eingeloggt ist, wird auf die Loginseite verwiesen.
require 'try_sessionlogin.php';

if (!isset($_GET['id'])) {
  echo 'Es wurde keine Id angegeben.';
  exit;
}

/*$query = "SELECT u.user_id, firstname, lastname FROM participate AS p, users AS u
          WHERE p.wg_id=:id AND u.user_id=p.user_id AND p.schoolyear='".$schoolyear."'";*/
  $query = "SELECT * FROM (SELECT u.user_id, firstname, lastname FROM participate AS p, users AS u
            WHERE p.wg_id=:id AND u.user_id=p.user_id AND p.schoolyear='".$schoolyear."') AS one
            LEFT JOIN (SELECT students.user_id, class FROM students) AS two ON one.user_id=two.user_id
            LEFT JOIN (SELECT teachers.user_id, shorthand FROM teachers) AS three ON one.user_id=three.user_id
            ORDER BY lastname, firstname, class";
try {
  $res = $pdo->prepare($query);
  $res->bindValue(':id',$_GET['id'],PDO::PARAM_INT);
  $res->execute();
  $rows = $res->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e){
  echo "Es trat ein Datenbankfehler beim Abrufen der AG auf:<br/>".$e;
  exit;
}

foreach ($rows as $row) {
  $s=$row['class'].$row['shorthand'];
?>
  <div style="width: 200px;margin: 10px 0;">
    <input type="checkbox" id="user<?php echo $row['user_id'];?>" name="user<?php echo $row['user_id'];?>"><br/>
    <label for="user<?php echo $row['user_id'];?>"><?php echo $row['firstname'].' '.$row['lastname'].' ('.$s.')'; ?></label>
  </div>
    <?php
}
?>
