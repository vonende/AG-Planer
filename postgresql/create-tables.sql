/*
Als Superuser (postgres) in psql meldet man sich bei der Datenbank ag_manager" an
und importiert mit \i create_tables.sql" diese Datei. Es geschieht dann folgendes:
Alle relevanten Tabellen werden erstellt.
Ein Administratorzugang mit Benutzername "admin" und Passwort "admin" wird erstellt.
Dieser muss später wieder entfernt werden!!!!
Den Nutzern ag_admin und ag_user werden die notwendigen Rechte zugewiesen.
*/

CREATE TABLE IF NOT EXISTS users (
id		SERIAL PRIMARY KEY,
firstname		VARCHAR(30),
lastname	VARCHAR(30),
username	VARCHAR(30) UNIQUE NOT NULL,
password	VARCHAR(255) NOT NULL,
email		VARCHAR(100),
roll		VARCHAR(10),
registrationtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
enabled BOOLEAN NOT NULL
);

CREATE TABLE IF NOT EXISTS teachers (
shorthand VARCHAR(10) NOT NULL UNIQUE,
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS students (
studentnumber VARCHAR(20) NOT NULL UNIQUE,
class VARCHAR(10) NOT NULL,
last_import TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(id) ON DELETE CASCADE
);

/*
CREATE TABLE IF NOT EXISTS others (
user_id INTEGER PRIMARY KEY NOT NULL REFERENCES users(id) ON DELETE CASCADE
);
*/

CREATE TABLE IF NOT EXISTS sessions (
session_id  VARCHAR(255) PRIMARY KEY,
account_id  INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
logintime  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

GRANT ALL PRIVILEGES ON DATABASE ag_manager TO ag_admin;
GRANT USAGE ON SCHEMA public TO ag_admin;
GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA public TO ag_admin;

GRANT CONNECT ON DATABASE ag_manager TO ag_user;
GRANT USAGE ON SCHEMA public TO ag_user;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO ag_user;
