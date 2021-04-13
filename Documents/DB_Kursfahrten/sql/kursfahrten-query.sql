--Datenbank ''Kursfahrten''

--SQL-Anfragen


--''Select''

--1. Zeige alle Fahrten:

SELECT *

FROM fahrt;

--2. Zeige alle teilnehmenden Personen:

SELECT *

FROM teilnehmer;

--3. Zeige alle Ziele der Fahrten:

SELECT ziel

FROM fahrt;

--4. Zeige die Nachnamen der Teilnehmer mit den entsprechenden Notfallnummern:

SELECT nname, notfallnr

FROM teilnehmer;


--Bedingung ''WHERE''

--5. Zeige den vollständigen Namen aller weiblichen Teilnehmer:

SELECT nname, Vname

FROM teilnehmer

WHERE geschlecht = 'w';

--6. Zeige alle Teilnehmer mit dem Nachnamen Vogel:

SELECT *

FROM teilnehmer

WHERE nname = 'Vogel';

--7. Zeige alle Unternehmungen, die teuerer als 100 € sind:

SELECT *

FROM unternehmung

WHERE kosten > 100;

--8. Zeige alle Unternehmungen der Fahrt Nr. 201701, die teuerer als 100 € sind:

SELECT *

FROM unternehmung

WHERE kosten > 100 AND fahrtnr = 201701;


--Sortieren ''ORDER BY''

--9. Gib die nach dem Nachnamen sortierte Liste aller Teilnehmer aus:

SELECT *

FROM teilnehmer

ORDER BY nname;

--10. Gib die nach Kosten absteigend sortierte Liste der Unternehmungen aus:

SELECT *

FROM unternehmung

ORDER BY kosten DESC;

--11. Zeige nur die teuerste Unternehmung:

SELECT *

FROM unternehmung

ORDER BY kosten DESC LIMIT 1;


--''NATURAL JOIN''

--12. Zeige alle Teilnehmer, die nach Dresden fahren:

SELECT *

FROM faehrtmit NATURAL JOIN fahrt

WHERE ziel = 'Dresden';

--13. Zeige die Nachnamen aller begleitenden Lehrer:

SELECT nname

FROM begleiter NATURAL JOIN teilnehmer

WHERE stand = 'Lehrer';

--14. Zeige alle Besonderheiten der Unternehmungen auf der Fahrt nach Orlando:

SELECT titel, ubesonderes

FROM unternehmung NATURAL JOIN fahrt

WHERE ziel = 'Orlando' AND ubesonderes <> '';

--15. Zeige alle Besonderheiten der teilnehmenden Schüler mit Vor- und Nachnahmen der Schüler:

SELECT vname, nname, besonderes

FROM teilnehmer NATURAL JOIN schueler;

--16. Zeige nur Schüler (mit Vor- und Nachnamen) mit ihren Besonderheiten, wenn sie welche haben:

SELECT vname, nname,besonderes

FROM teilnehmer NATURAL JOIN schueler

WHERE besonderes <> '';

--17. Zeige eine sortierte Liste aller Unternehmungen (Titel), die nicht bei der Fahrt nach Orlando stattfinden:

SELECT titel 

FROM unternehmung NATURAL JOIN fahrt

WHERE ziel != 'Orlando'

ORDER BY titel;

--18. Zeige alle Teilnehmernummern und vollständigen Namen der Teilnehmer, die nach Dresden fahren:

SELECT teilnnr, vname, nname

FROM (teilnehmer NATURAL JOIN faehrtmit) NATURAL JOIN fahrt

WHERE ziel = 'Dresden';

--19. Zeige alle Schüler (mit vollständigem Namen), die nach London fahren:

SELECT vname, nname

FROM (teilnehmer NATURAL JOIN faehrtmit NATURAL JOIN fahrt NATURAL JOIN schueler)

WHERE ziel = 'London';

--20. Zeige, welche Aktivitäten bei der Kursfahrt in die USA (nach Datum sortiert) stattfinden:

SELECT DISTINCT titel, udatum

FROM unternehmung

WHERE fahrtnr=201701

ORDER BY udatum;


--21. Zeige die Teilnehmernummern und den Nachnamen der Schüler, die an mehreren Fahrten teilnehmen:

SELECT teilnnr, nname

FROM faehrtmit NATURAL JOIN teilnehmer

GROUP BY teilnnr, nname

HAVING COUNT (*) > 1;


--''SUM''

--22. Zeige die Summe der Kosten der Unternehmungen der Londonfahrt (Fahrtnr=201703):

SELECT SUM(kosten) AS "Kosten insgesamt:"

FROM unternehmung

WHERE fahrtnr = 201703;

