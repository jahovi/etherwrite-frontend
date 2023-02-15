# EtherWrite
EtherWrite ist ein Aktivitäten-Plugin für Moodle auf der Basis des EtherpadLite Texteditors für kollaboratives Schreiben. Zusätzlich bietet dieses Plugin zahlreiche Funktionalitäten zur Visualisierung verschiedener Aspekte der Zusammenarbeit der Teammitglieder sowie auch die Anzeige von KPIs.  

## Rechtliche Aspekte

Soweit Sie bzw. Ihre Organisation diese Software nutzen möchten, liegt es in Ihrer Verantwortung, sich über die für Sie geltenden datenschutzrechtlichen Rahmenbedingungen zu informieren und unter welchen rechtlichen Voraussetzungen diese Software eingesetzt werden kann und darf. Um etwaigen rechtlichen Problemen vorzubeugen, sollten Sie sich frühzeitig professionelle rechtliche Beratung einholen, bevor Sie diese Software außerhalb einer rein privaten Testumgebung einsetzen.  

Es ist sehr wahrscheinlich, dass Sie dafür sorgen müssen, dass alle Nutzer umfassend informiert werden und ihre Einwilligung erklärt dazu haben müssen, dass deren Handlungen und Verhalten im Bereich des Etherpad Texteditors für einen langen Zeitraum gespeichert werden. Dies umfasst unter anderem die Interaktionen mit dem User Interface des Editors. Aber z.B. auch Ereignisse wie das Einloggen oder Ausloggen einzelner User im Editor. Alle diese Daten werden von der Software auf vielfältige Weise automatisiert ausgewertet. Wie Sie im Detail Ihre Nutzer über diese Themen aufzuklären haben und auf welche Weise Sie deren Einverständnis einzuholen haben, sollten Sie mit Ihrer Rechtsberatung klären. 



## Installation des Plugins in Moodle
Es wird an dieser Stelle vorausgesetzt, dass Sie bereits über einen Moodle Server verfügen. 

Um das Plugin nutzen zu können, benötigen Sie außerdem 

* das Modul Etherpad Serverside (einschließlich CouchDB)
* das Modul EVA

Diese befinden sich einschließlich der Installationsanweisungen jeweils in separaten Repositories. Bitte beachten Sie, dass die vorgenannten Serverinstanzen gestartet sein müssen, bevor das EtherWrite Plugin aus dem Moodle heraus genutzt werden kann. 

Als nächstes stellen Sie bitte sicher, dass in der config.php-Datei Ihrer Moodle-Installation die Zeile  
`$CFG->cachejs = false;`  
vorhanden ist. 

Um das Plugin zu installieren, führen Sie bitte folgende Schritte aus:
* Navigieren Sie mit der Git Bash in den Ordner `{moodle-pfad}/mod` und klonen Sie dieses Repository mit <br />`git clone ***REMOVED***`.
* Navigieren Sie in den dadurch erstellten `write/vue`-Ordner und führen Sie den Befehl `npm install` aus. Hierbei werden alle benötigten JavaScript-Module durch den `Node Package Manager` installiert. Mehr informationen zum Package Manager finden Sie [hier](https://www.npmjs.com/).
* Navigieren Sie zur Moodle-Hauptseite und folgen Sie dem angezeigten Dialog zur Installation des Plugins.

## Konfiguration
Das Plugin verwendet die folgenden Konfigurationseinstellungen:
<table>
    <tr>
        <td>Etherpad Server URL</td>
        <td>Die URL zur Etherpad-Instanz. (http://etherpad:9001 sofern Sie MoodleDocker und Etherpad Serverside auf dem selben Host verwenden)</td>
    </tr>
    <tr>
        <td>Etherpad API-Schlüssel</td>
        <td>Der API-Schlüssel von Etherpad. (s. APIKEY.txt im Etherpad-Root-Verzeichnis)</td>
    </tr>
    <tr>
        <td>EVA Server URL</td>
        <td>URL zur EVA-Instanz für die Datenanalyse</td>
    </tr>
    <tr>
        <td>EVA API-Schlüssel</td>
        <td>Schlüssel für den Datenaustausch zwischen Moodle und EVA. Der gleiche Schlüssel muss auch in der EVA-Instanz hinterlegt werden.</td>
    </tr>
    <tr>
        <td>Lokale Installation</td>
        <td>Muss ausgewählt werden, sofern MoodleDocker und Etherpad Serverside auf demselben Hostsystem laufen.</td>
    </tr>
</table>

## Einrichten einer EtherWrite Aktivität

EtherWrite kann als Moodle Aktivität in einem bestehenden Kurs eingerichtet werden, siehe insoweit die allgemeine Moodle-Dokumentation:  https://docs.moodle.org/401/de/Hauptseite



## Nutzung einer vorhandenen EtherWrite Aktivität

Nach Anklicken der entsprechenden Aktivität gelangen Sie auf den Hauptbildschirm. Dort finden Sie mittig drei Tabs vor ("Aufgabenstellung", "Texteditor", "Dashboard"). 

### Aufgabenstellung
Hier wird in Textform die für die Aktivität festgelegte Aufgabenstellung präsentiert. Der Text wird beim Anlegen der Aktivität in dem Eingabefeld "Beschreibung" festgelegt. 

### Texteditor
Hier finden Sie neben dem Etherpad Texteditor noch weitere nützliche Informationen und KPIs: 
* Zeit bis Abgabefrist  
Diese Zeit kann ebenfalls durch die Webseitenadministration bzw. die Kursleitung im Rahmen der Aktivitätseinstellungen definiert werden. 

* Anzahl der Zeichen und Wörter
Hier wird durch Auswertung von Daten aus dem EVA Backend die Anzahl der aktuell im Editor vorhandenen Zeichen und Wörter dargestellt. 

* Projektteilnehmende
Hier werden sämtliche Gruppenmitglieder (einschließlich auch der Trainer) namentlich angegeben. Zu den einzelnen Namen wird auch deren Farbe im Editor mit dargestellt. Sofern einzelne Mitglieder noch nicht den Editor besucht haben, ist für diese in der Regel noch keine Farbe definiert. Dies erkennt man daran, dass das Farbfeld weiß mit grauer Umrandung dargestellt wird. 

* Minimap
Rechts vom Editor befindet sich die sogenannte Minimap. Die Minimap enthält eine verkleinerte Silhouette des aktuell im Editor vorhandenen Textes. Die Minimap stellt die Text-Silhouette in den autorenspezifischen Farben dar, so dass man hiermit auf einen Blick schnell erkennen kann, welche Textbereiche schwerpunktmäßig von welchem Nutzer bearbeitet worden sind.  

Darüber hinaus kann man an der Minimap auch erkennen, welche anderen Projektteilnehmer gerade ebenfalls im Editor aktiv sind und welche Textbereiche sie derzeit gerade in ihrem Blickfeld haben. Dies wird dargestellt durch entsprechende, farblich unterlegte Rechtecke auf der Minimap. 

### Dashboard
Auch hier findet der Nutzer zunächst ebenfalls die KPIs Zeit bis Abgabefrist sowie Anzahl der Zeichen und Wörter.  

Des Weiteren kann der Nutzer hier eine große Anzahl von Charts auswählen und anzeigen lassen. Das grün umrandete Pluszeichen auf der rechten Bildschirmseite öffnet nach einem Linksklick den Chartkatalog. Der Chartkatalog ist gegliedert in die Kategorien "Aktivität", "Zeitverlauf", "Zusammenarbeit". Diese Kategorien werden durch entsprechend beschriftete Tabs im oberen Bereich des Katalogs ausgewählt. 

Im Chartkatalog wird dem Nutzer zunächst das Aussehen der einzelnen Charts auf der Basis von fiktiven Daten ("Mockdaten") vor Augen geführt. Zu jedem Katalogeintrag gibt es wiederum am rechten oberen Rand ein Pluszeichen. Wenn dies vom Nutzer geklickt wird, wird der Katalog geschlossen und das Chart dann mit den realen, aus dem zu dieser Aufgabe vorhandenen Etherpad Texteditor generierten Daten auf dem Dashboard angezeigt.  

Das Dashboard ist nach unten nicht größenbeschränkt, so dass die Nutzer entsprechend durch erneutes Auswählen des Chartkataloges mehrere Charts gleichzeitig ins Dashboard holen können. 

Die Charts können in ihrer Größe verändert werden, indem man auf deren unterer, rechter Ecke die linke Maustaste gedrückt hält und gleichzeitig per Mausbewegung die Höhe und Breite des Chartrahmens verschiebt.  

Zudem kann auch die Positionierung der Charts auf dem Dashboard verändert werden. Dazu muss man die Strg-Taste auf dem Keyboard gedrückt halten. Dann kann man mit der linken Maustaste per Drag & Drop das Chart frei verschieben. Dies wird übrigens auch durch einen entsprechenden Hinweistext neben der Dashboard-Überschrift erläutert. 

Die getroffene Auswahl eines Nutzers hinsichtlich der ins Dashboard geholten Charts wird dauerhaft gespeichert. D.h. bei der nächsten Session findet der Nutzer die von ihm zuletzt im Dashboard abgelegten Charts so vor, wie er sie zuletzt hinterlassen hatte. 

Jedes Chart besitzt in der linken unteren Ecke einen Info-Button in Form eines umrandeten Fragezeichens. Nach einem Linksklick auf dieses Symbol wird dem Nutzer ein Infotext angezeigt, der die Bedeutung des jeweiligen Charts näher beschreibt. 

### Die einzelnen Charts
* Schreibanteil pro Person (als Balkendiagramm)
* Schreibanteil pro Person (als Tortendiagramm)
* Durchgeführte Operationen
* Aktivitäten im Zeitverlauf
* Revisionshistorie
* Partizipationsdiagramm
* Kohäsion

