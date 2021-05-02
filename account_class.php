<?php
// Quelle: https://alexwebdevelop.com/user-authentication/

// Verbindung mit der Datenbank "ag_manager" herstellen
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

// checkOwner(u,w) prüft, ob der user u die AG w leitet
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

// getLeaders(w) liefert zur AG w eine kommagetrennte Liste aller Nachnamen der AG-Leiter als String
function getLeaders(int $wid):string {
  global $pdo;
  $query = 'SELECT lastname FROM lead l, users u WHERE l.wg_id = :wid AND l.user_id = u.user_id ORDER BY lastname ASC';
  $return = '';
  try
  {
    $res = $pdo->prepare($query);
    $res->bindValue(':wid',$wid,PDO::PARAM_INT);
    $res->execute();
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

// Klasse zur Accountverwaltung. Wird von jedem PHP-Script benötigt, um die Rechtmäßigkeit des
// Zugriffs sicherzustellen.
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

    // Legt einen Benutzer als Lehrer fest und setzt das Lehrerkürzel (shorthand)
    // Es wird nicht geprüft, ob die Person bereits als Lehrer oder Schüler
    // registriert ist. Die Funktion darf daher nur beim Erstellen eines
    // Lehreraccounts einmalig aufgerufen werden.
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

    // Legt einen Benutzer als Schüler fest und setzt die Klasse und die
    // Schülernummer.
    // Es wird nicht geprüft, ob die Person bereits als Lehrer oder Schüler
    // registriert ist. Die Funktion darf daher nur beim Erstellen eines
    // Schüleraccounts einmalig aufgerufen werden.
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

    // getAccountData(u) liefert zum Benutzer u ein Array seine Daten zurück.
    // getAccountData(u)['member'] = 'other', 'student' oder 'teacher'
    // getAccountData(u)['shorthand'] enthält das Lehrerkürzel, falls es ein Lehrer ist
    // getAccountData(u)['class'] enthält die Klasse, falls es ein Schüler ist
    // getAccountData(u)['studentnumber'] enthält die Schülernummer, falls es ein Schüler ist
    // getAccountData(u)['user_id'] enthält die ID des Benutzers
    // getAccountData(u)['lastname'] enthält den Nachnamen des Benutzers
    // getAccountData(u)['firstname'] enthält den Vornamen des Benutzers
    // getAccountData(u)['username'] enthält den Loginnamen des Benutzers
    // getAccountData(u)['password'] enthält den Hashwert des Benutzerpassworts
    // getAccountData(u)['enabled'] enthält den Wahrheitswert, ob das Konto aktiv ist
    // getAccountData(u)['email'] enthält die E-Mailadresse des Benutzers
    // getAccountData(u)['roll'] enthält die Benutzersrolle (user, viewer, editor oder admin)
    // getAccountData(u)['registrationtime'] enthält das Registrierungsdatum des Benutzers
    // getAccountData(u)['last_update'] enthält das Datum der letzten Aktualisierung der Benutzerdaten
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

    // deleteAccount(u) löscht einen Benutzer u aus der Tabelle "users"
    // Da weitere Tabellen durch ON DELETE CASCADE mit der Tabelle "users" verbunden sind,
    // werden auch dort alle Einträge mit der entsprechenden user_id bereinigt.
    public function deleteAccount(int $id) {
    	global $pdo;
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
    }

    // login(n,p) prüft, ob der Benutzer u existiert und das Passwort p korrekt ist.
    // Falls ja, wird die Session in der Datenbank registriert, die privaten
    // Variablen der Klasse Account gesetzt und True zurückgegeben.
    // Andernfalls wird ein False retourniert.
    public function login(string $name, string $passwd):bool {
    	global $pdo;
    	$name = stripslashes(htmlspecialchars($name));
    	$passwd = stripslashes(htmlspecialchars($passwd));
    	if (!$this->isNameValid($name))
    	{
    		return FALSE;
    	}
    	if (!$this->isPasswdValid($passwd))
    	{
    		return FALSE;
    	}
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

    // logout() meldet den aktuellen Benutzer ab, indem die Session aus der Datenbank
    // entfernt und anschließend gelöscht wird.
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

    // closeOtherSessions() beendet alle Sessions des Benutzers, falls dieser über
    // mehrere Browser oder Geräte gerade eingeloggt ist.
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
      $valid = TRUE; // aktuell keine Einschränkungen hinsichtlich des Nutzernamens
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

    // getId() liefert "user_id" des aktuellen Benutzers
    public function getId():int
    {
      return $this->id;
    }

    // getRoll() liefert Rolle des aktuellen Benutzers (user, viewer, editor oder admin)
    public function getRoll():string
    {
      return $this->roll;
    }

    // isTeacher() liefert True, falls der aktuelle Benutzer ein Lehrer ist, sonst False
    public function isTeacher():bool
    {
      return $this->isTeacher;
    }

    // isStudent() liefert True, falls der aktuelle Benutzer ein Schüler ist, sonst False
    public function isStudent():bool
    {
      return $this->isStudent;
    }


    // getIdFromName(un) gibt die ID des Accounts zum Nutzernamen un zurück oder NULL,
    // falls dieser nicht existiert
    public function getIdFromName(string $name):?int
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
