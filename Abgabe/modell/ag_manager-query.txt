--******************************************************************
--Daten mit SELECT auswählen
--******************************************************************
--Gesucht ist jeweils eine Tabelle aller ...

--1. Arbeitsgemeinschaften:
SELECT * FROM wgs;

--2. Benutzerdaten (Benutzername, Vorname, Nachname):
SELECT username, firstname, lastname FROM users;

--3. AGs (Titel, Wochentag) dieses Schuljahres:
SELECT title, day FROM wgs WHERE schoolyear='2020/21';

--4. Benutzer (Vorname, Nachname, Benutzername) mit "Nina" im Vornamen:
SELECT firstname, lastname, username FROM users
WHERE firstname LIKE '%Nina%';

--5. AGs (Id, Titel) mit dem groß oder klein geschriebenen Wort "Theater"
--in der Beschreibung:
SELECT wg_id, title 
FROM wgs
WHERE description SIMILAR TO '%(T|t)heater%';

--6. Klassen aufsteigend sortiert:
SELECT DISTINCT class FROM students ORDER BY class ASC;

--7. Events (Id, Datum, Bemerkung) der letzten 7 Tage absteigend nach Datum sortiert:
SELECT event_id, date, annotation FROM events
WHERE date >= (now() - INTERVAL '7 days')
ORDER BY date DESC;

--8. User (Id, Vorname, Nachname) die innerhalb des letzten Jahres keine Session hatten:
SELECT user_id, firstname, lastname 
FROM sessions NATURAL JOIN users
WHERE logintime > now() - INTERVAL '1 year';

--9. AGs (Titel, Tag), die nicht am Mittwoch stattfinden:
SELECT title, day 
FROM wgs
WHERE NOT day = 'Mittwoch';

--10. SuS (Nachname,Rolle), die an einer AG teilnehmen:
SELECT u.lastname, u.roll 
FROM users u 
WHERE EXISTS
	(SELECT 1 
	 FROM participate p 
	 WHERE u.user_id=p.user_id) AND roll = 'user';


--******************************************************************
--Daten mit SELECT auswählen unter Verwendung von Joins:
--******************************************************************
--Gesucht ist jeweils eine Tabelle aller ...

--11. Lehrer (Vorname, Nachname, Kürzel):
SELECT firstname, lastname, shorthand
FROM users AS u, teachers AS t
WHERE u.user_id=t.user_id;

--12. Schüler (Vorname, Nachname, Klasse, Schülernummer):
SELECT firstname, lastname, class, studentnumber
FROM users AS u, students AS s
WHERE u.user_id=s.user_id;

--13. Veranstaltungen (= Events) (Datum, Bemerkung) der Näh-AG seit Beginn des Jahres:
SELECT date, annotation FROM events, wgs
WHERE title LIKE 'Nähen' AND wgs.wg_id=events.wg_id
AND date>='2021-01-01';

--14. Schüler (Vorname, Nachname) mit Tag und Zeit der AGs an denen sie teilnehmen:
SELECT firstname, lastname, wgs.day, wgs.time
FROM users NATURAL JOIN	students NATURAL JOIN participate NATURAL JOIN wgs;

--15. SuS (Name, Vorname), die an allen Terminen einer AG (Titel)          
--anwesend waren aufsteigend sortiert nach AG-Titel.
SELECT u3.fn AS vorname,u3.ln AS name, u3.canw AS anwesend, u3.title AS ag_name, COUNT(events.event_id) 
FROM 
	(SELECT u2.firstname AS fn, u2.lastname AS ln,u2.c1 AS canw,wgs.wg_id AS agname,wgs.title 
	FROM 
		(SELECT u1.firstname,u1.lastname,count(events.event_id) AS c1,wg_id 
		FROM 
			(SELECT firstname,lastname,event_id 
			FROM users, present 
			WHERE users.user_id=present.user_id) AS u1,events 
		WHERE u1.event_id = events.event_id 
		GROUP BY u1.firstname,u1.lastname, events.wg_id) AS u2, wgs 
	WHERE wgs.wg_id= u2.wg_id) AS u3,events 
WHERE events.wg_id = u3.agname 
GROUP BY u3.title,u3.fn,u3.ln,u3.canw 
HAVING u3.canw = COUNT(events.event_id) 
ORDER BY ag_name ASC;

--******************************************************************
--Daten mit SELECT auswählen unter Verwendung von Joins, Subqueries
--und Aggregatfunktionen:
--******************************************************************

--16. Gesucht ist die AG mit den meisten eingeschriebenen Teilnehmern:
SELECT title, COUNT(p.user_id) AS count FROM wgs, participate AS p
WHERE wgs.wg_id = p.wg_id
GROUP BY wgs.wg_id
ORDER BY count DESC LIMIT 1;

--17. Gesucht ist die Gesamtzahl an AG-Plätzen in AGs mit beschränkter Platzzahl
SELECT SUM(max_num) as anzahl_plaetze_gesamt
FROM wgs;

--18. Gesucht ist eine Tabelle der
--Gesamtdauern aller AG-Termine pro AG, die bisher stattgefunden haben:
SELECT SUM(events.duration) AS Gesamtdauer,wg_id 
FROM events NATURAL JOIN wgs 
GROUP BY wg_id;

--19. Gesucht ist eine Tabelle aller AGs (Titel, Schuljahr, Beschreibung)
--der Schülerin mit dem Benutzernamen "schneeflocke".
--Die Sortierung erfolgt absteigend nach Schuljahr und aufsteigend nach Titel.
SELECT title, p.schoolyear, description FROM wgs, participate AS p
WHERE p.user_id = (SELECT user_id FROM users WHERE username='schneeflocke')
AND p.wg_id = wgs.wg_id
ORDER BY p.schoolyear DESC, wgs.title ASC;

--20. Gesucht ist eine Tabelle aller
--Schüler der Klasse 7a, in welcher die AGs und die Anzahl der besuchten
--Veranstaltungen (= Events) seit Schuljahresbeginn 2020 aufgelistet werden.
--Die Sortierung erfolgt nach Nachname, Vorname und Titel aufsteigend:
SELECT firstname, lastname, title, COUNT(e.event_id)
FROM students AS s, users AS u, wgs AS w, present AS p, events AS e
WHERE class='7a' AND e.date>='2020-08-10' AND u.user_id=s.user_id
AND s.user_id=p.user_id AND p.event_id=e.event_id AND e.wg_id=w.wg_id
GROUP BY u.user_id, w.wg_id
ORDER BY lastname,firstname,title ASC;

--21. Gesucht ist eine Tabelle aller
--AGs mit mehr als zwei Leitern:
SELECT COUNT(wg_id) as AnzLeiter, wg_id, title 
FROM wgs NATURAL JOIN lead 
GROUP BY wg_id 
HAVING COUNT(wg_id) > 2;

--22. Gesucht ist eine Tabelle aller
--Schüler (Vorname, Nachname) mit Tag und Zeit und der Anzahl der AGs an denen sie teilnehmen, 
--die am selben Tag zur selben Zeit stattfinden:
SELECT COUNT(user_id) AS Teilnahmen, firstname, lastname, wgs.day, wgs.time 
FROM users NATURAL JOIN students NATURAL JOIN participate NATURAL JOIN wgs 
GROUP BY user_id, wgs.day, wgs.time 
HAVING count(user_id) > 1;

--23. Gesucht ist eine Tabelle der
--prozentualen Teilnahme des Schülers Edwin Edison an der AG Basketball:
SELECT (COUNT(wg_id)::FLOAT/anz_termine) AS teilnahme_prozentual, firstname,lastname,title 
FROM (SELECT COUNT(wg_id) AS anz_termine, wg_id 
	FROM events 
	GROUP BY wg_id) AS stattgefunden  NATURAL JOIN present NATURAL JOIN events NATURAL JOIN participate NATURAL JOIN wgs NATURAL JOIN users 
GROUP BY title,anz_termine,firstname,lastname 
HAVING lastname = 'Edison' AND title = 'Basketball';



