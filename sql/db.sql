DROP TABLE `games`;
CREATE TABLE `games` (
  `gameUid`    int(11) NOT NULL AUTO_INCREMENT,
  `code`       varchar(10)      DEFAULT NULL,
  `maxPlayers` int              DEFAULT '10',
  `nbPlayers`  int              DEFAULT '0',
  `started`    tinyint(1)       DEFAULT '0',
  `finished`   tinyint(1)       DEFAULT '0',
  PRIMARY KEY (`gameUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE `games_players`;
CREATE TABLE `games_players` (
  `gamePlayerUid` int(11)    NOT NULL AUTO_INCREMENT,
  `gameUid`       int(11)    NOT NULL,
  `playerUid`     int(11)    NOT NULL,
  `played`        tinyint(1) NOT NULL,
  PRIMARY KEY (`gamePlayerUid`),
  UNIQUE KEY `unique_player_game` (`gameUid`, `playerUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE `players`;
CREATE TABLE `players` (
  `playerUid` int(11) NOT NULL AUTO_INCREMENT,
  `name`      varchar(45)      DEFAULT NULL,
  `password`  varchar(255)     DEFAULT NULL,
  PRIMARY KEY (`playerUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `players` VALUES
  (1, 'Carte 1', 'XXXXX'),
  (2, 'Carte 2', 'XXXXX'),
  (3, 'Carte 3', 'XXXXX');

DROP TABLE `players_game_roles`;
CREATE TABLE `players_game_roles` (
  `playersRoleUid` int(11) NOT NULL AUTO_INCREMENT,
  `playerUid`      int(11)          DEFAULT NULL,
  `gameUid`        int(11)          DEFAULT NULL,
  `roleUid`        int(11)          DEFAULT NULL,
  `order`          int(11)          DEFAULT NULL,
  PRIMARY KEY (`playersRoleUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE `roles`;
CREATE TABLE `roles` (
  `roleUid`                int(11) NOT NULL AUTO_INCREMENT,
  `name`                   varchar(50)      DEFAULT NULL,
  `description`            text             DEFAULT NULL,
  `model`                  varchar(50)      DEFAULT NULL,
  `nb`                     tinyint(1)       DEFAULT '1',
  `loup`                   tinyint(1)       DEFAULT NULL,
  `tanneur`                tinyint(1)       DEFAULT NULL,
  `villageois`             tinyint(1)       DEFAULT NULL,
  `firstAction`            tinyint(1)       DEFAULT NULL,
  `firstActionName`        varchar(50)      DEFAULT NULL,
  `firstActionTargetType`  varchar(50)      DEFAULT NULL,
  `firstActionNbTargets`   tinyint(1)       DEFAULT NULL,
  `firstActionPassive`     tinyint(1)       DEFAULT NULL,
  `secondAction`           tinyint(1)       DEFAULT NULL,
  `secondActionName`       varchar(50)      DEFAULT NULL,
  `secondActionTargetType` varchar(50)      DEFAULT NULL,
  `secondActionNbTargets`  tinyint(1)       DEFAULT NULL,
  `secondActionPassive`    tinyint(1)       DEFAULT NULL,
  `castingOrder`           int     null,
  `runningOrder`           int     null,
  PRIMARY KEY (`roleUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `roles` VALUES
  (1, 'Loup',
      'Pendant la nuit, il va prendre connaissance de l’identité de son semblable. Si un loup est seul dans la partie, il a le droit de regarder un des rôles centraux.',
      'loup', 2, 1, 0, 0, 1, 'know_other_loup', 'player', 1, 1, 1, 'watch_card', 'card', 0, 0, 0, 10),
  (2, 'Voleur', 'Pendant la nuit, il va pouvoir choisir une cible avec laquelle il échangera de rôle et prendra connaissance de son nouveau rôle.', 'voleur', 1, 0, 0, 1, 1, 'steal_player', 'player', 1, 0, 0, '', '', 0, 0, 10, 50),
  (3, 'Noiseuse', 'Pendant la nuit, elle va pouvoir désigner deux personnes (elle peut se choisir elle-même) qui vont échanger de rôle. Elle ne prend pas connaissance des rôles échangés.', 'noiseuse', 1, 0, 0, 1, 1, 'switch_players', 'player', 2, 0, 0, '', '', 0, 0, 20, 60),
  (4, 'Tanneur', 'Il ne fait pas partie de l’équipe des villageois et ne gagne que s’il meurt lors du vote.', 'tanneur', 1, 0, 1, 0, 0, '', '', 0, 0, 0, '', '', 0, 0, 30, 999),
  (5, 'Soulard', 'Pendant la nuit, il va échanger son rôle avec l’un des rôles centraux sans en prendre connaissance.', 'soulard', 1, 0, 0, 1, 1, 'switch_with_card', 'card', 1, 0, 0, '', '', 0, 0, 40, 70),
  (6, 'Insomniaque', 'A la fin de la nuit, il va prendre connaissance de son rôle final.', 'insomniaque', 1, 0, 0, 1, 1, 'see_your_card', 'player', 1, 1, 0, '', '', 0, 0, 50, 80),
  (7, 'Voyante', 'Pendant la nuit, elle va pouvoir soit regarder le rôle d’un joueur, soit regarder 2 des 3 rôles centraux.', 'voyante', 1, 0, 0, 1, 1, 'see_player', 'player', 1, 0, 0, '', '', 0, 0, 50, 40),
  (8, 'Doppelganger', 'Pendant la nuit, elle va copier le rôle et rejoindre l’équipe d’un autre joueur.', 'doppelganger', 1, 0, 0, 1, 1, 'copy_player_role', 'player', 1, 1, 1, 'new_role_action', 'role', 1, 0, 60, 0),
  (9, 'Sbire', 'Pendant la nuit, l’identité des loups lui est révélé. S’il meurt lors du vote et qu’aucun loup n’est tué, lui et les loups gagnent la partie. Si les loups ne sont pas joués, il ne gagne que si un autre joueur est tué.', 'sbire', 1, 1, 0, 0, 1, 'know_loups', 'player', 0, 1, 0, '', '', 0, 0, 70, 20),
  (10, 'Chasseur', 'S’il meurt lors du vote, la personne contre qui il a voté meurt aussi.', 'chasseur', 1, 0, 0, 1, 1, 'kill_someone', 'player', 1, 0, 0, '', '', 0, 0, 90, 999),
  (11, 'Franc Maçon', 'Pendant la nuit, il va prendre connaissance de l’identité de son semblable.', 'francmac', 2, 0, 0, 1, 1, 'know_other_francmac', 'player', 0, 1, 0, '', '', 0, 0, 100, 30);

DROP TABLE `votes`;
CREATE TABLE `votes` (
  `voteUid`   int(11) NOT NULL AUTO_INCREMENT,
  `gameUid`   int(11)          DEFAULT NULL,
  `playerUid` int(11)          DEFAULT NULL,
  `targetUid` int(11)          DEFAULT NULL,
  PRIMARY KEY (`voteUid`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
