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

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if (empty($_SESSION['authenticated']) && !empty($_COOKIE['rememberMe']) && !empty($_COOKIE['email'])) {
    $_SESSION['authenticated'] = $_COOKIE['email'];
    header('Location: index.php');
}

if (!empty($_POST['email'])) {
	$user = new user();
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$status = $user->login($email, $password);
	if ($status === true) { header('Location: index.php'); }
}
?>
<html>
    <head>
	<meta charset="utf-8">
	<title>DM Stream Login</title>
	<link rel="stylesheet" href="css/regform.css">
    </head>
    <body>
	<h1 class="register-title">Login</h1>
	<form action="" method="POST" class="register">
	    <input type="email" name="email" class="register-input" placeholder="Email address">
	    <input type="password" name="password" class="register-input" placeholder="Password">
	    <input type="submit" name="submitted" value="Login" class="register-button">
	    <div class="register-link">
		<span class="register-link-left"><input type="checkbox" name="rememberMe" value="true">Remember Login</span>
		<span class="register-link-right"><a href="register.php">Register</a></span>
	    </div>
	    <div class="clear"></div>
	    <?php
	    if (!empty($status)) {
		echo '<br />' . $status;
	    }
	    if ($action == 'account_created') {
		echo 'Account created.<br />Please check your email for verification.<br /><br />Note: Email may be in spam.';
	    }
	    ?>
	</form>
    </body>
</html>