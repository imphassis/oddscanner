<?php

class convertCurrencies
{
  public $currencies = [];
  public $convertedRates = [];
  public $exchangeRate = ''; // Rate proportion between EUR and Selected Currency
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
      print_r("Initializing XML Parse...\n");
      $xml = curl_exec($ch);
      curl_close($ch);
      $xml = simplexml_load_string($xml);
      if ($xml === false) {
        throw new Exception('Error parsing XML');
      }
      // Saving all the currencies with their exchange rates in an array
      $this->currencies = $xml->Cube->Cube->Cube;
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
    }
  }

  private function findCoinRate()
  {
    foreach ($this->currencies as $c) {
      if ($c->attributes()->currency == $this->coin) {
        // Now, we have the rate of the chosen currency based on EUR.
        $this->exchangeRate = (string) $c->attributes()->rate;
      }
    }
  }

  private function convertCurrencies()
  {
    foreach ($this->currencies as $c) {
      // Divide the exchange value by the rate of the chosen currency
      $rate = round($c->attributes()->rate / $this->exchangeRate, 4);
      $rateName = $c->attributes()->currency;

      if ($rateName == $this->coin) {
        array_push(
          $this->convertedRates,
          'EUR' . ': ' . round($rate / $this->exchangeRate, 4)
        );
      } else {
        array_push($this->convertedRates, $rateName . ': ' . $rate);
      }
    }
  }

  private function saveToFile($array)
  {
    $date = date('Y_m_d');
    $fp = fopen("{$this->coin}_currency_rates_{$date}", 'w');
    $header = ['Currency_Code', 'Rate'];
    fputcsv($fp, $header);
    foreach ($array as $currency) {
      fputcsv($fp, explode(':', $currency));
    }
  }

  function main()
  {
    $this->getXML();
    $currenciesLength = count($this->currencies);
    if ($currenciesLength > 0) {
      $this->findCoinRate();
      $this->convertCurrencies();
      $this->saveToFile($this->convertedRates);
      print_r(
        "All currencies were converted to {$this->coin} base sucessfully."
      );
    } else {
      print 'Error: No data found.';
    }
  }
}

$convert = new convertCurrencies('BRL');
$convert->main();

?>
