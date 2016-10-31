-- MySQL Script generated by MySQL Workbench
-- Mo 31 Okt 2016 15:17:48 CET
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema tueGutes
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema tueGutes
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `tueGutes` DEFAULT CHARACTER SET utf8 ;
USE `tueGutes` ;

-- -----------------------------------------------------
-- Table `tueGutes`.`Trust`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`Trust` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`Trust` (
  `idTrust` INT NOT NULL,
  `trustleveldescription` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idTrust`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`UserGroup`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`UserGroup` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`UserGroup` (
  `idUserGroup` INT NOT NULL,
  `groupDescription` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idUserGroup`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`User` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`User` (
  `idUser` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(48) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `regDate` DATETIME NOT NULL,
  `points` INT NOT NULL,
  `idTrust` INT NOT NULL,
  `idUserGroup` INT NOT NULL,
  `status` ENUM('nichtVerifiziert', 'Verifiziert') NOT NULL,
  PRIMARY KEY (`idUser`),
  UNIQUE INDEX `idBenutzer_UNIQUE` (`idUser` ASC),
  UNIQUE INDEX `Benutzername_UNIQUE` (`username` ASC),
  UNIQUE INDEX `Email_UNIQUE` (`email` ASC),
  INDEX `fk_Benutzer_Vertrauen1_idx` (`idTrust` ASC),
  INDEX `fk_Benutzer_Benutzergruppe1_idx` (`idUserGroup` ASC),
  CONSTRAINT `fk_User_Trust1`
    FOREIGN KEY (`idTrust`)
    REFERENCES `tueGutes`.`Trust` (`idTrust`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_User_UserGroup1`
    FOREIGN KEY (`idUserGroup`)
    REFERENCES `tueGutes`.`UserGroup` (`idUserGroup`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`Privacy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`Privacy` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`Privacy` (
  `idPrivacy` INT NOT NULL,
  `privacykey` VARCHAR(64) NOT NULL,
  `cryptkey` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`idPrivacy`),
  CONSTRAINT `fk_Privacy_User1`
    FOREIGN KEY (`idPrivacy`)
    REFERENCES `tueGutes`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`Postalcode`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`Postalcode` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`Postalcode` (
  `postalcode` INT NOT NULL,
  `place` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`postalcode`, `place`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`PersData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`PersData` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`PersData` (
  `idPersData` INT NOT NULL,
  `firstname` VARCHAR(64) NOT NULL,
  `lastname` VARCHAR(64) NOT NULL,
  `gender` VARCHAR(1) NULL,
  `street` VARCHAR(128) NULL,
  `housenumber` VARCHAR(5) NULL,
  `postalcode` INT NULL,
  `telefonnumber` VARCHAR(20) NULL,
  `messengernumber` VARCHAR(20) NULL,
  `birthday` DATE NULL,
  PRIMARY KEY (`idPersData`),
  INDEX `PLZ` (`postalcode` ASC),
  CONSTRAINT `fk_PersData_Postalcode1`
    FOREIGN KEY (`postalcode`)
    REFERENCES `tueGutes`.`Postalcode` (`postalcode`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_PersData_User1`
    FOREIGN KEY (`idPersData`)
    REFERENCES `tueGutes`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`UserTexts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`UserTexts` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`UserTexts` (
  `idUserTexts` INT NOT NULL,
  `avatar` TEXT(65535) NULL,
  `hobbys` TINYTEXT NULL,
  `description` TEXT(2000) NULL,
  PRIMARY KEY (`idUserTexts`),
  CONSTRAINT `fk_UserTexts_User1`
    FOREIGN KEY (`idUserTexts`)
    REFERENCES `tueGutes`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`Deeds`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`Deeds` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`Deeds` (
  `idGuteTat` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NULL,
  `contactPerson` INT NOT NULL,
  `category` VARCHAR(64) NOT NULL,
  `street` VARCHAR(128) NULL,
  `housenumber` VARCHAR(5) NULL,
  `postalcode` INT NOT NULL,
  `time` DATETIME NULL,
  `organization` VARCHAR(128) NULL,
  `countHelper` INT NULL,
  `idTrust` INT NOT NULL,
  `status` ENUM('a', 'b', 'c') NOT NULL,
  PRIMARY KEY (`idGuteTat`, `contactPerson`),
  INDEX `fk_Taten_PLZ1_idx` (`postalcode` ASC),
  INDEX `fk_Taten_Vertrauen1_idx` (`idTrust` ASC),
  INDEX `fk_Taten_Benutzer1_idx` (`contactPerson` ASC),
  CONSTRAINT `fk_Deeds_Postalcode1`
    FOREIGN KEY (`postalcode`)
    REFERENCES `tueGutes`.`Postalcode` (`postalcode`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Deeds_Trust1`
    FOREIGN KEY (`idTrust`)
    REFERENCES `tueGutes`.`Trust` (`idTrust`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Deeds_User1`
    FOREIGN KEY (`contactPerson`)
    REFERENCES `tueGutes`.`User` (`idUser`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tueGutes`.`DeedTexts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tueGutes`.`DeedTexts` ;

CREATE TABLE IF NOT EXISTS `tueGutes`.`DeedTexts` (
  `idDeedTexts` INT NOT NULL,
  `description` TEXT(2000) NULL,
  `pictures` TEXT(65535) NULL,
  PRIMARY KEY (`idDeedTexts`),
  CONSTRAINT `fk_DeedTexts_Deeds1`
    FOREIGN KEY (`idDeedTexts`)
    REFERENCES `tueGutes`.`Deeds` (`idGuteTat`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- INSERT INTO TRUST
-- -----------------------------------------------------

INSERT INTO Trust(idTrust,trustleveldescription) VALUES(0, "Neuling");

-- -----------------------------------------------------
-- INSERT INTO USERGROUP
-- -----------------------------------------------------

INSERT INTO UserGroup(idUserGroup,groupDescription) VALUES(0, "Mitglied");
