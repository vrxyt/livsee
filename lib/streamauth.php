<?php

$file = '/var/log/nginx/streamauth.log';
$timestamp = new DateTime();
$timestamp = $timestamp->format('Y-m-d H:i:s');
$timestamp = "Connection Attempt: $timestamp\r\n";
$getstring = $timestamp;
$getstring .= print_r($_GET, true);
file_put_contents($file, $getstring, FILE_APPEND | LOCK_EX);
echo var_dump($_GET);
//check if querystrings exist or not
if (empty($_GET['name'])) {
	//no querystrings or wrong syntax
	$current = "wrong query input\n";
	file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
	echo "wrong query input";
	header('HTTP/1.0 404 Not Found');
	exit(1);
} else {
	//check and verify key against the DB
	require('/var/www/html/lib/dbconnect.php');
	$key = $_GET['name'];
	$result = pg_query($pglink, "SELECT * FROM users WHERE stream_key = '$key'");
	$row_cnt = pg_num_rows($result);
	if ($row_cnt >= 1) {
		$current = "Result: Valid stream key!\r\n";
		file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
		echo "Valid stream key!";
		return $_GET;
	} else {
		$current = "Result: Invalid stream key!\r\n";
		file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
		echo "Invalid stream key!";
		header('HTTP/1.0 404 Not Found');
	}
}