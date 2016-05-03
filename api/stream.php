<?php

class stream extends master {
	public $rtmp;
	public $rtmpinfo;
	public $channel;
	public $json;
	
	function __construct($params) {
		parent::__construct($params);
		
		// Check stream information
		$GLOBALS['furl'] = $this->furl;
		$this->rtmp = new rtmp();
		$this->rtmpinfo = $this->rtmp->checkStreams();

		// Prepare response Data
		$this->json = array(
			"data" => array(),
			"options" => 0
		);

		// Compute input params
		$_OPTIONS = array_merge($_GET, $_POST);
	}
	
	public function info() {
		return $this->rtmpinfo;
	}
	
	public function ping() {
		$this->channel = $this->params[0];
		$this->json["data"]["live"] = array_key_exists($this->channel, $this->rtmpinfo["rtmp"]["channels"]) && array_key_exists("publishing", $this->rtmpinfo["rtmp"]["channels"][$this->channel]);
		$this->json["data"]["recording"] =  array_key_exists($this->channel, $this->rtmpinfo["rtmp"]["channels"]) && array_key_exists("recording", $this->rtmpinfo["rtmp"]["channels"][$this->channel]);
		return $this->json;
	}
	
	public function record() {
		$action = strtolower($this->params[0]);
		$channel = $this->params[1];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		switch ($action) {
			case 'start':
				curl_setopt($ch, CURLOPT_URL, "$this->furl/control/record/start?app=live&name=" . $channel . "&rec=rec");
				break;
			case 'stop':
				curl_setopt($ch, CURLOPT_URL, "$this->furl/control/record/stop?app=live&name=" . $channel . "&rec=rec");	
				break;
		}
		$json = curl_exec($ch);
		curl_close($ch);
		return $json;
	}
}
