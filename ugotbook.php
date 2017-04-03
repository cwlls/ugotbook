#!/usr/bin/php -q
<?php namespace ugotbook; 
include_once('lib/functions.php');

// Read in configuration file
$config = parse_ini_file('config.ini', $process_sections = true);

// gather pieces of configuration
$endpoint = $config['API']['endpoint'];
$client_key = $config['API']['client_key'];
$client_secret = $config['API']['client_secret'];

// request patron using barcode
$api = new API($endpoint, $client_key, $client_secret);
$patron = $api->fetchPatron('1234567890123');
print_r($patron);

?>