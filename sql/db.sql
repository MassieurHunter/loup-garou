CREATE DATABASE `loup_garou` /*!40100 DEFAULT CHARACTER SET utf8 */;

CREATE TABLE `games` (
  `gameUid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`gameUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `games_players` (
  `gamePlayerUid` int(11) NOT NULL AUTO_INCREMENT,
  `gameUid` int(11) NOT NULL,
  `playerUid` int(11) NOT NULL,
  PRIMARY KEY (`gamePlayerUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `players` (
  `playerUid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`playerUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `players_game_roles` (
  `playersRoleUid` int(11) NOT NULL AUTO_INCREMENT,
  `playerUid` int(11) DEFAULT NULL,
  `gameUid` int(11) DEFAULT NULL,
  `roleUid` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`playersRoleUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `roleUid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `model` varchar(45) DEFAULT NULL,
  `nb` tinyint(1) DEFAULT '1',
  `loup` tinyint(1) DEFAULT NULL,
  `tanneur` tinyint(1) DEFAULT NULL,
  `villageois` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`roleUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` VALUES (1,'Loup',NULL,'loup',2,1,0,0),(2,'Voleur',NULL,'voleur',1,0,0,1),(3,'Noiseuse',NULL,'noiseuse',1,0,0,1),(4,'Tanneur',NULL,'tanneur',1,0,1,0),(5,'Soulard',NULL,'soulard',1,0,0,1),(6,'Insomniaque',NULL,'insomniaque',1,0,0,1),(7,'Voyante',NULL,'voyante',1,0,0,1),(8,'Doppelganger',NULL,'doppelganger',1,0,0,1),(9,'Sbire',NULL,'sbire',1,1,0,0),(10,'Chasseur',NULL,'chasseur',1,0,0,1),(11,'Franc Maçon',NULL,'francmac',2,0,0,1);

CREATE TABLE `players_game_roles` (
  `playersRoleUid` INT NOT NULL,
  `playerUid` INT NULL,
  `gameUid` INT NULL,
  `roleUid` INT NULL,
  `order` INT NULL,
  PRIMARY KEY (`playersRoleUid`));


CREATE TABLE `votes` (
  `voteUid` int(11) NOT NULL AUTO_INCREMENT,
  `gameUid` int(11) DEFAULT NULL,
  `playerUid` int(11) DEFAULT NULL,
  `targetUid` int(11) DEFAULT NULL,
  PRIMARY KEY (`voteUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
