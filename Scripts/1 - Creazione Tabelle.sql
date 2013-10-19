-- drop table `AV_Import`;

CREATE  TABLE `AV_Import` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `UID` VARCHAR(30) NOT NULL COMMENT 'Identificativo univoco dell\'importazione' ,
  `StartDate` DATETIME NOT NULL COMMENT 'Data inizio importazione da parte del client' ,
  `Prod_Delete` INT NULL DEFAULT 0 ,
  `Prod_Insert` INT NULL DEFAULT 0 ,
  `Img_Insert` INT NULL DEFAULT 0 ,
  `EndDate` DATETIME NULL ,
  `ElabQtaStart` DATETIME NULL ,
  `ElabQtaEnd` DATETIME NULL ,
  `ElabArtStart` DATETIME NULL ,
  `ElabArtEnd` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  UNIQUE INDEX `UID_UNIQUE` (`UID` ASC) )
COMMENT = 'Tabella imporrazioni Azzurra Vini';

-- drop table `AV_Import_Delete`;

CREATE  TABLE `AV_Import_Delete` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `IdImport` INT NOT NULL ,
  `UID` VARCHAR(30) NOT NULL ,
  `IdGestionale` VARCHAR(50) NOT NULL ,
  `InsertDate` DATETIME NULL ,
  `ExecuteDate` DATETIME NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX (IdImport ASC),
  UNIQUE INDEX `UID_PROD` (`UID` ASC, `IdGestionale` ASC) )
COMMENT = 'Tabella per iportazione azzurra contente gli articoli da cancellare';

-- drop table `AV_Import_Insert`;

CREATE  TABLE `AV_Import_Insert` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `IdImport` INT NOT NULL ,
  `UID` VARCHAR(30) NOT NULL ,
  `IdGestionale` VARCHAR(50) NOT NULL ,
  `InsertDate` DATETIME NULL ,
  `ExecuteDate` DATETIME NULL ,
  `QtaNew` DECIMAL null DEFAULT 0,
  `SerializedObject` TEXT null,
  PRIMARY KEY (`Id`) ,
  INDEX (IdImport ASC),
  UNIQUE INDEX `UID_PROD_UPD` (`UID` ASC, `IdGestionale` ASC) )
COMMENT = 'Tabella per iportazione azzurra contente gli articoli da Inserire/Aggiornare';

-- drop table `AV_Import_Image`;

CREATE  TABLE `AV_Import_Image` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `IdImport` INT NOT NULL ,
  `UID` VARCHAR(30) NOT NULL ,
  `IdGestionale` VARCHAR(50) NOT NULL ,
  `InsertDate` DATETIME NULL ,
  `ExecuteDate` DATETIME NULL ,
  `Base64Img` TEXT null,
  PRIMARY KEY (`Id`) ,
  INDEX (IdImport ASC),
  UNIQUE INDEX `UID_PROD_IMG` (`UID` ASC, `IdGestionale` ASC) )
COMMENT = 'Tabella per iportazione azzurra contente le immagini';


ALTER TABLE `AV_Import_Delete` 
  ADD CONSTRAINT `IMP_DELETE`
  FOREIGN KEY (`IdImport` )
  REFERENCES `AV_Import` (`Id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `AV_Import_Insert` 
  ADD CONSTRAINT `IMP_UPD`
  FOREIGN KEY (`IdImport` )
  REFERENCES `AV_Import` (`Id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE `AV_Import_Image` 
  ADD CONSTRAINT `IMP_IMG`
  FOREIGN KEY (`IdImport` )
  REFERENCES `AV_Import` (`Id` )
  ON DELETE CASCADE
  ON UPDATE NO ACTION;