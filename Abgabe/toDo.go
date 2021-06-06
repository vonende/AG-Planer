package main

// Programm, das folgendes leistet: 
// gegeben: schüler und lehrer daten als csv Tabelle
// in der db nach der schülerNr oder Lehrerkürzel gucken -> Mit csv Tabelle vgl
// Fall1: Existent -> sql Befehl: Update, falls daten unterschiedlich sind
// Fall2: nicht Existent: hinzufügen zur db mit insert
// Fall3: NUR bei Schülern, wenn in der csv-Tabelle ein Schüler nicht auftaucht, der aber in der db existiert -> schüler enabled= false
