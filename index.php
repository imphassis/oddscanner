<?php

function get_data() {
  $url =  'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=527';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  $data = json_decode($data, true);
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
    "BTSCNão" => 'BTSC-NO'
  ];
  return $array;
}
function competition_check($leagueid) {
  $dbConnection = connect();
  $sql = "SELECT * FROM competitions WHERE id = 
          (SELECT competition_id FROM competitions_map WHERE competition_op_id = :leagueid)";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([':leagueid' => $leagueid]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
}

function team_check($team_id) {
  $dbConnection = connect();
  $sql = "SELECT * FROM teams WHERE id = 
          (SELECT team_id FROM teams_map WHERE team_op_id = :team)";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([':team' => $team_id]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}


function fixture_check($home_team, $away_team, $date_time, $competition) {
  $dbConnection = connect();
  $sql = "SELECT * FROM fixtures WHERE
          team1_id = :home_team  AND
          team2_id = :away_team  AND 
          date = :date_time      AND 
          competition_season_id  IN 
          (SELECT competition_id FROM competition_season WHERE competition_id = :competition)";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':home_team' => $home_team['id'],
    ':away_team' => $away_team['id'],
    ':date_time' => $date_time,
    ':competition' => $competition["id"]
  ]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}

function market_check($market_type, $selection_name) {
  $market_array = market_array();
  $dbConnection = connect();
  $sql = "SELECT * FROM markets WHERE id = 
          (SELECT market_id FROM markets_map WHERE market_op_id = :market_type AND operator = 'BETANO')";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':market_type' => $market_array[$market_type . $selection_name],
  ]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}

function register_fixture_market($fixture_id, $market_id) {
  $dbConnection = connect();
  $sql = "INSERT INTO fixtures_markets (fixture_id, market_id) VALUES
         (:fixture_id, :market_id)";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':fixture_id' => $fixture_id,
    ':market_id' => $market_id
  ]);
  return true;
}

function register_fixture_market_odd($fixture_id, $value, $market_id) {
  $date_time = date('Y-m-d H:i:s');
  $dbConnection = connect();
  $sql = "INSERT INTO fixtures_markets_odds (fixture_market_id, operator, value, datetime) VALUES (
          (SELECT id FROM fixtures_markets WHERE fixture_id = :fixture_id AND market_id = :market_id),
          :operator,
          :value,
          :datetime)";

  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':fixture_id' => $fixture_id,
    ':market_id' => $market_id,
    ':operator' => 'BETANO',
    ':value' => $value,
    ':datetime' => $date_time
  ]);

  return true;
}

$data = get_data();

if ($data) {
  foreach ($data as $game) {
    $competition =  competition_check($game['leagueid']);
    $home_team = team_check($game['teams'][0]['id']);
    $away_team = team_check($game['teams'][1]['id']);
    $date_time = date_format(date_create($game['date']), 'Y-m-d H:i:s');

    $fixture =  fixture_check($home_team, $away_team, $date_time, $competition);
    foreach ($game['market'] as $market) {
      foreach ($market['selections'] as $selection) {
        $market_db = market_check($market['type'], $selection['name']);
        if (!$fixture ?? false) continue;
        register_fixture_market($fixture['id'], $market_db['id']);
        register_fixture_market_odd($fixture['id'], $selection["price"], $market_db['id']);
      }
    }
  }
}
