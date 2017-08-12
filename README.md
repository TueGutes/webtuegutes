## TueGutes in Hannover

TueGutes ist ein Projekt der Hochschule Hannover. Die Idee hinter “TueGutes” ist einfach, den Menschen in deiner Umgebung mit guten Taten zu helfen, wie zum Beispiel im Haushalt zu helfen oder mal für jemanden den Einkauf zu erledigen. Denkbar wäre auch, Helfer für einen Umzug zu gewinnen oder jemanden zu finden, der Nachhilfe in einem bestimmten Thema geben kann. Dies soll durch unsere Website für jeden einfach und schnell möglich sein.

Das Projekt befindet sich immoment im Status Open Source zu werden. Dementsprechend fehlen an vielen Stellen noch Informationen, die nachgereicht werden.

## Getting Started

Diese Anleitung ist dazu da, das Projekt auf deinem lokalen Gerät zu laufen zu bekommen, als auch einen Einstieg zu geben um am Projekt mitwirken zu können.

### Prerequisites

Benötigte Software und Applikationen:

```
Beliebiger Texteditor oder IDE
Phinx - für die Datenbankentwicklung
Lokaler Apache - Testen von Änderungen
Selenium
PHPDoc
PHP Unit
```

### Installing

#### Phinx:

Wie Phinx genutzt wird, kann in der OnlineDokumentation des Werkzeugs nachgeschlagen werden. Durch die Struktur des Deployments von TueGutes ist es nötig Phinx lokal in das Git-Repository zu installieren. Dies ist optimal über den Weg des Composers durchzuführen. Die Konfigurationsdatei von Phinx liegt schon im Master und jedem geforkten Branch vor, weshalb bei der Installation Phinx nicht neu initialisiert werden muss. Ist dies gemacht, so kann angefangen werden, die ersten Migrationen zu schreiben. Dabei müssen die Kommandozeilenbefehle von Phinx genutzt werden. Damit die Migration getestet werden kann, wird empfohlen einen lokalen MySQL-Server aufzusetzen, der eine Kopie der Datenbankstruktur enthalten soll. Auch dies wird lokal durch Phinx sichergestellt, indem schon ein automatisiertes Skript den lokalen Server auf die aktuelle im Git-befindliche DB aktualisiert. Sollen Migrationen getestet werden, so kann manuell durch Befehle der Kommandozeile lokal migriert werden. 

#### Selenium:

Eine Selenium GUI Testumgebung ist nun im git verfügbar (/tests/SeleniumGUITester/). Um sie auszuprobieren muss Firefox installiert sein.
Zwei Beispieltests sind ebenso vorhanden (/src/tests/).

Um einen Test zu erstellen sollte man folgendermaßen vorgehen:

Das Eclipse Projekt importieren (File -> Import -> Existing Project into Workspace)
Eine neue Klasse im pakage "tests" erstellen, die von der Klasse GUITest erbt
In der doTests Methode (von der GUITest Klasse geerbt) den eigentlichen Testcode verfassen
In der TestStarter.initTests() Methode die soeben erstellte Klasse hinzufügen
Das Programm starten

Momentan werden die GUI Elemente über den sogenannten xpath angesprochen, was bei kleineren Layoutänderungen schnell zu Problemen führt. Es empfiehlt sich also allen GUI Elementen eindeutige IDs zuzuweisen, um dieses Problem zu umgehen.

#### PHPDoc:

PHPDoc ist in das Projekt schon integriert. Wenn Code und Dateien die korrekte Kommentierung vorweisen für PHPDoc, so sind diese unter [TueGutes PHPDoc](http://tue-gutes-in-hannover.de/tueGutes/docs) einsehbar.


#### PHP Unit:

PHP Unit auf dem gängigsten Wege jeweils unter Windows oder unter Linux/MacOS installieren. Es muss darauf geachtet werden, dass gegebenfalls die Umgebungsvariable für PHP Unit im PATH gesetzt werden muss.

Konventionen/Richtlinien für Tests mit PHP Unit:
- Testmethoden müssen immer mit "test" beginnen und public sein, sonst werden sie nicht von PHPUnit ausgeführt
- Meistens sind die Testklassen Subklassen von "PHPUnit_Framework_TestCase",so können eingebaute Funktionen wie "setUp()" und "tearDown()" genutzt werden
- Die Methoden "setUp()" und "tearDown()" sind Hilfsfunktionen, die vor bzw. nach dem Test ausgeführt werden.
- Testklassen sollten immer so wie die Klasse heißen, die sie testen. Zum Beispiel testet "FirstClassTest" die Klasse "FirstClass".
- Testmethoden sollten keine Parameter erhalten. Sie sollten möglichst alles selbst enthalten, was nötig ist.
Soll zu saubereren, effektiveren Tests führen.
- nur eine Assertion pro Test

## Running the Tests

Das Ausführen von einzelnen Test zur direkten Überprüfung von einzelnen Problemen muss manuell von Hand gemacht werden. Allerdings sind alle implementierten Tests im Build-Prozess von Jenkins gehookt. Bei einem Build von den drei Umgebungen(siehe Abschnitt unten) werden die Tests automatisch ausgeführt. Die Ergebnisse sind Online unter folgenden Links abrufbar:

[Selenium GUI Tests](http://tue-gutes-in-hannover.de/tueGutes/docs/gui-test-results.html)
[PHP Unit Tests](http://tue-gutes-in-hannover.de/tueGutes/docs/unit-test-results.html)

## Deployment - Jenkins

Wir verwenden drei unterschiedliche Versionen des Projekts, die jeweils einem bestimmten Zweck dienen. Eine Entwicklerversion, die aktiv erweitert und angepasst wird, eine Präsentationsversion, für die Vorstellung neuer Features, sowie die eigentliche Live-Version, die nur Features beinhaltet, die innerhalb der Entwickler- bzw. Präsentationsversion erfolgreich getestet wurden. Innerhalb unseres Git Repositories befindet sich zu diesem Zweck jeweils ein Branch für jede dieser Versionen.

Jenkins überwacht das Repository und erkennt Änderungen an den Versionen. Diese Änderungen lösen den zugehörigen automatisierten Buildprozess aus. Ist der Prozess abgeschlossen, wird eine E-Mail an alle Entwickler versendet, die an dem Build beteiligt waren. Diese E-Mail beinhaltet Informationen über den Vorgang. Dazu gehört Erfolg oder Misserfolg des Builds, eine Liste der Änderungen seit dem letzten Build, sowie Links zu den lauffähigen Versionen und zu allen Ergebnissen der Tools, die während des Prozesses genutzt wurden. Des Weiteren wird jeder erfolgreiche Build als Archiv bereitgestellt und kann so auch auf anderen Systemen installiert werden.

## Contributing

TueGutes ist ein OpenSource Projekt. Jedem ist es erlaubt, an dem Projekt mitzuwirken

## Versioning

Es wird SemVer genutzt

## License

Apache License, Version 2.0, January 2004, http://www.apache.org/licenses/
