<?php
/*    ******************   *
 *    /lib/database.php    *
 *    ******************   */

class database {

// Connection params
	private $host = '';
	private $port = '';
	private $user = '';
	private $password = '';
	private $dbname = '';
// This can be accessed by the database class and any class that extends database by using $this->link
	public $link;

	public function __construct() {

// Open database connection
		$this->link = pg_connect("host=$this->host port=$this->port dbname=$this->dbname user=$this->user password=$this->password");
		if (!$this->link) {
			$message = "A connection error occured.";
			$code = pg_connection_status($this->link);
			throw new Exception($message, $code);
		}
	}

	public function __destruct() {
// Close database connection
		pg_close($this->link);
	}

}