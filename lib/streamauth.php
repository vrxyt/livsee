<?php

/*
 * ---------------------------------------------------------------
 * Stream authentication
 * ---------------------------------------------------------------
 *
 * This file must be specified in the nginx RTMP block using the
 * on_publish directive. Example:
 *
 *              # Live Stream Application
 *              application live {
 *                      live on;
 *                      on_publish http://path/to/streamauth.php;
 *              }
 *
 * Publish URL should be rtmp://yourserver/live/$displayname?key=$streamkey
 *
 * In OBS:
 *              - URL: rtmp://yourserver/live
 *              - Stream key: $DisplayName?key=$StreamKey
 *              (Where $DisplayName and $StreamKey are the appropriate values for your account)
 *
 */

require_once '../inc/config.php';
function __autoload($class) {
        include '../lib/' . $class . '.class.php';
}

$rtmp = new rtmp();
$key = filter_input(INPUT_GET, 'key', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

$timestamp = new DateTime();
$timestamp = $timestamp->format('Y-m-d H:i:s');
$timestamp = "Connection Attempt: $timestamp\r\n";

$getstring = $timestamp;
$getstring .= print_r($_GET, true);

// write connection attempt to log, regardless of success
file_put_contents($SAlogfile, $getstring, FILE_APPEND | LOCK_EX);

//check if querystrings exist or not
if (empty($key)) {
        //no querystrings or wrong syntax
        $current = "ERROR: Invalid query input.\n";
        file_put_contents($SAlogfile, $current, FILE_APPEND | LOCK_EX);
        echo "wrong query input";
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        exit(1);
} else {
        //check and verify key against the DB
        $check = $rtmp->stream_check($key, $SAlogfile);
        if ($check === false) {
                header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        }
}