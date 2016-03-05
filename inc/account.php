<?php

// includes
include('inc/functions.php');

// enable if error reporting is on
if ($debug === true){error_reporting(E_ALL);ini_set('display_errors', 1);}

// open user class for use
$user = new user();

// set $email based on session var, but verify it's at least close to an email address first
$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);

// run account info update if data was posted
if (!empty($_POST)) {
	$channelname = $_POST['channelname'];
	$channeltitle = $_POST['channeltitle'];
	$displayname = $_POST['displayname'];
	$status = $user->update($email, $channelname, $channeltitle, $displayname);
}

// grab accpimt info
$accountinfo = $user->info($email);

?>
<h1 class="account-title">Account Information</h1>

<form action="" method="POST" class="account-info">

	<div class="account-info-content"><div class="account-categories">Display Name: </div><input type="text" name="displayname" class="account-input" value="<?= $accountinfo['display_name'] ?>"></div>
	<div class="account-info-content"><div class="account-categories">Email:</div><?= $accountinfo['email'] ?></div>
	<div class="account-info-content"><div class="account-categories">Stream key:</div><?= $accountinfo['stream_key'] ?></div>
	<div class="account-info-content"><div class="account-categories">Channel Name: </div><input type="text" name="channelname" class="account-input" value="<?= $accountinfo['channel_name'] ?>"></div>
	<div class="account-info-content"><div class="account-categories">Channel Title: </div><input type="text" name="channeltitle" class="account-input" value="<?= $accountinfo['channel_title'] ?>"></div>
	<input type="submit" name="submitted" value="Update Channel Info" class="account-button">

	<?php
	if (!empty($status)) {
		echo '<div class="account-statusblock">' . $status . '</div>';
	}
	?>

</form>
</body>
</html>