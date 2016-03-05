<?php

$pgcon = "host= port= dbname= user= password=";
$pglink = pg_connect($pgcon);

if (!$pglink) {
	$pgerror = pg_connection_status($pglink);
  echo "An error occurred.\n";
  echo $pgerror;
  exit;
}