CREATE TABLE Fahrtenkonto (IBAN varchar(22) primary key, BIC varchar(11), Inhaber varchar(40), Bank varchar(40));
CREATE TABLE Fahrt (FahrtNr integer  primary key, Fahrtname varchar(40), Ziel varchar(40), Von date, Bis date, IBAN varchar(22) references Fahrtenkonto);
CREATE TABLE Teilnehmer (TeilnNr integer primary key, MobilNr varchar(20), Geschlecht char, Adresse varchar(100), NotfallNr varchar(20), VName varchar(40), NName varchar(40), Besonderes varchar(100));
CREATE TABLE Begleiter (TeilnNr integer references Teilnehmer primary key, Stand varchar(20));
CREATE TABLE Schueler (TeilnNr integer references Teilnehmer primary key, Erziehungsberechtigte varchar(40), GebDatum date);
CREATE TABLE Faehrtmit (TeilnNr integer references Teilnehmer, FahrtNr integer references Fahrt, CONSTRAINT beg PRIMARY KEY (TeilnNr, FahrtNr));
CREATE TABLE Unternehmung (UNr integer primary key, Titel varchar(40), Kosten float(20), Veranstalter varchar(40), UBesonderes varchar(200), UDatum date, Uhrzeit time, FahrtNr integer references Fahrt);
CREATE TABLE Bezahlt (UNr integer references Unternehmung, IBAN varchar(22) references Fahrtenkonto , BDatum date, Bar bool, CONSTRAINT bez PRIMARY KEY (IBAN, UNr));
CREATE TABLE Ueberwiesen (TeilnNr integer references Teilnehmer, FahrtNr integer references Fahrt, FKBetrag float(20), FKDatum date, CONSTRAINT ueb PRIMARY KEY (TeilnNr, FahrtNr));
