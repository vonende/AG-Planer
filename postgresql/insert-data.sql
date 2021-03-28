/* Anlegen eines Testaccounts namens admin mit dem Passwort admin */
INSERT INTO users (username,password, enabled) VALUES ('admin','$2y$10$g5rEA6MdkIm.YvigvbrDkucvEytY8bAhUtinpv4Sc.SQ0SG88JHke',True);
INSERT INTO teachers (shorthand,user_id) VALUES ('adm',(SELECT id FROM users WHERE username='admin'));
