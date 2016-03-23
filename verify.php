<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// start the session, in case we're already logged in (currently broke, future enhancements)
session_start();

//includes
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

// check if the URL has both required pieces of information
if (!empty($_GET['email']) && !empty($_GET['c'])) {
	$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
	$code = filter_input(INPUT_GET, 'c', FILTER_VALIDATE_INT);
	$user = new user();
	$status = $user->verify($email, $code);
	$vstatus = $status;
	
	// if it all went smoothly, pause for 5 seconds then redirect to login page
	if ($status === 'true') {
		header("refresh:5;url=login.php");
		$vstatus = 'Account verification successful! You\'ll be redirected to login in about 5 secs.';
	}
} else {
	// if not, redirect back to index
	header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
		<meta charset="utf-8">
		<title>DM Stream Verify</title>
		<link rel="stylesheet" href="css/regform.css">
    </head>
    <body>
		<h1 class="register-title-wide">
			<?php
			if (!empty($vstatus)) {
				echo $vstatus;
			}
			?>
		</h1>
		<?php
		if ($status === 'true') {
			echo '<div class="verify-login-button"><a class="login-link" href="login.php">Or, click here to login</a></div>';
		}
		?>
	</body>
</html>