<?php
// Quelle: https://alexwebdevelop.com/user-authentication/
try {
  $pdo = new PDO ('pgsql:host=localhost; dbname=ag_manager',"ag_admin",'kq9Ba8kf61;6]f');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Fehler: Verbindung mit der Datenbank schlug fehl.\n";
  echo "Fehlermeldung: " . htmlspecialchars ($e->getMessage ());
  die();
}

require 'config.php';

function sanitize($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


class Account
{
    private   $id;            // ID des eingeloggten Users
    private   $name;          // Benutzername des eingeloggten Users
    private   $authenticated; // True, wenn sich der Benutzer authentifiziert hat
    private   $roll;
    // Fügt der Datenbank einen neuen Benutzer hinzu.
    public function addAccount(string $name, string $passwd, bool $enabled, string $firstname,
    string $lastname, string $email, string $roll): int
    {
      global $pdo;  // Objekt für Datenbankanbindung

      $name   = sanitize($name);
	    $passwd = sanitize($passwd);
      $email = sanitize($email);
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
    	$name = sanitize($name);
    	$passwd = sanitize($passwd);
      $email = sanitize($email);
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
        $query = 'SELECT user_id FROM teachers WHERE (user_id = :id)';
        $values = array(':id' => $id);
        try
        {
      	   $res = $pdo->prepare($query);
      	   $res->execute($values);
        }
        catch (PDOException $e)
        {
      	   throw new Exception("Datenbankfehler beim Suchen des Lehrerdatensatzes.<br/>".htmlspecialchars($e->getMessage()));
        }
        $row = $res->fetch(PDO::FETCH_ASSOC);
        if (is_array($row))
        {
          $query = 'UPDATE teachers SET shorthand = :sh WHERE user_id = :id';
        } else {
          $query = 'INSERT INTO teachers (user_id, shorthand) VALUES (:id, :sh)';
        }
        $values = array(':id' => $id, ':sh' => $shorthand);
        try
        {
      	   $res = $pdo->prepare($query);
      	   $res->execute($values);
           $query = 'DELETE FROM students WHERE user_id = :id';
           $values = array(':id' => $id);
           $res = $pdo->prepare($query);
      	   $res->execute($values);
        }
        catch (PDOException $e)
        {
      	   throw new Exception("Datenbankfehler beim Aktualisieren des Lehrerdatensatzes.<br/>".htmlspecialchars($e->getMessage()));
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
    public function deleteAccount(int $id)
    {
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

    public function login(string $name, string $passwd): bool
    {
    	global $pdo;
    	$name = sanitize($name);
    	$passwd = sanitize($passwd);
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
    	$query = 'SELECT user_id, username, password, roll FROM users WHERE (username = :name) AND (enabled = TRUE)';
    	$values = array(':name' => $name);
    	try
    	{
    		$res = $pdo->prepare($query);
    		$res->execute($values);
    	}
    	catch (PDOException $e)
    	{
    	   echo "Datenbankfehler beim Login";
         exit;
      }
      $row = $res->fetch(PDO::FETCH_ASSOC);
      if (is_array($row))
      {
         if (password_verify($passwd, $row['password']))
         {
             $this->id = intval($row['user_id'], 10);
             $this->name = $name;
             $this->authenticated = TRUE;
             $this->roll = $row['roll'];
             $this->registerLoginSession();
             return TRUE;
         }
      }
    	return FALSE;
    }

    // registerLoginSession speichert die SessionID und die userID mit Zeitstempel
    // in der Datenbank.
    private function registerLoginSession()
    {
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
    		   echo "Datenbankfehler beim Setzen der Session während des Logins\n";
           echo $e->getMessage(), "\n";
           exit;
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
    public function sessionLogin(): bool
    {
    	global $pdo;
    	if (session_status() == PHP_SESSION_ACTIVE)
    	{
    		/*
    			Query template to look for the current session ID on the account_sessions table.
    			The query also make sure the Session is not older than 7 days
    		*/

    		$query =
    		'SELECT users.user_id, username, roll FROM sessions, users WHERE (sessions.session_id = :sid) '.
    		'AND (sessions.logintime >= (now() - INTERVAL \'7 days\')) AND (sessions.user_id = users.user_id) '.
    		'AND (users.enabled = TRUE)';
    		$values = array(':sid' => session_id());

        try
    		{
    			$res = $pdo->prepare($query);
    			$res->execute($values);
    		}
    		catch (PDOException $e)
    		{
    		   echo "Datenbankfehler beim Sessionlogin: ".$e->getMessage();
           exit;
    		}

    		$row = $res->fetch(PDO::FETCH_ASSOC);
    		if (is_array($row))
    		{
    			$this->id = intval($row['user_id'], 10);
    			$this->name = $row['username'];
    			$this->authenticated = TRUE;
          $this->roll = $row['roll'];
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
    	if (session_status() == PHP_SESSION_ACTIVE)
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
    		   echo 'Datenbankfehler beim Logout';
           die();
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
    public function isAuthenticated(): bool
    {
    	return $this->authenticated;
    }

    // isNameValid prüft, ob der Nutzername gültig ist.
    // Ggf. Sonderzeichen ausschließen?
    public function isNameValid(string $name): bool
    {
      $valid = TRUE;
      return $valid;
    }

    // isPasswdValid prüft das neue Passwort auf Gültigkeit
    public function isPasswdValid(string $passwd): bool
    {
	     $valid = TRUE;
       $len = mb_strlen($passwd);
       if ($len < 5)
       {
         $valid = FALSE;
       }
       return $valid;
    }


    public function getId(): int
    {
      return $this->id;
    }

    public function getRoll(): string
    {
      return $this->roll;
    }



// getIdFromName gibt die ID des Accounts zurück oder NULL, falls dieser nicht existiert
    public function getIdFromName(string $name): ?int
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
    public function isIdValid(int $id): bool
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
