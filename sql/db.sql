CREATE DATABASE `loup_garou` /*!40100 DEFAULT CHARACTER SET utf8 */;

CREATE TABLE `game` (
  `gameUid` int(11) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`gameUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `players` (
  `playerUid` int(11) NOT NULL,
  `gameUid` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `roleUid` int(11) DEFAULT NULL,
  `dead` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`playerUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles` (
  `roleUid` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `class` varchar(45) DEFAULT NULL,
  `loup` tinyint(1) DEFAULT NULL,
  `tanneur` tinyint(1) DEFAULT NULL,
  `villageois` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`roleUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `votes` (
  `voteUid` int(11) NOT NULL,
  `gameUid` int(11) DEFAULT NULL,
  `playerUid` int(11) DEFAULT NULL,
  `targetUid` int(11) DEFAULT NULL,
  PRIMARY KEY (`voteUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
