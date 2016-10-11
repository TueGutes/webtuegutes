drop table Benutzer;

create table Benutzer(
UserID int NOT NULL AUTO_INCREMENT,
Benutzername varchar(48) NOT NULL,
Passwort varchar(32) NOT NULL,
Email varchar(128) NOT NULL,
RegDatum varchar(32) NOT NULL,
UNIQUE (Benutzername),
UNIQUE (Email),
PRIMARY KEY (UserID)
);