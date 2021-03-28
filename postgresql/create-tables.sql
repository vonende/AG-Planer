/*
Als Superuser (postgres) in psql meldet man sich bei der Datenbank ag_manager" an
und importiert mit \i create_tables.sql" diese Datei. Es geschieht dann folgendes:
Alle relevanten Tabellen werden erstellt.
Ein Administratorzugang mit Benutzername "admin" und Passwort "admin" wird erstellt.
Dieser muss später wieder entfernt werden!!!!
Den Nutzern ag_admin und ag_user werden die notwendigen Rechte zugewiesen.
*/

CREATE TABLE IF NOT EXISTS users (
user_id		SERIAL PRIMARY KEY,
firstname		VARCHAR(30),
lastname	VARCHAR(30),
username	VARCHAR(30) UNIQUE NOT NULL,
password	VARCHAR(255) NOT NULL,
email		VARCHAR(100),
roll		VARCHAR(10),
registrationtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
enabled BOOLEAN NOT NULL,
last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE OR REPLACE FUNCTION update_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.last_update = now();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER last_userupdate BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE update_timestamp();

CREATE TABLE IF NOT EXISTS teachers (
shorthand VARCHAR(10) NOT NULL UNIQUE,
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS students (
studentnumber VARCHAR(20) NOT NULL UNIQUE,
class VARCHAR(10) NOT NULL,
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(user_id) ON DELETE CASCADE
);

/*
CREATE TABLE IF NOT EXISTS others (
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(id) ON DELETE CASCADE
);
*/

CREATE TABLE IF NOT EXISTS sessions (
session_id  VARCHAR(255) PRIMARY KEY,
user_id  INTEGER NOT NULL REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
logintime  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS wgs (
  wg_id SERIAL PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  day INTEGER DEFAULT 0 CHECK (day>=0 AND day<=7),
  time TIME,
  duration INTEGER DEFAULT 45 CHECK(duration>=0),
  max_num INTEGER DEFAULT 0 CHECK (max_num>=0),
  multiple BOOLEAN DEFAULT TRUE,
  schoolyear VARCHAR(7) CHECK (schoolyear SIMILAR TO '[0-9]{4}/[0-9]{2}'),
  description TEXT
);

CREATE TABLE IF NOT EXISTS events (
  event_id SERIAL PRIMARY KEY,
  date DATE DEFAULT CURRENT_DATE,
  time TIME DEFAULT CURRENT_TIME,
  duration INTEGER DEFAULT 45 CHECK (duration>=0),
  annotation TEXT,
  wg_id INTEGER NOT NULL REFERENCES wgs(wg_id) ON DELETE CASCADE ON UPDATE CASCADE
);

GRANT ALL PRIVILEGES ON DATABASE ag_manager TO ag_admin;
GRANT USAGE ON SCHEMA public TO ag_admin;
GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA public TO ag_admin;

GRANT CONNECT ON DATABASE ag_manager TO ag_user;
GRANT USAGE ON SCHEMA public TO ag_user;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO ag_user;
