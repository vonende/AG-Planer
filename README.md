# AG-Manager

## Datenexport
Das PHP-Script sql_insert_data.php liest die Datenbank aus und stellt deren Inhalte mittels INSERT-Anweisungen auf dem Bildschirm dar.

## Was muss die App können?
### Für alle
- Login und Logout
- Profil einsehen und ändern
- Liste aller AGs dieses Schuljahres ansehen
- AG-Beschreibungen lesen

### Aus Schülersicht (Rolle: user)
- AG-Teilnahmen einsehen
- AG-Termine einsehen

### Aus AG-Leiter-Sicht (keine Rolle, sondern über Beziehung "leitet" zw. AGs und Benutzern definiert)
- Termine seiner AG erstellen
- Anwesenheit dokumentieren

### Aus Lehrer-Sicht (Rolle: editor)
- neue AG anlegen
- eigene AG editieren
- eigene AG-Termine editieren
- Anwesenheit eigener AGs editieren
- Schüler einschreiben und austragen

### Aus Klassenlehrersicht (Rolle: viewer)
- Teilnahmen eines jeden Schülers an den AGs anzeigen lassen im aktuellen Schuljahr (x von y Termine)

### Aus Administratorsicht (Rolle: admin)
- Benutzeraccounts verwalten, neben den typischen Profileinstellungen können auch folgende Attribute eingegeben werden:
  - roll
  - enabled
  - teacher (shorthand), student (studentnumber, class)


## Diskussion

### Werden Sessions schon gespeichert?
Ja, sobald du dich anmeldest, ist die Session gespeichert.

### Man könnte noch constraints wie "ein Schüler kann nicht an zwei AG's die zur selben Zeit stattfinden/sich überschneiden teilnehmen" hinzufügen.
Ist im Alltag nicht wirklich relevant. Falls es zwischen zwei AGs eine leichte zeitliche Überschneidung geben sollte, ist das auch ok, wenn es mit den Leiter*innen abgesprochen wird.

### AG-Termine können nur für den Wochentag angelegt werden, an dem die AG auch stattfindet und nur ab dem Tag rückwirkend, nicht im Vorhinein.
Nein, es gibt bei AGs auch abweichende Termine, wie zum Beispiel am "Tag der offenen Tür" oder bei Exkursionen. Daher halte ich dies nicht für sinnvoll.

### wäre es sinnvoll eine maxAnzahl für besuchte AGs pro Schüler einzuführen?
Sollte ein(e) Schüler*in echt so fleißig und interessiert sein, wer wollte ihn/sie davon abhalten? Zu Beginn gibt es sowieso Fluktuationen.

### Beim editieren kann man aktuell Klasse und Schülernummer nicht ändern - Absicht?
Erledigt.

### Sortieren von Tabellen nach verschiedenen Attributen ist nicht notwendig, wäre aber vllt. nett
Wäre nett, aber nicht notwendig und auch keine Ahnung wie. Momentan sind andere Dinge wichtiger.

### Das Attribut email bei users könnte auch unique, sein, oder?
Ja, durchaus. Wenn aber Eltern eine Mailadresse für mehrere Kinder verwenden wollen, ist das doof. Das passiert ständig bei unserer HPI-Schulcloud.

### Der Trigger auf last_update bewirkt ja nur dann eine Aktualisierung, wenn in der Users Tabelle etwas geändert wird. Das soll uns ja helfen, "Karteileichen" zu löschen, oder? Wie würde das genau ablaufen? Ist mir nicht ganz klar...
Beim Import der Schülerdaten soll last_update selbstverständlich auch aktualisiert werden, um Karteileichen zu finden. Es fehlt auch aktuell (6.4.2021) noch ein Trigger, der bei Aktualisierung der Klassenzugehörigkeit das last_update anpasst. Dieser ist für eine sinnvolle Funktion sogar noch wichtiger.
Der aktuelle Trigger könnte dafür verwendet werden, nachzuvollziehen, ob ein Benutzer in letzter Zeit sein Passwort geändert hat.
Ich überlege aktuell noch, ob nicht vielleicht eine Relation sinnvoll ist, die Änderungen der wichtigsten Daten protokolliert. So könnte ein Schüler ja seinen Namen in "Hans Wurst" ändern und dann weiß niemand mehr, wer er ist. Protokolliert man die Änderungen, lässt sich leicht klären, wem der Account gehört, ohne die Schülernummer abgleichen zu müssen. Andererseits ist der Abgleich der Schülernummer sicher auch nicht sehr aufwändig...

### @Rayk: Warum wollten wir die Tabelle "others" nochmal rauslassen? (Weil sie z.Zt. auskommentiert ist.) - Hatten wir, glaub ich, mal besprochen, aber mir fällt's nicht mehr ein.
Wer kein Schüler oder Lehrer ist, ist ein "other". Da diese Tabelle keine eigenen Attribute hat, ist sie verzichtbar.

### Könnten Lehrer auch ein Attribut „Letzte Aktualisierung“ bekommen? Andere kann man ja einfach rausschmeißen, wenn die letzte Session 1 Jahr her ist oder so…

Gegen das Attribut spricht erst einmal nichts. So lassen sich ehem. Kollegen ggf. leichter aufspüren. Die letzte Session ist jedoch kein Kriterium. Wenn ein Lehrer nach 2 Jahren Pause mal wieder Klassenlehrer wird, dann soll er auf die AG-Teilnahmen zugreifen können, muss also noch im System vorhanden sein.
Nachtrag:
Das Attribut last_update in der Usertabelle gilt nun für teacher, student und other.

### Benutzer besitzt Session: 1:1 Beziehung? Muss jeder Benutzer immer genau 1 Session besitzen und umgekehrt?

Nein, wenn du dich parallel von verschiedenen Geräten oder Browsern anmeldest, kannst du mehrere Sessions besitzen.

### Wie wird ID festgelegt?
Automatisch vom System, daher der Datentyp SERIAL. (Siehe create.sql)

### Wie hängen ID und Kürzel bzw. Schülernummer zusammen?
Gar nicht. ID ist der eindeutige Primary Key für Benutzer. So vermeiden wir, dass wir unterschiedliche Schlüsseltypen für Lehrer und Schüler haben. Kürzel und Schülernummer sind aber für Datenimport und Synchronisation von Nutzen.

### Benötigt man beides?
Ja, siehe oben.

### Können Benutzer ohne Benutzername existieren?
Nein, daher ist der Benutzername als NOT NULL gekennzeichnet. Man könnte ihn auch als Schlüssel verwenden, da er UNIQUE ist. So wäre aber eine Änderung des Benutzernamens durch den Benutzer schwierig, da man alle Einträge in der Datenbank, die diesen als Fremdschlüssel verwenden, ebenfalls ändern müsste.

## TODO
### Laut Teilaufgabe d) des Datenbankenpraktikums sollen u.a. "abstrakte Wertebereiche" angegeben werden. Ich habe Herker eine Mail geschrieben und gefragt, ob er da mehr als Nat bzw. String sehen möchte und wenn ja, was (Date, Float etc.?).
Wir können die SQL-Domains nehmen (was ich auch schon getan habe.
