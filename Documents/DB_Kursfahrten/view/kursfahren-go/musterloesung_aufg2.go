// Autor: Thomas Nordmann
// Zweck: Musterlösung Aufgabe 2
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
				"strconv"
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
	b1 := button.New (1200,600); but[1] = b1; but[1].Positionieren (0,150);  but[1].Groesse (200,50); but[1].InhaltSetzen ("Erneute Anfrage");	but[1].Zeichnen ()
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


func teilnehmer_schueler_name () uint {
// Notwendige Variablen
var (
	rueckgabe uint
//	but 
	tupel teilnehmer
    anzahl_elemente uint16 = 11
	posx uint16 = 225
	posy uint16 = 230
	anzahl uint
	)
	
	
// Grundlayout

Basic ("Aufgabe 1", "Schüler abfragen","Thomas Nordmann","Musterlösung" )
Was_wird_gemacht ("Eingabe", "Alle Schüler und Schülerinnen, die an der Fahrt teilnehmen")
Was_kommt_raus ("Ausgabe", "")
a := Buttonzeichnen () 


// SQL Anfrage 


eingabe := formularfelder.FormEingabe (225, 140, 30, "Nachname des Schülers, der Schülerin", alphabet)
query := fmt.Sprintf (`
    SELECT  teilnehmer.teilnnr,teilnehmer.mobilnr, teilnehmer.Geschlecht, teilnehmer.adresse, teilnehmer.notfallnr, teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes, schueler.erziehungsberechtigte, schueler.gebdatum, fahrt.fahrtname 
    FROM    teilnehmer natural join faehrtmit natural join fahrt natural join schueler
    WHERE   teilnehmer.nname='%s';`, eingabe)
    fmt.Printf ("%s\n\n", query)
    rs := conn.Anfrage (query)
    
    // SQL Ausgabe

	for rs.GibtTupel () {
    rs.LeseTupel (&tupel.teilnr, &tupel.mobilnr, &tupel.Geschlecht, &tupel.adresse, &tupel.notfallnr, &tupel.vname, &tupel.nname, &tupel.besonderes, &tupel.erziehungsberechtigte, &tupel.gebdatum, &tupel.fahrtname)
    
    
     // Optimierung der Ausgabe, wenn es mehr Einträge gibt, als Platz zur Verfügung stehen
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Was_kommt_raus_loeschen ()
		posx = 225 
		posy = 230
	}
 	
// Ausgabe der Ergebnisse

    gfx.SchreibeFont (posx,  posy,  "Nr: " + strconv.Itoa (tupel.teilnr))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Vorname: " + tupel.vname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Nachname: " + tupel.nname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Geschlecht: " + tupel.Geschlecht)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Adresse: " + tupel.adresse)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Mobil: " + tupel.mobilnr)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Notfall: " + tupel.notfallnr)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Besonderes: " + tupel.besonderes)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Erziehungsberechtig: " + tupel.erziehungsberechtigte)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Geburtsdatum: " + tupel.gebdatum)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Fahrtname: " + tupel.fahrtname)
    posy = posy+35    
 
 // Zeile voll? Dann in die nächste Zeile
    if posy + anzahl_elemente * 16 > 530 {
		posx = posx + 350 
		posy = 230
	}

// Azahl der Ergebisse inkrementieren
	anzahl++
}

// Keine Ergebnisse? 

if anzahl == 0 {Was_kommt_raus ("Ausgabe", "Schüler od. SChülerin " + eingabe + " fährt bei keiner Fahrt mit !")}

//Anzahl der Ergbnisse:

erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
gfx.SchreibeFont (222,  530,  erg)
  


// Button auslösen

rueckgabe = Warten_auf_Button_klick (a)	

// Alles Lösche

Ganze_Seite_Loeschen ()

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
		weiter = teilnehmer_schueler_name ()
		if weiter == 0 {break}
	}	
}

