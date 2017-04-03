<?php namespace ugotbook;
include_once 'Sierra.php'; use Sierra;

/*
* API class -- a class for API call functions
*/
class API {
  // API class constructor
  public function __construct($endpoint, $key, $secret) {
    $this->connection = new Sierra(array(
      'endpoint' => $endpoint,
      'key' => $key,
      'secret' => $secret
    ));
  }
  
  public function fetchPatron($bcode) {
    return $this->connection->query('patrons/find?barcode=' . $bcode . '&fields=id%2Cnames%2Cemails%2CbirthDate%2ChomeLibraryCode', array(), false);
  }
  
}

/*
* DNA class -- a class for Sierra DNA related call functions
*/
class DNA {
  public function __construct() {
    
  }
}
