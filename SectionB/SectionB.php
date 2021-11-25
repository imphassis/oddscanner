<?php
$URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

class convertCurrencies
{
  public $currencies = [];
  public $rates = [];
  public $base = 'EUR';

  function getXML()
  {
    try {
      global $URL;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $URL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $xml = curl_exec($ch);
      curl_close($ch);
      return simplexml_load_string($xml);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  function getRate($chosenCurrency)
  {
    global $rates;
    foreach ($rates as $r) {
      if ($r->attributes()->currency == $chosenCurrency) {
        return (string) $r->attributes()->rate;
      }
    }
  }

  function convertCurrencies($currency)
  {
    $currencyValue = getRate($currency);
    global $rates;
    $convertedRates = [];
    foreach ($rates as $r) {
      $roundedRate = round($r->attributes()->rate / $currencyValue, 4);
      $rateName = $r->attributes()->currency;

      if ($rateName == $currency) {
        array_push(
          $convertedRates,
          'EUR' . ': ' . round($roundedRate / $currencyValue, 4)
        );
      } else {
        array_push($convertedRates, $rateName . ': ' . $roundedRate);
      }
    }
    return $convertedRates;
  }

  function saveToFile()
  {
    $currencyArray = convertCurrencies('BRL');

    $date = date('Y_m_d');
    $fp = fopen("usd_currency_rates_{$date}", 'w');
    $header = ['Currency_Code', 'Rate'];

    fputcsv($fp, $header);
    foreach ($currencyArray as $currency) {
      fputcsv($fp, explode(':', $currency));
    }
    print "\n Done.";
  }
}

// function getXML()
// {
//   try {
//     global $URL;
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $URL);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     $xml = curl_exec($ch);
//     curl_close($ch);
//     // Put the XML data into a SimpleXML object
//     return simplexml_load_string($xml);
//   } catch (\Throwable $th) {
//     throw $th;
//   }
// }

// $xml = getXML();
// $rates = $xml->Cube->Cube->Cube;

// function getRate($chosenCurrency)
// {
//   global $rates;
//   foreach ($rates as $r) {
//     if ($r->attributes()->currency == $chosenCurrency) {
//       return (string) $r->attributes()->rate;
//     }
//   }
// }

// function convertCurrencies($currency)
// {
//   $currencyValue = getRate($currency);
//   global $rates;
//   $convertedRates = [];
//   foreach ($rates as $r) {
//     $roundedRate = round($r->attributes()->rate / $currencyValue, 4);
//     $rateName = $r->attributes()->currency;

//     if ($rateName == $currency) {
//       array_push(
//         $convertedRates,
//         'EUR' . ': ' . round($roundedRate / $currencyValue, 4)
//       );
//     } else {
//       array_push($convertedRates, $rateName . ': ' . $roundedRate);
//     }
//   }
//   return $convertedRates;
// }

// } else {
//   print 'Error. Check your URL Link';
// }

?>
