<?php
// includes site vars
include '../inc/config.php';

// enable if error reporting is on
if ($debug === true) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// includes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists('../api/' . strtolower($class) . '.php')) {
			include '../api/' . strtolower($class) . '.php';
		} elseif (file_exists('../lib/' . strtolower($class) . '.class.php')) {
			include '../lib/' . strtolower($class) . '.class.php';
		}
	}
});

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
$subemail = $user->updateStreamkey($streamkey, 'email');
// Set up data for checking subscription status
$sub = new subscription($accountinfo['api_key'], [$email]);
$list = $sub->_list();
$subarray = json_decode($list);
if (in_array($subemail, $subarray->subscribed)) {
	$substatus = 'Odsubskrybuj';
	$subcolor = 'style="background-color: rgb(0, 188, 212)"';
} else {
	$substatus = 'Subskrybuj';
	$subcolor = '';
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Oglądasz <?= $streamkey ?> - <?= $sitetitle ?></title>
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,300,100,700,900' rel='stylesheet'
		  type='text/css'>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/js/vjs/7.2.3/video-js.min.css">
	<link rel="stylesheet" type="text/css" href="/js/vjs/video-js-skin.css">
	<link rel="stylesheet" href="/css/application.css">
	<link rel="stylesheet" href="/css/site.css">
</head>
<body>
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
	<header class="mdl-layout__header">
		<div class="mdl-layout__header-row">
			<div class="mdl-layout-spacer"></div>

			<div class="avatar-dropdown" id="icon">
				<span><?= $accountinfo['display_name'] ?></span>
				<img src="<?= $accountinfo['profile_img'] ?>">
			</div>

			<ul class="mdl-menu mdl-list mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect mdl-shadow--2dp account-dropdown"
				for="icon">
				<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<img class="material-icons mdl-list__item-avatar"
									 src="<?= $accountinfo['profile_img'] ?>">
								<span><?= $accountinfo['display_name'] ?></span>
								<span class="mdl-list__item-sub-title"><?= $accountinfo['email'] ?></span>
							</span>
				</li>

				<li class="list__item--border-top"></li>

				<li class="mdl-list__item mdl-list__item--two-line">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">vpn_key</i>
								<span>Klucz strumieniowania</span>
								<span class="mdl-list__item-sub-title"><?= $accountinfo['stream_key']; ?></span>
							</span>
				</li>

				<li class="list__item--border-top"></li>

				<a href="/settings" target="_blank" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon">settings</i>
								Ustawienia
							</span>
				</a>
				<a href="?action=logout" class="mdl-menu__item mdl-list__item">
							<span class="mdl-list__item-primary-content">
								<i class="material-icons mdl-list__item-icon text-color--secondary">exit_to_app</i>
								Wyloguj się
							</span>
				</a>
			</ul>
		</div>
	</header>
	<div class="mdl-layout__drawer">
		<header class="dm-logo-header"><?= $sitetitle ?></header>
		<nav class="mdl-navigation">
			<a class="mdl-navigation__link" onclick="closepopoutPlayer()" href="">
				<i class="material-icons" role="presentation">arrow_back</i>
				Wstecz
			</a>

			<a class="mdl-navigation__link mdl-navigation__link--current" href="/watch/<?= $streamkey; ?>">
				<i class="material-icons" role="presentation">visibility</i>
				W tej chwili ogląda: <?php echo $user->updateStreamkey($streamkey, 'channel'); ?>
			</a>

			<button id="subButton" class="mdl-button mdl-js-button mdl-button--raised" channel="<?= $streamkey; ?>"
					type="button" <?= $subcolor ?>><?= $substatus ?></button>
			<div id="subToast" class="mdl-js-snackbar mdl-snackbar">
				<div class="mdl-snackbar__text"></div>
				<button class="mdl-snackbar__action" type="button"></button>
			</div>

			<div class="mdl-layout-spacer"></div>
		</nav>
	</div>

	<main class="mdl-layout__content-popout">

		<div class="live-player">
			<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered" controls autoplay preload
				   id="popoutPlayer"
				   width="100%" height="100%" poster="//<?= $surl ?>/img/channel/channel_<?= $streamkey ?>.png">
			</video>
		</div>

	</main>
</div>
<script src="/js/material.1.3.0.js"></script>
<script src="/js/getmdl-select.min.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/jqui/jquery-ui.min.js"></script>
<script src="/js/vjs/7.2.3/video.min.js"></script>
<script src="/js/vjs/tech/videojs-flvjs.min.js"></script>
<script src="/js/vjs/videojs-persistvolume.js"></script>
<script type='text/javascript'>
	var popoutPlayer = videojs('popoutPlayer', {
        techOrder: ['html5', 'flvjs'],
        sources: [{
            src: '<?= $furl ?>/flv-live?port=1935&app=live&stream=<?= $streamkey ?>',
            type: 'video/flv',
            label: 'HTTP-FLV'
        }]
	});
	popoutPlayer.persistvolume({namespace: "Rachni-Volume-Control"});
	var api_key = "<?= $accountinfo['api_key'] ?>";
	<?php if (!empty($streamkey)) { ?> var stream_key = "<?php echo $user->updateStreamkey($streamkey, 'channel'); ?>"; <?php } ?>
	function closepopoutPlayer() {
		window.open("<?= $furl ?>/channels");
		window.close();
	}
	$('#subButton').click(function () {
		let snackbarContainer = document.querySelector('#subToast');
		let button = $(this);
		let channel = $(this).attr('channel');
		let action = $(this).text();
		'use strict';
		if (action === 'Odsubskrybuj') {
			console.log('Action = Odsubskrybuj');
			$.getJSON('/api/' + api_key + '/subscription/remove/' + channel, function (result) {
				if (result === false) {
					console.log('Error podczas odsubkrybowania');
				} else {
					const data = {
						message: 'Odsubskrybowano ' + stream_key + '.',
						timeout: 5000
					};
					console.log(result);
					button.text('Subskrybuj');
					button.css('background-color', '');
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				}
			});
		} else if (action === 'Subskrybuj') {
			console.log('Action = Subskrybuj');
			$.getJSON('/api/' + api_key + '/subscription/add/' + channel, function (result) {
				if (result === false) {
					const data = {
						message: 'Error podczas subskrybowania (prawdopodobnie już to robisz)!',
						timeout: 5000
					};
					console.log('Error subscribing' + result);
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				} else {
					const data = {
						message: 'Zasubskrybowano ' + stream_key + '!',
						timeout: 5000
					};
					console.log(result);
					button.text('Odsubskrybuj');
					button.css('background-color', '#00bcd4');
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				}
			});
		}
	});
</script>
</body>
</html>

