<?php

session_start();
require('/var/www/html//lib/dbconnect.php');
$auth_email = filter_var($_COOKIE['email'], FILTER_VALIDATE_EMAIL);
$auth_result = pg_query($pglink, "SELECT * FROM users WHERE email = '$auth_email'");
$auth_row_cnt = pg_num_rows($auth_result);
//echo $auth_row_cnt;
//if ($auth_row_cnt != 1) {
//    session_destroy();
//    setcookie('rememberMe', null, -1, '/');
//    setcookie('email', null, -1, '/');
//    header('Location: login.php');
//    echo 'destroyed';
//} else {
	//echo 'not destroyed';
//}
if (empty($_SESSION['authenticated']) && $_COOKIE['rememberMe'] === true && !empty($_COOKIE['email'])) {
    $_SESSION['authenticated'] = $_COOKIE['email'];
}
if (!empty($_SESSION['authenticated'])) {
    //$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);
    //$check = pg_fetch_assoc(pg_query($pglink, "SELECT * FROM users WHERE email = '$email'"));
    //$channel_name = $check['channel_name'];
    //$channel_title = $check['channel_title'];
    if (isset($_GET["action"])) {
	$action = $_GET['action'];
	if ($action == 'logout') {
	    session_destroy();
	    setcookie('rememberMe', null, -1, '/');
	    setcookie('email', null, -1, '/');
	    header('Location: login.php');
	}
	if ($action == 'already_logged_in') {
	    $status = 'Already logged in!';
	}
    }
} else {
    header('Location: login.php');
}


