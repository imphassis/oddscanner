<?php

class Betano {
  public $name = 'Betano';
  public $timezone = 'Europe/Berlin';
  public $url = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=527';
  // public $url = 'https://www.betano.pt/adserve?type=OddsComparisonFeed&lang=pt&sport=FOOT&leagueId=17067';
  public function getName() {
    return $this->name;
  }

  public function getTimezone() {
    return $this->timezone;
  }

  public function getUrl() {
    return $this->url;
  }
}

class Placard {
  public $name = 'Placardpt ';
  public $timezone = 'Europe/Berlin';
  public $url = 'https://api.oddsplacardpt.com/api/odds/events/global/all/all/all/all/all/all/all/all';


  public function getName() {
    return $this->name;
  }

  public function getTimezone() {
    return $this->timezone;
  }

  public function getUrl() {
    return $this->url;
  }
}


$betano = new Betano();


function get_data($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($ch);
  curl_close($ch);
  $data = json_decode($data, true);
  return $data;
}


function get_local_files() {
  $file_name = 'placard.json';
  $file_path = './' . $file_name;
  $file_content = file_get_contents($file_path);
  $file_content = json_decode($file_content, true);
  return $file_content;
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


function competition_check($leaguename) {
  if (!$leaguename) return false;
  $dbConnection = connect();
  $sql = "SELECT * FROM competitions WHERE name =:leaguename";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([':leaguename' => $leaguename]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
}


function team_check($team_name) {
  if (!$team_name) return false;
  $dbConnection = connect();
  $sql = "SELECT * FROM teams WHERE name =:team_name";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([':team_name' => $team_name]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}


function fixture_check($home_team, $away_team, $date_time, $competition) {
  $fixture_array = [
    'team1_id' => $home_team['id'] ?? null,
    'team2_id' => $away_team['id'] ?? null,
    'date' => $date_time->format('Y-m-d H:i:s') ?? null,
    'competition_season_id' => $competition["id"] ?? null
  ];
  if (in_array(null, $fixture_array)) return false;
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
    ':date_time' => $date_time->format('Y-m-d H:i:s'),
    ':competition' => $competition["id"]
  ]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}


function market_check($market_type, $name) {
  $dbConnection = connect();
  $sql = "SELECT * FROM markets WHERE id = 
          (SELECT market_id FROM markets_map WHERE market_op_id = :market_type AND operator = :operator)";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':market_type' => $market_type,
    ':operator' => $name
  ]);
  if ($stmt->rowCount() > 0) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result);
  }
}


function verify_fixture_market($fixture_id, $market_id) {
  $dbConnection = connect();
  $sql = "SELECT * FROM fixtures_markets WHERE fixture_id = :fixture_id AND market_id = :market_id";
  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':fixture_id' => $fixture_id,
    ':market_id' => $market_id
  ]);
  return $stmt->rowCount() > 0;
}


function register_fixture_market($fixture_id, $market_id) {
  if (!verify_fixture_market($fixture_id, $market_id)) {
    $dbConnection = connect();
    $sql = "INSERT INTO fixtures_markets (fixture_id, market_id) VALUES (:fixture_id, :market_id)";
    $stmt = $dbConnection->prepare($sql);
    $stmt->execute([
      ':fixture_id' => $fixture_id,
      ':market_id' => $market_id
    ]);
  }
}


function register_fixture_market_odd($fixture_id, $value, $market_id) {
  $date = new DateTime('now');
  $dbConnection = connect();
  $sql = "INSERT INTO fixtures_markets_odds (fixture_market_id, value, datetime) VALUES (
          (SELECT id FROM fixtures_markets WHERE fixture_id = :fixture_id AND market_id = :market_id),
          :value,
          :datetime)";

  $stmt = $dbConnection->prepare($sql);
  $stmt->execute([
    ':fixture_id' => $fixture_id,
    ':market_id' => $market_id,
    ':value' => $value,
    ':datetime' => $date->format('Y-m-d H:i:s')
  ]);
}


function register_betano_odds($timezone, $name, $url) {
  $data = get_data($url);
  if ($data) {
    foreach ($data as $game) {
      $competition = competition_check($game['leaguename']);
      $home_team = team_check($game['teams'][0]['id']);
      $away_team = team_check($game['teams'][1]['id']);
      $date_time = new DateTime($game['date'], new DateTimeZone($timezone));
      $fixture =  fixture_check($home_team, $away_team, $date_time, $competition);
      foreach ($game['market'] as $market) {
        foreach ($market['selections'] as $selection) {
          $market_type = $market['type'] . $selection['name'];
          $market_db = market_check($market_type, $name);
          if (!$fixture ?? false) continue;
          register_fixture_market($fixture['id'], $market_db['id']);
          register_fixture_market_odd($fixture['id'], $selection["price"], $market_db['id']);
        }
      }
    }
  }
}

$betano = new Betano();
$placard = new Placard();


// register_betano_odds($betano->getTimezone(), $betano->getName(), $betano->getUrl());

function register_placard_odds($timezone, $name, $url) {
  // $data = get_data($url);
  $data = get_local_files();
  if ($data) {
    foreach ($data as $game) {
      $competition = competition_check($game['leagues'] ?? null);
      $home_team = team_check($game['homeTeam'] ?? null);
      $away_team = team_check($game['awayTeam'] ?? null);
      $date_time = new DateTime($game['startDate'] ?? null, new DateTimeZone($timezone));
      $fixture =  fixture_check($home_team, $away_team, $date_time, $competition);
      $market = $game['markets']['1x2'] ?? false;
      if ($market && $fixture) {
        foreach ($market as $key => $selection) {
          $market_type = '1x2' . array_search($key, $game);
          $market_db = market_check($market_type, $name);
          register_fixture_market($fixture['id'], $market_db['id']);
          register_fixture_market_odd($fixture['id'], $selection, $market_db['id']);
        }
      }
    }
  }
}

register_placard_odds($placard->getTimezone(), $placard->getName(), $placard->getUrl());
