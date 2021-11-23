<?php
$URL = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

//  Get the XML File using cURL
$ch = curl_init($URL);

// Set the options for the cURL transfer
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $URL);  

// Instead of returning the result, we will save it in a variable
$data = curl_exec($ch);

// Close the cURL session
curl_close($ch);

// Put the XML data into a SimpleXML object
$xml = simplexml_load_string($data);

// Get the time of the last update, whom is the unique attribute of the second cube element
$date = $xml->Cube->Cube->attributes()->time;

// Get the USD rate, which is the "rate" attribute of the third cube element 
// (which is in position 0 of the parent array)
$rates = $xml->Cube->Cube->Cube[0]->attributes()->rate; 
$exchange = [$date, $rates];

// Opening a new file to write the data
$fp = fopen("usd_currency_rates_{$date}", 'w');


// Setting the header as an array
$header = ["Currency_Code", "Rate"];

// Writing the header and the data to the file
fputcsv($fp, $header);
fputcsv($fp, $exchange);

print "Done."
?>