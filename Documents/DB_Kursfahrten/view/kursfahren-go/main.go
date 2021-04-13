package main

//////////////////////////////////////////////////////////////////////////////////////
//             Importieren
//////////////////////////////////////////////////////////////////////////////////////

//import "fmt"
import button "./eckigebutton"
import "gfx"
import "SQL"
import "./formularfelder"
import "fmt"
import "strconv"
//import "time"


//////////////////////////////////////////////////////////////////////////////////////
//             Structs
//////////////////////////////////////////////////////////////////////////////////////

type schueler struct {
	teilnnr 				int
	erziehungsberechtigte 	string
	gebdatum 				string
}

type schuelerbezahlt struct {
	fahrtname 		string
	vorname			string
	nachname		string
	fkbetrag		int
	fkdatum			string
}



type teilnehmer struct {
	teilnr 	int
	mobilnr 	string
	Geschlecht 	string
	adresse 	string
	notfallnr 	string 
	vname 		string
	nname 		string
	besonderes 	string
}

type teilnehmer2 struct {
	teilnr 		int
	mobilnr 	string
	Geschlecht 	string
	adresse 	string
	notfallnr 	string 
	vname 		string
	nname 		string
	besonderes 	string
	stand		string
}

type teilnehmer3 struct {
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
}



type bezahlt struct {
	unr 	int
	iban 	string
	bdatum 	string
	bar 	bool
}

type faehrtmit struct {
	teilnnr int
	fahrtnr int
}

type mehrfach struct {
	teilnnr		int
	nname		string
	vname		string
	fahrtname 	string
}

type begleiter struct {
	teilnnr int
	stand string
}

type besonderheit struct {
	vorname	  string
	nachname  string
	besonders string
}

type bezahltes struct {
	fahrtname 	string
	ziel		string
	titel		string
	kosten		int
	bdatum		string
}	
	
type fahrt struct {
	fahrtnr 	int
	fahrtname 	string
	ziel 		string
	von			string
	bis			string
	iban		string
}

type preis struct {
	titel	string
	kosten	int
	}

type fahrtenkonto struct {
	iban 	string
	bic  	string
	inhaber string
	bank 	string
}

type unternehmung2 struct {
	titel 	string
	udatum	string
}


var n    int64

const (
    // Eingabemasken
    alphabet = "abcdefghijklmnopqrstuvwxyzäöüß ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ"
    specials = ",;.:-_#'+*~´`!\"§$%&/()=?\\}][{^°<>|~"
    ziffern  = "0123456789"
    all      = alphabet + specials + ziffern
    // Fonts
    fontpath = "/home/lewein/font/OpenSans-Bold.ttf"
  )

var (
	breite uint16 = 1000   //Fenster
	hoehe uint16  = 600   // Fenster
	conn SQL.Verbindung

	)

//////////////////////////////////////////////////////////////////////////////////////
//             Strukturelemente
//////////////////////////////////////////////////////////////////////////////////////

func Basic (inhalt string) {
gfx.Stiftfarbe (0,0,0)	
gfx.Rechteck (0,0, 1200, 60)
gfx.Linie (0,87,breite,87)
gfx.Linie (0,hoehe-40,breite,hoehe-40)
gfx.Linie (220,87,220,hoehe-40)
gfx.SetzeFont (fontpath,30)
gfx.SchreibeFont (100,20,"Zentrale Datenbank zu Klassenfahrten an der LWB-Schule")
gfx.SetzeFont (fontpath,15)
gfx.SchreibeFont (10,560,"Datenbankpraktikum") 
gfx.SchreibeFont (10,580,"Sommersemester 2017 - FU-Berlin")
gfx.SetzeFont (fontpath,15)
gfx.SchreibeFont (400,580,"Thorsten Hartung, Thomas Nordmann, Sebastian Herker, Alisa Vogt")
gfx.SetzeFont (fontpath,15)	
gfx.SchreibeFont (5,60,inhalt)
}

func Loeschen () {
	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (0,0,breite,hoehe) 
	gfx.Stiftfarbe (0,0,0)
}

func Loeschen_Ausgabe () {
	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (221,201,breite,359)
	gfx.Stiftfarbe (0,0,0)	
}


func Abfrage () {
gfx.Stiftfarbe (0,0,0)
gfx.SetzeFont (fontpath,20)
gfx.SchreibeFont (222,90,"Anfrage")
gfx.Linie (222,200,breite,200)
gfx.SchreibeFont (222,200,"Ergebnis")
}

func Eingabe (name string) {
gfx.Stiftfarbe (0,0,0)
gfx.SetzeFont (fontpath,20)
gfx.SchreibeFont (250,110,name)
gfx.Linie (222,200,breite,200)
gfx.SetzeFont (fontpath,20)
gfx.SchreibeFont (222,200,"Eingabe")
}

//////////////////////////////////////////////////////////////////////////////////////
//             Sonderseiten
//////////////////////////////////////////////////////////////////////////////////////

func Fehlerseite () uint{
	Basic (">>> FEHLERSEITE <<<") 
	fehlermeldung := "Upps  .... da ist etwas schiefgelaufen. Drücken Sie die Enter Taste"
    gfx.SchreibeFont (300,  120, fehlermeldung)
    var a uint16
    var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen ()
	return 0
}


func _0_Startseite () uint{	
Basic ("Startseite >")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (300,200);  Startseite[0].Groesse (200,100); Startseite[0].InhaltSetzen (" Datenabfrage");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (600,200);  Startseite[1].Groesse (200,100); Startseite[1].InhaltSetzen (" Dateneingabe"); 	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

	for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 18; break} 
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 19; break} 
	}

Loeschen ()
	return rueckgabe
}
	

		
func _18_Datenabfrage () uint{
Basic ("Startseite > Datenabfrage")
	var Startseite [13] button.EckigeButtons	
	var rueckgabe uint

// FAHRTEN 
	gfx.Stiftfarbe (0,0,0)
	gfx.SetzeFont (fontpath,20)
	gfx.SchreibeFont (560,110,"Fahrten")
	gfx.SchreibeFont (560,320,"Personen")
	gfx.Linie (220,300,breite,300)

	b0  := button.New (1200,600); Startseite[0]   = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse  (200,50); Startseite[0].InhaltSetzen (" Zur Startseite"); ;	Startseite[0].Zeichnen ()
	b1  := button.New (1200,600); Startseite[1]   = b1; Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen  (" Alle Fahrten - Kurzinfo"); Startseite[1].Positionieren (230+27,160); Startseite[1].Zeichnen()
	b2  := button.New (1200,600); Startseite[2]   = b2; Startseite[2].Groesse (200,50); Startseite[2].InhaltSetzen  (" Fahrtinfo Kurz");          Startseite[2].Positionieren (480+27,160); Startseite[2].Zeichnen()
	b3  := button.New (1200,600); Startseite[3]   = b3; Startseite[3].Groesse (200,50); Startseite[3].InhaltSetzen  (" Fahrtinfo Detail");        Startseite[3].Positionieren (730+27,160); Startseite[3].Zeichnen()
	b4  := button.New (1200,600); Startseite[4]   = b4; Startseite[4].Groesse (200,50); Startseite[4].InhaltSetzen (" Gesamtkosten Fahrt");	      Startseite[4].Positionieren (230+27,220); Startseite[4].Zeichnen()	
	b5  := button.New (1200,600); Startseite[5]   = b5; Startseite[5].Groesse (200,50); Startseite[5].InhaltSetzen (" Bezahlte Unternehmungen");    Startseite[5].Positionieren (480+27,220); Startseite[5].Zeichnen()
	b6  := button.New (1200,600); Startseite[6]   = b6; Startseite[6].Groesse (200,50); Startseite[6].InhaltSetzen ("Uternehmungen nach Preis");      Startseite[6].Positionieren (730+27,220); Startseite[6].Zeichnen()
	b7  := button.New (1200,600); Startseite[7]   = b7; Startseite[7].Groesse (200,50); Startseite[7].InhaltSetzen     (" Fahrtteilnehmer");     Startseite[7].Positionieren (257,370); Startseite[7].Zeichnen()
	b8  := button.New (1200,600); Startseite[8]   = b8; Startseite[8].Groesse (200,50); Startseite[8].InhaltSetzen     (" Teilnahme Begleiter"); Startseite[8].Positionieren (480+27,370); Startseite[8].Zeichnen()
	b9  := button.New (1200,600); Startseite[9]   = b9; Startseite[9].Groesse (200,50); Startseite[9].InhaltSetzen     (" Teilnahme Schüler");   Startseite[9].Positionieren (730+27,370); Startseite[9].Zeichnen()
	b10 := button.New (1200,600); Startseite[10]  = b10; Startseite[10].Groesse (200,50); Startseite[10].InhaltSetzen  (" Schüler bezahlt");     Startseite[10].Positionieren (257,430); Startseite[10].Zeichnen()
	b11 := button.New (1200,600); Startseite[11]  = b11; Startseite[11].Groesse (200,50); Startseite[11].InhaltSetzen  (" Mehrfach Teilnahme");         Startseite[11].Positionieren (480+27,430); Startseite[11].Zeichnen()
	b12 := button.New (1200,600); Startseite[12]  = b12; Startseite[12].Groesse (200,50); Startseite[12].InhaltSetzen  (" Bes. Schüler");        Startseite[12].Positionieren (730+27,430); Startseite[12].Zeichnen()

	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()
	b2vonx,b2bisx := Startseite[2].XWertebereich()
	b2vony,b2bisy := Startseite[2].YWertebereich()
	b3vonx,b3bisx := Startseite[3].XWertebereich()
	b3vony,b3bisy := Startseite[3].YWertebereich()
	b4vonx,b4bisx := Startseite[4].XWertebereich()
	b4vony,b4bisy := Startseite[4].YWertebereich()
	b5vonx,b5bisx := Startseite[5].XWertebereich()
	b5vony,b5bisy := Startseite[5].YWertebereich()
	b6vonx,b6bisx := Startseite[6].XWertebereich()
	b6vony,b6bisy := Startseite[6].YWertebereich()
	b7vonx,b7bisx := Startseite[7].XWertebereich()
	b7vony,b7bisy := Startseite[7].YWertebereich()
	b8vonx,b8bisx := Startseite[8].XWertebereich()
	b8vony,b8bisy := Startseite[8].YWertebereich()
	b9vonx,b9bisx := Startseite[9].XWertebereich()
	b9vony,b9bisy := Startseite[9].YWertebereich()
	b10vonx,b10bisx := Startseite[10].XWertebereich()
	b10vony,b10bisy := Startseite[10].YWertebereich()
	b11vonx,b11bisx := Startseite[11].XWertebereich()
	b11vony,b11bisy := Startseite[11].YWertebereich()
	b12vonx,b12bisx := Startseite[12].XWertebereich()
	b12vony,b12bisy := Startseite[12].YWertebereich()

	for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx    && xmaus <= b0bisx    && ymaus >= b0vony   && ymaus <= b0bisy  {Startseite[0].Druecken(true); Startseite[0].Zeichnen();  rueckgabe = 0;  break} 
	if  taste ==1 && xmaus >= b1vonx    && xmaus <= b1bisx    && ymaus >= b1vony   && ymaus <= b1bisy  {Startseite[1].Druecken(true); Startseite[1].Zeichnen();  rueckgabe = 1;  break} 
	if  taste ==1 && xmaus >= b2vonx    && xmaus <= b2bisx    && ymaus >= b2vony   && ymaus <= b2bisy  {Startseite[2].Druecken(true); Startseite[2].Zeichnen();  rueckgabe = 2;  break} 
	if  taste ==1 && xmaus >= b3vonx    && xmaus <= b3bisx    && ymaus >= b3vony   && ymaus <= b3bisy  {Startseite[3].Druecken(true); Startseite[3].Zeichnen();  rueckgabe = 3;  break} 
	if  taste ==1 && xmaus >= b4vonx    && xmaus <= b4bisx    && ymaus >= b4vony   && ymaus <= b4bisy  {Startseite[4].Druecken(true); Startseite[4].Zeichnen();  rueckgabe = 4;  break} 
	if  taste ==1 && xmaus >= b5vonx    && xmaus <= b5bisx    && ymaus >= b5vony   && ymaus <= b5bisy  {Startseite[5].Druecken(true); Startseite[5].Zeichnen();  rueckgabe = 5;  break} 
	if  taste ==1 && xmaus >= b6vonx    && xmaus <= b6bisx    && ymaus >= b6vony   && ymaus <= b6bisy  {Startseite[6].Druecken(true); Startseite[6].Zeichnen();  rueckgabe = 6;  break} 
	if  taste ==1 && xmaus >= b7vonx    && xmaus <= b7bisx    && ymaus >= b7vony   && ymaus <= b7bisy  {Startseite[7].Druecken(true); Startseite[7].Zeichnen();  rueckgabe = 7;  break} 
	if  taste ==1 && xmaus >= b8vonx    && xmaus <= b8bisx    && ymaus >= b8vony   && ymaus <= b8bisy  {Startseite[8].Druecken(true); Startseite[8].Zeichnen();  rueckgabe = 8;  break} 
	if  taste ==1 && xmaus >= b9vonx    && xmaus <= b9bisx    && ymaus >= b9vony   && ymaus <= b9bisy  {Startseite[9].Druecken(true); Startseite[9].Zeichnen();  rueckgabe = 9;  break} 
	if  taste ==1 && xmaus >= b10vonx   && xmaus <= b10bisx   && ymaus >= b10vony  && ymaus <= b10bisy {Startseite[10].Druecken(true);Startseite[10].Zeichnen(); rueckgabe = 10; break} 
	if  taste ==1 && xmaus >= b11vonx   && xmaus <= b11bisx   && ymaus >= b11vony  && ymaus <= b11bisy {Startseite[11].Druecken(true);Startseite[11].Zeichnen(); rueckgabe = 11; break} 
	if  taste ==1 && xmaus >= b12vonx   && xmaus <= b12bisx   && ymaus >= b12vony  && ymaus <= b12bisy {Startseite[12].Druecken(true);Startseite[12].Zeichnen(); rueckgabe = 12; break} 
	}

Loeschen ()		
return rueckgabe
}


func _19_Dateneingabe () uint{
Basic ("Startseite > Datenabfrage")
	var Startseite [6] button.EckigeButtons	
	var rueckgabe uint

// FAHRTEN 
	gfx.Stiftfarbe (0,0,0)
	gfx.SetzeFont (fontpath,20)
	gfx.SchreibeFont (560,110,"Fahrten")
	gfx.SchreibeFont (560,320,"Personen")
	gfx.Linie (220,300,breite,300)

	b0  := button.New (1200,600); Startseite[0]   = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse  (200,50); Startseite[0].InhaltSetzen (" Zur Startseite"); ;	Startseite[0].Zeichnen ()
	b1  := button.New (1200,600); Startseite[1]   = b1; Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen  (" Fahrt eintragen");            Startseite[1].Positionieren (230+27,160); Startseite[1].Zeichnen()
	b2  := button.New (1200,600); Startseite[2]   = b2; Startseite[2].Groesse (200,50); Startseite[2].InhaltSetzen  (" Unternehmung eintragen");      Startseite[2].Positionieren (480+27,160); Startseite[2].Zeichnen()
	b3  := button.New (1200,600); Startseite[3]   = b3; Startseite[3].Groesse (200,50); Startseite[3].InhaltSetzen  (" Bezahlung Unternehmung");     Startseite[3].Positionieren (730+27,160); Startseite[3].Zeichnen()
	b4  := button.New (1200,600); Startseite[4]   = b4; Startseite[4].Groesse (200,50); Startseite[4].InhaltSetzen  (" Teilnehmer eintragen");	     Startseite[4].Positionieren (230+27,370); Startseite[4].Zeichnen()	
	b5  := button.New (1200,600); Startseite[5]   = b5; Startseite[5].Groesse (200,50); Startseite[5].InhaltSetzen  ("Zahlung eintragen");           Startseite[5].Positionieren (480+27,370); Startseite[5].Zeichnen()

	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()
	b2vonx,b2bisx := Startseite[2].XWertebereich()
	b2vony,b2bisy := Startseite[2].YWertebereich()
	b3vonx,b3bisx := Startseite[3].XWertebereich()
	b3vony,b3bisy := Startseite[3].YWertebereich()
	b4vonx,b4bisx := Startseite[4].XWertebereich()
	b4vony,b4bisy := Startseite[4].YWertebereich()
	b5vonx,b5bisx := Startseite[5].XWertebereich()
	b5vony,b5bisy := Startseite[5].YWertebereich()

	for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx    && xmaus <= b0bisx    && ymaus >= b0vony   && ymaus <= b0bisy  {Startseite[0].Druecken(true); Startseite[0].Zeichnen();  rueckgabe = 0;  break} 
	if  taste ==1 && xmaus >= b1vonx    && xmaus <= b1bisx    && ymaus >= b1vony   && ymaus <= b1bisy  {Startseite[1].Druecken(true); Startseite[1].Zeichnen();  rueckgabe = 13;  break} 
	if  taste ==1 && xmaus >= b2vonx    && xmaus <= b2bisx    && ymaus >= b2vony   && ymaus <= b2bisy  {Startseite[2].Druecken(true); Startseite[2].Zeichnen();  rueckgabe = 14;  break} 
	if  taste ==1 && xmaus >= b3vonx    && xmaus <= b3bisx    && ymaus >= b3vony   && ymaus <= b3bisy  {Startseite[3].Druecken(true); Startseite[3].Zeichnen();  rueckgabe = 15;  break} 
	if  taste ==1 && xmaus >= b4vonx    && xmaus <= b4bisx    && ymaus >= b4vony   && ymaus <= b4bisy  {Startseite[4].Druecken(true); Startseite[4].Zeichnen();  rueckgabe = 16;  break} 
	if  taste ==1 && xmaus >= b5vonx    && xmaus <= b5bisx    && ymaus >= b5vony   && ymaus <= b5bisy  {Startseite[5].Druecken(true); Startseite[5].Zeichnen();  rueckgabe = 17;  break} 
	}

Loeschen ()		
return rueckgabe
}


//////////////////////////////////////////////////////////////////////////////////////
//             Abfrageseiten
//////////////////////////////////////////////////////////////////////////////////////

func _1_alle_Fahrten_der_schule () uint {
Basic ("Startseite > Datenabfrage > Fahrten > Alle Fahrten der Schule")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel fahrt
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Aktualisieren");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Fahrten angezeigt, die an der Schule stattfinden")

// SQL Anfrage ((
query := fmt.Sprintf (`
    SELECT  *
    FROM    fahrt;`)
  
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 6 
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.fahrtnr, &tupel.fahrtname, &tupel.ziel, &tupel.von, &tupel.bis, &tupel.iban)
       if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Fahrnummer: " + strconv.Itoa (tupel.fahrtnr))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Fahrtname: " + tupel.fahrtname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Ziel: " + tupel.ziel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "von: " + tupel.von)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "bis: " + tupel.bis)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Iban: " + tupel.iban)
    posy = posy+40
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
  }

	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)

  
// Kein Ergebnis
 if tupel.fahrtnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Es finden derzeit keine Fahrten statt.")
	gfx.SchreibeFont (225,  246,  "Kehren Sie zur Startseite zurück")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 1; break}	

	}


// Alles Löschen
Loeschen ()
return rueckgabe
}
	
func _2_Fahrtinfo_Kurz () uint {
//
// Textbereich vor Fahrt!
//
Basic ("Startseite > Datenabfrage > Fahrt > Fahrtinfo Kurz")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel fahrt
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

	eingabe := formularfelder.FormEingabe (225, 120, 30, "Nummer der Fahrt", ziffern)

// SQL Anfrage ((
	query := fmt.Sprintf (`
    SELECT  *
    FROM    fahrt
    WHERE   fahrtnr='%s';`, eingabe)
 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 6 
	var posx uint16 = 225
	var posy uint16 = 230
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.fahrtnr, &tupel.fahrtname, &tupel.ziel, &tupel.von, &tupel.bis, &tupel.iban)
    gfx.SchreibeFont (posx,  posy,  "Fahrnummer: " + strconv.Itoa (tupel.fahrtnr))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Fahrtname: " + tupel.fahrtname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Ziel: " + tupel.ziel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "von: " + tupel.von)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "bis: " + tupel.bis)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Iban: " + tupel.iban)
    posy = posy+40
    if posy + anzahl_elemente * 16 > hoehe-40 {posx = posx + 250; posy = 230}
  }
  
// Kein Ergebnis
 if tupel.fahrtnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Daten für die Fahrtennummer " + eingabe + " vorhanden.")
	gfx.SchreibeFont (225,  246,  "Starten sie erneut eine Anfrage oder kehren Sie zur Startseite zurück")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 2; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe

}
	
func _3_Fahrtinfo_Detail () uint {
Basic ("Startseite > Datenabfrage > Fahrten > Alle Fahrten der Schule")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel unternehmung2
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Unternehmungen der Fahrt nach Datum sortiert angezeigt")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)

//SQL 

query := fmt.Sprintf (`
    SELECT  DISTINCT     unternehmung.titel, unternehmung.udatum
    FROM    	         unternehmung 
    WHERE   		     fahrtnr='%s'
    ORDER BY 		     udatum;`, eingabe)

  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)


// SQL Ausgabe
    var anzahl_elemente uint16 = 2
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint = 0
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.titel, &tupel.udatum)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Unternehmung: " + tupel.titel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Datum: " + tupel.udatum)
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 355 
		posy = 230
	}
	anzahl++
}
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
	
// Kein Ergebnis
if tupel.titel == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Daten zur Fahrtnummer " + eingabe + " vorhanden.")
	}



	
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break} 
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 3; break} 
}
Loeschen ()

return rueckgabe
}
	
func _4_gesamtkosten_fahrt () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Gesamtkosten")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons

	var summe int
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden die Gesamtkosten der Fahrt ausgegeben.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
	fmt.Println (eingabe)

// SQL Anfrage ((

query := fmt.Sprintf (`
    SELECT  SUM (kosten) AS Kosten 
    FROM    unternehmung
    WHERE   fahrtnr='%s';`, eingabe)
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
	var anzahl uint = 0

	for rs.GibtTupel () {
    rs.LeseTupel (&summe)
    gfx.SchreibeFont (225,  230,  "Gesamtsumme: " + strconv.Itoa (summe) +" Euro")
	anzahl++
	}
	
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
	
// Kein Ergebnis
if summe == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Kosten zur Fahrt " + eingabe + " vorhanden.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 7; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe

}

	
func _5_bezahlung_unternehmungen () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Bezahlte Unternehmungen")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel bezahltes
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden die Bezahlten Anweisungen angezeigt. Entweder alle oder alle bar bezahlten oder alle die nicht bar bezahlt wurden.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	eingabe2 := formularfelder.FormEingabe (500, 140, 30, "Für alle schreibe >>a<<, für nur bar schriebe >>b<< und für nicht bar >>nb<<", ziffern)

switch eingabe2 {
	case "a" 	  : eingabe2 = "bezahlt.bar = true or bezahlt.bar = false"
	case "b" 	  : eingabe2 = "bezahlt.bar = true"
	case "nb" 	  : eingabe2 = "bezahlt.bar = false"	
	default  : Loeschen (); return 100 
	}

// SQL Anfrage ((

query := fmt.Sprintf (`

	SELECT fahrt.fahrtname, fahrt.ziel, Neu.titel, Neu.kosten, Neu.bdatum 
	FROM   fahrt natural join (select unternehmung.kosten, bezahlt.bdatum, unternehmung.titel from unternehmung natural join bezahlt natural join fahrt where %s) As Neu 
	where  fahrt.fahrtnr = '%s';`, eingabe2, eingabe)

 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 4
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.fahrtname, &tupel.ziel, &tupel.titel, &tupel.kosten, &tupel.bdatum)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Fahrtname: " + tupel.fahrtname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Ziel: " + tupel.ziel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Titel: " + tupel.titel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Kosten: " + strconv.Itoa (tupel.kosten))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Belegdatum: " + tupel.bdatum)
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}

	
// Kein Ergebnis
if tupel.fahrtname == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Kosten zur Fahrt " + eingabe + " vorhanden.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 5; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe

}
	
func _6______ () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Unternehmungen nach Preis")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel preis
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Unternehmungen der angegeben Fahrt die teurer als die angegebene Summe sind.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	eingabe2 := formularfelder.FormEingabe (500, 140, 30, "Mindestbetrag", ziffern)

// SQL Anfrage ((

query := fmt.Sprintf (`
	SELECT unternehmung.titel, unternehmung.kosten 
	FROM   fahrt natural join unternehmung  
	where  fahrt.fahrtnr = %s and unternehmung.kosten > %s;`, eingabe, eingabe2)

 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 2
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.titel, &tupel.kosten)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Titel: " + tupel.titel)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Kosten: " + strconv.Itoa (tupel.kosten) + " Euro")
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}

	
// Kein Ergebnis
if tupel.titel == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Die Fahrt " + eingabe + " hat keine Unternehmungen die mehr als " + eingabe2 +" Euro kosten.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 6; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe

}
	
func _7_Fahrtteilnehmer () uint {
Basic ("Startseite > Datenabfrage > Teilnehmer > Fahrtteilnehmer")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel teilnehmer
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Teilnehmer die bei der angegeben Fahrt mitfahren angezeigt.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
	fmt.Println (eingabe)

// SQL Anfrage ((

query := fmt.Sprintf (`
    SELECT  teilnehmer.teilnnr,teilnehmer.mobilnr, teilnehmer.Geschlecht, teilnehmer.adresse, teilnehmer.notfallnr, teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes 
    FROM    teilnehmer natural join faehrtmit natural join fahrt
    WHERE   fahrtnr='%s';`, eingabe)
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 8
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint = 0
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.teilnr, &tupel.mobilnr, &tupel.Geschlecht, &tupel.adresse, &tupel.notfallnr, &tupel.vname, &tupel.nname, &tupel.besonderes)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
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
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
}
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
	
// Kein Ergebnis
if tupel.teilnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Begleiter zur Fahrt " + eingabe + " vorhanden.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 7; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe

}

func _8_Teilnahme_Begleiter () uint {
Basic ("Startseite > Datenabfrage > Teilnehmer > Begleiter")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel teilnehmer2
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Begleiter die bei der angegeben Fahrt mitfahren angezeigt.")

	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	

// SQL Anfrage ((

query := fmt.Sprintf (`
    SELECT  teilnehmer.teilnnr,teilnehmer.mobilnr, teilnehmer.Geschlecht, teilnehmer.adresse, teilnehmer.notfallnr, teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes, begleiter.stand 
    FROM    teilnehmer natural join faehrtmit natural join fahrt natural join begleiter
    WHERE   fahrtnr='%s';`, eingabe)
 
 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 9
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.teilnr, &tupel.mobilnr, &tupel.Geschlecht, &tupel.adresse, &tupel.notfallnr, &tupel.vname, &tupel.nname, &tupel.besonderes, &tupel.stand)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
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
    gfx.SchreibeFont (posx,  posy, "Stand: " + tupel.stand)
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	anzahl++
	}
		
}
  
  	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel.teilnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Begleiter zur Fahrt " + eingabe + " vorhanden.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 8; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe
}
	
func _9_Teilnahme_Schueler () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Teilnehmer > Schüler")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel teilnehmer3
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Schüler die bei der angegeben Fahrt mitfahren angezeigt.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
	fmt.Println (eingabe)

// SQL Anfrage ((

query := fmt.Sprintf (`
    SELECT  teilnehmer.teilnnr,teilnehmer.mobilnr, teilnehmer.Geschlecht, teilnehmer.adresse, teilnehmer.notfallnr, teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes, schueler.erziehungsberechtigte, schueler.gebdatum 
    FROM    teilnehmer natural join faehrtmit natural join fahrt natural join schueler
    WHERE   fahrtnr='%s';`, eingabe)
 
 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 8
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.teilnr, &tupel.mobilnr, &tupel.Geschlecht, &tupel.adresse, &tupel.notfallnr, &tupel.vname, &tupel.nname, &tupel.besonderes, &tupel.erziehungsberechtigte, &tupel.gebdatum)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
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
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}
  
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel.teilnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Keine Begleiter zur Fahrt " + eingabe + " vorhanden.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 1; break}

	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe
}

func _10_Schueler_bezahlt () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Teilnehmer > Schüler bezahlt")
Abfrage ()
	var rueckgabe uint
	var Startseite [4] button.EckigeButtons
	var tupel1 schuelerbezahlt


	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b2 := button.New (1200,600); Startseite[2] = b2; Startseite[2].Positionieren (225,120);  Startseite[2].Groesse (200,50); Startseite[2].InhaltSetzen (" Alle Schüler");	        Startseite[2].Zeichnen ()
	b3 := button.New (1200,600); Startseite[3] = b3; Startseite[3].Positionieren (450,120);  Startseite[3].Groesse (200,50); Startseite[3].InhaltSetzen (" Ausgewählter Schüler");	Startseite[3].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()
	b2vonx,b2bisx := Startseite[2].XWertebereich()
	b2vony,b2bisy := Startseite[2].YWertebereich()
	b3vonx,b3bisx := Startseite[3].XWertebereich()
	b3vony,b3bisy := Startseite[3].YWertebereich()

var auswahl uint 

for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b2vonx   && xmaus <= b2bisx   && ymaus >= b2vony  && ymaus <= b2bisy {Startseite[2].Druecken(true);Startseite[2].Zeichnen(); auswahl = 0; break}
	if  taste ==1 && xmaus >= b3vonx   && xmaus <= b3bisx   && ymaus >= b3vony  && ymaus <= b3bisy {Startseite[3].Druecken(true);Startseite[3].Zeichnen(); auswahl = 1; break}
}

	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (221,117,450,118)
	gfx.Stiftfarbe (0,0,0)	
	Abfrage ()
	gfx.SetzeFont (fontpath,12)

	
// Auswahlfeld ////

//
var eingabe string

	
if auswahl == 0 {
    gfx.SchreibeFont (225,  120,  "Alle Teilnehmer, die bezahlt haben werden ausgegeben.")
	eingabe = formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
// SQL Anfrage /////

query := fmt.Sprintf (`
	SELECT fahrt.fahrtname, teilnehmer.vname, teilnehmer.nname, ueberwiesen.fkbetrag, ueberwiesen.fkdatum 
	FROM ueberwiesen natural join teilnehmer natural join schueler natural join fahrt natural join faehrtmit 
	WHERE fahrt.fahrtnr = '%s';`, eingabe) 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 3
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint

  for rs.GibtTupel () {
    rs.LeseTupel (&tupel1.fahrtname, &tupel1.nachname, &tupel1.vorname, &tupel1.fkbetrag, &tupel1.fkdatum)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Nr: " +  tupel1.fahrtname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Nachname: " + tupel1.nachname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Vorname: " + tupel1.vorname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Betrag: " + strconv.Itoa (tupel1.fkbetrag))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Datum: " + tupel1.fkdatum)
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}
  
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel1.fahrtname == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Es hat noch keiner bezahlt oder die Fahrt existiert nicht")
	}



} else {
	gfx.SchreibeFont (225,  120,  "Alle Teilnehmer, die bezahlt haben werden ausgegeben.")
	eingabe = formularfelder.FormEingabe (225, 140, 30, "Nachname", ziffern)	

// SQL Anfrage ((

query := fmt.Sprintf (` 
	SELECT 	fahrt.fahrtname, teilnehmer.vname, teilnehmer.nname, ueberwiesen.fkbetrag, ueberwiesen.fkdatum 
	FROM 	ueberwiesen natural join teilnehmer natural join schueler natural join fahrt natural join faehrtmit 
	WHERE 	teilnehmer.nname = '%s';`, eingabe)
  

  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 8
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
	
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel1.fahrtname, &tupel1.nachname, &tupel1.vorname, &tupel1.fkbetrag, &tupel1.fkdatum)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    gfx.SchreibeFont (posx,  posy,  "Nr: " +  tupel1.fahrtname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Nachname: " + tupel1.nachname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Vorname: " + tupel1.vorname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Betrag: " + strconv.Itoa (tupel1.fkbetrag))
    posy = posy+16
    gfx.SchreibeFont (posx,  posy,  "Datum: " + tupel1.fkdatum)
    posy = posy+35    
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}
  
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel1.fahrtname == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Für den Namen: " + eingabe + "ist keine Überweisung eingetragen.")
	}

}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 10; break}
	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe	
}	
	
		
func _11_Mehrfach_Teilnahme () uint {
Basic ("Startseite > Datenabfrage > Fahrten > Mehrfachteilnahme")
Abfrage ()
	var rueckgabe uint
	var Startseite [2] button.EckigeButtons
	var tupel mehrfach
	
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Aktualisieren");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()

    gfx.SchreibeFont (225,  120,  "Es werden alle Schüler angezeigt, die an mehr als einer Fahrt teilnehmen")

// SQL Anfrage ((
query := fmt.Sprintf (`select new.teilnnr, new.nname, new.vname, fahrt.fahrtname from fahrt natural join faehrtmit natural join (select teilnehmer.teilnnr,teilnehmer.nname, teilnehmer.vname from faehrtmit natural join teilnehmer natural join fahrt group by teilnehmer.teilnnr, teilnehmer.nname  having count (*) > 1) As new;`)

  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 5 
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint
	var teilnnr int
	var nname, vname string
	var altposx uint16 
	var altposy uint16
  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.teilnnr, &tupel.nname, &tupel.vname, &tupel.fahrtname)
       if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
    if teilnnr != tupel.teilnnr { 
	anzahl++
    gfx.SchreibeFont (posx,  posy,  "Teilnehmernummer: " + strconv.Itoa (tupel.teilnnr))
    teilnnr = tupel.teilnnr
    posy = posy+16
	}
	if nname != tupel.nname {
    gfx.SchreibeFont (posx,  posy,  "Nachname: " + tupel.nname)
    nname = tupel.nname
    posy = posy+16
	}
	if vname != tupel.vname {
    gfx.SchreibeFont (posx,  posy, "Vorname: " + tupel.vname)
    vname = tupel.vname 
    posy = posy+16
	} else {
	posy = altposy+16 
	posx = altposx
	}
    gfx.SchreibeFont (posx,  posy, "Fahrtname: " + tupel.fahrtname)
	altposy = posy
	altposx = posx 
	posy = posy+40

    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
  }

	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)

  
// Kein Ergebnis
 if tupel.teilnnr == 0 {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Es ginbt keine Schüler, die an mehr als an einer Fahrt teilnehmen.")
	}

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 11; break}	

	}


// Alles Löschen
Loeschen ()
return rueckgabe

}

func _12_Bes_Schueler () uint {
Basic ("Startseite > Datenabfrage > Fahrt > Teilnehmer > Besonderheiten")
Abfrage ()
	var rueckgabe uint
	var Startseite [4] button.EckigeButtons
//	var tupel1 schuelerbezahlt
	var auswahl uint 
	var tupel besonderheit

	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Erneute Anfrage");	Startseite[1].Zeichnen ()
	b2 := button.New (1200,600); Startseite[2] = b2; Startseite[2].Positionieren (225,120);  Startseite[2].Groesse (200,50); Startseite[2].InhaltSetzen (" Schüler");	        Startseite[2].Zeichnen ()
	b3 := button.New (1200,600); Startseite[3] = b3; Startseite[3].Positionieren (450,120);  Startseite[3].Groesse (200,50); Startseite[3].InhaltSetzen (" Begleiter");	Startseite[3].Zeichnen ()
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()
	b2vonx,b2bisx := Startseite[2].XWertebereich()
	b2vony,b2bisy := Startseite[2].YWertebereich()
	b3vonx,b3bisx := Startseite[3].XWertebereich()
	b3vony,b3bisy := Startseite[3].YWertebereich()



for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b2vonx   && xmaus <= b2bisx   && ymaus >= b2vony  && ymaus <= b2bisy {Startseite[2].Druecken(true);Startseite[2].Zeichnen(); auswahl = 0; break}
	if  taste ==1 && xmaus >= b3vonx   && xmaus <= b3bisx   && ymaus >= b3vony  && ymaus <= b3bisy {Startseite[3].Druecken(true);Startseite[3].Zeichnen(); auswahl = 1; break}
}

	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (221,117,450,118)
	gfx.Stiftfarbe (0,0,0)	
	Abfrage ()
	gfx.SetzeFont (fontpath,12)

if auswahl == 0 {
	
    gfx.SchreibeFont (225,  120,  "Alle Besonderheiten der Schüler der angegeben Fahrt.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
// SQL Anfrage /////

query := fmt.Sprintf (`
	SELECT 	teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes 
	FROM	teilnehmer natural join schueler natural join faehrtmit natural join fahrt 
	WHERE fahrt.fahrtnr = '%s';`, eingabe) 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 1
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint

  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.vorname, &tupel.nachname, &tupel.besonders)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
	if tupel.besonders == "" {continue}
	gfx.SchreibeFont (posx,  posy, "Vorname: " + tupel.vorname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Nachname: " + tupel.nachname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Besonderes: " + tupel.besonders)
    posy = posy+35 
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}
  
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel.vorname == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Es gibt für die Fahrt" + eingabe + "keine Besonderheiten")
}


	
} else {
    gfx.SchreibeFont (225,  120,  "Alle Besonderheiten der Schüler der angegeben Fahrt.")
	eingabe := formularfelder.FormEingabe (225, 140, 30, "Nummer der Fahrt", ziffern)
	
// SQL Anfrage /////

query := fmt.Sprintf (`
	SELECT 	teilnehmer.vname, teilnehmer.nname, teilnehmer.besonderes 
	FROM	teilnehmer natural join begleiter natural join faehrtmit natural join fahrt 
	WHERE fahrt.fahrtnr = '%s';`, eingabe) 
 
  fmt.Printf ("%s\n\n", query)
  rs := conn.Anfrage (query)

// SQL Ausgabe
    var anzahl_elemente uint16 = 1
	var posx uint16 = 225
	var posy uint16 = 230
	var anzahl uint

  for rs.GibtTupel () {
    rs.LeseTupel (&tupel.vorname, &tupel.nachname, &tupel.besonders)
    if posx > 800 {
		gfx.Stiftfarbe (204,0,0)
		gfx.SchreibeFont (800,  530,  "Enter Taste drücken")
		var a uint16
		var b uint8
		for {
			a,b,_ = gfx.TastaturLesen1()
			if a == 13 && b == 1 {break}
		}
		Loeschen_Ausgabe ()
		posx = 225 
		posy = 230
	}
	if tupel.besonders == "" {continue}
	gfx.SchreibeFont (posx,  posy, "Vorname: " + tupel.vorname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Nachname: " + tupel.nachname)
    posy = posy+16
    gfx.SchreibeFont (posx,  posy, "Besonderes: " + tupel.besonders)
    posy = posy+35 
    if posy + anzahl_elemente * 16 > 530 {   // In die nächste Zeile
		posx = posx + 350 
		posy = 230
	}
	anzahl++
		
}
  
	erg := "Anzahl der Ergebnisse " + fmt.Sprint(anzahl)
	gfx.SchreibeFont (222,  530,  erg)
  
// Kein Ergebnis
if tupel.vorname == "" {
	gfx.Stiftfarbe (204,0,0)
    gfx.SchreibeFont (225,  230,  "Es gibt für die Fahrt " + eingabe + " keine Besonderheiten")
}

}


for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 12; break}
	}
	
// Alles Löschen
Loeschen ()

// Rückgabewert
	return rueckgabe	
}	


func _13_Fahrt_eintragen () uint {
Basic ("Startseite > Dateneingabe > Fahrt")
Eingabe ("Hier werden die Fahrten eingetragen")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Weitere Fahrt tragen");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()


	fahrtnummer := formularfelder.FormEingabe (225, 230, 30, "Nummer der Fahrt", ziffern)
	fahrtname	:= formularfelder.FormEingabe (225, 230+35, 30, "Name der Fahrt", alphabet)
	ziel		:= formularfelder.FormEingabe (225, 300   , 30,"Ziel", alphabet)
	von			:= formularfelder.FormEingabe (225, 300+35, 30, "von >>Eingabeformat YYYY-MM-DD<<", alphabet)
	bis			:= formularfelder.FormEingabe (225, 300+70, 30, "bis >>Eingabeformat YYYY-MM-DD<<", alphabet)
	iban		:= formularfelder.FormEingabe (225, 370+35, 30, "IBAN", alphabet)
	bic			:= formularfelder.FormEingabe (225, 405+35, 30, "BIC", alphabet)
	inhaber		:= formularfelder.FormEingabe (225, 405+70, 30, "Inhaber", alphabet)
	bank		:= formularfelder.FormEingabe (225, 475+35, 30, "Bank", alphabet)
		

fmt.Println (fahrtnummer,bic,fahrtname, ziel,von,bis,iban,bis,inhaber,bank)

var test int
var anzahl uint

for {
	query := fmt.Sprintf (`
    SELECT  fahrt.fahrtnr
    FROM    fahrt
    WHERE   fahrtnr='%s';`, fahrtnummer)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	break
} else {
	gfx.SchreibeFont (490,  230,  "Fahrtnummer vorhanden neue Fahrtnummer eingeben!")
	fahrtnummer = formularfelder.FormEingabe (490, 265, 30, "Nummer der Fahrt", ziffern)
	anzahl = 0
	}
}

// Fahrt eintragen


var test22 string
zw := "'" + iban + "'"

// Iban schon vorhanden
	var teste bool
	query := fmt.Sprintf (`
    SELECT  fahrtenkonto.iban
    FROM    fahrtenkonto
    WHERE   iban=%s;`, zw)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test22)
	teste = true
	}

if !teste {
	stri2 := "INSERT INTO fahrtenkonto VALUES ('"  + iban  + "','" + bic + "','" + inhaber +"','" + bank + "');"
	fmt.Println (stri2)	
	n = conn.Ausfuehren (stri2)
}

stri := "INSERT INTO fahrt VALUES ("  + fahrtnummer  + ",'" + fahrtname + "','" + ziel +"','" + von + "','"  + bis + "','" + iban + "');"
fmt.Println (stri)
n = conn.Ausfuehren (stri)


Loeschen_Ausgabe ()

	gfx.SchreibeFont (225, 230, "Daten wurden erfolgreich eingegeben")

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 13; break}	
	}


// Alles Löschen


Loeschen ()
return rueckgabe
}
	
func _17_Zahlung_eintragen () uint {
Basic ("Startseite > Dateneingabe > Fahrt")
Eingabe ("Hier werden die Fahrten eingetragen")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Weitere Fahrt tragen");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()


	fahrtnummer := formularfelder.FormEingabe (225, 230, 30, "Nummer der Fahrt", ziffern)
	fahrtname	:= formularfelder.FormEingabe (225, 230+35, 30, "Name der Fahrt", alphabet)
	ziel		:= formularfelder.FormEingabe (225, 300   , 30,"Ziel", alphabet)
	von			:= formularfelder.FormEingabe (225, 300+35, 30, "von >>Eingabeformat YYYY-MM-DD<<", alphabet)
	bis			:= formularfelder.FormEingabe (225, 300+70, 30, "bis >>Eingabeformat YYYY-MM-DD<<", alphabet)
	iban		:= formularfelder.FormEingabe (225, 370+35, 30, "IBAN", alphabet)
	bic			:= formularfelder.FormEingabe (225, 405+35, 30, "BIC", alphabet)
	inhaber		:= formularfelder.FormEingabe (225, 405+70, 30, "Inhaber", alphabet)
	bank		:= formularfelder.FormEingabe (225, 475+35, 30, "Bank", alphabet)
		

fmt.Println (fahrtnummer,bic,fahrtname, ziel,von,bis,iban,bis,inhaber,bank)

var test int
var anzahl uint

for {
	query := fmt.Sprintf (`
    SELECT  fahrt.fahrtnr
    FROM    fahrt
    WHERE   fahrtnr='%s';`, fahrtnummer)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	break
} else {
	gfx.SchreibeFont (490,  230,  "Fahrtnummer vorhanden neue Fahrtnummer eingeben!")
	fahrtnummer = formularfelder.FormEingabe (490, 265, 30, "Nummer der Fahrt", ziffern)
	anzahl = 0
	}
}

// Fahrt eintragen

stri := "INSERT INTO fahrt VALUES ("  + fahrtnummer  + ",'" + fahrtname + "','" + ziel +"','" + von + "','"  + bis + "','" + iban + "');"
fmt.Println (stri)
n = conn.Ausfuehren (stri)

var test22 string
zw := "'" + iban + "'"

// Iban schon vorhanden
	var teste bool
	query := fmt.Sprintf (`
    SELECT  fahrtenkonto.iban
    FROM    fahrtenkonto
    WHERE   iban=%s;`, zw)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test22)
	teste = true
	}

if !teste {
	stri2 := "INSERT INTO fahrtenkonto VALUES ('"  + iban  + "','" + bic + "','" + inhaber +"','" + bank + "');"
	fmt.Println (stri2)	
	n = conn.Ausfuehren (stri2)
}


Loeschen_Ausgabe ()

	gfx.SchreibeFont (225, 230, "Daten wurden erfolgreich eingegeben")

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 13; break}	
	}


// Alles Löschen


Loeschen ()
return rueckgabe
}
	
func _15_Bezahlung_Unternehmung () uint {
Basic ("Startseite > Dateneingabe > Bezahlung Unternehmung")
Eingabe ("Hier werden die Bezahlungen eingetragen")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Weitere Fahrt tragen");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()


	unr 		:= formularfelder.FormEingabe (225, 230, 30, "Nummer der Unternehmungen", ziffern)
	iban		:= formularfelder.FormEingabe (225, 230+35, 30, "IBAN", alphabet)
	datum		:= formularfelder.FormEingabe (225, 300   , 30, "Datum >>Eingabeformat YYYY-MM-DD<<", alphabet)
	bar			:= formularfelder.FormEingabe (225, 300+35, 30, "Bar (wenn Ja: true, wenn nein false eingeben", alphabet)
			
fmt.Println (unr,iban,datum,bar)

var test int
var anzahl uint

for {
	query := fmt.Sprintf (`
    SELECT 	bezahlt.unr, bezahlt.iban
    FROM    bezahlt
    WHERE   iban='%s' AND unr='%s';`, iban, unr)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	break
} else {
	gfx.SchreibeFont (490,  230,  "Ist bereits eingetragen!")
	return 0
	}
}

// Zahlung eintragen

stri := "INSERT INTO bezahlung VALUES ("  + unr  + ",'" + iban + "','" + datum +"','" + bar + "');"
fmt.Println (stri)
n = conn.Ausfuehren (stri)



Loeschen_Ausgabe ()

	gfx.SchreibeFont (225, 230, "Daten wurden erfolgreich eingegeben")

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 15; break}	
	}


// Alles Löschen


Loeschen ()
return rueckgabe
}
	
func _16_Teilnehmer_eintragen () uint {
Basic ("Startseite > Dateneingabe > Fahrt")
Eingabe ("Hier werden die Fahrten eingetragen")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Weitere Fahrt tragen");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()


	fahrtnummer := formularfelder.FormEingabe (225, 230, 30, "Nummer der Fahrt", ziffern)
	fahrtname	:= formularfelder.FormEingabe (225, 230+35, 30, "Name der Fahrt", alphabet)
	ziel		:= formularfelder.FormEingabe (225, 300   , 30,"Ziel", alphabet)
	von			:= formularfelder.FormEingabe (225, 300+35, 30, "von >>Eingabeformat YYYY-MM-DD<<", alphabet)
	bis			:= formularfelder.FormEingabe (225, 300+70, 30, "bis >>Eingabeformat YYYY-MM-DD<<", alphabet)
	iban		:= formularfelder.FormEingabe (225, 370+35, 30, "IBAN", alphabet)
	bic			:= formularfelder.FormEingabe (225, 405+35, 30, "BIC", alphabet)
	inhaber		:= formularfelder.FormEingabe (225, 405+70, 30, "Inhaber", alphabet)
	bank		:= formularfelder.FormEingabe (225, 475+35, 30, "Bank", alphabet)
		

fmt.Println (fahrtnummer,bic,fahrtname, ziel,von,bis,iban,bis,inhaber,bank)

var test int
var anzahl uint

for {
	query := fmt.Sprintf (`
    SELECT  fahrt.fahrtnr
    FROM    fahrt
    WHERE   fahrtnr='%s';`, fahrtnummer)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	break
} else {
	gfx.SchreibeFont (490,  230,  "Fahrtnummer vorhanden neue Fahrtnummer eingeben!")
	fahrtnummer = formularfelder.FormEingabe (490, 265, 30, "Nummer der Fahrt", ziffern)
	anzahl = 0
	}
}

// Fahrt eintragen

stri := "INSERT INTO fahrt VALUES ("  + fahrtnummer  + ",'" + fahrtname + "','" + ziel +"','" + von + "','"  + bis + "','" + iban + "');"
fmt.Println (stri)
n = conn.Ausfuehren (stri)

var test22 string
zw := "'" + iban + "'"

// Iban schon vorhanden
	var teste bool
	query := fmt.Sprintf (`
    SELECT  fahrtenkonto.iban
    FROM    fahrtenkonto
    WHERE   iban=%s;`, zw)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test22)
	teste = true
	}

if !teste {
	stri2 := "INSERT INTO fahrtenkonto VALUES ('"  + iban  + "','" + bic + "','" + inhaber +"','" + bank + "');"
	fmt.Println (stri2)	
	n = conn.Ausfuehren (stri2)
}


Loeschen_Ausgabe ()

	gfx.SchreibeFont (225, 230, "Daten wurden erfolgreich eingegeben")

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 13; break}	
	}


// Alles Löschen


Loeschen ()
return rueckgabe
}
	
func _14_Unternehmung_eintragen () uint {
Basic ("Startseite > Dateneingabe > Fahrt")
Eingabe ("Hier werden die Fahrten eingetragen")

	var Startseite [2] button.EckigeButtons
	var rueckgabe uint
	
	b0 := button.New (1200,600); Startseite[0] = b0; Startseite[0].Positionieren (0,90);  Startseite[0].Groesse (200,50); Startseite[0].InhaltSetzen (" Zur Startseite");	Startseite[0].Zeichnen ()
	b1 := button.New (1200,600); Startseite[1] = b1; Startseite[1].Positionieren (0,150);  Startseite[1].Groesse (200,50); Startseite[1].InhaltSetzen (" Weitere Unternehmung eintragen");	Startseite[1].Zeichnen ()	
	b0vonx,b0bisx := Startseite[0].XWertebereich()
	b0vony,b0bisy := Startseite[0].YWertebereich()
	b1vonx,b1bisx := Startseite[1].XWertebereich()
	b1vony,b1bisy := Startseite[1].YWertebereich()


 

	UNr 				:= formularfelder.FormEingabe (225, 230, 30, "Unternehmungsnummer", ziffern)
	Titel				:= formularfelder.FormEingabe (225, 230+35, 30, "Tiel", alphabet)
	Kosten				:= formularfelder.FormEingabe (225, 300   , 30,"Kosten", alphabet)
	Veranstalter		:= formularfelder.FormEingabe (225, 300+35, 30, "Veranstalter", alphabet)
	UBesonderes			:= formularfelder.FormEingabe (225, 300+70, 30, "Besonderheiten", alphabet)
	UDatum				:= formularfelder.FormEingabe (225, 370+35, 30, "Datum >>Eingabeformat YYYY-MM-DD<<", alphabet)
	Uhrzeit				:= formularfelder.FormEingabe (225, 405+35, 30, "Uhrzeit >>Eingabeformat HH:MM:SS<<", alphabet)
	fahrtnr				:= formularfelder.FormEingabe (225, 405+70, 30, "Fahrtnummer", alphabet)
		 



var test int
var anzahl uint

	query := fmt.Sprintf (`
    SELECT  fahrt.fahrtnr
    FROM    fahrt
    WHERE   fahrtnr='%s';`, fahrtnr)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	Loeschen_Ausgabe ()
	gfx.SchreibeFont (225, 230, "Bitte legem Sie erst die Fahrt an. Drücken Sie die Enter-Taste")
	for {
		a,b,_ := gfx.TastaturLesen1()
		if a == 13 && b == 1 {break}
	}
	return 13
}

anzahl = 0

for {
	query := fmt.Sprintf (`
    SELECT  unternehmung.UNr
    FROM    unternehmung
    WHERE   UNr='%s';`, UNr)
    
	fmt.Printf ("%s\n\n", query)
	rs := conn.Anfrage (query)

	// SQL Ausgabe
	for rs.GibtTupel () {
		rs.LeseTupel (&test)
	anzahl++
	}
	
	println (anzahl)

if anzahl == 0 {
	break
} else {
	gfx.SchreibeFont (490,  230,  "Unternehmungsnummer vorhanden neue Unternehmungsnummer eingeben!")
	UNr = formularfelder.FormEingabe (490, 265, 30, "Unternehmungsnummer", ziffern)
	anzahl = 0
	}
}


stri := "INSERT INTO unternehmung VALUES (" + UNr + ",'" + Titel + "'," + Kosten + ",'" + Veranstalter + "','" + UBesonderes + "','" + UDatum + "','" + Uhrzeit + "'," + fahrtnr + ");"
fmt.Println (stri)
n = conn.Ausfuehren (stri)



Loeschen_Ausgabe ()

	gfx.SchreibeFont (225, 230, "Daten wurden erfolgreich eingegeben")

// Warten, dass es zurückgeht
for {
	_,taste,xmaus,ymaus := gfx.MausLesen1 ()
	if  taste ==1 && xmaus >= b0vonx   && xmaus <= b0bisx   && ymaus >= b0vony  && ymaus <= b0bisy {Startseite[0].Druecken(true);Startseite[0].Zeichnen(); rueckgabe = 0; break}
	if  taste ==1 && xmaus >= b1vonx   && xmaus <= b1bisx   && ymaus >= b1vony  && ymaus <= b1bisy {Startseite[1].Druecken(true);Startseite[1].Zeichnen(); rueckgabe = 14; break}	
	}


// Alles Löschen


Loeschen ()
return rueckgabe
}


//////////////////////////////////////////////////////////////////////////////////////
//             Main
//////////////////////////////////////////////////////////////////////////////////////

func main () {
gfx.Fenster (breite,hoehe)	
var lokal uint

// SQL Verbidung wird eingerichtet
  conn = SQL.PgSQL ("user=lewein dbname=lewein")
  defer conn.Beenden ()

//	Grafisches Material


lokal = _0_Startseite () 
for {
	switch lokal {
	case 0: lokal  = _0_Startseite ()
	case 1: lokal  = _1_alle_Fahrten_der_schule ()
	case 2: lokal  = _2_Fahrtinfo_Kurz () 
	case 3: lokal  = _3_Fahrtinfo_Detail ()
	case 4: lokal  = _4_gesamtkosten_fahrt ()
	case 5: lokal  = _5_bezahlung_unternehmungen ()
	case 6: lokal  = _6______ ()
	case 7: lokal  = _7_Fahrtteilnehmer ()
	case 8: lokal  = _8_Teilnahme_Begleiter ()
	case 9: lokal  = _9_Teilnahme_Schueler ()
	case 10: lokal = _10_Schueler_bezahlt ()
	case 11: lokal = _11_Mehrfach_Teilnahme ()
	case 12: lokal = _12_Bes_Schueler ()
	case 13: lokal = _13_Fahrt_eintragen ()
	case 14: lokal = _14_Unternehmung_eintragen ()
	case 15: lokal = _15_Bezahlung_Unternehmung ()
	case 16: lokal = _16_Teilnehmer_eintragen ()
	case 17: lokal = _17_Zahlung_eintragen ()
	case 18: lokal = _18_Datenabfrage ()
	case 19: lokal = _19_Dateneingabe ()
	default: lokal = Fehlerseite ()		
	}
}
}
