DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS teachers;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS lead;
DROP TABLE IF EXISTS present;
DROP TABLE IF EXISTS participate;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS wgs;
DROP TRIGGER IF EXISTS last_userupdate ON users;
DROP TABLE IF EXISTS users;
DROP FUNCTION update_timestamp();
DROP DOMAIN schoolyear;
DROP TYPE weekday;