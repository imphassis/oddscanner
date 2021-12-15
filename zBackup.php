<?php

include 'tableManager.php';

// class BetanoSports extends tableManager {

//   public $gameOdds = [];
//   public $URL = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT';
//   // public $URL = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=527';
//   public $type_array = [
//     "Total de Golos Mais/Menos" => 'goal_count',
//     "Resultado Final" => "final_score",
//     "Ambas as Equipas Marcam" => "both_teams_score",
//   ];

//   public function __construct() {
//     $this->db_host = "localhost";
//     $this->db_user = "root";
//     $this->db_pass = "mysql";
//     $this->db_name = "betano";
//   }

//   public function get_data() {
//     try {
//       $data = file_get_contents($this->URL);
//       $data = json_decode($data, true);
//       $this->gameOdds = $data;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function verify_fields($game) {
//     $this->verify_geographical_areas($game['regionname']);
//     $this->verify_competition($game['leaguename'], $game['regionname']);
//     $this->verify_team($game['teams'][0]['name']);
//     $this->verify_team($game['teams'][1]['name']);
//     $this->verify_team_map($game['teams'][0]);
//     $this->verify_team_map($game['teams'][1]);
//   }


//   public function verify_geographical_areas($regionname) {
//     try {
//       $dbConnection = $this->connect();
//       $stmt = $dbConnection->prepare("SELECT * FROM geographical_areas WHERE name = :name");
//       $stmt->execute([':name' => $regionname]);
//       $result = $stmt->fetchAll();
//       if (count($result) == 0) {
//         $this->register_geographical_area($regionname);
//       }
//       $dbConnection = null;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function verify_competition($leaguename, $regionname) {
//     try {
//       $dbConnection = $this->connect();
//       $stmt = $dbConnection->prepare("SELECT * FROM competitions WHERE name = :name");
//       $stmt->execute([':name' => $leaguename]);
//       $result = $stmt->fetchAll();
//       if (count($result) == 0) {
//         $this->register_competition($leaguename, $regionname);
//       }
//       $dbConnection = null;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function verify_team($team) {
//     try {
//       $dbConnection = $this->connect();
//       $stmt = $dbConnection->prepare("SELECT * FROM teams WHERE name = :name");
//       $stmt->execute([':name' => $team]);
//       $result = $stmt->fetchAll();
//       if (count($result) == 0) {
//         $this->register_team($team);
//       }
//       $dbConnection = null;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function verify_team_map($equipe) {
//     try {
//       $dbConnection = $this->connect();
//       $sql = "SELECT * FROM teams_map WHERE operator = 'BETANO' and team_id = 
//              (SELECT id FROM teams WHERE name = :name)";
//       $stmt = $dbConnection->prepare($sql);
//       $stmt->execute([':name' => $equipe['name']]);
//       $result = $stmt->fetchAll();
//       if (count($result) == 0) {
//         $this->register_team_map($equipe);
//       }
//       $dbConnection = null;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function verify_fixture_exists($id) {
//     try {
//       $dbConnection = $this->connect();
//       $sql = "SELECT * FROM fixtures_map WHERE operator = 'BETANO' and fixture_id = 
//              (SELECT id FROM fixtures WHERE id = :id)";
//       $stmt = $dbConnection->prepare($sql);
//       $stmt->execute([':id' => $id]);
//       $result = $stmt->fetchAll();
//       $dbConnection = null;
//       return (count($result) == 0) ? false : true;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_geographical_area($regionname) {
//     try {
//       $dbConnection = $this->connect();
//       $stmt = $dbConnection->prepare("INSERT INTO geographical_areas (name) VALUES (:name)");
//       $stmt->execute([':name' => $regionname]);
//       $dbConnection = null;
//       print_r($regionname . ' registered successfully!' . "\n");
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_competition($leaguename, $regionname) {

//     try {
//       $dbConnection = $this->connect();
//       $sql = "INSERT INTO competitions (name, geographical_area_id) VALUES (:name,  (SELECT id FROM geographical_areas WHERE name = :region_name))";
//       $stmt = $dbConnection->prepare($sql);
//       $stmt->execute([':name' => $leaguename, ':region_name' => $regionname]);
//       $dbConnection = null;
//       print_r($leaguename . ' registered successfully!' . "\n");
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function register_team($equipe) {
//     try {
//       $dbConnection = $this->connect();
//       $stmt = $dbConnection->prepare("INSERT INTO teams (name) VALUES (:name)");
//       $stmt->execute([':name' => $equipe]);
//       $dbConnection = null;
//       print_r($equipe . ' registered successfully!' . "\n");
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_team_map($team) {
//     try {
//       print_r($team, 'EQUIPE');
//       $dbConnection = $this->connect();
//       $sql = "INSERT INTO teams_map (team_op_id, operator, team_id) VALUES 
//               (:team_op_id, 'BETANO', (SELECT id FROM teams WHERE name = :name))";
//       $stmt = $dbConnection->prepare($sql);
//       $stmt->execute([':team_op_id' => $team['id'], ':name' => $team['name']]);
//       $dbConnection = null;
//       print_r("Team $team[name] registered successfully! \n");
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_fixture($game) {
//     try {
//       $dbConnection = $this->connect();
//       $sql = "INSERT INTO fixtures (team1_id, team2_id, date, competition_id, geographical_area_id) VALUES 
//               ((SELECT id FROM teams WHERE name = :team1), (SELECT id FROM teams WHERE name = :team2), :date,
//               (SELECT id FROM competitions WHERE name = :competition), (SELECT id FROM geographical_areas WHERE name = :g_area))";
//       $stmt = $dbConnection->prepare($sql);
//       $date = date_create($game['date']);
//       $stmt->execute([
//         ':team1' => $game['teams'][0]['name'],
//         ':team2' => $game['teams'][1]['name'],
//         ':date' => date_format($date, 'Y-m-d H:i:s'),
//         ':competition' => $game['leaguename'],
//         ':g_area' => $game['regionname']
//       ]);
//       $dbConnection = null;
//       print_r($game['teams'][0]['name'] .  $game['teams'][1]['name'] . ' registered successfully!' . "\n");
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_fixtures_map($game) {
//     try {
//       $dbConnection = $this->connect();
//       $sql = "INSERT INTO fixtures_map (fixture_op_id, operator, fixture_id) VALUES 
//               (:fixture_op_id, 'BETANO', (SELECT id FROM fixtures WHERE id = 
//               (SELECT id FROM fixtures WHERE
//                 team1_id = (SELECT id FROM teams WHERE name = :team1) AND
//                 team2_id = (SELECT id FROM teams WHERE name = :team2) AND 
//                 competition_id = (SELECT id FROM competitions WHERE name = :competition) AND 
//                 geographical_area_id = (SELECT id FROM geographical_areas WHERE name = :g_area) AND
//                 date = :date)))";
//       $stmt = $dbConnection->prepare($sql);
//       $date = date_create($game['date']);
//       $date =
//         $stmt->execute([
//           ':fixture_op_id' => $game['id'],
//           ':team1' => $game['teams'][0]['name'],
//           ':team2' => $game['teams'][1]['name'],
//           ':date' => date_format($date, 'Y-m-d H:i:s'),
//           ':competition' => $game['leaguename'],
//           ':g_area' => $game['regionname']
//         ]);
//       $dbConnection = null;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function register_market($market) {
//     try {
//       $dbConnection = $this->connect();
//       foreach ($market['selections'] as $selection) {
//         $sql = "INSERT INTO markets (type, side, value) VALUES 
//                 (:type, :side, :value)";
//         $stmt = $dbConnection->prepare($sql);
//         if ($market['name'] == 'Total de Golos Mais/Menos') {
//           $stmt->execute([
//             ':type' => $this->type_array[$market['name']],
//             ':side' => null,
//             ':value' => $selection['name']
//           ]);
//         } elseif ($market['name'] == 'Resultado Final') {
//           $stmt->execute([
//             ':type' => $this->type_array[$market['name']],
//             ':side' => $selection['name'],
//             ':value' => null
//           ]);
//         }
//       }
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function register_markets_map($game) {
//     try {
//       $dbConnection = $this->connect();
//       foreach ($game['market'] as $market) {
//         $sql = "INSERT INTO markets_map (market_op_id, operator, market_id) VALUES (
//                 :market_op_id, 
//                 'BETANO', 
//                 (SELECT id FROM markets WHERE ???))";
//         $stmt = $dbConnection->prepare($sql);
//         foreach ($market['selections'] as $selection) {
//           $stmt->execute([
//             ':market_op_id' => $market['type'] . $selection['name'],
//           ]);
//         }
//       }
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function register_fixture_markets($game) {
//     try {
//       $dbConnection = $this->connect();
//       foreach ($game['market'] as $market) {
//         $sql = "INSERT INTO fixtures_markets (fixture_id, market_id) VALUES 
//               ((SELECT fixture_id from fixtures_map WHERE fixture_op_id = :fixture_id AND operator = 'BETANO'),
//               (SELECT )";
//         $stmt = $dbConnection->prepare($sql);
//         $stmt->execute([
//           ':fixture_id' => $game['id'],
//         ]);
//       }
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function verify_competition_season($game) {
//     try {
//       $dbConnection = $this->connect();
//       $sql = "SELECT competition_id FROM competition_season WHERE
//               competition_id = (SELECT id FROM competitions WHERE name = :competition) AND 
//               year =:year";
//       $stmt = $dbConnection->prepare($sql);
//       $stmt->execute([
//         ':competition' => $game['leaguename'],
//         ':year' => date_format(date_create($game['date']), 'Y')
//       ]);
//       $result = $stmt->fetchAll();
//       return (count($result) > 0) ? true : false;
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function register_competition_season($game) {
//     try {
//       if (!$this->verify_competition_season($game)) {
//         $dbConnection = $this->connect();
//         $sql = "INSERT INTO competition_season (competition_id, year) VALUES 
//               ((SELECT id FROM competitions WHERE name = :competition), :year)";
//         $stmt = $dbConnection->prepare($sql);
//         $stmt->execute([
//           ':competition' => $game['leaguename'],
//           ':year' => date_format(date_create($game['date']), 'Y')
//         ]);
//         $dbConnection = null;
//       }
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }


//   public function insertGamesToDb() {
//     try {
//       $this->get_data();
//       foreach ($this->gameOdds as $game) {
//         if (!$this->verify_fixture_exists($game['id'])) {
//           $this->verify_fields($game);
//           $this->register_fixture($game);
//           $this->register_fixtures_map($game);
//           foreach ($game['market'] as $market) {
//             $this->register_market($market);
//           }
//           // $this->register_markets_map($game);
//           $this->register_competition_season($game);
//           // $this->register_fixture_markets($game);
//           // print_r("Game: " . $game['name'] . " inserted successfully\n");
//         } else {
//           print_r("Game: " . $game['name'] . " already exists\n");
//         }
//       }
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }

//   public function save_data() {
//     try {
//       $this->get_data();
//       $data = json_encode($this->gameOdds);
//       $fp = fopen('data.json', 'w');
//       fwrite($fp, $data);
//     } catch (Exception $e) {
//       echo 'Caught exception: ',  $e->getMessage(), "\n";
//     }
//   }
// }



// $odds = new BetanoSports();


// $odds->save_data();
// $odds->drop_all_tables();
// $odds->create_tables();
// $odds->insertGamesToDb();
// print_r('DONE');
