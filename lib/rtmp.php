<?php

/*
 * ---------------------------------------------------------------
 * RTMP functions
 * ---------------------------------------------------------------
 *
 * This page contains the functions for showing, updating, checking,
 * changing, etc. the RTMP streams themselves. Currently many of
 * these functions live in the MediaInfo.class.php and RTMP.class.php,
 * but they will eventually all be rewritten and moved here.
 * 
 * TODO:
 *
 *     -Move all MediaInfo/RTMP.class.php functions here
 *     -General code clean up
 *
 */

class rtmp extends database {

	public $user_table = 'users';
	
	/* 	Streamkey Auth Functions	 */

	// Check if the stream key is valid
	public function stream_check($key, $file) {
		$params = array($key);
		$sql = "SELECT * FROM $this->user_table WHERE stream_key = $1";
		$query = pg_query_params($this->link, $sql, $params);
		$row_cnt = pg_num_rows($query);
		if ($row_cnt >= 1) {
			$current = "Result: Valid stream key!\r\n";
			file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
			return true;
		} else {
			$current = "Result: Invalid stream key!\r\n";
			file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
			return false;
		}
	}

}
