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
 *     -Fix stream functions, they suck
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
	/* Stream stuff */
	
	// this is all gross, needs fixing.
	public static function checkStreams($forceCheck = true) {
		if (!isset($_SESSION["rtmp"])) {
			$_SESSION["rtmp"] = array(
				"lastUpdate" => 0,
				"channels" => array()
			);
		}

		if ($forceCheck || time() - $_SESSION["rtmp"]["lastUpdate"] > 5) {
			rtmp::fetchChannels();
		}
	}

	private static function fetchChannels() {
		$_SESSION["rtmp"]["lastUpdate"] = time();
		$_SESSION["rtmp"]["channels"] = array();
		$rtmp = json_decode(json_encode((array) simplexml_load_file($GLOBALS['furl'].'/stat.xml')), TRUE);

		if (!empty($rtmp["server"]["application"][1]["live"]["stream"])) {
			if (array_key_exists("name", $rtmp["server"]["application"][1]["live"]["stream"])) {
				$channel = $rtmp["server"]["application"][1]["live"]["stream"];

				if (empty($channel["name"])) {
					$channel["name"] = "default";
				}
				$_SESSION["rtmp"]["channels"][$channel["name"]] = $channel;
				$_SESSION["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
			} else {
				foreach ($rtmp["server"]["application"][1]["live"]["stream"] as $key => $channel) {
					if (empty($channel["name"])) {
						$channel["name"] = "default";
					}
					$_SESSION["rtmp"]["channels"][$channel["name"]] = $channel;
					$_SESSION["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
				}
			}
		}
	}

	private static function isRecordingChannel($channelName) {
		return (count(glob("/var/tmp/rec/" . $channelName . "*.flv")) > 0);
	}

}
