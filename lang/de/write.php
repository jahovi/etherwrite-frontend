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
$string['leftclick'] = "Linksklick";

// Webservices
$string['novalidepurl'] = 'Ungültige Etherpad-URL.';
$string['novalidapikey'] = 'Ungültiger Etherpad-API-Schlüssel.';
$string['nogrouping'] = 'Keine Gruppierung beim Modul ausgewählt.';
$string['nogroup'] = 'Benutzer/-in gehört nicht zu einer Gruppe der Gruppierung.';
$string['couldnotcreateepgroup'] = 'Etherpad-Gruppe konnte nicht erstellt werden.';
$string['couldnotfetchgrouppads'] = 'Liste der Pads der Etherpad-Gruppe konnten nicht abgerufen werden.';
$string['couldnotcreatepad'] = 'Etherpad-Pad konnte nicht erstellt werden.';
$string['couldnotcreateauthor'] = 'Konnte Etherpad-Autor/-in nicht erstellen.';
$string['couldnotcreatesession'] = 'Die Etherpad-Sitzung konnte nicht erstellt werden.';

$string['legend'] = 'Legende';
$string['legend.click-to-highlight'] = 'Zum Hervorheben klicken';

// Dashboard
$string['dashboard.nothingToSee.title'] = "Nichts zu sehen?";
$string['dashboard.nothingToSee.cta'] = "Einfach mit diesem Button neue Widgets aus dem Katalog hinzufügen";
$string['dashboard.move'] = "Widgets verschieben mit";

// Widget categories
$string['widgets.category.barchart'] = 'Balkendiagramm';
$string['widgets.category.piechart'] = 'Kuchendiagramm';
$string['widgets.category.linechart'] = 'Liniendiagramm';
$string['widgets.category.other'] = 'Sonstige';

$string['widgets.groupParticipants'] = "Projektteilnehmende";
$string['widgets.groupParticipants.group'] = "Gruppe";

$string['widgets.cohesionDiagram.title'] = "Kohäsion (Interaktion zwischen Teilnehmenden)";

// KPIs
$string['kpi-countdown-text'] = 'Zeit bis zur Abgabefrist: ';
$string['kpi-countdown-no-deadline'] = 'Keine Abgabefrist';

// Widgets Info-Texts
$string['widget-info-authoringRatios_bar'] = 'Das Balkendiagramm zeigt den prozentualen Anteil pro Autor/-in am momentan bestehenden Text im Editor des Projekts.';

$string['widget-info-authoringRatios_pie'] = 'Das Kuchendiagramm zeigt den prozentualen Anteil pro Autor/-in am momentan bestehenden Text im Editor des Projekts.';

$string['widget-info-participation_diagram'] = 'Dieses Diagramm zeigt die Partizipation der Teilnehmenden zu einzelnen Stunden oder Tagen an. 
Bei noch sehr kurzen Arbeiten wird die Partizipation stündlich angezeigt. 
Bei Arbeiten, welche bereits länger laufen wird die Partizipation täglich angezeigt. 
Die Nutzenden werden in ihrer jeweiligen Farbe auf den Balken angezeigt. Sollte in einer Stunde / an einem Tag keine Arbeit geleistet worden sein, existiert kein Balken. Sonst ist die Gesamthöhe eines Balkens immer 1.0 und bezieht sich auf die gesamte geleistete Arbeit zu einer Stunde / einem Tag.';

$string['widget-info-operations_bar'] = 'Dieses Diagramm zeigt die Anzahl an durchgeführten Operationen an. 
Für Teilnehmende werden die eigenen durchgeführten Operationen und die von allen anderen Teilnehmenden durchschnittlich durchgeführten Operationen angezeigt. 
Für Moderatoren/-innen werden die durchgeführten Operationen aller Teilnehmenden angezeigt. 
Operationen sind: Schreiben, Bearbeiten, Kopieren und Löschen.';

$string['widget-info-activityOverTime'] = 'Zeigt die Anzahl der Aktivitäten der Benutzenden im Zeitverlauf an. Aktivitäten sind das Bearbeiten, Schreiben, von der Zwischenablage Einfügen und Löschen von Text. Sind seit Beginn der Aufgabe weniger als drei Tage vergangen, werden die Aktivitäten stündlich gruppiert, danach tageweise.';

$string['widget-info-cohesionDiagram'] = 'Zeigt die Kohäsion zwischen den Teilnehmenden der Gruppe. Der Abstand zwischen zwei Kreisen stellt die Häufigkeit dar, in der die beiden Teilnehmenden gemeinsam im Text gearbeitet haben. 
Die gerichteten Pfeile stehen für die Mitarbeit am Text anderer. Ein Pfeil von A nach B deutet darauf hin, dass Teilnehmer/-in A den Text von B ergänzt oder bearbeitet hat. Die Dicke des Pfeils zeigt die Intensität der Mitarbeit.
<em><strong>Hinweis:</strong> Mit Klick auf die Legende können einzelne Teilnehmende hervorgehoben werden.</em>';

$string['widget-info-etherViz'] = 'Das EtherViz Diagramm gibt dir eine Übersicht über die Texterstellung im zeitlichen Verlauf. Die y-Achse gibt Aufschluss über die Anzahl Buchstaben, die sich zu einem Stichtag im Text befinden. Die x-Achse zeigt dir so viele Revisionen an, wie das Projekt Tage hat.';

// Operations widget
$string['operationswidgettitle'] = 'Durchgeführte Operationen';
$string['operationswidgetedit'] = 'Bearbeiten';
$string['operationswidgetwrite'] = 'Schreiben';
$string['operationswidgetpaste'] = 'Kopieren';
$string['operationswidgetdelete'] = 'Löschen';
$string['operationswidgetaverage'] = 'Durchschnitt';
$string['operationswidgetxaxisteacher'] = 'Teilnehmende';
$string['operationswidgetxaxisstudent'] = 'Operationen';
$string['operationswidgetyaxis'] = 'Anzahl';

// Participation Widget
$string['participationwidgettitle'] = 'Partizipationsdiagramm';
$string['participationwidgetyaxis'] = 'Partizipation';
$string['participationwidgetxaxis'] = 'Datum';
// ActivityOverTime Widget
$string['activityovertimewidgettitle'] = 'Aktivitäten im Zeitverlauf';
$string['activityovertimewidgetyaxis'] = 'Aktivität';
$string['activityovertimewidgetxaxis'] = 'Zeit';
$string['activityovertimewidgetscalinglinear'] = 'Linear';
$string['activityovertimewidgetscalinglogarithmic'] = 'Logarithmisch';

// DocumentMetrics widget
$string['documentMetricsWidgetTitle'] = 'Dokumentenmetriken';
$string['documentMetricsWidgetNumChars'] = 'Zeichen';
$string['documentMetricsWidgetNumWords'] = 'Wörter';
