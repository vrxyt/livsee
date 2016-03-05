<?php

function updateStreamkey($input, $function) {
	require('/var/www/html/lib/dbconnect.php');
	if ($function === 'channel') {
		$kuresults = pg_fetch_assoc(pg_query($pglink, "SELECT channel_name FROM users WHERE stream_key = '$input'"));
		$kuchannelname = $kuresults['channel_name'];
		return $kuchannelname;
	} elseif ($function === 'title') {
		$kuresults = pg_fetch_assoc(pg_query($pglink, "SELECT channel_title FROM users WHERE stream_key = '$input'"));
		$kuchanneltitle = $kuresults['channel_title'];
		return $kuchanneltitle;
	} else {
		return 'Error in updateStreamkey()!';
	}
}
