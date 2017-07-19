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

Phinx:

```
Wie Phinx genutzt wird, kann in der OnlineDokumentation des Werkzeugs nachgeschlagen werden. Durch die Struktur des Deployments von TueGutes ist es nötig Phinx lokal in das Git-Repository zu installieren. Dies ist optimal über den Weg des Composers durchzuführen. Die Konfigurationsdatei von Phinx liegt schon im Master und jedem geforkten Branch vor, weshalb bei der Installation Phinx nicht neu initialisiert werden muss. Ist dies gemacht, so kann angefangen werden, die ersten Migrationen zu schreiben. Dabei müssen die Kommandozeilenbefehle von Phinx genutzt werden. Damit die Migration getestet werden kann, wird empfohlen einen lokalen MySQL-Server aufzusetzen, der eine Kopie der Datenbankstruktur enthalten soll. Auch dies wird lokal durch Phinx sichergestellt, indem schon ein automatisiertes Skript den lokalen Server auf die aktuelle im Git-befindliche DB aktualisiert. Sollen Migrationen getestet werden, so kann manuell durch Befehle der Kommandozeile lokal migriert werden. 
```

Selenium

```
Platzhalter
```

PHPDoc

```
Platzhalter
```

PHP Unit

```
Platzhalter
```

## Running the Tests

Dies wird automatisch gemacht. Der master branch wird online auf ein Testserver mit Jenkins deployed. In Jenkins sind die Tests gehookt und werden bei jedem Update des Branches ausgeführt und können online evaluiert werden.

### Selenium Tests

Platzhalter

```
Platzhalter
```

### PHP Unit Tests

Platzhalter

```
Platzhalter
```

## Deployment - Jenkins

Wir verwenden drei unterschiedliche Versionen des Projekts, die jeweils einem bestimmten Zweck dienen. Eine Entwicklerversion, die aktiv erweitert und angepasst wird, eine Präsentationsversion, für die Vorstellung neuer Features, sowie die eigentliche Live-Version, die nur Features beinhaltet, die innerhalb der Entwickler- bzw. Präsentationsversion erfolgreich getestet wurden. Innerhalb unseres Git Repositories befindet sich zu diesem Zweck jeweils ein Branch für jede dieser Versionen.

Jenkins überwacht das Repository und erkennt Änderungen an den Versionen. Diese Änderungen lösen den zugehörigen automatisierten Buildprozess aus. Ist der Prozess abgeschlossen, wird eine E-Mail an alle Entwickler versendet, die an dem Build beteiligt waren. Diese E-Mail beinhaltet Informationen über den Vorgang. Dazu gehört Erfolg oder Misserfolg des Builds, eine Liste der Änderungen seit dem letzten Build, sowie Links zu den lauffähigen Versionen und zu allen Ergebnissen der Tools, die während des Prozesses genutzt wurden. Des Weiteren wird jeder erfolgreiche Build als Archiv bereitgestellt und kann so auch auf anderen Systemen installiert werden.

## Contributing

TueGutes ist ein OpenSource Projekt. Jedem ist es erlaubt, an dem Projekt mitzuwirken

## Versioning

Geplant ist es  [SemVer](http://semver.org/) zu nutzen. Bis jetzt wurde ohne dies gearbeitet.

## License

Apache License, Version 2.0, January 2004, http://www.apache.org/licenses/
