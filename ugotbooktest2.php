#!/usr/bin/php -q
<?php namespace ugotbook;
include_once('lib/functions.php');

//BEFORE EMAILING we need to double check we're not emailing
//random people.

// Read in configuration file
$config = parse_ini_file('config.ini', $process_sections = true);

// gather pieces of configuration
$endpoint = $config['API']['endpoint'];
$client_key = $config['API']['client_key'];
$client_secret = $config['API']['client_secret'];
$dbhost = $config['DNA']['hostname'];
$dbport = $config['DNA']['port'];
$dbname = $config['DNA']['dbase'];
$dbuser = $config['DNA']['dbuser'];
$dbpass = $config['DNA']['dbpass'];

// create API object for requests
$api = new API($endpoint, $client_key, $client_secret);

// Change the 700 in the last line to 1 for daily orders
// Currently limiting to Phil's request
$query = "select distinct v.field_content, b.title, rm.record_num
    FROM sierra_view.bib_view b
    JOIN sierra_view.bib_record_order_record_link bro ON bro.bib_record_id=b.id
    JOIN sierra_view.order_view o ON o.id=bro.order_record_id
    JOIN sierra_view.varfield v ON v.record_id=o.id AND varfield_type_code='n'
    JOIN sierra_view.record_metadata rm ON rm.id=b.id AND rm.record_type_code='b'
    WHERE
    o.record_creation_date_gmt::date>=NOW()::DATE-EXTRACT(DOW FROM NOW())::INTEGER-700";

$db = new DNA($dbhost, $dbport, $dbname, $dbuser, $dbpass);
$result = $db ->query($query);


foreach($result as $line) {
     if (preg_match_all($config['DNA']['bcode_pattern'],$line['field_content'],$note)) {
        foreach($note as $barcodes){
            //Accounts for multiple barcodes in note field
            foreach ($barcodes as $barcode){
                print "Barcode: " . $barcode . "\n";
                $bib_id = $line['record_num'];
                print "Bib ID: " . $bib_id . "\n";
                $title = $line['title'];
                print "Title: " . $title . "\n\n";
            }
        }
    }
}

