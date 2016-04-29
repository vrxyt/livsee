<?php
// includes site vars
include '../inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// includes
function __autoload($class) {
	include '../lib/' . $class . '.class.php';
}

// verify we're logged in
require '../inc/auth.php';

// Load RTMP channels informations
$rtmpclass = new rtmp();
$rtmpinfo = $rtmpclass->checkStreams();

// grab account info
$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);
$accountinfo = $user->info($email);

// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);

$page = $uriVars[0];
$streamkey = $uriVars[1];
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Watching <?= $streamkey ?> - DM Stream Site</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,300,100,700,900' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/js/vjs/video-js.css">
		<link rel="stylesheet" type="text/css" href="/js/vjs/videojs-resolution-switcher.css">
		<link rel="stylesheet" href="/css/application.css">
		<link rel="stylesheet" href="/css/site.css">
		<script src="/js/vjs/video-js.js"></script>
		<script src="/js/vjs/videojs-resolution-switcher.js"></script>
		<script src="/js/vjs/videojs-contrib-hls.min.js"></script>
	<body>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row">
					<div class="mdl-layout-spacer"></div>

					<div class="avatar-dropdown" id="icon">
						<span><?= $accountinfo['display_name'] ?></span>
						<img src="<?= $accountinfo['profile_img'] ?>">
					</div>

					<ul class="mdl-menu mdl-list mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect mdl-shadow--2dp account-dropdown" for="icon">
						<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<img class="material-icons mdl-list__item-avatar" src="<?= $accountinfo['profile_img'] ?>">
								<span><?= $accountinfo['display_name'] ?></span>
								<span class="mdl-list__item-sub-title"><?= $accountinfo['email'] ?></span>
							</span>
						</li>

						<li class="list__item--border-top"></li>

						<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">vpn_key</i>
								<span>Stream Key</span>
								<span class="mdl-list__item-sub-title"><?= $accountinfo['stream_key']; ?></span>
							</span>
						</li>

						<li class="list__item--border-top"></li>

						<a href="/settings" target="_blank" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">settings</i>
								Settings
							</span>
						</a>
						<a href="?action=logout" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon text-color--secondary">exit_to_app</i>
								Log out
							</span>
						</a>
					</ul>
				</div>
			</header>
			<div class="mdl-layout__drawer">
				<header class="dm-logo-header">DM Stream</header>
				<nav class="mdl-navigation">
					<a class="mdl-navigation__link" onclick="closepopoutPlayer()" href="">
						<i class="material-icons" role="presentation">arrow_back</i>
						Back
					</a>

					<a class="mdl-navigation__link mdl-navigation__link--current" href="/watch/<?= $streamkey; ?>">
						<i class="material-icons" role="presentation">visibility</i>
						Watching: <?php echo $user->updateStreamkey($streamkey, 'channel'); ?>
					</a>

					<div class="mdl-layout-spacer"></div>
				</nav>
			</div>

			<main class="mdl-layout__content-popout">
				
					<div class="live-player">
						<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
							   data-setup='{"controls": true, "autoplay": true, "preload": "auto"}'
							   id="streamPlayer"
							   width="100%" height="100%"
							   poster="//<?= $surl ?>/img/channel_<?= $streamkey ?>.png"
							   >
							<source src="//<?= $surl ?>/hls/<?= $streamkey ?>.m3u8" type="application/x-mpegurl" label='HLS'/>
							<source src="rtmp://<?= $surl ?>/live/<?= $streamkey ?>" type="rtmp/flv" label='Flash'/>
						</video>
						<script>
							videojs('streamPlayer').videoJsResolutionSwitcher();
						</script>
					</div>
				
			</main>
		</div>
		<script src="/js/material.js"></script>
		<script>
							function closepopoutPlayer() {
								window.open("<?= $furl ?>/channels");
								window.close();
							}
		</script>
	</body>
</html>

