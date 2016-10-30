<?php

class chat extends master {

    public $db;

    function __construct($key, $params)
    {
        parent::__construct($key, $params);

        // Open database connection
        $this->db = new database();
    }

    /* 	Chat Functions	 */

    // Grab the database of chat lines
    public function read()
    {
        $channel = $this->params[0];
        $params = array($channel);
        $sql = "SELECT * FROM ". $this->db->chat_table . " WHERE channel_email = (SELECT email FROM " . $this->db->user_table . " WHERE display_name = $1 LIMIT 1) ORDER BY id DESC LIMIT 60";
        $result = pg_query_params($this->db->link, $sql, $params);
        if ($result === false) {
            $message = 'Error in: class:chat | function:getChatLines';
            $code = 1;
            throw new Exception($message, $code);
        }
        $array = [];
        while ($row = pg_fetch_assoc($result)) {
            $array[] = $row;
        }
        return array_reverse($array);
    }

    public function write($channel = null, $timestamp = null, $user = null, $message = null, $type = null) {
        $channel = $channel === null ? filter_input(INPUT_POST, 'channel', FILTER_SANITIZE_STRING) : $channel;
        $timestamp = $timestamp === null ? filter_input(INPUT_POST, 'timestamp', FILTER_SANITIZE_NUMBER_INT) : $timestamp;
        $user = $user === null ? filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING) : $user;
        $message = $message === null ? filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING) : $message;
        $params =  array($timestamp, $user, $channel, $message, $type);
        if ($message != null) {
            $sql = "INSERT INTO " . $this->db->chat_table . " (timestamp, channel_email, sender, message, type) VALUES ($1, (SELECT email FROM " . $this->db->user_table . " WHERE display_name = $3 LIMIT 1), $2, $4, $5)";
            $result = pg_query_params($this->db->link, $sql, $params);
            if ($result === false) {
                $message = 'Error in: class:chat | function:writeChatLine';
                $code = 1;
                throw new Exception($message, $code);
            }
            return true;
        }
        return false;
    }
	
	public function join() {
		$channel = $this->params[0];
		$sql = "SELECT display_name FROM " . $this->db->user_table . " WHERE api_key = $1 LIMIT 1";
		$params = [$this->key];
		$result = pg_query_params($this->db->link, $sql, $params);
		$array = [];
        while ($row = pg_fetch_assoc($result)) {
            $array[] = $row;
        }
		$user = $array[0]['display_name'];
		$this->write($channel, time(), $user, 'has joined the channel.', 'SYSTEM');
        return true;
	}
	
	public function leave() {
		$channel = $this->params[0];
		$sql = "SELECT display_name FROM " . $this->db->user_table . " WHERE api_key = $1 LIMIT 1";
		$params = [$this->key];
		$result = pg_query_params($this->db->link, $sql, $params);
		$array = [];
        while ($row = pg_fetch_assoc($result)) {
            $array[] = $row;
        }
		$user = $array[0]['display_name'];
		$this->write($channel, time(), $user, 'has left the channel.', 'SYSTEM');
        return true;
	}
}