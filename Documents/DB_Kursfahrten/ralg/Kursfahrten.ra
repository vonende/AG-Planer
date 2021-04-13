/compact_listings on
% Kursfahrten
%


/output           off
/multiline        on

create or replace table fahrtenkonto(IBAN string, BIC string, inhaber string, bank string);
create or replace table fahrt(fahrtnr integer, fahrtname string, ziel string, von string, bis string, IBAN string);
create or replace table teilnehmer(teilnnr integer, mobilnr string, geschlecht string, adresse string, notfallnr string, vname string, nname string, besonderes string);
create or replace table begleiter(teilnrr integer, stand string);
create or replace table schueler(teilnnr integer, erziehungsberechtigte string, gebdatum string);
create or replace table faehrtmit(teilnnr integer, fahrtnr integer);
create or replace table unternehmung(unr integer, titel string, kosten float, veranstalter string, ubesonderes string, udatum string, uhrzeit string, fahrtnr integer); 
create or replace table bezahlt  (unr integer, IBAN string, bdatum string, bar string);
create or replace table ueberwiesen (teilnnr integer, fahrtnr integer, fkbetrag float, fkdatum string);

INSERT INTO fahrtenkonto VALUES ('DE19183417341534123414', 'DEUTDEFF', 'Conrad Weiland', 'Deutsche Bank');
INSERT INTO fahrtenkonto VALUES ('DE19167944520905876021', 'COBADEFF', 'Daniela Altmann', 'Norisbank');
INSERT INTO fahrtenkonto VALUES ('DE19200456783417345678', 'DEUTDEFF', 'Matthias Lang', 'Deutsche Bank');

INSERT INTO fahrt VALUES (201701, 'Englisch_Kose', 'Orlando', '2017-05-25', '2017-05-30', 'DE19200456783417345678');
INSERT INTO fahrt VALUES (201702, 'Geschichte_Aktmann', 'Dresden', '2017-06-05', '2017-06-10', 'DE19167944520905876021');
INSERT INTO fahrt VALUES (201703, 'Informatik_Schachmann', 'London', '2017-06-07', '2017-06-12', 'DE19183417341534123414');

insert into teilnehmer values(1,'+49 159 0416361','w','Gruenauer Strasse 60 12353 Berlin','+49 30 271166','Claudia','Nadel','');
insert into teilnehmer values(2,'+49 117 9933864','w','Kurfuerstenstraße 77 12353 Berlin','+49 30 278966','Lisa','Vogel','Veganerin');
insert into teilnehmer values(3,'+49 174 0281630','w','Fugger Strasse 23 12353 Berlin ','+49 30 270356','Maria','Roth','Pollenallergie, Notfallspray beim Lehrer');
insert into teilnehmer values(4,'+49 174 5192538','w','Eichendorffstr. 69 12353 Berlin','+49 30 454566','Jennifer','Werner','');
insert into teilnehmer values(5,'+49 159 0484162','w','An Der Urania 32 12353 Berlin ','+49 30 343666','Diana','Wannemaker','');
insert into teilnehmer values(6,'+49 171 0374239','w','Pappelallee 89 12353 Berlin','+49 30 234536','Michelle','Reinhardt','Vegetarierin');
insert into teilnehmer values(7,'+49 159 0875182','m','Neuer Jungfernstieg 43 12336 Berlin','+49 30 223456','Maximilian','Gersten','Rollstuhlfahrer mit Begleiter');
insert into teilnehmer values(8,'+49 171 0762998','m','Hedemannstasse 52 12338 Berlin','+49 30 267876','Markus','Koehler','');
insert into teilnehmer values(9,'+49 159 0821816','w','Albrechtstrasse 43 12359 Berlin ','+49 30 293466','Angelika','Fuhrmann','');
insert into teilnehmer values(10,'+49 159 0923336','w','Fontenay 31 12354 Berlin','+49 30 345329','Simone','Probst','');
insert into teilnehmer values(11,'+49 159 0353321','m','Lietzensee-Ufer 85 12357 Berlin','+49 30 234236','René','Fleischer','');
insert into teilnehmer values(12,'+49 171 0885129','m','Marseiller Strasse 47 12358 Berlin','+49 30 456456','Kevin','Weiss','');
insert into teilnehmer values(13,'+49 171 0643914','w','Hollander Strasse 21 12358 Berlin','+49 30 937476','Jana','Köhler','');
insert into teilnehmer values(14,'+49 175 0333192','w','Landhausstraße 50 12351 Berlin','+49 30 239836','Christina','Eichelberger','Vegetarier');
insert into teilnehmer values(15,'+49 175 0454244','w','Hans-Grade-Allee 27 12338 Berlin','+49 30 234566','Christine','Vogel','');
insert into teilnehmer values(16,'+49 171 0206077','w','Amsinckstrasse 22 12353 Berlin','+49 30 983666','Stefanie','Hoffmann','Nussallergie');
insert into teilnehmer values(17,'+49 159 0622410','m','Hermannstrasse 39 12353 Berlin','+49 30 278966','Mohammed','Bauer','Kein Schweinefleisch');
insert into teilnehmer values(18,'+49 177 0334434','m','Landhausstraße 25 12353 Berlin','+49 30 438566','Swen','Faust','');
insert into teilnehmer values(19,'+49 152 0952112','m','Luebecker Strasse 93 12352 Berlin','+49 30 948466','Frank','Maurer','');
insert into teilnehmer values(20,'+49 171 0337448','m','Schmarjestrasse 26 12350 Berlin','+49 30 949466','Wolfgang','Koertig','');
insert into teilnehmer values(21,'+49 171 0554363','m','Leipziger Straße 12 12353 Berlin','+49 30 119966','Maximilian','Ackermann','');
insert into teilnehmer values(22,'+49 152 0850775','w','Ziegelstr. 21 12353 Berlin','+49 30 223366','Paul','Gersten','');
insert into teilnehmer values(23,'+49 171 0540433','m','Meinekestraße 11 10559 Berlin','+49 30 293666','Tom','Egger','Administrative officer');
insert into teilnehmer values(24,'+49 152 0730363','m','Sömmeringstr. 44 10829 Berlin','+49 30 293666','Marco','Schulz','Vegetarier');
insert into teilnehmer values(25,'+49 171 0704544','w','Brandenburgische Str. 56 10787 Berlin','+49 30 947666','Markus','Bar','');
insert into teilnehmer values(26,'+49 152 0950368','w','Luebecker Strasse 35 10115 Berlin','+49 30 145666','Lisa','Eichmann','');
insert into teilnehmer values(27,'+49 171 0655612','m','Rudower Strasse 48 14553 Berlin','+49 30 098766','Jörg','Schiffer','');
insert into teilnehmer values(28,'+49 152 0421566','m','Grolmanstraße 60 14482 Potdamm','+49 331 200066','Max','Möller','Begleiter vom Rollstuhlfahrer');

insert into begleiter values (21,'Elternteil');
insert into begleiter values (22,'Lehrer');
insert into begleiter values (23,'Elternteil');
insert into begleiter values (24,'Lehrer');
insert into begleiter values (25,'Begleiter');
insert into begleiter values (26,'Elternteil');
insert into begleiter values (27,'Elternteil');
insert into begleiter values (28,'Begleiter');

insert into schueler values (1,'Anette Nadel','1999-09-24');
insert into schueler values (2,'Mark Vogel','2003-17-12'); 
insert into schueler values (3,'Felix Roth','2002-02-01'); 
insert into schueler values (4,'Andreas Werner','2002-12-04');
insert into schueler values (5,'Cecilia Wannemaker','2002-08-08'); 
insert into schueler values (6,'Claudia Reinhardt','2002-05-01'); 
insert into schueler values (7,'Anette Gersten','2002-07-06'); 
insert into schueler values (8,'Axel Koehler','2002-09-26'); 
insert into schueler values (9,'Sonja Fuhrmann','2004-12-01'); 
insert into schueler values (10,'Frederike Probst','2001-12-08'); 
insert into schueler values (11,'Albrecht Fleischer','1999-12-01'); 
insert into schueler values (12,'Anja Weiss','1999-07-11');
insert into schueler values (13,'Ludwig Köhler','2002-01-01');
insert into schueler values (14,'Hans Eichelberger','2002-04-17'); 
insert into schueler values (15,'Conrad Vogel','2002-02-11');
insert into schueler values (16,'Justin Hoffmann','2004-01-16'); 
insert into schueler values (17,'Alexandra Bauer','1999-01-18');
insert into schueler values (18,'Elfriede Faust','2002-04-09'); 
insert into schueler values (19,'Gunter Maurer','2002-11-11');
insert into schueler values (20,'Frieda Koertig','2002-08-18');

insert into faehrtmit values (1,201701);
insert into faehrtmit values (1,201703);
insert into faehrtmit values (2,201701);
insert into faehrtmit values (3,201703);
insert into faehrtmit values (4,201703);
insert into faehrtmit values (5,201703);
insert into faehrtmit values (5,201701);
insert into faehrtmit values (6,201702);
insert into faehrtmit values (7,201703);
insert into faehrtmit values (8,201702);
insert into faehrtmit values (9,201702);
insert into faehrtmit values (10,201702);
insert into faehrtmit values (11,201701);
insert into faehrtmit values (12,201702);
insert into faehrtmit values (13,201703);
insert into faehrtmit values (14,201701);
insert into faehrtmit values (15,201701);
insert into faehrtmit values (16,201702);
insert into faehrtmit values (17,201702);
insert into faehrtmit values (18,201702);
insert into faehrtmit values (19,201703);
insert into faehrtmit values (20,201703);
insert into faehrtmit values (21,201701);
insert into faehrtmit values (22,201702);
insert into faehrtmit values (23,201703);
insert into faehrtmit values (24,201701);
insert into faehrtmit values (25,201701);
insert into faehrtmit values (26,201702);
insert into faehrtmit values (27,201702);
insert into faehrtmit values (28,201703);

insert into unternehmung values (1,'Anreise Bus',1400.00,'Bussard-Travel','10 Std. Nachtfahrt', '2017-06-07','18:00:00',201703);
insert into unternehmung values (2,'Abreise Bus',0.00,'Bussard-Travel','Abreisekosten incl.','2017-06-12','8:00:00',201703);
insert into unternehmung values (3,'Mittagessen',0.00,'Queen Hostel','','2017-06-08','12:00:00',201703);
insert into unternehmung values (4,'Fuehrung durch London',75.00,'Guides International','','2017-06-08','12:00:00',201703);
insert into unternehmung values (5,'Abendessen',300.00,'Hangmans Soup Corner','','2017-06-08','18:00:00',201703);
insert into unternehmung values (6,'Fruehstueck',0.00,'Queen Hostel','','2017-06-09','8:00:00',201703);
insert into unternehmung values (7,'Bootsfahrt auf der Themse',70.00,'Titanic Services','','2017-06-09','10:00:00',201703);
insert into unternehmung values (8,'Shakespeare Workshop',150.00,'Globe Theater','','2017-06-09','16:00:00',201703);
insert into unternehmung values (9,'Abendessen',70.00,'Globe Theater','','2017-06-09','18:00:00',201703);
insert into unternehmung values (10,'Fruehstueck',0.00,'Queen Hostel','','2017-06-10','8:00:00',201703);
insert into unternehmung values (11,'Fuehrung mit Promis',70.00,'Madame Tussauds','','2017-06-10','10:00:00',201703);
insert into unternehmung values (12,'Schwimmen',30.00,'Stadtbad','','2017-06-10','15:00:00',201703);
insert into unternehmung values (13,'Abendessen',0.00,'Queen Hostel','','2017-06-10','18:00:00',201703);
insert into unternehmung values (14,'Fruehstueck',0.00,'Queen Hostel','','2017-06-11','8:00:00',201703);
insert into unternehmung values (15,'The Tower of London',70.00,'Knastis und Co. KG','','2017-06-11','10:00:00',201703);
insert into unternehmung values (16,'London Eye',85.00,'Spass und Spiel','','2017-06-11','12:00:00',201703);
insert into unternehmung values (17,'St. Paul Cathedral',170.00,'Monastry of the cross','','2017-06-11','14:00:00',201703);
insert into unternehmung values (18,'Abendessen',0.00,'Queen Hostel','','2017-06-11','18:00:00',201703);
insert into unternehmung values (19,'Billy Elliot',320.00,'Theater of the Stars','','2017-06-11','20:00:00',201703);
insert into unternehmung values (20,'4 uebernachtungen',4200.00,'Queen Hostel','Nur 4-Bett Zimmer','2017-06-08','8:00:00',201703);
insert into unternehmung values (21,'Anreise Flug',2500.00,'Try&Fly','"Zwischenlandung Rejkjavijk, USA-Visum erforderlich"','2017-05-25','8:00:00',201701);
insert into unternehmung values (22,'Verteilen auf die Gastfamilien',0.00,'Hosting Today','','2017-05-25','14:00:00',201701);
insert into unternehmung values (23,'Schulbesuch',0.00,'Higher Highschool','','2017-05-26','9:00:00',201701);
insert into unternehmung values (24,'Gemeinsames Abendessen',320.00,'Burger Palast','','2017-05-26','18:00:00',201701);
insert into unternehmung values (25,'Schwimmen',80.00,'Stadtbad','Badekappen einpacken','2017-05-27','9:00:00',201701);
insert into unternehmung values (26,'Eimal Astronaut sein',420.00,'Kennedy Space Center','','2017-05-27','14:00:00',201701);
insert into unternehmung values (27,'Tag mit den Gasteltern',0.00,'Hosting Today','','2017-05-28','08:00:00',201701);
insert into unternehmung values (28,'Schulbesuch',0.00,'Higher Highschool','','2017-05-29','8:00:00',201701);
insert into unternehmung values (29,'Abschlussparty',0.00,'Higher Highschool','','2017-05-29','20:00:00',201701);
insert into unternehmung values (30,'Abreise Flug',12500.00,'Fluege deluxe,nonstop','','2017-05-30','08:00:00',201701);
insert into unternehmung values (31,'Anreise Bahn',2156.00,'Bahn','6 Std. Fahrt','2017-06-05','08:00:00',201702);
insert into unternehmung values (32,'5 uebernachtungen',3200.00,'DJH Dresden','','2017-06-05','13:00:00',201702);
insert into unternehmung values (33,'Mittagessen',0.00,'DJH Dresden','','2017-06-06','12:00:00',201702);
insert into unternehmung values (34,'Abendessen',0.00,'DJH Dresden','','2017-06-06','18:00:00',201702);
insert into unternehmung values (35,'Faust',320.00,'Junge Barde','','2017-06-06','18:00:00',201702);
insert into unternehmung values (36,'Fruehstueck',0.00,'DJH Dresden','','2017-06-07','8:00:00',201702);
insert into unternehmung values (37,'Mittagessen',0.00,'DJH Dresden','','2017-06-07','12:00:00',201702);
insert into unternehmung values (38,'Gemäldegalerie',200.00,'Kunstmuseum Schirn','','2017-06-07','14:00:00',201702);
insert into unternehmung values (39,'Abendessen',0.00,'DJH Dresden','','2017-06-07','18:00:00',201702);
insert into unternehmung values (40,'Fruehstueck',0.00,'DJH Dresden','','2017-06-08','8:00:00',201702);
insert into unternehmung values (41,'Stadtfuehrung',0.00,'Fuehrer von heute','','2017-06-08','8:00:00',201702);
insert into unternehmung values (42,'Bowling mit Abendessen',120.00,'Bowlingcenter nix hier','','2017-06-08','17:00:00',201702);
insert into unternehmung values (43,'Fruehstueck',0.00,'DJH Dresden','','2017-06-09','8:00:00',201702);
insert into unternehmung values (44,'Ausflug ins Elbsandstein-Gebirge',420.00,'Return Tours','','2017-06-09','10:00:00',201702);
insert into unternehmung values (45,'Abendessen',0.00,'DJH Dresden','','2017-06-09','18:00:00',201702);
insert into unternehmung values (46,'Fruehstueck',0.00,'DJH Dresden','','2017-06-10','08:00:00',201702);
insert into unternehmung values (47,'Abreise Bahn',0.00, 'Bahn24','Abreisekosten incl.','2017-06-10','08:00:00',201702);
insert into unternehmung values (48,'Abendessen',0.00,'DJH Dresden','','2017-06-05','18:00:00',201702);
insert into unternehmung values (49,'Fruehstueck',0.00,'DJH Dresden','','2017-06-06','08:00:00',201702);
insert into unternehmung values (50,'Schwimmen',30.00,'Freibad am See','Schwimmerlaubnis erforderlich','2017-06-06','12:00',201702);

insert into bezahlt values (1,'DE19183417341534123414','2017-04-01','false');
insert into bezahlt values (4,'DE19183417341534123414','2017-06-08','true');
insert into bezahlt values (5,'DE19183417341534123414','2017-06-08','true');
insert into bezahlt values (7,'DE19183417341534123414','2016-12-12','false');
insert into bezahlt values (8,'DE19183417341534123414','2017-04-01','false');
insert into bezahlt values (9,'DE19183417341534123414','2017-06-09','true');
insert into bezahlt values (11,'DE19183417341534123414','2017-03-21','false');
insert into bezahlt values (12,'DE19183417341534123414','2017-06-10','true');
insert into bezahlt values (15,'DE19183417341534123414','2017-04-01','false');
insert into bezahlt values (16,'DE19183417341534123414','2016-12-12','false');
insert into bezahlt values (17,'DE19183417341534123414','2017-01-9','false');
insert into bezahlt values (19,'DE19183417341534123414','2017-03-18','false');
insert into bezahlt values (20,'DE19183417341534123414','2017-02-11','false');
insert into bezahlt values (21,'DE19200456783417345678','2016-10-01','false');
insert into bezahlt values (24,'DE19200456783417345678','2017-05-26','true');
insert into bezahlt values (25,'DE19200456783417345678','2017-01-12','false');
insert into bezahlt values (26,'DE19200456783417345678','2017-02-02','false');
insert into bezahlt values (30,'DE19200456783417345678','2017-03-21','false');
insert into bezahlt values (31,'DE19167944520905876021','2016-10-10','false');
insert into bezahlt values (32,'DE19167944520905876021','2017-02-18','false');
insert into bezahlt values (34,'DE19167944520905876021','2017-06-06','true');
insert into bezahlt values (35,'DE19167944520905876021','2017-01-16','false');
insert into bezahlt values (38,'DE19167944520905876021','2017-01-06','false');

insert into ueberwiesen values (1,201701,1600.00,'2017-01-17');
insert into ueberwiesen values (1,201703,750.00,'2016-12-06');
insert into ueberwiesen values (2,201701,1600.00,'2017-01-22');
insert into ueberwiesen values (3,201703,750.00,'2017-02-23');
insert into ueberwiesen values (4,201703,750.00,'2017-03-01');
insert into ueberwiesen values (5,201703,750.00,'2016-12-05');
insert into ueberwiesen values (5,201701,1600.00,'2017-01-17');
insert into ueberwiesen values (6,201702,600.00,'2017-01-17');
insert into ueberwiesen values (7,201703,750.00,'2017-01-17');
insert into ueberwiesen values (8,201702,600.00,'2017-01-17');
insert into ueberwiesen values (9,201702,600.00,'2017-01-17');
insert into ueberwiesen values (10,201702,600.00,'2017-01-17');
insert into ueberwiesen values (11,201701,1600.00,'2017-01-17');
insert into ueberwiesen values (12,201702,600.00,'2017-01-17');
insert into ueberwiesen values (13,201703,750.00,'2017-01-17');
insert into ueberwiesen values (14,201701,1600.00,'2017-01-08');
insert into ueberwiesen values (15,201701,1600.00,'2017-01-18');
insert into ueberwiesen values (16,201702,600.00,'2016-11-30');
insert into ueberwiesen values (17,201702,600.00,'2016-12-23');
insert into ueberwiesen values (18,201702,600.00,'2016-12-12');
insert into ueberwiesen values (19,201703,750.00,'2017-02-18');
insert into ueberwiesen values (20,201703,750.00,'2017-01-03');
insert into ueberwiesen values (21,201701,1600.00,'2017-01-01');
insert into ueberwiesen values (22,201702,600.00,'2017-01-14');
insert into ueberwiesen values (23,201703,750.00,'2017-01-21');
insert into ueberwiesen values (24,201701,1600.00,'2017-01-11');
insert into ueberwiesen values (25,201701,1600.00,'2017-01-16');
insert into ueberwiesen values (26,201702,600.00,'2017-01-18');
insert into ueberwiesen values (27,201702,600.00,'2017-01-12');
insert into ueberwiesen values (28,201703,750.00,'2017-01-01');



/output on
/dbschema
