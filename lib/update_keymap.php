<?php
// failed experiment to attempt to hide the stream keys. Open to ideas on how to properly make them private.

function updateKeymap() {
    require '/var/www/html/lib/dbconnect.php';
    $file = '/var/local/map/keymap.map';
    $timestamp = new DateTime();
    $timestamp = $timestamp->format('Y-m-d H:i:s');
    $timestamp = "#Keymap Written: $timestamp\r\n";
    $output = $timestamp;
    $results = pg_query($pglink, "SELECT channel_name, stream_key FROM users");
    foreach ($results as $result) {
	$streamkey = $result['stream_key'];
	$channelname = $result['channel_name'];
	$key = "$streamkey \"$channelname\";\r\n";
	$output .= $key;
    }
    file_put_contents($file, $output, LOCK_EX);
}
