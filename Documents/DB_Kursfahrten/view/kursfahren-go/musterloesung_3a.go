// Autor: Thomas Nordmann
// Zweck: Musterlösung Aufgabe 3a
// Datum: 27.Juni 2017

package main

//////////////////////////////////////////////////////////////////////////////////////
//             Globale Variablen
//////////////////////////////////////////////////////////////////////////////////////

import (
		button 	"./eckigebutton"
				"gfx"
				"SQL"
				"formularfelder"
				"fmt"
		.		"./layout"
		)
		
//////////////////////////////////////////////////////////////////////////////////////
//             Globale Variablen
//////////////////////////////////////////////////////////////////////////////////////

		
var n    int64

const (
    alphabet = "abcdefghijklmnopqrstuvwxyzäöüß ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ"
    specials = ",;.:-_#'+*~´`!\"§$%&/()=?\\}][{^°<>|~"
    ziffern  = "0123456789"
	za 		 = alphabet + ziffern
    all      = alphabet + specials + ziffern
    fontpath = "/home/lewein/font/OpenSans-Bold.ttf"
  )

var (
	breite uint16 = 1000   //Fenster
	hoehe uint16  = 600   // Fenster
	conn SQL.Verbindung
	)
		
//////////////////////////////////////////////////////////////////////////////////////
//             Ergebnis-Struct
//////////////////////////////////////////////////////////////////////////////////////


type teilnehmer struct {
	teilnr 					int
	mobilnr 				string
	Geschlecht			 	string
	adresse				 	string
	notfallnr			 	string 
	vname 					string
	nname 					string
	besonderes 				string
	erziehungsberechtigte 	string
	gebdatum 				string
	fahrtname				string
}


//////////////////////////////////////////////////////////////////////////////////////
//             Buttonfunktionen
//////////////////////////////////////////////////////////////////////////////////////

func Buttonzeichnen () [2] button.EckigeButtons {
	var but [2] button.EckigeButtons
	b0 := button.New (1200,600); but[0] = b0; but[0].Positionieren (0,90);  but[0].Groesse (200,50); but[0].InhaltSetzen ("Programm beenden");	but[0].Zeichnen ()
	b1 := button.New (1200,600); but[1] = b1; but[1].Positionieren (0,150);  but[1].Groesse (200,50); but[1].InhaltSetzen ("Eintragung SchülerIn");	but[1].Zeichnen ()
	return but
}


func Warten_auf_Button_klick (but [2] button.EckigeButtons) (rueckgabe uint) {
	b0vonx,b0bisx := but[0].XWertebereich()
	b0vony,b0bisy := but[0].YWertebereich()
	b1vonx,b1bisx := but[1].XWertebereich()
	b1vony,b1bisy := but[1].YWertebereich()

for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {but[0].Druecken(true);but[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {but[1].Druecken(true);but[1].Zeichnen(); rueckgabe = 1; break}
}
	return rueckgabe
}


//////////////////////////////////////////////////////////////////////////////////////
//             Abfragefunktion
//////////////////////////////////////////////////////////////////////////////////////

func eintragung_schueler () uint {
// Notwendige Variablen
var (
	rueckgabe uint
	schuelervorhanden bool
	schuelerinfahrt bool
	fahrtvorhanden bool
	anzahl int
)
	
	
// Grundlayout
Basic ("Aufgabe 3a", "Schüler eintragen","Thomas Nordmann","Musterlösung" )
Was_wird_gemacht ("Einen neuen Schüler eintragen", "")
Was_kommt_raus ("Eintragung", "")
a := Buttonzeichnen () 

//Eingabefelder
	teilnr 					:= formularfelder.FormEingabe (225, 230   , 30, 	"Teilnehmernummer", ziffern)
	mobilnr					:= formularfelder.FormEingabe (225, 230+35, 30, 	"Mobilnummer", all)
	geschlecht				:= formularfelder.FormEingabe (225, 300   , 30,		"Geschlecht", alphabet)
	adresse					:= formularfelder.FormEingabe (225, 300+35, 30, 	"Adresse", alphabet)
	notfallnr				:= formularfelder.FormEingabe (225, 300+70, 30, 	"Notfallnummer", alphabet)
	vname					:= formularfelder.FormEingabe (225, 370+35, 30, 	"Vorname", alphabet)
	nname					:= formularfelder.FormEingabe (225, 405+35, 30, 	"Nachname", alphabet)
	besonderes				:= formularfelder.FormEingabe (500, 230, 30, 	"Besonderes", alphabet)
	erziehungsberechtigte	:= formularfelder.FormEingabe (500, 265, 30, 	"Erziehungsberechtigter", alphabet)
	gebdatum				:= formularfelder.FormEingabe (500, 300, 30,    "Gaburtsdatum", alphabet)
	fahrtnr					:= formularfelder.FormEingabe (500, 335, 30,    "Fahrtnummer", alphabet)

fmt.Println (teilnr,mobilnr,geschlecht,adresse,notfallnr,vname,nname,besonderes,erziehungsberechtigte,gebdatum,fahrtnr)	

// Prüfungen 
// 1. Schüler schon vorhanden!!
	query := fmt.Sprintf (`
    SELECT  COUNT(*) AS NEU
    FROM    schueler
    WHERE   teilnnr='%s';`, teilnr)
    fmt.Printf ("%s\n\n", query)
    rs := conn.Anfrage (query)
	for rs.GibtTupel () {rs.LeseTupel (&anzahl)}
	if anzahl != 0 {schuelervorhanden = true}

// 2. Fahrt vorhanden!!
	query = fmt.Sprintf (`
    SELECT  COUNT(*) AS NEU
    FROM    fahrt
    WHERE   fahrtnr='%s';`, fahrtnr)
    fmt.Printf ("%s\n\n", query)
    rs = conn.Anfrage (query)
    for rs.GibtTupel () {rs.LeseTupel (&anzahl)}
	if anzahl != 0 {fahrtvorhanden = true} 

	println (schuelervorhanden)
	println (fahrtvorhanden)

// 3. Schüler schon in Fahrt vorhanden!!
	query = fmt.Sprintf (`
    SELECT  COUNT(*) AS NEU
    FROM    fahrt natural join faehrtmit
    WHERE   fahrtnr='%s'AND teilnnr='%s';`,fahrtnr,teilnr)
    fmt.Printf ("%s\n\n", query)
    rs = conn.Anfrage (query)
    for rs.GibtTupel () {rs.LeseTupel (&anzahl)}
	if anzahl != 0 {schuelerinfahrt = true} 

	println (schuelervorhanden)
	println (fahrtvorhanden)
	println (schuelerinfahrt)
	
// Eingabe

//Fahrt nicht vorhanden
if !fahrtvorhanden {Was_kommt_raus_loeschen (); Was_kommt_raus ("","Die Fahrt ist nicht eingetragen. Eintragung von " + nname + " nicht erfolgreich!"); rueckgabe = Warten_auf_Button_klick (a); return rueckgabe}

// Schüler schon in der Fahrt eingetragen
if schuelerinfahrt {Was_kommt_raus_loeschen (); Was_kommt_raus ("", nname + " schon in der Fahrt mit der Nummer " + fahrtnr + " eingetragen"); rueckgabe = Warten_auf_Button_klick (a); return rueckgabe}

if !schuelervorhanden {
	stri := "INSERT INTO teilnehmer VALUES ("  + teilnr  + "," + mobilnr + ",'" + geschlecht +"','" + adresse + "','"  + notfallnr + "','" + vname + "','" + nname + "','" + besonderes + "');"
	fmt.Println (stri)
	n = conn.Ausfuehren (stri)

	stri = "INSERT INTO schueler VALUES ("  + teilnr  + ",'" + erziehungsberechtigte + "','" + gebdatum + "');"
	fmt.Println (stri)
	n = conn.Ausfuehren (stri)
	} 

	stri := "INSERT INTO faehrtmit VALUES (" + teilnr  + "," + fahrtnr +");"
	fmt.Println (stri)
	n = conn.Ausfuehren (stri)
	
Was_kommt_raus_loeschen () 
Was_kommt_raus ("","Eintragung von " + nname + " erfolgreich!")


// Button auslösen

rueckgabe = Warten_auf_Button_klick (a)	


return rueckgabe
}




//////////////////////////////////////////////////////////////////////////////////////
//             MAIN
//////////////////////////////////////////////////////////////////////////////////////



func main () {
var weiter uint
gfx.Fenster (breite,hoehe)	

// SQL Verbidung wird eingerichtet
  conn = SQL.PgSQL ("user=lewein dbname=lewein")
  defer conn.Beenden ()



	for {
		weiter = eintragung_schueler ()
		if weiter == 0 {break}
	}	
}

