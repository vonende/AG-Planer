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

--5. Klassen aufsteigend sortiert:
SELECT DISTINCT class FROM students ORDER BY class ASC;

--6. Events (Id, Datum, Bemerkung) der letzten 7 Tage absteigend nach Datum sortiert:
SELECT event_id, date, annotation FROM events
WHERE date >= (now() - INTERVAL '7 days')
ORDER BY date DESC;

--******************************************************************
--Daten mit SELECT auswählen unter Verwendung von Joins:
--******************************************************************
--Gesucht ist jeweils eine Tabelle aller ...

--Lehrer (Vorname, Nachname, Kürzel):
SELECT firstname, lastname, shorthand
FROM users AS u, teachers AS t
WHERE u.user_id=t.user_id;

--Schüler (Vorname, Nachname, Klasse, Schülernummer):
SELECT firstname, lastname, class, studentnumber
FROM users AS u, students AS s
WHERE u.user_id=s.user_id;

--Veranstaltungen (= Events) (Datum, Bemerkung) der Näh-AG seit Beginn des Jahres:
SELECT date, annotation FROM events, wgs
WHERE title LIKE 'Nähen' AND wgs.wg_id=events.wg_id
AND date>='2021-01-01';


--******************************************************************
--Daten mit SELECT auswählen unter Verwendung von Joins, Subqueries
--und Aggregatfunktionen:
--******************************************************************

--Gesucht ist die AG mit den meisten eingeschriebenen Teilnehmern:
SELECT title, COUNT(p.user_id) AS count FROM wgs, participate AS p
WHERE wgs.wg_id = p.wg_id
GROUP BY wgs.wg_id
ORDER BY count DESC LIMIT 1;

--Gesucht ist eine Tabelle aller AGs (Titel, Schuljahr, Beschreibung)
--der Schülerin mit dem Benutzernamen "schneeflocke".
--Die Sortierung erfolgt absteigend nach Schuljahr und aufsteigend nach Titel.
SELECT title, p.schoolyear, description FROM wgs, participate AS p
WHERE p.user_id = (SELECT user_id FROM users WHERE username='schneeflocke')
AND p.wg_id = wgs.wg_id
ORDER BY p.schoolyear DESC, wgs.title ASC;

--Gesucht ist eine Tabelle aller
--Schüler der Klasse 7a, in welcher die AGs und die Anzahl der besuchten
--Veranstaltungen (= Events) seit Schuljahresbeginn 2020 aufgelistet werden.
--Die Sortierung erfolgt nach Nachname, Vorname und Titel aufsteigend:
SELECT firstname, lastname, title, COUNT(e.event_id)
FROM students AS s, users AS u, wgs AS w, present AS p, events AS e
WHERE class='7a' AND e.date>='2020-08-10' AND u.user_id=s.user_id
AND s.user_id=p.user_id AND p.event_id=e.event_id AND e.wg_id=w.wg_id
GROUP BY u.user_id, w.wg_id
ORDER BY lastname,firstname,title ASC;
