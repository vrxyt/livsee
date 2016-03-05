<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
session_start();
if (empty($_SESSION['authenticated']) && $_COOKIE['rememberMe'] === '1' && !empty($_COOKIE['email'])) {
    $_SESSION['authenticated'] = $_COOKIE['email'];
    header('Location: index.php');
}
require('/var/www/html/lib/dbconnect.php');
if (isset($_GET["action"])) {
    $action = $_GET['action'];
    if ($action == 'account_created') {
	$newuser = true;
    }
}
if (!empty($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $pass = $_POST['password'];
    $pwcheck = pg_fetch_assoc(pg_query($pglink, "SELECT * FROM users WHERE email = '$email'"));
    $pwhash = $pwcheck['password'];
    $verifycheck = $pwcheck['verified'];
    //echo '<br />email: ' . $email . '<br />pw:' . $pass . '<br />Hash: ' . $pwhash . 'verify: ' . $verifycheck;
    if ($verifycheck === '1') {
	//echo '<br />email: ' . $email . '<br />pw:' . $pass . '<br />Hash: ' . $pwhash . 'verify: ' . $verifycheck;
	if (password_verify($pass, $pwhash)) {
	    $status = 'Login successful.';
	    $_SESSION['authenticated'] = $email;
	    if ($_POST['rememberMe'] === 'true') {
		setcookie('rememberMe', true, time() + 31000000);
		setcookie('email', $email, time() + 31000000);
	    }
	    header('Location: index.php');
	} else {
	    $status = 'Login failed.';
	}
    } else {
	$status = 'Account not verified/account doesn\'t exist.';
    }
} else {
    //$status = 'No action.';
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
	    if (!empty($newuser)) {
		echo 'Account created.<br />Please check your email for verification.<br />Note: Email may be in spam.';
	    }
	    ?>
	</form>
    </body>
</html>