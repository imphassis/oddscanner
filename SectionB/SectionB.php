<?php

class convertCurrencies
{
  public $currencies = [];
  public $convertedRates = [];
  public $currencyRate = ''; // Rate based on chosen currency
  public $URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

  function __construct($COIN)
  {
    $this->coin = $COIN;
  }

  private function getXML()
  {
    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->URL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $xml = curl_exec($ch);
      curl_close($ch);
      $xml = simplexml_load_string($xml);
      $this->currencies = $xml->Cube->Cube->Cube;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function findCoinRate()
  {
    foreach ($this->currencies as $c) {
      if ($c->attributes()->currency == $this->coin) {
        $this->currencyRate = (string) $c->attributes()->rate;
      }
    }
  }

  private function convertCurrencies()
  {
    foreach ($this->currencies as $c) {
      $rate = round($c->attributes()->rate / $this->currencyRate, 4);
      $rateName = $c->attributes()->currency;

      if ($rateName == $this->coin) {
        array_push(
          $this->convertedRates,
          'EUR' . ': ' . round($rate / $this->currencyRate, 4)
        );
      } else {
        array_push($this->convertedRates, $rateName . ': ' . $rate);
      }
    }
  }

  private function saveToFile()
  {
    $date = date('Y_m_d');
    $fp = fopen("{$this->coin}_currency_rates_{$date}", 'w');
    $header = ['Currency_Code', 'Rate'];
    fputcsv($fp, $header);
    foreach ($this->convertedRates as $currency) {
      fputcsv($fp, explode(':', $currency));
    }
  }

  function getData()
  {
    $this->getXML();
    $this->findCoinRate();
    $this->convertCurrencies();
    $this->saveToFile();
    print "\n Done.";
  }
}

$convert = new convertCurrencies('BRL');
$convert->getData();

?>
