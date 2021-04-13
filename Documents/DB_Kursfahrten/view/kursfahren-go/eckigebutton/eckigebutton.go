// Datum: 21.03.2017
// Autoren: Thomas Nordmann und Thorsten Hartung
// Zweck: Klasse eckige Button für Kniffel
// Benötigte Klassen: -- keine --

package eckigebutton 

type EckigeButtons interface {
// Vor.: keine
// Erg.: Ein neuer Button nach den Vorgaben Fensterbreite und Fensterhoehe wird erstellt.
// New (fensterbreite, fensterhoehe uint16)

// Vor.: Keine
// Erg.: Der Button ändert die Farbe ist Grün bei true und Grau bei false. 
Druecken (wert bool) 

// Vor.: Keine
// Erg.: Wernn der Button gedrueckt wurde, dann gibt es true zurück, sonst false.
Gedrueckt () bool 

// Vor.: Keine
// Erg.: Die position des Buttons wird geändert. X und Y- Wert bestimmen die linke obere Ecke des Buttons.
Positionieren (x,y uint16) 

// Vor.: Keine
// Erg.: Die Höhe des Buttons wird durch die gegebenen Werte verändert 
Groesse (breite,hoehe uint16)

// Vor.: Keine
// Erg.: Der Button erscheint auf dem Bildschirm.
Zeichnen ()

// Vor.: Keine
// Erg.: Es werden zwei Werte ausgegeben, die den ersten X-Punkt und den letzten X-Punkt des Buttons angeben.
XWertebereich () (von,bis uint16)

// Vor.: Keine
// Erg.: Es werden zwei Werte ausgegeben, die den ersten Y-Punkt und den letzten Y-Punkt des Buttons angeben.
YWertebereich () (von,bis uint16)

// Vor.: Keine
// Erg.: Ein inhalt wird in den Button geschrieben. Der alte Inhalt wird überschrieben.
InhaltSetzen (s string)

// Vor.: Keine
// Erg.: Der eckige Button ist nicht mehr im sichtbaren Bereich.
Loeschen () 
}
