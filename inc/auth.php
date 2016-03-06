<?php

// Include this file on pages you want hidden behind the auth system

session_start();
require_once 'inc/functions.php';
$user = new user();
$auth = new auth();
$action = null;
if (!empty($_COOKIE['email'])) { $auth->cookiecheck($_COOKIE['email']); }
if (isset($_GET["action"])) { $action = $_GET['action']; }

if (empty($_SESSION['authenticated']) && $_COOKIE['rememberMe'] === true && !empty($_COOKIE['email'])) {
	$_SESSION['authenticated'] = $_COOKIE['email'];
}

if (!empty($_SESSION['authenticated'])) {
	$email = $_SESSION['authenticated'];
	$check_auth = $auth->session_authenticate($email, $action);
}
else { header('Location: login.php'); }


