create table TueGutesDB(
UserID int NOT NULL AUTO_INCREMENT,
Benutzername varchar(50) NOT NULL,
Passwort varchar(100) NOT NULL,
Email varchar(300) NOT NULL,
RegDatum date NOT NULL,
UNIQUE (Benutzername),
UNIQUE (Email),
PRIMARY KEY (UserID)
);