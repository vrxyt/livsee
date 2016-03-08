<?php

/*
 *---------------------------------------------------------------
 * Global site configuaration
 *---------------------------------------------------------------
 *
 * This page contains global site config variables. Right now
 * it just has the debug functions var amd site URL strings, but 
 * I will likely update this later to include more things as I 
 * find uses for it. This should be included on any user-facing pages.
 * 
 * TODO:
 *
 *     -Think of ways to better use this
 *
 */

$debug = true; // setting to true will enable debug output in various places. Live sites should set to false.
$protocol = $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$surl = $_SERVER['HTTP_HOST'];
$furl = $protocol . $surl;


