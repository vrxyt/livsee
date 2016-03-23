<?php

// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true){error_reporting(E_ALL);ini_set('display_errors', 1);}

// start the session, in case we're already logged in (currently broke, future enhancements)
session_start();

//includes
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

// check if info was posted
if (!empty($_POST['submitted'])) {
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$displayname = filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_STRING);
	
	// verify all required info is present before executing register
	if (empty($email) || empty($password) || empty($displayname)) {
		$status = 'Please enter a valid email address, display name, and a password.';
	} else {
		$user = new user();
		$status = $user->register($email, $password, $displayname, $furl);
		if ($status === true) {	header('Location: login.php?action=account_created'); }
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
		<meta charset="utf-8">
		<title>DM Stream Registration</title>
		<link rel="stylesheet" href="css/regform.css">
    </head>
    <body>
		<h1 class="register-title">Register</h1>
		<form action="" method="POST" class="register">
			<input type="email" name="email" class="register-input" placeholder="Email Address">
			<input type="text" name="displayname" class="register-input" placeholder="Display Name">
			<input type="password" name="password" class="register-input" placeholder="Password">
			<input type="submit" name="submitted" value="Create Account" class="register-button">
			<div class="register-link"><a class="register-link-right" href="login.php">Login</a></div>
			<?php
			if (!empty($status)) {
				echo '<br />' . $status;
			}
			?>
		</form>
    </body>
</html>