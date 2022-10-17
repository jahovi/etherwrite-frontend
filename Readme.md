# Beispiel-Plugin "mod_write"
Das hinterlegte Plugin `mod_write` soll Ihnen als Vorlage dienen.

Bitte beachten Sie, dass Sie beim Anlegen eines neuen Kurses nicht automatisch in diesen Kurs eingeschrieben sind!
<br /><br />Versuchen Sie, einen Überblick über die einzelnen Komponenten des Plugins zu erlangen, und entwickeln Sie das Plugin nach Ihren Anforderungen weiter. Die Wahl des Plugin-Typs bleibt Ihnen überlassen.

Folgende Komponenten benötigen <ins>keiner</ins> näheren Betrachtung.
Sofern allerdings Interesse besteht, kann der Nutzen der Komponenten mithilfe des jeweiligen folgenden Links nachgelesen werden.
* nodemon.json - [Nodemon](https://www.npmjs.com/package/nodemon)
* .babelrc - [Babel](https://babeljs.io/)
* webpack.config.js - [Webpack](https://webpack.js.org/)
* package.json; package-lock.json - [Node Package Manager](https://www.npmjs.com/)
* .gitignore - [Gitignore](https://git-scm.com/docs/gitignore)

## Installation des Plugins
Um das Plugin zu installieren, führen Sie bitte folgende Schritte aus:
* Navigieren Sie mit der Git Bash in den Ordner `{moodle-pfad}/mod` und klonen Sie dieses Repository mit <br />`git clone git@gitlab.pi6.fernuni-hagen.de:ks/fapra/fachpraktikum-2022/write.git`.
Achten Sie darauf, dass Sie die neuste Version des Plugins haben. Dies kann jederzeit mit dem Ausführen von `git pull origin master` (im vorher erstellten Plugin-Ordner) gewährleistet werden.
* Navigieren Sie in den dadurch erstellten `template/vue`-Ordner und führen Sie den Befehl `npm install` aus. Hierbei werden alle benötigten JavaScript-Module durch den `Node Package Manager` installiert. Mehr informationen zum Package Manager finden Sie [hier](https://www.npmjs.com/).
* Navigieren Sie zur Moodle-Hauptseite und folgen Sie dem angezeigten Dialog zur Installation des Plugins.

## JavaScript-Änderungen bzw. Vue-Änderungen übernehmen
Für das Frontend sollten Sie hauptsächlich im Ordner `vue` arbeiten. Dort können Sie JavaScript- bzw. Vue-Dateien anlegen.
Führen Sie den Befehl `npm run build` mithilfe des Terminals in diesen Verzeichnis des Plugins aus, um die Änderungen minifiziert einmalig zu übernehmen. 
Möchten Sie, dass die Änderungen automatisch nach dem Speichern übernommen werden, rufen Sie den Befehl `npm run nodemon` auf.

Verwendet wird die Vue-Version 2.6.12. Ein Guide ist unter [https://vuejs.org/v2
