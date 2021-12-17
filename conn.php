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
