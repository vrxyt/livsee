<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// includes
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

// verify we're logged in
require 'inc/auth.php';

// Load RTMP channels informations
$rtmpclass = new rtmp();
$rtmpinfo = $rtmpclass->checkStreams();

// grab account info
$email = filter_var($_SESSION['authenticated'], FILTER_VALIDATE_EMAIL);
$accountinfo = $user->info($email);

function bitsConvert($bites, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bites) - 1) / 3);
	return sprintf("%.{$decimals}f", $bites / pow(1000, $factor)) . @$sz[$factor];
}

function bytesConvert($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// Check if we're trying to watch a video or stream
// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);

// Set current page and then
// check if we're trying to 
// watch a video or stream
$page = $uriVars[0];
if ($page === 'watch') { $streamkey = $uriVars[1]; }
if ($page === 'video') { $video = $uriVars[1]; }
if ($page === 'download') { $download = filter_var($uriVars[1], FILTER_SANITIZE_STRING);
	$file = "/var/tmp/rec/" . $download;
	if (file_exists($file)) {
		$size = filesize("./" . basename($file));
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . $size);
		header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		readfile($file);
		exit();
	}
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>DM Stream Site</title>
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,300,100,700,900' rel='stylesheet' type='text/css'>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="/js/vjs/video-js.css">
		<link rel="stylesheet" type="text/css" href="/js/vjs/videojs-resolution-switcher.css">
		<link rel="stylesheet" href="/css/lib/getmdl-select.min.css">
		<link rel="stylesheet" href="/css/application.css">
		<link rel="stylesheet" href="/css/site.css">
		<script src="/js/vjs/video-js.js"></script>
		<script src="/js/vjs/videojs-resolution-switcher.js"></script>
		<script src="/js/vjs/videojs-contrib-hls.min.js"></script>
	<body>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header"><!-- START LAYOUT WRAP -->
			<!-- START NAV HEADER -->
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row">
					<div class="mdl-layout-spacer"></div>

					<?php if (!empty($streamkey)) { ?>
						<a class="mdl-navigation__link" onclick="popoutPlayer()" href="">
							<i class="material-icons" role="presentation">video_label</i>
							Switch to Popout Player
						</a>
					<?php } ?>

					<div class="avatar-dropdown" id="icon">
						<span><?= $accountinfo['display_name'] ?></span>
						<img src="<?= $accountinfo['profile_img'] ?>">
					</div>


					<ul class="mdl-menu mdl-list mdl-menu--bottom-right mdl-js-menu mdl-shadow--2dp account-dropdown" for="icon">
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

						<a href="/settings" class="mdl-menu__item mdl-list__item">
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
			<!-- END NAV HEADER -->

			<!-- START NAV DRAWER -->
			<div class="mdl-layout__drawer">
				<header class="dm-logo-header">DM Stream</header>
				<nav class="mdl-navigation">
					<a class="mdl-navigation__link<?php
					if ($page === 'channels' || (empty($page) && empty($streamkey))) {
						echo ' mdl-navigation__link--current';
					}
					?>" href="/channels">
						<i class="material-icons" role="presentation">videogame_asset</i>
						Live channels
					</a>
					<a class="mdl-navigation__link<?php
					if ($page === 'videos') {
						echo ' mdl-navigation__link--current';
					}
					?>" href="/videos">
						<i class="material-icons" role="presentation">video_library</i>
						Videos
					</a>
					<?php if (!empty($streamkey)) { ?>
						<a class="mdl-navigation__link mdl-navigation__link--current mdl-typography--text-nowrap-ellipsis" href="/watch/<?= $streamkey; ?>">
							<i class="material-icons" role="presentation">visibility</i>
							Watching: <?php echo $user->updateStreamkey($streamkey, 'channel'); ?>
						</a>
					<?php } else { ?>
						<a class="mdl-navigation__link" href="/">
							<i class="material-icons" role="presentation">visibility</i>
							Watching: N/A
						</a>
					<?php } ?>
					<div class="mdl-layout-spacer"></div>
					<a class="mdl-navigation__link" href="https://github.com/Fenrirthviti/">
						<i class="material-icons" role="presentation">link</i>
						GitHub
					</a>
				</nav>
			</div>
			<!-- END NAV DRAWER-->			

			<!-- START CONTENT PAGE -->
			<main class="mdl-layout__content">

				<?php
				// Load the appropriate page, but only if it exists
				// and checking first if trying to watch a stream or play a video
				if (!empty($streamkey)) { include 'inc/index/streamplayer.php';
				} elseif (!empty($video)) { include 'inc/index/videoplayer.php';
				} elseif (file_exists('inc/index/' . $page . '.php') === true) { include 'inc/index/' . $page . '.php';
				} elseif (empty($page) || ($page === 'index.php')) { include 'inc/index/channels.php';
				} else { include 'inc/404.php';	}
				?>		


			</main>
			<!-- END CONTENT PAGE-->

		</div> <!-- END LAYOUT WRAP -->

		<!-- START FOOTER -->
		<script src="/js/getmdl-select.min.js"></script>
		<script src="/js/material.js"></script>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/channels.js"></script>
		<?php if (!empty($streamkey)) { ?>
		<script>
			var streamPlayer = videojs("streamPlayer");
			this.popoutPlayer = function () {
				streamPlayer.pause();
				window.open("<?= $furl ?>/popout/<?= $streamkey ?>", "_blank", "menubar=0,scrollbars=0,status=0,titlebar=0,toolbar=0,top=200,left=200,resizable=yes,width=1280,height=784");
			}
		</script>
		<?php } ?>
	</body>
</html>
<!-- END FOOTER -->
