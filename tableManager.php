<?php

class tableManager {


  private function __construct() {
    $this->db_host;
    $this->db_user;
    $this->db_pass;
    $this->db_name;
  }


  public function connect() {
    try {
      $conn = "mysql:host=$this->db_host;dbname=$this->db_name";
      $dbConnection = new PDO($conn, $this->db_user, $this->db_pass);
      $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbConnection;
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function create_geographical_areas_table() {

    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `geographical_areas` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `name` VARCHAR(255) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id` (`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

      $dbConnection->exec($sql);
      print_r("Table geographical_areas created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function create_teams_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `teams` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `name` VARCHAR(255) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id` (`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      print_r("Table teams created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function create_teams_map() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `teams_map` (
             `team_op_id` int(11) NOT NULL,
             `operator` VARCHAR(255) NOT NULL,
             `team_id` int(11) NOT NULL,
              PRIMARY KEY (`team_op_id`, `operator`),	
              FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      print_r("Table teams_map created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function create_fixtures_map_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `fixtures_map` (
              `fixture_op_id` int(11) NOT NULL,
              `operator` VARCHAR(255) NOT NULL,
              `fixture_id` int(11) NOT NULL,
              PRIMARY KEY (`fixture_op_id`, `operator`), 
              FOREIGN KEY (`fixture_id`) REFERENCES `fixtures`(`id`)          
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      print_r("Table fixtures_map created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function create_fixtures_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `fixtures` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `team1_id` int(11) NOT NULL,
             `team2_id` int(11) NOT NULL,
             `date` datetime NOT NULL,
             `competition_id` int(11) DEFAULT NULL,
             `geographical_area_id` int(11) DEFAULT NULL,
              UNIQUE KEY `id` (`id`),
              PRIMARY KEY (`id`),
              CONSTRAINT `fixtures_ibfk_1` FOREIGN KEY (`team1_id`) REFERENCES `teams` (`id`),
              CONSTRAINT `fixtures_ibfk_2` FOREIGN KEY (`team2_id`) REFERENCES `teams` (`id`),
              CONSTRAINT `fixtures_ibfk_3` FOREIGN KEY (`competition_id`) REFERENCES `competitions` (`id`),
              CONSTRAINT `fixtures_ibfk_4` FOREIGN KEY (`geographical_area_id`) REFERENCES `geographical_areas` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      print_r("Table fixtures created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function create_competitions_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `competitions` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `name` VARCHAR(255) NOT NULL,
             `geographical_area_id` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `id` (`id`),
              FOREIGN KEY (`geographical_area_id`) REFERENCES `geographical_areas`(`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      $dbConnection = null;
      print_r("Table competitions created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function create_market_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `markets` (
             `id` INT(11) NOT NULL AUTO_INCREMENT,
             `type` VARCHAR(255) NOT NULL,
             `side` VARCHAR(255),
             `value` VARCHAR(255),
              PRIMARY KEY (`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      $dbConnection = null;
      print_r("Table markets created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function create_fixtures_markets_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `fixtures_markets` (
             `fixture_id` INT(11) NOT NULL,
             `market_id` INT(11) NOT NULL,
              PRIMARY KEY (`fixture_id`, `market_id`),
              FOREIGN KEY (`fixture_id`) REFERENCES `fixtures`(`id`),
              FOREIGN KEY (`market_id`) REFERENCES `markets`(`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      $dbConnection = null;
      print_r("Table fixture_markets created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function create_market_map_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `markets_map` (
             `market_op_id` VARCHAR(255) NOT NULL,
             `operator` VARCHAR(255) NOT NULL,
             `market_id` INT(11) NOT NULL,
              PRIMARY KEY (`market_op_id`, `operator`),
              FOREIGN KEY (`market_id`) REFERENCES `markets`(`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $dbConnection->exec($sql);
      $dbConnection = null;
      print_r("Table market_map created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }





  public function create_competition_season_table() {
    try {
      $dbConnection = $this->connect();
      $sql = "CREATE TABLE IF NOT EXISTS `competition_season` (
             `competition_id` INT(11) NOT NULL,
             `year` INT(11) NOT NULL,
              PRIMARY KEY (`competition_id`, `year`),
              FOREIGN KEY (`competition_id`) REFERENCES `competitions`(`id`)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8;";

      $dbConnection->exec($sql);
      $dbConnection = null;
      print_r("Table competition_season created successfully\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_fixture_exists($id) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM fixtures_map WHERE fixture_op_id = '$id'";
      $result = $dbConnection->query($sql);
      $result = $result->fetchAll();
      return (count($result) > 0) ? true : false;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_team_exists($id) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM betano.teams WHERE id = '$id'";
      $result = $dbConnection->query($sql);
      $result = $result->fetchAll();
      return (count($result) > 0) ? true : false;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function drop_all_tables() {
    try {
      $dbConnection = $this->connect();
      $sql = "DROP TABLE IF EXISTS `geographical_areas`, `competition_season`, `fixtures_markets`, `markets_map`, `teams`, `competitions`, `teams_map`, `fixtures_map`,`fixtures`, `markets`;";
      $dbConnection->exec($sql);

      print_r("All tables dropped successfully\n");
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function create_tables() {
    try {
      $this->create_geographical_areas_table();
      $this->create_teams_table();
      $this->create_competitions_table();
      $this->create_teams_map();
      $this->create_fixtures_table();
      $this->create_fixtures_map_table();
      $this->create_competition_season_table();
      $this->create_market_table();
      $this->create_fixtures_markets_table();
      $this->create_market_map_table();
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }
}
