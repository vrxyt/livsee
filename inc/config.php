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

// grabs the urls for dynamic use elsewhere in the page. Saves time having to change dozens of hardcoded links.
// $furl (full URL) will be http(s)://sub.domain.com where $surl (short URL) will just be sub.domain.com
$protocol = $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$surl = $_SERVER['HTTP_HOST'];
$furl = $protocol . $surl;

// sets the stream auth log file location. Can be changed to anything you want.
$SAlogfile = '/var/log/nginx/streamauth.log';

// title at the top of the page.
$sitetitle = "DM Stream"; // max 16 characters
$sitesubtitle = "(A Dancing Mad Production)"; // currently unused.


