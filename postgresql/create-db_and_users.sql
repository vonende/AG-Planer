/*
Damit ein user beim localhost sich mit dem Befehl
psql -d ag_manager -U ag_admin
anmelden kann, muss in
/etc/postgresql/12/main/pg_hba.conf
statt "peer" überall "md5" stehen.
Dafür sind zunächst alle "peer" auf "trust" zu setzen.
Dann muss der Server neu gestartet werden mit:
sudo /etc/init.d/postgresql restart
Nun kann man sich ohne Passwort mit
psql -d ag_manager -U ag_admin
anmelden.
Nun muss das Passwort neu gesetzt werden:
ALTER USER ag_admin with password 'kq9Ba8kf61;6]f';
Mit \q psql wieder verlassen und wieder
/etc/postgresql/12/main/pg_hba.conf
öffnen. Nun alle "trust" durch "md5" ersetzen.
Nochmals den Server neu starten.
Ab jetzt gibt es keine Fehlermeldung mehr, wenn man sich mittels
psql -d ag_manager -U ag_admin
anmelden möchte, sondern es wird das Passwort abgefragt.
*/

CREATE USER ag_admin WITH PASSWORD 'kq9Ba8kf61;6]f';
CREATE USER ag_user WITH PASSWORD 'kd83kCd[dj0i';

CREATE DATABASE ag_manager WITH OWNER ag_admin ENCODING 'UTF8';
