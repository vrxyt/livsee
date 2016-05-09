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
	public $rtmpinfo = array();

	/* 	Streamkey Auth Functions	 */

	// Check if the stream key is valid
	public function stream_check($key, $name, $file) {
		$params = array($name, $key);
		$sql = "SELECT * FROM $this->user_table WHERE display_name = $1 AND stream_key = $2 AND stream_key IS NOT null";
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

	public function onLive($key, $furl) {
		$params = array($key);
		$sql = "SELECT subscribers, display_name FROM $this->user_table WHERE stream_key = $1 AND stream_key IS NOT null";
		$result = pg_fetch_assoc(pg_query_params($this->link, $sql, $params));
		$subscribers = empty($result['subscribers']) ? 'No Subscribers' : $result['subscribers'];
		$name = $result['display_name'];
		$timestamp = new DateTime();
		$timestamp = $timestamp->format('Y-m-d H:i:s');
		$timestamp = $name . " went live: $timestamp\r\n";
		$write = $timestamp . 'Notified: ' . $subscribers . "\r\n";
		file_put_contents("/var/log/nginx/on-live_notice.log", $write, FILE_APPEND | LOCK_EX);
		$subs = explode(',', $subscribers);
		foreach ($subs as $sub) {
			$subject = $GLOBALS['sitetitle'] . ' - ' . $name . ' went live!';
			$message = "$name just started streaming.<br /><br />Watch here: <a href='$furl/watch/$name'>$furl/watch/$name</a>";
			$headers = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-Type: text/html; charset=UTF-8";
			$headers[] = "From: DM Stream <noreply@rirnef.net>";
			$headers[] = "Bcc: DM Stream Admin <fenrirthviti@gmail.com>";
			$headers[] = "Reply-To: issues@rirnef.net";
			$headers[] = 'X-Mailer: PHP/' . phpversion();
			mail($sub, $subject, $message, implode("\r\n", $headers));
		}
	}

	/* Stream stuff */

	// this is all gross, needs fixing.
	public function checkStreams($forceCheck = true) {
		if (!isset($this->rtmpinfo["rtmp"])) {
			$this->rtmpinfo["rtmp"] = array(
				"lastUpdate" => 0,
				"channels" => array()
			);
		}

		if ($forceCheck || time() - $this->rtmpinfo["rtmp"]["lastUpdate"] > 5) {
			$this->fetchChannels();
		}
		return $this->rtmpinfo;
	}

	private function fetchChannels() {
		$this->rtmpinfo["rtmp"]["lastUpdate"] = time();
		$this->rtmpinfo["rtmp"]["channels"] = array();
		$rtmp = json_decode(json_encode((array) simplexml_load_file($GLOBALS['furl'] . '/stat.xml')), TRUE);

		if (!empty($rtmp["server"]["application"][1]["live"]["stream"])) {
			if (array_key_exists("name", $rtmp["server"]["application"][1]["live"]["stream"])) {
				$channel = $rtmp["server"]["application"][1]["live"]["stream"];

				if (empty($channel["name"])) {
					$channel["name"] = "default";
				}
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]] = $channel;
				$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
			} else {
				foreach ($rtmp["server"]["application"][1]["live"]["stream"] as $key => $channel) {
					if (empty($channel["name"])) {
						$channel["name"] = "default";
					}
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]] = $channel;
					$this->rtmpinfo["rtmp"]["channels"][$channel["name"]]["recording"] = rtmp::isRecordingChannel($channel["name"]);
				}
			}
		}
		return $this->rtmpinfo;
	}

	private static function isRecordingChannel($channelName) {
		return (count(glob("/var/tmp/rec/" . $channelName . "*.flv")) > 0);
	}

}
