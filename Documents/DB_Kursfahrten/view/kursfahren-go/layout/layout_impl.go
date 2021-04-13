package layout

// Autor: Thomas Nordmann
// Zweck: Erstellung von einem Layout für Datenbanken
// Datum: 27.Juni 2017
//////////////////////////////////////////////////////////////////////////////////////
//
//   WICHTIG!!! Das Fenster ist immer breite = 1000 und hoehe = 600 !!!!!!!!
//
//////////////////////////////////////////////////////////////////////////////////////
//
//
//////////////////////////////////////////////////////////////////////////////////////
//             Importieren
//////////////////////////////////////////////////////////////////////////////////////

import "gfx"

//////////////////////////////////////////////////////////////////////////////////////
//             Globale Variablen
//////////////////////////////////////////////////////////////////////////////////////

var (
	breite uint16 = 1000   //Fenster
	hoehe uint16  = 600   // Fenster
	fontpath = "/home/lewein/font/OpenSans-Bold.ttf"
	)

//////////////////////////////////////////////////////////////////////////////////////
//             Funktionen
//////////////////////////////////////////////////////////////////////////////////////


func Basic (titel string, untertitel string, fusszeile_links string, fusszeile_rechts string) {
gfx.Stiftfarbe (0,0,0)	
gfx.Rechteck (0,0, 1200, 60)
gfx.Linie (0,87,breite,87)
gfx.Linie (0,hoehe-40,breite,hoehe-40)
gfx.Linie (220,87,220,hoehe-40)
gfx.SetzeFont (fontpath,30)
gfx.SchreibeFont (100,20, titel)
gfx.SetzeFont (fontpath,15)
gfx.SchreibeFont (10,580,fusszeile_links)
gfx.SetzeFont (fontpath,15)
gfx.SchreibeFont (400,580,fusszeile_rechts)
gfx.SetzeFont (fontpath,15)	
gfx.SchreibeFont (5,60,untertitel)
}


func Was_wird_gemacht (titel string, beschreibung string) {
	gfx.Stiftfarbe (0,0,0)
	gfx.SetzeFont (fontpath,20)
	gfx.SchreibeFont (222,90,titel)
	gfx.Linie (222,200,breite,200)
	gfx.SetzeFont (fontpath,15)
	gfx.SchreibeFont (222,110,beschreibung)
}

func Was_kommt_raus (titel string, beschreibung string) {
	gfx.Stiftfarbe (0,0,0)
	gfx.SetzeFont (fontpath,20)
	gfx.SchreibeFont (222,200, titel)
	gfx.SetzeFont (fontpath,15)
	gfx.SchreibeFont (222,222, beschreibung)
}


func Ganze_Seite_Loeschen () {
	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (0,0,breite,hoehe) 
	gfx.Stiftfarbe (0,0,0)
}


func Was_kommt_raus_loeschen () {
	gfx.Stiftfarbe (255,255,255)
	gfx.Vollrechteck (221,201,breite,359)
	gfx.Stiftfarbe (0,0,0)	
}





