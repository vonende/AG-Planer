/* Anlegen eines Testaccounts namens admin mit dem Passwort admin */
INSERT INTO users (username,password, enabled) VALUES ('admin','$2y$10$g5rEA6MdkIm.YvigvbrDkucvEytY8bAhUtinpv4Sc.SQ0SG88JHke',True);
INSERT INTO teachers (shorthand,user_id) VALUES ('adm',(SELECT user_id FROM users WHERE username='admin'));


/* AGs anlegen */
INSERT INTO wgs (title, day, time, duration, max_num, multiple, schoolyear, description)
VALUES
('Nähen','Dienstag','15:00',90,10,FALSE,'2019/20','Wir nähen alle einen Teddybären.'),
('Nähen','Montag','15:00',90,10,FALSE,'2020/21','Wir nähen alle einen Teddybären.'),
('Schach','Montag','15:00',90,10,TRUE,'2020/21','Klassisches Schachspiel für jung und alt.'),
('Basketball','Mittwoch','16:00',90,20,TRUE,'2020/21','Was gibt es da zu erklären?');


/* Termine anlegen */
INSERT INTO events (date, time, duration, annotation, wg_id)
VALUES
  ('2021-03-22','15:00:00',90,'Gemeinsames Kennenlernen',1),
  ('2021-03-28','15:00:00',90,'Schnittmuster vorbereiten',1),
  ('2021-04-05','15:00:00',60,'Stoff ausschneiden',1);
