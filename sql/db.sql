CREATE DATABASE `loup_garou` /*!40100 DEFAULT CHARACTER SET utf8 */;

CREATE TABLE `games` (
  `gameUid` int(11) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`gameUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `games_players` (
  `gamePlayerUid` int(11) NOT NULL,
  `gameUid` int(11) NOT NULL,
  `playerUid` int(11) NOT NULL,
  PRIMARY KEY (`playerUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `players` (
  `playerUid` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`playerUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `roleUid` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `model` varchar(45) DEFAULT NULL,
  `loup` tinyint(1) DEFAULT NULL,
  `tanneur` tinyint(1) DEFAULT NULL,
  `villageois` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`roleUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `players_game_roles` (
  `playersRoleUid` INT NOT NULL,
  `playerUid` INT NULL,
  `gameUid` INT NULL,
  `roleUid` INT NULL,
  `order` INT NULL,
  PRIMARY KEY (`playersRoleUid`));


CREATE TABLE `votes` (
  `voteUid` int(11) NOT NULL,
  `gameUid` int(11) DEFAULT NULL,
  `playerUid` int(11) DEFAULT NULL,
  `targetUid` int(11) DEFAULT NULL,
  PRIMARY KEY (`voteUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
