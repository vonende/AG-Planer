# AG-Manager

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

### Wie wird ID festgelegt?
Automatisch vom System, daher der Datentyp SERIAL. (Siehe create.sql)

### Wie hängen ID und Kürzel bzw. Schülernummer zusammen?
Gar nicht. ID ist der eindeutige Primary Key für Benutzer. So vermeiden wir, dass wir unterschiedliche Schlüsseltypen für Lehrer und Schüler haben. Kürzel und Schülernummer sind aber für Datenimport und Synchronisation von Nutzen.

### Benötigt man beides?
Ja, siehe oben.

### Können Benutzer ohne Benutzername existieren?
Nein, daher ist der Benutzername als NOT NULL gekennzeichnet. Man könnte ihn auch als Schlüssel verwenden, da er UNIQUE ist. So wäre aber eine Änderung des Benutzernamens durch den Benutzer schwierig, da man alle Einträge in der Datenbank, die diesen als Fremdschlüssel verwenden, ebenfalls ändern müsste.

### Situation
Leiter bieten AGs an, SuS kommen am Schuljahresbeginn zum Vortreffen (keine Zweit- und Drittwünsche?), Leiter wählt SuS aus (was ist bei zu vielen SuS?).
Leiter & SuS sind an den Terminen anwesend, an denen die AGs stattfinden (was ist bei Krankheit? Des Leiters?). Sie können sich auf dem AG-Portal mit einer Session anmelden.
Leiter können dort AGs erstellen. Sie tragen die Anwesenheit dort ein.
SuS können AGs sehen. (Und WunschAG angeben?) Sie können auch die Eintragung ihrer Anwesenheit prüfen (ihre Eltern auch, wenn sie Benutzername und Passwort kennen)
