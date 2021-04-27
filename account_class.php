<?php
// Quelle: https://alexwebdevelop.com/user-authentication/
try {
  $pdo = new PDO ('pgsql:host=localhost; dbname=ag_manager',"ag_admin",'kq9Ba8kf61;6]f');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
  echo "Fehler: Verbindung mit der Datenbank schlug fehl.\n";
  echo "Fehlermeldung: " . htmlspecialchars ($e->getMessage ());
  die();
}

require 'config.php';

function checkOwner(int $uid,int $wid):bool {
  global $pdo;
  try {
    $query = "SELECT count(user_id) FROM lead WHERE user_id = :uid AND wg_id = :wid";
    $res = $pdo->prepare($query);
    $res->bindValue(":wid",$wid,PDO::PARAM_INT);
    $res->bindValue(":uid",$uid,PDO::PARAM_INT);
    $res->execute();
    $ct = $res->fetch(PDO::FETCH_ASSOC);
    if ($ct['count']>0) { return true; }
  }
  catch (PDOException $e) {
    throw new Exception("Datenbankfehler bei Überprüfung der AG-Leitung: <br/>".$e->getMessage());
  }
  return false;
}

function getLeaders(int $id) {
  global $pdo;
  $query = 'SELECT users.lastname FROM lead, users WHERE lead.wg_id = :id AND lead.user_id = users.user_id ORDER BY lastname ASC';
  $values = array(':id' => $id);
  $leaders = array();
  $return = '';
  try
  {
    $res = $pdo->prepare($query);
    $res->execute($values);
  	$leaders = $res->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (PDOException $e)
  {
    echo 'Datenbankfehler bei Abfrage der AG-Leiter.';
  }
  foreach ($leaders as $leader) {
    if ($return!='') {$return = $return.', '.$leader['lastname'];} else {$return = $leader['lastname'];}
  }
  return $return;
}

class Account
{
    private   $id;            // ID des eingeloggten Users
    private   $name;          // Benutzername des eingeloggten Users
    private   $authenticated; // True, wenn sich der Benutzer authentifiziert hat
    private   $roll;          // admin, editor, viewer oder user
    private   $isTeacher;     // True, wenn der Benutzer ein Lehrer ist
    private   $isStudent;     // True, wenn der Benutzer ein Schüler ist

    // Fügt der Datenbank einen neuen Benutzer hinzu.
    public function addAccount(string $name, string $passwd, bool $enabled, string $firstname,
    string $lastname, string $email, string $roll)
    {
      global $pdo;  // Objekt für Datenbankanbindung

      $name   = stripslashes(htmlspecialchars($name));
	    $passwd = stripslashes(htmlspecialchars($passwd));
      $email = stripslashes(htmlspecialchars($email));
      $firstname = stripslashes(htmlspecialchars($firstname));
      $lastname = stripslashes(htmlspecialchars($lastname));

      if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email!='') {
        throw new Exception("Ungültiges E-Mail-Format");
      }

      if (!($roll=='user' || $roll=='admin' || $roll=='viewer' || $roll=='editor')) {
        throw new Exception('Ungültige Rolle');
      }

	    if (!$this->isNameValid($name))
	    {
	       throw new Exception('Ungültiger Benutzername');
	    }

	    if (!$this->isPasswdValid($passwd))
	    {
		     throw new Exception('Ungültiges Passwort');
	    }

	    if (!is_null($this->getIdFromName($name)))
	    {
		     throw new Exception('Der Benutzename ist bereits vergeben');
	    }

	    $query = 'INSERT INTO users (username, password, firstname, lastname, email, roll, enabled) VALUES (:na, :pw, :fn, :ln, :em, :ro, :en)';

	    $hash = password_hash($passwd, PASSWORD_DEFAULT);

      $values = array(':na' => $name, ':pw' => $hash, ':fn'=>$firstname, ':ln'=>$lastname, ':em'=>$email, ':ro'=>$roll, ':en'=>$enabled);

      try
      {
        $res = $pdo->prepare($query);
        $res->execute($values);
      }
      catch (PDOException $e)
      {
        throw new Exception('Datenbankfehler beim Hinzufügen des Accounts: '.$e->getMessage());
      }

      return $pdo->lastInsertId();
    }

    // editAccount ändert die Daten für einen bestehenden Account
    // Es würd nur auf Gültigkeit hinsichtlich der Syntax geprüft.
    public function editAccount(int $id, string $name, string $passwd, bool $enabled, string $firstname,
    string $lastname, string $email, string $roll)
    {
    	global $pdo;
    	$name = stripslashes(htmlspecialchars($name));
    	$passwd = stripslashes(htmlspecialchars($passwd));
      $email = stripslashes(htmlspecialchars($email));
      $firstname = stripslashes(htmlspecialchars($firstname));
      $lastname = stripslashes(htmlspecialchars($lastname));

    	if (!$this->isIdValid($id)) {
    		throw new Exception('Ungültige Benutzer-ID');
    	}

    	if (!$this->isNameValid($name)) {
    		throw new Exception('Ungültiger Benutzername');
    	}

    	if (!$this->isPasswdValid($passwd) && $passwd!='') {
    		throw new Exception('Ungültiges Passwort (mind. 8 Zeichen)');
    	}

    	$idFromName = $this->getIdFromName($name);

    	if (!is_null($idFromName) && ($idFromName != $id)) {
    		throw new Exception('Der Benutzername ist schon vergeben');
    	}

      // Lässt man das Passwortfeld leer, so wird das alte Passwort beibehalten.
      // Der Hash in der Datenbank bleibt dann unverändert.
      if ($passwd=='') {
        $query = 'UPDATE users SET username = :na, enabled = :en, firstname = :fn, lastname = :ln, email = :email, roll = :roll WHERE user_id = :id';
      	$values = array(':na' => $name, ':en' => $enabled ? 'TRUE' : 'FALSE', ':id' => $id, ':fn' => $firstname, ':ln' => $lastname, ':email' => $email, ':roll'=>$roll);
      } else {
        $query = 'UPDATE users SET username = :na, password = :pwd, enabled = :en, firstname = :fn, lastname = :ln, email = :email, roll = :roll WHERE user_id = :id';
      	$hash = password_hash($passwd, PASSWORD_DEFAULT);
      	$values = array(':na' => $name, ':pwd' => $hash, ':en' => $enabled ? 'TRUE' : 'FALSE', ':id' => $id, ':fn' => $firstname, ':ln' => $lastname, ':email' => $email, ':roll'=>$roll);
      }

    	try {
    		$res = $pdo->prepare($query);
    		$res->execute($values);
    	}
    	catch (PDOException $e) {
    	   throw new Exception('Datenbankfehler beim Ändern der Benutzerdaten');
    	}
    }

    public function setTeacher(int $id, string $shorthand){
      global $pdo;
      if ($this->authenticated) {
        $shorthand = stripslashes(htmlspecialchars($shorthand));
        $query = 'INSERT INTO teachers (user_id, shorthand) VALUES (:id, :sh)';
        $values = array(':id' => $id, ':sh' => $shorthand);
        try {
          $res = $pdo->prepare($query);
          $res->execute($values);
        }
        catch (PDOException $e) {
          throw new Exception("Datenbankfehler beim Einfügen des Lehrerkürzels.");
        }
      }
    }

    public function setStudent(int $id, string $class, string $number){
      global $pdo;
      if ($this->authenticated) {
        $class  = stripslashes(htmlspecialchars($class));
        $number = stripslashes(htmlspecialchars($number));
        $query = 'INSERT INTO students (user_id, class, studentnumber) VALUES (:id, :cl, :sn)';
        $values = array(':id' => $id, ':cl' => $class, ':sn' => $number);
        try {
          $res = $pdo->prepare($query);
          $res->execute($values);
        }
        catch (PDOException $e) {
          throw new Exception("Datenbankfehler beim Einfügen der Schülernummer und der Klasse.");
        }
      }
    }

    public function getAccountData(int $id) {
      global $pdo;
      if ($this->authenticated) {
        $query = 'SELECT * FROM users WHERE (user_id = :id)';
        $values = array(':id' => $id);
        try
        {
      	   $res = $pdo->prepare($query);
      	   $res->execute($values);
        }
        catch (PDOException $e)
        {
      	   throw new Exception("Datenbankfehler beim Aufruf des Profils.<br/>".htmlspecialchars($e->getMessage()));
        }
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row))
        {
      	   throw new Exception("Datensatz in der Benutzertabelle konnte nicht gefunden werden.");
        }
        $row['member'] = 'other';
        $query = 'SELECT * FROM students WHERE (user_id = :id)';
        $values = array(':id' => $id);
        try
        {
      	   $res = $pdo->prepare($query);
      	   $res->execute($values);
        }
        catch (PDOException $e)
        {
      	   throw new Exception("Datenbankfehler beim Aufruf des Profils. </br>".htmlspecialchars ($e->getMessage ()));
        }
        $row2 = $res->fetch(PDO::FETCH_ASSOC);
        if (is_array($row2))
        {
          $row['member'] = 'student';
          $row['studentnumber'] = $row2['studentnumber'];
          $row['class'] = $row2['class'];
        }
        $query = 'SELECT * FROM teachers WHERE (user_id = :id)';
        $values = array(':id' => $id);
        try
        {
      	   $res = $pdo->prepare($query);
      	   $res->execute($values);
        }
        catch (PDOException $e)
        {
      	   throw new Exception("Datenbankfehler beim Aufruf des Profils. </br>".htmlspecialchars ($e->getMessage ()));
        }
        $row2 = $res->fetch(PDO::FETCH_ASSOC);
        if (is_array($row2))
        {
          $row['member'] = 'teacher';
          $row['shorthand'] = $row2['shorthand'];
        }
      } else {
        header('Location: authenticate.php');
      	exit;
      }
      return $row;
    }

    //deleteAccount löscht einen Benutzer aus der Tabelle users und aus der Tabelle sessions
    public function deleteAccount(int $id) {
    	global $pdo;
    	if (!$this->isIdValid($id))
    	{
    		throw new Exception('Unbekannte Benutzer-ID');
    	}
    	$query = 'DELETE FROM users WHERE user_id = :id';
    	$values = array(':id' => $id);
    	try
    	{
    		$res = $pdo->prepare($query);
    		$res->execute($values);
    	}
    	catch (PDOException $e)
    	{
    	   throw new Exception('Datenbankfehler beim Löschen des Benutzers');
    	}
      /* Unnötig, da Fremdschlüssel in Datenbank mittels ON DELETE CASCADE definiert wurde
    	$query = 'DELETE FROM sessions WHERE (user_id = :id)';
    	$values = array(':id' => $id);
    	try
    	{
    		$res = $pdo->prepare($query);
    		$res->execute($values);
    	}
    	catch (PDOException $e)
    	{
    	   throw new Exception('Datenbankfehler beim Löschen des gespeicherten Sessions.');
    	}
      */
    }

    public function login(string $name, string $passwd) {
    	global $pdo;
    	$name = stripslashes(htmlspecialchars($name));
    	$passwd = stripslashes(htmlspecialchars($passwd));
    	if (!$this->isNameValid($name))
    	{
    		return FALSE;
    	}
      /*
    	if (!$this->isPasswdValid($passwd))
    	{
    		return FALSE;
    	}
      */
    	$query = 'SELECT user_id, username, password, roll, enabled FROM users WHERE (username = :name)';
    	$values = array(':name' => $name);
    	try
    	{
    		$res = $pdo->prepare($query);
    		$res->execute($values);
    	}
    	catch (PDOException $e)
    	{
    	   throw new Exception("Datenbankfehler beim Login");
         exit;
      }

      $row = $res->fetch(PDO::FETCH_ASSOC);
      if (is_array($row))
      {
        if (!$row['enabled']) {
          throw new Exception('Der Account ist gesperrt. Bitte an einen Administrator wenden.');
        }
         if (password_verify($passwd, $row['password']))
         {
             $this->id = intval($row['user_id'], 10);
             $this->name = $name;
             $this->authenticated = TRUE;
             $this->roll = $row['roll'];
             $this->registerLoginSession();
             $query = 'SELECT * FROM teachers WHERE user_id = :id';
           	 $values = array(':id' => $this->id);
           	 try {
            		$res = $pdo->prepare($query);
            		$res->execute($values);
           	 }
           	 catch (PDOException $e) {
           	   throw new Exception("Datenbankfehler beim Login <br>".$e->getMessage());
               exit;
             }
             $row = $res->fetch(PDO::FETCH_ASSOC);
             $this->isTeacher = is_array($row)?true:false;

             $query = 'SELECT * FROM students WHERE user_id = :id';
           	 $values = array(':id' => $this->id);
           	 try {
            		$res = $pdo->prepare($query);
            		$res->execute($values);
           	 }
           	 catch (PDOException $e) {
           	   throw new Exception("Datenbankfehler beim Login <br>".$e->getMessage());
               exit;
             }
             $row = $res->fetch(PDO::FETCH_ASSOC);
             $this->isStudent = is_array($row)?true:false;

             return TRUE;
         }
      }
    	return FALSE;
    }

    // registerLoginSession speichert die SessionID und die userID mit Zeitstempel
    // in der Datenbank.
    private function registerLoginSession() {
    	global $pdo;

    	if (session_status() == PHP_SESSION_ACTIVE)
    	{
    		/* 	Use a REPLACE statement to:
    			- insert a new row with the session id, if it doesn't exist, or...
    			- update the row having the session id, if it does exist.
    		*/
    		$query = 'INSERT INTO sessions (session_id, user_id, logintime) VALUES (:sid, :accountId, now())
                  ON CONFLICT (session_id) DO UPDATE SET user_id = :accountId, logintime = now()';
    		$values = array(':sid' => session_id(), ':accountId' => $this->id);
    		try
    		{
    			$res = $pdo->prepare($query);
    			$res->execute($values);
    		}
    		catch (PDOException $e)
    		{
    		   throw new Exception("Datenbankfehler beim Setzen der Session während des Logins\n".$e->getMessage());
    		}
    	}
    }

    // sessionsLogin ermöglicht ein Login mittels einer gültigen SessionID,
    // welche nicht älter als 7 Tage ist, ohne dass Username und Passwort
    // eingegeben werden müssen.
    // Voraussetzung ist, dass serverseitig in der php.ini die Session-Cookie-Lifetime
    // hoch genug gewählt wurde. Diese muss in Sekunden angegeben werden:
    // session.cookie_lifetime = 604800
    // Ein Wert von 0 bedeutet, dass das Cookie beim Schließen des Browsers gelöscht wird.
    public function sessionLogin()
    {
    	global $pdo;
    	if (session_status() == PHP_SESSION_ACTIVE)
    	{
    		/*
    			Query template to look for the current session ID on the account_sessions table.
    			The query also make sure the Session is not older than 7 days
    		*/

    		$query = "SELECT users.user_id, username, roll, enabled
                  FROM sessions, users
                  WHERE (sessions.session_id = :sid)
    		          AND (sessions.logintime >= (now() - INTERVAL '7 days'))
                  AND (sessions.user_id = users.user_id)";
        try	{
    			$res = $pdo->prepare($query);
          $res->bindValue(':sid',session_id(),PDO::PARAM_STR);
    			$res->execute();
    		}
    		catch (PDOException $e)	{
    		   throw new Exception("Datenbankfehler beim Sessionlogin: ".$e->getMessage());
    		}

    		$row = $res->fetch(PDO::FETCH_ASSOC);
    		if (is_array($row))
    		{
          if (!$row['enabled']){
            throw new Exception('Der Account ist gesperrt. Bitte an einen Administrator wenden.');
          }
    			$this->id = intval($row['user_id'], 10);
    			$this->name = $row['username'];
    			$this->authenticated = TRUE;
          $this->roll = $row['roll'];
          $query = 'SELECT * FROM teachers WHERE user_id = :id';
          $values = array(':id' => $this->id);
          try
          {
             $res = $pdo->prepare($query);
             $res->execute($values);
          }
          catch (PDOException $e)
          {
            throw new Exception("Datenbankfehler beim Login <br>".$e->getMessage());
            exit;
          }
          $row = $res->fetch(PDO::FETCH_ASSOC);
          $this->isTeacher = is_array($row)?true:false;

          $query = 'SELECT   * FROM students WHERE user_id = :id';
          $values = array(':id' => $this->id);
          try
          {
             $res = $pdo->prepare($query);
             $res->execute($values);
          }
          catch (PDOException $e)
          {
            throw new Exception("Datenbankfehler beim Login <br>".$e->getMessage());
            exit;
          }
          $row = $res->fetch(PDO::FETCH_ASSOC);
          $this->isStudent = is_array($row)?true:false;
return TRUE;
    		}
    	}

    	/* If we are here, the authentication failed */
    	return FALSE;
    }

    public function logout()
    {
    	global $pdo;
    	if (is_null($this->id))
    	{
    		return;
    	}
    	$this->id = NULL;
    	$this->name = NULL;
    	$this->authenticated = FALSE;
    	if (session_status() === PHP_SESSION_ACTIVE)
    	{
    		$query = 'DELETE FROM sessions WHERE (session_id = :sid)';
    		$values = array(':sid' => session_id());
        session_unset();
        session_destroy();
    		try
    		{
    			$res = $pdo->prepare($query);
    			$res->execute($values);
    		}
    		catch (PDOException $e)
    		{
    		   throw new Exception('Datenbankfehler beim Logout <br>'.$e->getMessage());
    		}
    	}
    }

    public function closeOtherSessions()
    {
    	global $pdo;
    	if (is_null($this->id))
    	{
    		return;
    	}
    	if (session_status() == PHP_SESSION_ACTIVE)
    	{
    		$query = 'DELETE FROM sessions WHERE (session_id != :sid) AND (user_id = :user_id)';
    		$values = array(':sid' => session_id(), ':user_id' => $this->id);
    		try
    		{
    			$res = $pdo->prepare($query);
    			$res->execute($values);
    		}
    		catch (PDOException $e)
    		{
    		   throw new Exception('Datenbankfehler beim Schließen aller weiteren Sessions');
    		}
    	}
    }

    // isAuthenticated gibt den Wert von authenticated des Objekts zurück
    public function isAuthenticated()
    {
    	return $this->authenticated;
    }

    // isNameValid prüft, ob der Nutzername gültig ist.
    // Ggf. Sonderzeichen ausschließen?
    public function isNameValid(string $name)
    {
      $valid = TRUE;
      return $valid;
    }

    // isPasswdValid prüft das neue Passwort auf Gültigkeit
    public function isPasswdValid(string $passwd)
    {
	     $valid = TRUE;
       $len = mb_strlen($passwd);
       if ($len < 5)
       {
         $valid = FALSE;
       }
       return $valid;
    }


    public function getId()
    {
      return $this->id;
    }

    public function getRoll()
    {
      return $this->roll;
    }

    public function isTeacher()
    {
      return $this->isTeacher;
    }

    public function isStudent()
    {
      return $this->isStudent;
    }


// getIdFromName gibt die ID des Accounts zurück oder NULL, falls dieser nicht existiert
    public function getIdFromName(string $name)
    {
       global $pdo;
       if (!$this->isNameValid($name))
       {
         throw new Exception('Ungültiger Benutzername');
       }
       $id = NULL;
	     $query = 'SELECT user_id FROM users WHERE (username = :name)';
	     $values = array(':name' => $name);
       try
       {
         $res = $pdo->prepare($query);
         $res->execute($values);
       }
       catch (PDOException $e)
       {
         throw new Exception('Abfragefehler der ID in der Datenbank');
       }
       $row = $res->fetch(PDO::FETCH_ASSOC);
       if (is_array($row))
       {
         $id = intval($row['user_id'], 10);
       }
       return $id;
    }

    // isIdValid prüft die formale Gültigkeit der ID
    public function isIdValid(int $id)
    {
      $valid = TRUE;
      return $valid;
    }

    // __construct ist der Konstruktor des PHP Daten Objektes
    public function __construct()
    {
      $this->id = NULL;
      $this->name = NULL;
      $this->authenticated = FALSE;
      $this->roll = NULL;
    }

    public function __destruct()
    {

    }
}

$account = new Account();

 ?>
