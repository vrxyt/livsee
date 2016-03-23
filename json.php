<?php

session_start();

require_once 'inc/config.php';
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

// Check stream information
rtmp::checkStreams();

// Prepare response Data
$json = array(
	"data" => array(),
	"options" => 0
);

// Compute input params
$_OPTIONS = array_merge($_GET, $_POST);

// Execute correct action
switch ($_GET["action"]) {
	case "ping":
		$json["data"]["live"] = array_key_exists($_GET["channel"], $_SESSION["rtmp"]["channels"]) && array_key_exists("publishing", $_SESSION["rtmp"]["channels"][$_GET["channel"]]);
		break;
	case "record":
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if (isset($_GET["start"])) {
			curl_setopt($ch, CURLOPT_URL, "https://localhost/control/record/start?app=live&name=" .$_GET["channel"]. "&rec=rec");
		} else if (isset($_GET["stop"])) {
			curl_setopt($ch, CURLOPT_URL, "https://localhost/control/record/stop?app=live&name=" .$_GET["channel"]. "&rec=rec");	
		}
		$json["data"]["file"] = curl_exec($ch);
		curl_close($ch);
		break;
	default:
		$json["data"]["channels"] = $_SESSION["rtmp"]["channels"];
		break;
}

// Prettify on demand, and force object mode
if (isset($_GET["pretty"])) {
	$json["options"] = $json["options"] | JSON_PRETTY_PRINT;
}
$json["options"] = $json["options"] | JSON_FORCE_OBJECT;

// Output response as JSON, without any kind of cache allowed
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Content-type: text/json');
print json_encode($json["data"], $json["options"]);
?>