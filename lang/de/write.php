<?php

// Required strings.
$string['modulename'] = 'Write';
$string['modulenameplural'] = 'Writing';
$string['modulename_help'] = 'Write Plugin';
$string['pluginadministration'] = 'Administrator';
$string['pluginname'] = 'Write';

// Unknown  error
$string['unknown_error'] = 'Es ist ein unbekannter Fehler aufgetreten.';

// Settings
$string['serverurl'] = 'Etherpad Server URL';
$string['serverurldescription'] = 'Die Server-URL der Etherpad-Instanz.';
$string['apikey'] = 'Etherpad API-Schlüssel';
$string['apikeydescription'] = 'Der API-Schlüssel für die Etherpad-Instanz.';
$string['localinstallation'] = 'Lokale Installation';
$string['localinstallationdescription'] = 'Wählen Sie diese Option, sofern Moodle und Etherpad als Docker-Container auf dem gleichen Host betrieben werden.';
$string['nogroupselect'] = "(Gruppierung auswählen)";

// CM
$string['grouping'] = 'Gruppierung';

// Vue
$string['loadeditor'] = 'Versucht den Editor zu laden ...';
$string['loading'] = 'Laden..';

// Webservices
$string['novalidepurl'] = 'Ungültige Etherpad-URL.';
$string['novalidapikey'] = 'Ungültiger Etherpad-API-Schlüssel.';
$string['nogrouping'] = 'Keine Gruppierung beim Modul ausgewählt.';
$string['nogroup'] = 'Der Benutzer gehört nicht zu einer Gruppe der Gruppierung.';
$string['couldnotcreateepgroup'] = 'Etherpad-Gruppe konnte nicht erstellt werden.';
$string['couldnotfetchgrouppads'] = 'Liste der Pads der Etherpad-Gruppe konnten nicht abgerufen werden.';
$string['couldnotcreatepad'] = 'Etherpad-Pad konnte nicht erstellt werden.';
$string['couldnotcreateauthor'] = 'Konnte den Etherpad-Autor nicht erstellen.';
$string['couldnotcreatesession'] = 'Die Etherpad-Sitzung konnte nicht erstellt werden.';

$string['legend'] = 'Legende';
$string['legend.click-to-highlight'] = 'Zum Hervorheben klicken';

// Widget categories
$string['widgets.category.barchart'] = 'Balkendiagramm';
$string['widgets.category.piechart'] = 'Kuchendiagramm';
$string['widgets.category.linechart'] = 'Liniendiagramm';
$string['widgets.category.other'] = 'Sonstige';

$string['widgets.groupParticipants'] = "Projektteilnehmer(innen)";
$string['widgets.groupParticipants.group'] = "Gruppe";

$string['widgets.cohesionDiagram.title'] = "Kohäsion (Interaktion zwischen Teilnehmern)";

// KPIs
$string['kpi-countdown-text'] = 'Zeit bis zur Abgabefrist: ';
$string['kpi-countdown-no-deadline'] = 'Keine Abgabefrist';

// Widgets Info-Texts
$string['widget-info-authoringRatios_pie'] = 'Beschreibung des Authorenanteil-Kuchendiagramms.';
$string['widget-info-authoringRatios_bar'] = 'Beschreibung des Authorenanteil-Balkendiagramms.';
$string['widget-info-participation_diagram'] = 'Dieses Diagramm zeigt die Partizipation der Teilnehmer zu einzelnen Stunden oder Tagen an. 
Bei noch sehr kurzen Arbeiten wird die Partizipation stündlich angezeigt. 
Bei Arbeiten, welche bereits länger laufen wird die Partizipation täglich angezeigt. 
Die Nutzer werden in ihrer jeweiligen Farbe auf den Balken angezeigt. Sollte in einer Stunde / an einem Tag keine Arbeit geleistet worden sein, existiert kein Balken. Sonst ist die Gesamthöhe eines Balkens immer 1.0 und bezieht sich auf die gesamte geleistete Arbeit zu einer Stunde / einem Tag.';
$string['widget-info-authoringRatios_pie'] = 'Das Kuchendiagramm zeigt den prozentualen Anteil pro Autor am geschriebenen Text im Editor des Projekts.';
$string['widget-info-authoringRatios_bar'] = 'Das Balkendiagramm zeigt den prozentualen Anteil pro Autor am geschriebenen Text im Editor des Projekts.';
$string['widget-info-groupParticipants'] = 'Beschreibung des Gruppen-Partizipations-Diagramms.';
$string['widget-info-operations_bar'] = 'Beschreibung des Operations-Diagramms.';
$string['widget-info-activityOverTime'] = 'Beschreibung des Aktivitäts-Diagramms über die Zeit.';
$string['widget-info-etherViz'] = 'Das EtherViz Diagramm gibt dir eine Übersicht über die Texterstellung im zeitlichen Verlauf. Die y-Achse gibt Aufschluss über die Anzahl Buchstaben, die sich zu einem Stichtag im Text befinden. Die x-Achse zeigt dir so viele Revisionen an, wie das Projekt Tage hat.';
$string['widget-info-cohesionDiagram'] = 'Zeigt die Kohäsion zwischen den Teilnehmern der Gruppe. Der Abstand zwischen zwei Kreisen stellt die Häufigkeit dar, in der die beiden Teilnehmer(innen) gemeinsam im Text gearbeitet haben. 
Die gerichteten Pfeile stehen für die Mitarbeit am Text anderer. Ein Pfeil von A nach B deutet darauf hin, dass Teilnehmer(in) A den Text von B ergänzt oder bearbeitet hat. Die Dicke des Pfeils zeigt die Intensivität der Mitarbeit.
<em><strong>Hinweis:</strong> Mit Klick auf die Legende können einzelne Teilnehmer(innen) hervorgehoben werden.</em>';

// Operations widget
$string['operationswidgettitle'] = 'Durchgeführte Operationen';
$string['operationswidgetedit'] = 'Bearbeiten';
$string['operationswidgetwrite'] = 'Schreiben';
$string['operationswidgetpaste'] = 'Kopieren';
$string['operationswidgetdelete'] = 'Löschen';
$string['operationswidgetaverage'] = 'Durchschnitt';
$string['operationswidgetxaxisteacher'] = 'Operationen je Teilnehmer';
$string['operationswidgetxaxisstudent'] = 'Operationen';
$string['operationswidgetyaxis'] = 'Anzahl';

// Participation Widget
$string['participationwidgettitle'] = 'Partizipationsdiagramm';
$string['participationwidgetyaxis'] = 'Partizipation';
$string['participationwidgetxaxis'] = 'Datum';
