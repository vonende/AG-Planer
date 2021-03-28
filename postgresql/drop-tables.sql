DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS teachers;
DROP TABLE IF EXISTS students;


DROP TRIGGER IF EXISTS last_userupdate ON users;

DROP TABLE IF EXISTS users;

DROP FUNCTION update_timestamp();
