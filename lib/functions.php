<?php namespace ugotbook;
include_once 'sierra-api-client/Sierra.php'; use Sierra;

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
  
  // fetch a patron, given a barcode
  public function fetchPatron($bcode) {
    return $this->connection->query('patrons/find?barcode=' . $bcode . '&fields=id%2Cnames%2Cemails%2CbirthDate%2ChomeLibraryCode', array(), false);
  }
  
  // request a hold on behalf of a patron
  public function requestHold($patron, $bib_id) {
    
    // create an array to be encoded to json for POST body
    $hold_object = Array(
      'recordType' => 'b',
      'recordNumber' => $bib_id,
      'pickupLocation' => $patron['homeLibraryCode']
    );
    
    // create an array for request parameters
    $params = Array(
      'type' => 'post'
    );
    
    return $this->connection->query('patrons/' . $patron['id'] . '/holds/requests' , $params);
  }
  
}

/*
 * DNA class -- a class for Sierra DNA related call functions
 * */
class DNA {
      public function __construct($dbhost, $dbport, $dbname, $dbuser, $dbpass) {
              $dsn = 'pgsql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $dbname;
                  
                  $this->connection = new PDO($dsn, $dbuser, $dbpass);
                }
        
        public function query($stmt) {
                return $this->connection->query($stmt)->fetchAll();
                  }
}
