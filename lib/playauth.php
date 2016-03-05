<?php
require ('/var/www/html/lib/dbconnect.php');

$file = '/var/log/nginx/streamauth.log';
$timestamp = new DateTime();
$timestamp = $timestamp->format('Y-m-d H:i:s');
$timestamp = "Playback Attempt: $timestamp\r\n";
$getstring = $timestamp;
$getstring .= print_r($_GET, true);
file_put_contents($file, $getstring, FILE_APPEND | LOCK_EX);
if (isset($_GET["name"])) {
    $channel = $_GET["name"];
    $result = pg_query($pglink, "SELECT * FROM users WHERE stream_key = '$channel'");
    $row_cnt = pg_num_rows($result);
    if ($row_cnt >= 1) {
	$check = pg_fetch_assoc(pg_query($pglink, "SELECT * FROM users WHERE stream_key = '$channel'"));
	$channel_name = $check['channel_name'];
	$channel_title = $check['channel_title'];
	$add = "name: $channel_name title: $channel_title \r\n";
	file_put_contents($file, $add, FILE_APPEND | LOCK_EX);
    } else {
	$add = "$channel present, no match\r\n";
	file_put_contents($file, $add, FILE_APPEND | LOCK_EX);
    }
} else {
    $add ="Nothing found!\r\n";
file_put_contents($file, $add, FILE_APPEND | LOCK_EX);
}
