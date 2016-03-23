<?php

/*
 * ---------------------------------------------------------------
 * Authentication check script
 * ---------------------------------------------------------------
 *
 * This page is used for verifying that a user is actually logged in.
 * It will check for cookie, set that as active session if session isn't
 * already present. It will also verify a cookie is valid against the
 * user database, to prevent a deleted user from connecting with a 
 * previously valid cookie.
 * 
 * TODO:
 *
 *     -Make this whole page not suck
 *
 * NOTE: This file sets up $user for use later
 * 		 If this file is included, do not set again on the page.
 */

session_start();
require_once 'inc/functions.php';
$user = new user();
$action = null;

if (!empty($_COOKIE['email']) && $user->session_check($_COOKIE['email']) === false) {
	session_destroy();
	setcookie('rememberMe', null, -1, '/');
	setcookie('email', null, -1, '/');
	header("Location: login.php");
}

if (isset($_GET["action"])) {
	$action = $_GET['action'];
}

if (!empty($_SESSION['authenticated']) && $user->session_authenticate($_SESSION['authenticated'], $action) === false) {
	header('Location: login.php');
}

if (empty($_SESSION['authenticated'])) {
	header('Location: login.php');
}

