<?php

class subscription extends master {

	public $db;

	function __construct($key, $params) {
		parent::__construct($key, $params);

		// Open database connection
		$this->db = new database();
	}

	public function add() {
		$host = filter_var($this->params[0], FILTER_SANITIZE_STRING);
		$key = $this->key;
		$params = array($host, $key);
		$sql = "INSERT INTO " . $this->db->sub_table . " (host_account, subscriber) VALUES ((SELECT email FROM " . $this->db->user_table . " WHERE display_name = $1 LIMIT 1), (SELECT email FROM " . $this->db->user_table . " WHERE api_key = $2 LIMIT 1))";
		return @!pg_query_params($this->db->link, $sql, $params) ? false : true;
	}

	public function remove() {
		$host = filter_var($this->params[0], FILTER_SANITIZE_STRING);
		$key = $this->key;
		$params = array($host, $key);
		$sql = "DELETE FROM " . $this->db->sub_table . " WHERE host_account = (SELECT email FROM " . $this->db->user_table . " WHERE display_name = $1 LIMIT 1) AND subscriber = (SELECT email FROM " . $this->db->user_table . " WHERE api_key = $2 LIMIT 1)";
		return @!pg_query_params($this->db->link, $sql, $params) ? false : true;
	}

	public function _list() {
		//$host = filter_var($this->params[0], FILTER_SANITIZE_STRING);
		// assign user based on api key
		if (empty($this->params[0])) {
			$json = [
				'error' => 'No channel specified'
			];
			return $json;
		}
		$account = filter_var($this->params[0], FILTER_SANITIZE_STRING);
		$params = array($account);
		$sql = "SELECT * FROM " . $this->db->sub_table . " WHERE subscriber = $1 OR host_account = $1";
		$result = pg_query_params($this->db->link, $sql, $params);
		$subscribed = [];
		$subscribers = [];
		while ($row = pg_fetch_assoc($result)) {
			if ($row['host_account'] === $account) {
				$subscribers[] = $row['subscriber'];
			} 
			if ($row['subscriber'] === $account) {
				$subscribed[] = $row['host_account'];
			}
		}
		$json = [
			'subscribed' => $subscribed,
			'subscribers' => $subscribers
		];
		return json_encode($json);
	}

}
