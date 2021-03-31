/* Anlegen eines Testaccounts namens admin mit dem Passwort admin */
INSERT INTO users (username,password, enabled, roll) VALUES
('admin','$2y$10$g5rEA6MdkIm.YvigvbrDkucvEytY8bAhUtinpv4Sc.SQ0SG88JHke',True,'admin');
INSERT INTO teachers (shorthand,user_id) VALUES
('adm',(SELECT user_id FROM users WHERE username='admin'));


/* AGs anlegen */
INSERT INTO wgs (title, day, time, duration, max_num, multiple, schoolyear, description)
VALUES
('Nähen','Dienstag','15:00',90,10,FALSE,'2019/20','Wir nähen alle einen Teddybären.'),
('Nähen','Montag','15:00',90,10,FALSE,'2020/21','Wir nähen alle einen Teddybären.'),
('Schach','Montag','15:00',90,10,TRUE,'2020/21','Klassisches Schachspiel für jung und alt. Beginn: 29. März 2021'),
('Karate','Mittwoch','15:30',90,0,TRUE,'2020/21','Shotokan-Karate für die persönliche Entwicklung.'),
('Technik','Freitag','14:00',90,6,TRUE,'2020/21','Auf- und Abbau der Theatertechnik. Betreuung während der Proben und Aufführungen.'),
('Chemie','Dienstag','16:00',90,8,TRUE,'2020/21','Experimente, die Puffen und Stinken.'),
('Basketball','Mittwoch','16:00',90,20,TRUE,'2020/21','Was gibt es da zu erklären?');


/* Termine anlegen */
INSERT INTO events (date, time, duration, annotation, wg_id)
VALUES
  ('2021-03-22','15:00:00',90,'Gemeinsames Kennenlernen',1),
  ('2021-03-28','15:00:00',90,'Schnittmuster vorbereiten',1),
  ('2021-04-05','15:00:00',60,'Stoff ausschneiden',1);
