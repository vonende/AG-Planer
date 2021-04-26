/* Anlegen eines Testaccounts namens admin mit dem Passwort admin */
INSERT INTO users (username,password,firstname,lastname,enabled,roll) VALUES
('admin'         ,'$2y$10$g5rEA6MdkIm.YvigvbrDkucvEytY8bAhUtinpv4Sc.SQ0SG88JHke','Adam'  ,   'Adler', True,'admin'),
('sonnenschein'  ,'$2y$10$sQYHCXBpamR9d.p//7j6Fe9a9mtJpsp4tB66ftr1drr9jhxxk8jA.','Edwin' ,   'Edison',True,'editor'),
('herbstnebel'   ,'$2y$10$dD2.3nwlnewnqX/MgpczjOJgMka2k7y6ijKMQoBNvUOqyBw4JG/XO','Viki'  ,   'Vigor', True,'viewer'),
('frühlingsblume','$2y$10$CjCqCHAmXn4Ymlc0VaA11eBkeOMS8qwuLtc089BKSoTmrv6nPGqhW','Uschi' ,   'Usus',  True,'user'),
('schneeflocke',  '$2y$10$frQYeAqITH5WFMIyswS/x.MHb/7uf87WMikkQI5wX5PhCa97pI7Va','Nina' ,    'Nadel', True,'user'),
('luftballon',    '$2y$10$10aSVrGbeHXmMUzufaWtG.u56DQCUH7wowtndocYmsGPVjM0V2A3G','Gregor',   'Müller', TRUE,'user'),
('weintraube',    '$2y$10$.M/0kk/NXMz5ODJJtOQRPeDccDxQc82V3IkwEsucBXl/nbnCvI432','Walter',   'Dremel',TRUE,'user'),
('eisenhut',      '$2y$10$xEB//3wlrPGCJzgbeRhVB.u5be9agVWz.Of/DM6lhAzVUY2mrc4xm','Fidora',   'Flanell',TRUE,'user'),
('rosskastanie',  '$2y$10$4JB9hFNJCL2WmN0dgiId5ewWJejycSRnfjKZLKRJQlBsp0iEMUHJK','Liselotte','Müller',TRUE,'user'),
('silberstreif',  '$2y$10$SOIMoE7IM2vWBxv2y4z5H.9KcIXOTFHoOZWYGbtAGGhIGFL1vdRVm','Jana',     'Jensen',TRUE,'editor'),
('ostseebrise',   '$2y$10$7noloL/vpWM7PQeZZClyZ.YLvYv7sy9Z1b5JyZzOTz748rHuRsUm.','Bernd',    'Brot',TRUE,'editor');

INSERT INTO teachers (shorthand,user_id) VALUES
('ADL',1),
('EDI',2),
('VIG',3),
('JEN',10),
('BRO',11);

INSERT INTO students (class, studentnumber, user_id) VALUES
('7a','usus01',4),
('7b','nina01',5),
('7a','grmü01',6),
('7b','wadr01',7),
('8a','fifl01',8),
('8a','limü01',9);

/* AGs anlegen */
/* schoolyear gibt an, in welchem Schuljahr die AG zuletzt angeboten wurde.*/
INSERT INTO wgs (title, day, time, duration, max_num, multiple, schoolyear, description)
VALUES
('Nähen','Montag','15:00',90,10,FALSE,'2020/21','Wir nähen alle einen Teddybären.'),
('Schach','Montag','15:00',90,10,TRUE,'2020/21','Klassisches Schachspiel für jung und alt. Beginn: 29. März 2021'),
('Karate','Mittwoch','15:30',90,0,TRUE,'2020/21','Shotokan-Karate für die persönliche Entwicklung.'),
('Technik','Freitag','14:00',90,6,TRUE,'2020/21','Auf- und Abbau der Theatertechnik. Betreuung während der Proben und Aufführungen.'),
('Chemie','Dienstag','16:00',90,8,TRUE,'2019/20','Experimente, die Puffen und Stinken.'),
('Basketball','Mittwoch','16:00',90,20,TRUE,'2020/21','Was gibt es da zu erklären?');

INSERT INTO lead (user_id, wg_id) VALUES
(2,1),(2,2),(4,2),(2,3),(11,5),(11,6),(10,4),
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6);

INSERT INTO participate (user_id,wg_id,schoolyear) VALUES
(4,1,'2020/21'),
(4,2,'2019/20'),
(4,6,'2020/21'),
(5,3,'2020/21'),
(7,6,'2020/21'),
(6,6,'2020/21'),
(5,6,'2020/21'),
(9,3,'2020/21'),
(8,3,'2020/21'),
(5,1,'2020/21'),
(5,2,'2020/21'),
(6,2,'2020/21'),
(4,4,'2020/21'),
(7,4,'2020/21');


/* Termine anlegen */
INSERT INTO events (date, time, duration, annotation, wg_id)
VALUES
('2021-03-22','15:00:00',90,'Gemeinsames Kennenlernen',1),
('2021-03-28','15:00:00',90,'Schnittmuster vorbereiten',1),
('2021-04-05','15:00:00',60,'Stoff ausschneiden',1),
('2021-03-24','16:00:00',90,'Verhaltensregeln und Kennenlernen',3),
('2021-03-31','16:00:00',90,'Wurftechniken',6),
('2021-04-07','16:00:00',90,'Taktik 1',6),
('2021-04-14','16:00:00',90,'Taktik 2',6),
('2021-04-21','16:00:00',90,'Fairplay',6),
('2021-03-31','15:30:00',90,'Taikyoku Shodan',3);

INSERT INTO present (event_id,user_id) VALUES
(1,4),
(2,4),
(3,4),
(4,5),
(5,7),
(5,6),
(5,5),
(5,4),
(6,6),
(6,5),
(6,4),
(7,7),
(7,6),
(7,5),
(7,4),
(8,7),
(8,6),
(8,5),
(8,4),
(9,8),
(9,9),
(9,5);
