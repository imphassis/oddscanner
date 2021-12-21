INSERT INTO markets_map(market_op_id,operator,market_id) VALUES
("MRES-1", "BETANO", 1),
("MRES-X", "BETANO", 2),
("MRES-2", "BETANO", 3),
("HCTG-+0.5", "BETANO", 4),
("HCTG--0.5", "BETANO", 5),
("HCTG-+1.5", "BETANO", 6),
("HCTG--1.5", "BETANO", 7),
("HCTG-+2.5", "BETANO", 8),
("HCTG--2.5", "BETANO", 9),
("HCTG-+3.5", "BETANO", 10),
("HCTG--3.5", "BETANO", 11),
("HCTG-+4.5", "BETANO", 12),
("HCTG--4.5", "BETANO", 13),
("HCTG-+5.5", "BETANO", 14),
("HCTG--5.5", "BETANO", 15),
("HCTG-+6.5", "BETANO", 16),
("HCTG--6.5", "BETANO", 17),
("BTSC-YES", "BETANO", 18),
("BTSC,-NO", "BETANO", 19);


CREATE TABLE `fixtures_markets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fixture_id` int NOT NULL,
  `market_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_fixture` (`fixture_id`, `market_id`),
  CONSTRAINT `fixtures_markets_ibfk_1` FOREIGN KEY (`fixture_id`) REFERENCES `fixtures` (`id`),
  CONSTRAINT `fixtures_markets_ibfk_2` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



CREATE TABLE IF NOT EXISTS `fixtures_markets_odds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` decimal(10,2) NOT NULL,
  `fixtures_markets_id` int NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_fixture` (`fixtures_markets_id`, `value`),
  CONSTRAINT `fixtures_markets_odds_ibfk_1` FOREIGN KEY (`fixtures_markets_id`) REFERENCES `fixtures_markets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DELETE FROM fixtures;

DELETE FROM fixtures_map; 
DELETE FROM fixtures_markets;
DELETE FROM fixtures_markets_odds;






SELECT id FROM competition_season WHERE competition_id = 1;








SELECT id FROM fixtures WHERE
team1_id = 7 AND 
team2_id = 8 AND 
date = "2021-12-18 15:00:00" AND 
competition_season_id = 
        (SELECT id FROM competition_season WHERE competition_id =
        (SELECT id from competitions WHERE name = 'League One'))
AND geographical_area_id = (SELECT id FROM geographical_areas WHERE name = 'Inglaterra');


ALTER TABLE fixtures_markets_odds DROP COLUMN `operator`;



INSERT INTO markets_map(market_op_id,operator,market_id) VALUES('draw','Placardpt',2);