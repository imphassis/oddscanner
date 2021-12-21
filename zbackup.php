<?php

include 'tableManager.php';

class BetanoSports extends tableManager {

  public $gameOdds = [];
  // public $URL = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT';
  public $URL = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=527';


  public function __construct() {
    $this->db_host = "localhost";
    $this->db_user = "root";
    $this->db_pass = "mysql";
    $this->db_name = "betano";
  }

  public function get_data() {
    try {
      $data = file_get_contents($this->URL);
      $data = json_decode($data, true);
      $this->gameOdds = $data;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_fields($game) {
    $this->verify_geographical_areas($game['regionname']);
    $this->verify_competition($game['leaguename'], $game['regionname']);
    $this->verify_team($game['teams'][0]['name']);
    $this->verify_team($game['teams'][1]['name']);
    $this->verify_team_map($game['teams'][0]);
    $this->verify_team_map($game['teams'][1]);
    $this->verify_competition_map($game);
    $this->verify_competition_season($game);
  }


  public function verify_geographical_areas($regionname) {
    try {
      $dbConnection = $this->connect();
      $stmt = $dbConnection->prepare("SELECT * FROM geographical_areas WHERE name = :name");
      $stmt->execute([':name' => $regionname]);
      $result = $stmt->fetchAll();
      if (count($result) == 0) {
        $this->register_geographical_area($regionname);
      }
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function verify_competition($leaguename, $regionname) {
    try {
      $dbConnection = $this->connect();
      $stmt = $dbConnection->prepare("SELECT * FROM competitions WHERE name = :name");
      $stmt->execute([':name' => $leaguename]);
      $result = $stmt->fetchAll();
      if (count($result) == 0) {
        $this->register_competition($leaguename, $regionname);
      }
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_competition_map($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM competitions_map WHERE competition_op_id = :competition_op_id AND operator = 'BETANO'";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':competition_op_id' => $game['leagueid']
      ]);
      $result = $stmt->fetchAll();
      if (count($result) == 0) {
        $this->register_competition_map($game);
      }
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_team($team) {
    try {
      $dbConnection = $this->connect();
      $stmt = $dbConnection->prepare("SELECT * FROM teams WHERE name = :name");
      $stmt->execute([':name' => $team]);
      $result = $stmt->fetchAll();
      if (count($result) == 0) {
        $this->register_team($team);
      }
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function verify_team_map($equipe) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM teams_map WHERE operator = 'BETANO' and team_id = 
             (SELECT id FROM teams WHERE name = :name)";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([':name' => $equipe['name']]);
      $result = $stmt->fetchAll();
      if (count($result) == 0) {
        $this->register_team_map($equipe);
      }
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function verify_fixture_exists($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM fixtures_map WHERE operator = 'BETANO' and fixture_id = 
             (SELECT id FROM fixtures WHERE id = :id)";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([':id' => $game["id"]]);
      $result = $stmt->fetchAll();
      $dbConnection = null;
      return count($result) > 0;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function register_fixture($game) {
    try {
      if (!$this->verify_fixture_exists($game)) {
        $dbConnection = $this->connect();
        $sql = "INSERT INTO fixtures (team1_id, team2_id, date, competition_season_id) VALUES (
                (SELECT team_id FROM teams_map WHERE team_op_id = :team1), 
                (SELECT team_id FROM teams_map WHERE team_op_id = :team2), 
                :date,
                (SELECT id FROM competition_season WHERE id = (SELECT id from competitions WHERE name = :competition)))";
        $stmt = $dbConnection->prepare($sql);
        $date = new DateTime($game['date']);
        $stmt->execute([
          ':team1' => $game['teams'][0]['id'],
          ':team2' => $game['teams'][1]['id'],
          ':date' => $date->format('Y-m-d H:i:s'),
          ':competition' => $game['leaguename'],
        ]);
        $dbConnection = null;
        print_r($game['teams'][0]['name'] .  $game['teams'][1]['name'] . ' registered successfully!' . "\n");
      } else {
        print_r($game['teams'][0]['name'] .  $game['teams'][1]['name'] . ' already exists!' . "\n");
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }






  public function register_geographical_area($regionname) {
    try {
      $dbConnection = $this->connect();
      $stmt = $dbConnection->prepare("INSERT INTO geographical_areas (name) VALUES (:name)");
      $stmt->execute([':name' => $regionname]);
      $dbConnection = null;
      print_r($regionname . ' registered successfully!' . "\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function register_competition($leaguename, $regionname) {
    try {
      $dbConnection = $this->connect();
      $sql = "INSERT INTO competitions (name, geographical_area_id) VALUES (:name,  (SELECT id FROM geographical_areas WHERE name = :region_name))";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([':name' => $leaguename, ':region_name' => $regionname]);
      $dbConnection = null;
      print_r($leaguename . ' registered successfully!' . "\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function register_team($equipe) {
    try {
      $dbConnection = $this->connect();
      $stmt = $dbConnection->prepare("INSERT INTO teams (name) VALUES (:name)");
      $stmt->execute([':name' => $equipe]);
      $dbConnection = null;
      print_r($equipe . ' registered successfully!' . "\n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function register_team_map($team) {
    try {
      print_r($team, 'EQUIPE');
      $dbConnection = $this->connect();
      $sql = "INSERT INTO teams_map (team_op_id, operator, team_id) VALUES 
              (:team_op_id, 'BETANO', (SELECT id FROM teams WHERE name = :name))";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([':team_op_id' => $team['id'], ':name' => $team['name']]);
      $dbConnection = null;
      print_r("Team $team[name] registered successfully! \n");
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }





  public function verify_fixtures_map($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM fixtures_map WHERE operator = 'BETANO' and fixture_op_id =:fixture_op_id";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':fixture_op_id' => $game['id']
      ]);
      $result = $stmt->fetchAll();
      $dbConnection = null;
      return (count($result) == 0) ? false : true;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }



  public function register_fixtures_map($game) {
    try {
      if (!$this->verify_fixtures_map($game)) {
        $dbConnection = $this->connect();
        $sql = "INSERT INTO fixtures_map (fixture_op_id, operator, fixture_id) VALUES 
                (:fixture_op_id, 'BETANO',
                (SELECT id FROM fixtures WHERE
                team1_id = (SELECT id FROM teams WHERE name = :team1) AND 
                team2_id = (SELECT id FROM teams WHERE name = :team2) AND 
                date = :date                                          AND 
                competition_season_id = 
                        (SELECT id FROM competition_season WHERE competition_id =
                        (SELECT id from competitions WHERE name = :competition))));";

        $stmt = $dbConnection->prepare($sql);
        $date = new DateTime($game['date']);
        $date =
          $stmt->execute([
            ':fixture_op_id' => $game['id'],
            ':team1' => $game['teams'][0]['name'],
            ':team2' => $game['teams'][1]['name'],
            ':date' => $date->format('Y-m-d H:i:s'),
            ':competition' => $game['leaguename']
          ]);
        $dbConnection = null;
      } else {
        print_r("Fixture" . $game['id'] . ' already registered!' . "\n");
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }




  public function verify_fixture_markets($game_id, $market_id) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT * FROM fixtures_markets WHERE 
              fixture_id =  (SELECT fixture_id FROM fixtures_map WHERE fixture_op_id = :game_id AND operator = 'BETANO') AND 
              market_id =   (SELECT market_id FROM markets_map WHERE market_op_id = :market_id AND operator = 'BETANO')";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':game_id' => $game_id,
        ':market_id' => $market_id
      ]);
      $result = $stmt->fetchAll();
      $dbConnection = null;
      return (count($result) == 0) ? false : true;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }



  public function register_fixture_markets($game) {
    try {
      $dbConnection = $this->connect();
      foreach ($game['market'] as $market) {
        foreach ($market['selections'] as $selection) {
          $market_op_id = $market["type"] . $selection['name'];
          if (!$this->verify_fixture_markets($game["id"], $market_op_id)) {
            $sql = "INSERT INTO fixtures_markets (fixture_id, market_id) VALUES (
                   (SELECT fixture_id FROM fixtures_map WHERE fixture_op_id = :game_id AND operator = 'BETANO'), 
                   (SELECT market_id FROM markets_map WHERE market_op_id = :market_op_id AND operator = 'BETANO'))";
            $stmt = $dbConnection->prepare($sql);
            $stmt->execute([
              ':game_id' => $game['id'],
              ':market_op_id' => $market_op_id
            ]);
          }
        }
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function verify_competition_season($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "SELECT competition_id FROM competition_season WHERE
              competition_id = (SELECT id FROM competitions WHERE name = :competition) AND 
              year =:year";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':competition' => $game['leaguename'],
        ':year' => date_format(date_create($game['date']), 'Y')
      ]);
      $result = $stmt->fetchAll();
      $dbConnection = null;
      if (count($result) == 0) {
        $this->register_competition_season($game);
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function register_competition_season($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "INSERT INTO competition_season (competition_id, year) VALUES 
              ((SELECT id FROM competitions WHERE name = :competition), :year)";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':competition' => $game['leaguename'],
        ':year' => date_format(date_create($game['date']), 'Y')
      ]);
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function register_competition_map($game) {
    try {
      $dbConnection = $this->connect();
      $sql = "INSERT INTO competitions_map (competition_op_id, operator, competition_id) VALUES 
              (:competition_op_id, 'BETANO', (SELECT id FROM competitions WHERE name = :competition))";
      $stmt = $dbConnection->prepare($sql);
      $stmt->execute([
        ':competition_op_id' => $game['leagueid'],
        ':competition' => $game['leaguename']
      ]);
      $dbConnection = null;
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }


  public function register_fixtures_markets_odds($game) {
    try {
      $dbConnection = $this->connect();
      foreach ($game['market'] as $market) {
        foreach ($market['selections'] as $selection) {
          $market_op_id = $market["type"] . $selection['name'];
          $sql = "INSERT INTO fixtures_markets_odds (value, fixtures_markets_id, date) VALUES  (
            :value, 
            (SELECT id FROM fixtures_markets WHERE
                       fixture_id =  (SELECT fixture_id FROM fixtures_map WHERE fixture_op_id = :game_id AND operator = 'BETANO') AND
                       market_id  =  (SELECT market_id FROM markets_map WHERE market_op_id = :market_op_id AND operator = 'BETANO')),
            :date)";
          $stmt = $dbConnection->prepare($sql);
          $stmt->execute([
            ':value' => $selection['price'],
            ':game_id' => $game['id'],
            ':market_op_id' => $market_op_id,
            ':date' => date_format(date_create($game['date']), 'Y-m-d H:i:s')
          ]);
        }
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }



  public function insertGamesToDb() {
    try {
      $this->get_data();
      foreach ($this->gameOdds as $game) {
        $this->verify_fields($game);
        $this->register_fixture($game);
        $this->register_fixtures_map($game);
        // $this->register_fixture_markets($game);
        // $this->register_fixtures_markets_odds($game);
      }
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

  public function save_data() {
    try {
      $this->get_data();
      $data = json_encode($this->gameOdds);
      $fp = fopen('data.json', 'w');
      fwrite($fp, $data);
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }
}



$odds = new BetanoSports();


// $odds->save_data();
// $odds->drop_all_tables();
$odds->create_tables();
// $odds->insertGamesToDb();
print_r('DONE');
