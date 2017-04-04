<?php namespace ugotbook;
include_once 'sierra-api-client/Sierra.php'; use Sierra;

require '...\PHPMailerAutoload.php';

use PDO;

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
      'recordNumber' => (int)$bib_id,
      'pickupLocation' => $patron['homeLibraryCode']
    );
    
    // create an array for request parameters
    $params = Array();
    $json_body = json_encode($hold_object);
    
    return $this->connection->query('patrons/' . $patron['id'] . '/holds/requests' , $params, false, 'post', $json_body);
  }
 
   // Send e-mail notification to patron
   function emailPatron($patron, $title) {

		$to = $patron['emails'][0];
		$homelibrary = $patron['homeLibraryCode'];
  
  
		if (preg_match("/.*@.*\..*/", $email) > 0) {

			//SMTP needs accurate times, and the PHP time zone MUST be set
			//This should be done in your php.ini, but this is how to do it if you don't have access to that
			date_default_timezone_set('Etc/UTC');

			//Create a new PHPMailer instance
			$mail = new PHPMailer;
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 2;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host = 'smtp.gmail.com';
			// use
			// $mail->Host = gethostbyname('smtp.gmail.com');
			// if your network does not support SMTP over IPv6
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port = 587;
			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = 'tls';
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = "catmanlibrarian@gmail.com";
			//Password to use for SMTP authentication
			$mail->Password = "testing123";
			//Set who the message is to be sent from
			$mail->setFrom('catmanlibrarian@gmail.com', 'CATMAN LIBRARIAN');
			//Set an alternative reply-to address
			$mail->addReplyTo('catmanlibrarian@gmail.com', 'CATMAN LIBRARIAN');
			//Set who the message is to be sent to
			$mail->addAddress($to, '');
			//Set the subject line
			$mail->Subject = 'You got Book!';
			 $mail->Body = 'A book you requested "' . $title . '" has arrived and a hold has been placed on your behalf';

		if (!$mail->send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
			echo "Message sent!";
		}
			 
	  
    } else {
		echo "No email found in patron record, hold was placed anyway";
	}
  
}
  
  public function fetchHolds($patron) {
    return $this->connection->query('patrons/' . $patron['id'] . '/holds');
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
