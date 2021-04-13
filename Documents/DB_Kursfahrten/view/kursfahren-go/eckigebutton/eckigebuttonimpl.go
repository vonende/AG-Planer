// Datum: 21.03.2017
// Autoren: Thomas Nordmann und Thorsten Hartung
// Zweck: Implementierung "eckige Button"
// Benötigte Klassen: -- keine --

package eckigebutton 

import "gfx"

var fontpath string = "/home/lewein/font/OpenSans-Bold.ttf"

type impl struct {
	x				uint16
	y				uint16
	rgb1			uint8
	rgb2			uint8
	rgb3 			uint8
	breite	 		uint16
	hoehe			uint16
	fx				uint16
	fy				uint16
	gedrueckt       bool
	inhalt          string
}

func New (fensterbreite, fensterhoehe uint16) *impl {
a := new (impl)
(*a).fx = fensterbreite
(*a).fy = fensterhoehe
(*a).gedrueckt = false
(*a).rgb1 =200 ; (*a).rgb2 =200 ; (*a).rgb3 = 200
return a

}

func (a *impl) InhaltSetzen (s string) {
(*a).inhalt = s
}

func (a *impl) XWertebereich () (von,bis uint16) {
von = (*a).x
bis = (*a).x + (*a).breite
return von,bis
}

func (a *impl) YWertebereich () (von,bis uint16) {
von = (*a).y
bis = (*a).y + (*a).hoehe
return von,bis
}

func (a *impl) Druecken (wert bool) {
(*a).gedrueckt = wert
	if wert {
	(*a).rgb1=0 ; (*a).rgb2 =255; (*a).rgb3 =51
	}else    {
		(*a).rgb1 =200 ; (*a).rgb2 =200 ; (*a).rgb3 = 200
	}
}

func (a *impl) Gedrueckt () bool {
return (*a).gedrueckt 
}

func (a *impl) Positionieren (x,y uint16) {
	(*a).x = x
	(*a).y = y 
}

func (a *impl) Groesse (breite,hoehe uint16) {
(*a).breite = breite
(*a).hoehe = hoehe
}

func (a *impl) Zeichnen () {
gfx.UpdateAus()
gfx.Sperren ()
gfx.Stiftfarbe ((*a).rgb1,(*a).rgb2,(*a).rgb3)
gfx.Vollrechteck ((*a).x, (*a).y, (*a).breite, (*a).hoehe)
gfx.Stiftfarbe (0,0,0)
gfx.SetzeFont (fontpath,int (float64((*a).hoehe)/4))
gfx.SchreibeFont ((*a).x,(*a).y + uint16(float64((*a).hoehe)/4),(*a).inhalt)
gfx.Entsperren()
gfx.UpdateAn()
}

func (a *impl) Loeschen () {
gfx.UpdateAus ()
gfx.Stiftfarbe (255,255,255)
gfx.Vollrechteck ((*a).x, (*a).y, (*a).breite, (*a).hoehe)
gfx.UpdateAn()
}
