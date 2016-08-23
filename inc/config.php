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

// sets the site log file location. Can be changed to anything you want.
$logfile = '/var/log/rachni/';
$site_recpath = '/var/tmp/rec/'; // path to saved recordings

// enable/disable registration. TODO - Move this to the database
$reg_open = false;

// title at the top of the page.
$sitetitle = "DM Stream"; // max 16 characters

// Email settings
$sitesubtitle = "Len's Streaming Site"; // currently only used for email template
$admin_account = 'fenrirthviti@gmail.com'; // this is a terrible way to do this, will fix.

// Sets the from/reply email address for notification emails. Edit as needed, following the proper "DisplayName <email@address.com>" syntax. Display name is optional
$from_email = $sitetitle . ' <noreply@rirnef.net>';
$reply_email = 'issues@rirnef.net';
$bcc_email = $sitetitle . ' Admin <' . $admin_account .'>';