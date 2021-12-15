<?php


function market_array() {
  $array = [
    'MRES1' => 'MRES-1',
    'MRESX' => 'MRES-X',
    'MRES2' => 'MRES-2',
    'HCTGMais de 0.5' => 'HCTG-+0.5',
    'HCTGMenos de 0.5' => 'HCTG--0.5',
    'HCTGMais de 1.5' => 'HCTG-+1.5',
    'HCTGMenos de 1.5' => 'HCTG--1.5',
    'HCTGMais de 2.5' => 'HCTG-+2.5',
    'HCTGMenos de 2.5' => 'HCTG--2.5',
    'HCTGMais de 3.5' => 'HCTG-+3.5',
    'HCTGMenos de 3.5' => 'HCTG--3.5',
    'HCTGMais de 4.5' => 'HCTG-+4.5',
    'HCTGMenos de 4.5' => 'HCTG--4.5',
    'HCTGMais de 5.5' => 'HCTG-+5.5',
    'HCTGMenos de 5.5' => 'HCTG--5.5',
    'HCTGMais de 6.5' => 'HCTG-+6.5',
    'HCTGMenos de 6.5' => 'HCTG--6.5',
    'BTSCSim' => 'BTSC-YES',
    "BTSCNão" => 'BTSC-NO',
  ];
  return $array;
}

function get_data() {
  $url =  'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=527';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function connect() {
  $db_host = "localhost";
  $db_user = "root";
  $db_pass = "mysql";
  $db_name = "betano";
  try {
    $conn = "mysql:host=$db_host;dbname=$db_name";
    $dbConnection = new PDO($conn, $db_user, $db_pass);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}


$gameOdds = get_data();


function competition_check($competition_id) {
  try {
    $dbConnection = connect();
    $sql = "SELECT * FROM competitions_map WHERE  competition_id = '$competition_id' AND operator = 'BETANO'";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return count($result) != 0;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}


function home_team_check($home_team) {
  try {
    $dbConnection = connect();
    $sql = "SELECT * FROM teams_map WHERE team_op_id = $home_team and operator = 'BETANO'";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return count($result) != 0;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

function away_team_check($away_team) {
  try {
    $dbConnection = connect();
    $sql = "SELECT * FROM teams_map WHERE team_op_id = $away_team and operator = 'BETANO'";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return count($result) != 0;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}


function fixture_check($fixture_op_id) {
  try {
    $dbConnection = connect();
    $sql = "SELECT * FROM fixtures_map WHERE fixture_op_id = '$fixture_op_id' AND operator = 'BETANO'";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return count($result) != 0;
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

function market_check($game) {
  $market_array = market_array();
  try {
    $dbConnection = connect();
    foreach ($game->markets as $market) {
      foreach ($market as $selection) {
        $sql = "SELECT * FROM markets_map WHERE market_op_id =:market_op_id AND operator = 'BETANO'";
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute([
          'market_op_id' => $market_array[$market["type"] . $selection['name']]
        ]);
        $result = $stmt->fetchAll();
        if (count($result) != 0) {
          return true;
        }
      }
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}


function fixture_market_check($game) {
  $market_array = market_array();
  try {
    $dbConnection = connect();
    foreach ($game->markets as $market) {
      foreach ($market as $selection) {
        $sql = "SELECT * FROM fixtures_markets WHERE 
                  fixture_id =  (SELECT fixture_id FROM fixtures_map WHERE fixture_op_id = :game_id AND operator = 'BETANO') AND 
                  market_id  =  (SELECT market_id FROM markets_map WHERE market_op_id = :market_op_id AND operator = 'BETANO')";
        $stmt = $dbConnection->prepare($sql);
        $stmt->execute([
          'game_id' => $game->game_id,
          'market_op_id' => $market_array[$market["type"] . $selection['name']]
        ]);
        $result = $stmt->fetchAll();
        if (count($result) != 0) {
          return true;
        }
      }
    }
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

function fixture_market_odd($game) {
  $market_array = market_array();
  try {
    $dbConnection = connect();
    foreach ($game['market'] as $market) {
      foreach ($market['selections'] as $selection) {
        $market_op_id = $market_array[$market["type"] . $selection['name']];
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
