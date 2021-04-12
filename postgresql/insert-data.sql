/* Anlegen eines Testaccounts namens admin mit dem Passwort admin */
INSERT INTO users (user_id,username,password,firstname,lastname,enabled,roll) VALUES
(1,'admin'         ,'$2y$10$g5rEA6MdkIm.YvigvbrDkucvEytY8bAhUtinpv4Sc.SQ0SG88JHke','Adam'  ,'Adler', True,'admin'),
(2,'sonnenschein'  ,'$2y$10$sQYHCXBpamR9d.p//7j6Fe9a9mtJpsp4tB66ftr1drr9jhxxk8jA.','Edwin' ,'Edison',True,'editor'),
(3,'herbstnebel'   ,'$2y$10$dD2.3nwlnewnqX/MgpczjOJgMka2k7y6ijKMQoBNvUOqyBw4JG/XO','Viki'  ,'Vigor', True,'viewer'),
(4,'frühlingsblume','$2y$10$CjCqCHAmXn4Ymlc0VaA11eBkeOMS8qwuLtc089BKSoTmrv6nPGqhW','Uschi' ,'Usus',  True,'user');

INSERT INTO teachers (shorthand,user_id) VALUES
('adm',1),
('edi',2),
('vig',3);

INSERT INTO students (class, studentnumber, user_id) VALUES
('7a','usus01',4);

/* AGs anlegen */
INSERT INTO wgs (wg_id,title, day, time, duration, max_num, multiple, schoolyear, description)
VALUES
(1,'Nähen','Dienstag','15:00',90,10,FALSE,'2019/20','Wir nähen alle einen Teddybären.'),
(2,'Nähen','Montag','15:00',90,10,FALSE,'2020/21','Wir nähen alle einen Teddybären.'),
(3,'Schach','Montag','15:00',90,10,TRUE,'2020/21','Klassisches Schachspiel für jung und alt. Beginn: 29. März 2021'),
(4,'Karate','Mittwoch','15:30',90,0,TRUE,'2020/21','Shotokan-Karate für die persönliche Entwicklung.'),
(5,'Technik','Freitag','14:00',90,6,TRUE,'2020/21','Auf- und Abbau der Theatertechnik. Betreuung während der Proben und Aufführungen.'),
(6,'Chemie','Dienstag','16:00',90,8,TRUE,'2020/21','Experimente, die Puffen und Stinken.'),
(7,'Basketball','Mittwoch','16:00',90,20,TRUE,'2020/21','Was gibt es da zu erklären?');

INSERT INTO lead (user_id, wg_id) VALUES
(2,1),(2,2),(4,3),(2,3);

INSERT INTO participate (user_id,wg_id,schoolyear) VALUES
(4,1,'2019/20'),(4,2,'2020/21'),(4,6,'2020/21');


/* Termine anlegen */
INSERT INTO events (event_id,date, time, duration, annotation, wg_id)
VALUES
  (1,'2021-03-22','15:00:00',90,'Gemeinsames Kennenlernen',1),
  (2,'2021-03-28','15:00:00',90,'Schnittmuster vorbereiten',1),
  (3,'2021-04-05','15:00:00',60,'Stoff ausschneiden',1);

INSERT INTO present (event_id,user_id) VALUES
  (1,4),(2,4),(3,4);
