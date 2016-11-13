USE 'tueGutes'
GO

-- -----------------------------------------------------
-- INSERT INTO TRUST
-- -----------------------------------------------------

INSERT INTO Trust(idTrust,trustleveldescription) VALUES(1, "Neuling");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(2, "Mitglied");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(3, "Stammmitglied");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(4, "Veteran");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(5, "GuteFreund");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(6, "Familienmitglied");
INSERT INTO Trust(idTrust,trustleveldescription) VALUES(7, "Seelenverwandte");

-- -----------------------------------------------------
-- INSERT INTO USERGROUP
-- -----------------------------------------------------

INSERT INTO UserGroup(idUserGroup,groupDescription) VALUES(1, "Mitglied");
INSERT INTO UserGroup(idUserGroup,groupDescription) VALUES(2, "Moderator");
INSERT INTO UserGroup(idUserGroup,groupDescription) VALUES(3, "Administrator");

-- -----------------------------------------------------
-- Fügt ein TestUser ein
-- -----------------------------------------------------

INSERT INTO User (idUser,username, password, email, regDate, points, status, idUserGroup, idTrust) values(1,"testuser","9d53fbca481ed20edc0c10d6e45fcedf","Mail@funkt.nicht","2016-11-01",0,'Verifiziert',3,3);
INSERT INTO Privacy (idPrivacy, privacykey, cryptkey) values (1,"111111111111111","345485c1dfc5ebc4dd3fb90e3d591518");
INSERT INTO UserTexts (idUserTexts) values (1);
Insert into PersData (idPersData, firstname, lastname) values(1,"testmax","testmuster");

-- -----------------------------------------------------
-- Fügt Postleitzahlen und Orte ein
-- -----------------------------------------------------

INSERT INTO Postalcode(postalcode,place) VALUES(30159, "Bult");
INSERT INTO Postalcode(postalcode,place) VALUES(30159, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30159, "Nordstadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30159, "Südstadt");

INSERT INTO Postalcode(postalcode,place) VALUES(30161, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30161, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30161, "Oststadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30161, "Vahrenwald");

INSERT INTO Postalcode(postalcode,place) VALUES(30163, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30163, "Vahrenwald");

INSERT INTO Postalcode(postalcode,place) VALUES(30165, "Hainholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30165, "Nordstadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30165, "Vahrenwald");
INSERT INTO Postalcode(postalcode,place) VALUES(30165, "Vinnhorst");

INSERT INTO Postalcode(postalcode,place) VALUES(30167, "Calenberger Neustadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30167, "Herrenhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30167, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30167, "Nordstadt");

INSERT INTO Postalcode(postalcode,place) VALUES(30169, "Calenberger Neustadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30169, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30169, "Südstadt");

INSERT INTO Postalcode(postalcode,place) VALUES(30171, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30171, "Südstadt");

INSERT INTO Postalcode(postalcode,place) VALUES(30173, "Bult");
INSERT INTO Postalcode(postalcode,place) VALUES(30173, "Südstadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30173, "Waldhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30173, "Waldheim");

INSERT INTO Postalcode(postalcode,place) VALUES(30175, "Bult");
INSERT INTO Postalcode(postalcode,place) VALUES(30175, "Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30175, "Oststadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30175, "Südstadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30175, "Zoo");

INSERT INTO Postalcode(postalcode,place) VALUES(30177, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30177, "Zoo");

INSERT INTO Postalcode(postalcode,place) VALUES(30179, "Brink-Hafen");
INSERT INTO Postalcode(postalcode,place) VALUES(30179, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30179, "Sahlkamp");
INSERT INTO Postalcode(postalcode,place) VALUES(30179, "Vahrenheide");
INSERT INTO Postalcode(postalcode,place) VALUES(30179, "Vahrenwald");

INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Burg");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Hainholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Herrenhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Ledeburg");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Leinhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Marienwerder");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Misburg-Nord");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Nordhafen");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Stöcken");
INSERT INTO Postalcode(postalcode,place) VALUES(30419, "Vinnhorst");

INSERT INTO Postalcode(postalcode,place) VALUES(30449, "Linden-Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30449, "Linden-Süd");

INSERT INTO Postalcode(postalcode,place) VALUES(30451, "Limmer");
INSERT INTO Postalcode(postalcode,place) VALUES(30451, "Linden-Nord");

INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Badenstedt");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Bornum");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Davenstedt");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Limmer");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Linden-Mitte");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Linden-Süd");
INSERT INTO Postalcode(postalcode,place) VALUES(30453, "Ricklingen");

INSERT INTO Postalcode(postalcode,place) VALUES(30455, "Badenstedt");
INSERT INTO Postalcode(postalcode,place) VALUES(30455, "Davenstedt");

INSERT INTO Postalcode(postalcode,place) VALUES(30457, "Mühlenberg");
INSERT INTO Postalcode(postalcode,place) VALUES(30457, "Oberricklingen");
INSERT INTO Postalcode(postalcode,place) VALUES(30457, "Wettbergen");

INSERT INTO Postalcode(postalcode,place) VALUES(30459, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30459, "Linden-Süd");
INSERT INTO Postalcode(postalcode,place) VALUES(30459, "Oberricklingen");
INSERT INTO Postalcode(postalcode,place) VALUES(30459, "Ricklingen");

INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Döhren");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Mittelfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Seelhorst");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Südstadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Waldhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Waldheim");
INSERT INTO Postalcode(postalcode,place) VALUES(30519, "Wülfel");

INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Ahlem");
INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Bemerode");
INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Leinhausen");
INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Mittelfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Oststadt");
INSERT INTO Postalcode(postalcode,place) VALUES(30521, "Südstadt");

INSERT INTO Postalcode(postalcode,place) VALUES(30539, "Bemerode");
INSERT INTO Postalcode(postalcode,place) VALUES(30539, "Mittelfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30539, "Seelhorst");
INSERT INTO Postalcode(postalcode,place) VALUES(30539, "Wülferode");

INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Anderten");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Bemerode");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Kirchrode");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Kleefeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Misburg-Süd");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Seelhorst");
INSERT INTO Postalcode(postalcode,place) VALUES(30559, "Waldheim");

INSERT INTO Postalcode(postalcode,place) VALUES(30625, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30625, "Heideviertel");
INSERT INTO Postalcode(postalcode,place) VALUES(30625, "Kleefeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30625, "Wettbergen");

INSERT INTO Postalcode(postalcode,place) VALUES(30627, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30627, "Heideviertel");
INSERT INTO Postalcode(postalcode,place) VALUES(30627, "Misburg-Nord");

INSERT INTO Postalcode(postalcode,place) VALUES(30629, "Misburg-Nord");
INSERT INTO Postalcode(postalcode,place) VALUES(30629, "Misburg-Süd");

INSERT INTO Postalcode(postalcode,place) VALUES(30655, "Bothfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30655, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30655, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30655, "Misburg-Nord");

INSERT INTO Postalcode(postalcode,place) VALUES(30657, "Bothfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30657, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30657, "Isernhagen-Süd");
INSERT INTO Postalcode(postalcode,place) VALUES(30657, "Lahe");
INSERT INTO Postalcode(postalcode,place) VALUES(30657, "List");
INSERT INTO Postalcode(postalcode,place) VALUES(30657, "Sahlkamp");

INSERT INTO Postalcode(postalcode,place) VALUES(30659, "Bothfeld");
INSERT INTO Postalcode(postalcode,place) VALUES(30659, "Groß Buchholz");
INSERT INTO Postalcode(postalcode,place) VALUES(30659, "Lahe");

INSERT INTO Postalcode(postalcode,place) VALUES(30669, "Flughafen");

INSERT INTO Postalcode VALUES(-1,0, "404");
