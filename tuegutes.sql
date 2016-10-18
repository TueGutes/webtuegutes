drop table Benutzer;

create table Benutzer(
	UserID int NOT NULL AUTO_INCREMENT,
	Benutzername varchar(48) NOT NULL,
	Passwort varchar(32) NOT NULL,
	Email varchar(128) NOT NULL,
	RegDatum varchar(32) NOT NULL,
	UNIQUE (Benutzername),
	UNIQUE (Email),
	Vorname varchar(64) NOT NULL,
	Nachname varchar(64) NOT NULL,
	Geburtsjahr int,
	Geburtstag varchar(4),
	Avatar text,
	Strasse varchar(128),
	Hausnummer varchar(5),
	TelNr varchar(20),
	MsgNr varchar(20),
	Hobbys tinyText,
	Beschreibung text,
	Datenschutz varchar(13),
	PRIMARY KEY (UserID)
);