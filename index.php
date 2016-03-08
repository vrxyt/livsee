<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true){error_reporting(E_ALL);ini_set('display_errors', 1);}

// includes
include 'inc/functions.php';
require 'inc/auth.php';
include 'lib/keyupdate.php';

function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}


if (isset($_GET["download"])) {
	$file = "/var/tmp/rec/" . $_GET["download"];
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

// Load RTMP channels informations
RTMP::checkStreams();


// Check if there is a channel to display
$streamkey = false;

if (isset($_GET["channel"])) {
	$streamkey = $_GET["channel"];
}

// Check if there is a video to display

$video = false;

if (isset($_GET["video"])) {
	$video = $_GET["video"];
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>DM Stream</title>
		<link rel="icon" href="img/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.test.css">
		<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="css/app.css">
		<link rel="stylesheet" type="text/css" href="css/regform.css">
		<link rel="stylesheet" type="text/css" href="js/vjs/video-js.css">
		<link rel="stylesheet" type="text/css" href="js/vjs/videojs-resolution-switcher.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/vjs/video-js.js"></script>
		<script src="js/vjs/videojs-resolution-switcher.js"></script>
		<script src="js/vjs/videojs-contrib-hls.min.js"></script>
		<script src="js/vjs/dash.all.min.js"></script>
		<script src="js/vjs/videojs-dash.new.js"></script>
		

    </head>
    <body>
		<?php
		if ($streamkey !== false) {
			if (count(array_keys($_SESSION["rtmp"]["channels"])) > 1) {
				?>
				<select class="channel">
					<?php
					foreach (array_keys($_SESSION["rtmp"]["channels"]) as $channelName) {
						echo "\t\t";
						echo '<option value="' . $channelName . '"';
						if ($channelName === $_GET["channel"]) {
							echo ' selected';
						}

						echo '>' . $channelName . '</option>';
					}
					?>
				</select>
				<?php
			}
			?>
			<div class="live-player">
				<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
					   controls data-setup='{}'
					   id="player"
					   preload="auto"
					   width="100%" height="100%"
					   poster="//<?= $surl ?>/img/channel_<?php echo $streamkey; ?>.jpg"
					   >
					<source src="//<?= $surl ?>/hls/<?php echo $streamkey; ?>.m3u8" type="application/x-mpegurl" label='HLS'/>
					<!--<source src="<?= $furl ?>/dash/<?php echo $streamkey; ?>.mpd" type='application/dash+xml' label="DASH"/>-->
					<source src="rtmp://<?= $surl ?>/live/<?php echo $streamkey; ?>" type="rtmp/flv" label='Flash'/>
				</video>

				<script>
					videojs('player').videoJsResolutionSwitcher();
				</script>   
				<script>var settings = {player: true, channel: "<?php echo $streamkey; ?>"};</script>

			</div>
			<?php
		} elseif ($video !== false) {
			?>
			<div class="live-player">
				<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
					   controls data-setup='{}'
					   id="player"
					   preload="auto"
					   width="100%" height="100%"
					   poster="//<?= $surl ?>/img/video_<?php echo str_replace(".mp4", ".jpg", $video); ?>">
					<source src="//<?= $surl ?>/rec/<?= $video ?>" type="video/mp4"/>
				</video>

				<script>var settings = {player: true, video: "<?= $video ?>"};</script>
			</div>
			<?php
		} else {
			?>
			<div id="wrap">
				<nav class="navbar navbar-default navbar-static-top" role="navigation">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-menu">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="/"><i class="fa fa-youtube-play"></i> Len's Streaming Emporium <span class="subtext">(A Dancing Mad Production)</span></a>
						</div>

						<div id="navbar-collapse-menu" class="collapse navbar-collapse navbar-right">
							<ul class="nav navbar-nav">
								<li<?php
								if (!isset($_GET['videos']) && !isset($_GET['stats']) && !isset($_GET['account'])) {
									echo ' class="active"';
								}
								?>><a href="/?channels">Channels</a></li>
								<li<?php
								if (isset($_GET["videos"])) {
									echo ' class="active"';
								}
								?>><a href="/?videos">Videos</a></li>
								<li<?php
								if (isset($_GET["stats"])) {
									echo ' class="active"';
								}
								?>><a href="/?stats">Stats</a></li>
								<li<?php
								if (isset($_GET["account"])) {
									echo ' class="active"';
								}
								?>><a href="/?account">Account</a></li>
								<li><a href="index.php?action=logout">Logout</a></li>
							</ul>
						</div><!-- /.navbar-collapse -->
					</div>
				</nav>
				<?php
				if (isset($_GET["videos"])) {
					?>
					<div class="jumbotron">
						<div class="container">
							<h1><i class="fa fa-film"></i> Recorded Videos</h1>
						</div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-md-12 text-right">
								<div class="btn-group">
									<a href="#" class="display-grid btn btn-default"><span class="fa fa-th-large"></span> Grid</a>
									<a href="#" class="display-list btn btn-default"><span class="fa fa-th-list"></span> List</a>
								</div>
							</div>
						</div>
						<br />
						<?php
						$videos = glob("/var/tmp/rec/*.mp4");
						if (count($videos) > 0) {
							foreach ($videos as $key => $video) {
								$file = substr($video, strrpos($video, "/") + 1);
								$streamkey = substr($file, 0, strrpos($file, "-"));
								$streamkey = updateStreamkey($streamkey, 'channel');
								$timestamp = substr($file, strrpos($file, "-") + 1, -4);
								$datetime = date("Y-m-d H:i:s", $timestamp);
								$screenshot = 'img/video_' . str_replace('mp4', 'jpg', $file);
								if (!file_exists($screenshot)) {
									$screenshot = 'img/no-preview.jpg';
								}

								$mediainfo = array();
								try {
									$mediainfo = MediaInfo::fetchVideo($video);

									// eval FPS to get rid of fractions:
									// - 30/1 becomes 30
									// - 2997/100 becomes 29.97

									eval('$mediainfo["streams"][0]["r_frame_rate"] = ' . $mediainfo["streams"][0]["r_frame_rate"] . ';');
								} catch (Exception $e) {
									print 'Cauth Exception with message ' . $e->getMessage();
								}

								$videos[$key] = ["file" => $file, "screenshot" => $screenshot, "channel" => $streamkey, "timestamp" => $timestamp, "datetime" => $datetime, "mediainfo" => $mediainfo];
							}
							?>
							<div class="row grid">
								<?php
								foreach ($videos as $key => $video) {
									$col = 'col-md-6';
									if (count($videos) === 1) {
										$col.= ' col-md-offset-3';
									}

									echo '<div class="' . $col . '">' . "\r\n";
									echo '	<div class="thumbnail">' . "\r\n";
									echo '		<a href="?video=' . $video["file"] . '">' . "\r\n";
									echo '			<label class="status live"><i class="fa fa-clock-o"></i> <span data-duration="' . $video["mediainfo"]["format"]["duration"] . '"></span></label>' . "\r\n";
									echo '			<img class="img-responsive" src="' . $video["screenshot"] . '" alt="' . $video["channel"] . '">' . "\r\n";
									echo '		</a>' . "\r\n";
									echo '		<div class="caption">' . "\r\n";
									echo '			<h3><a href="?video=' . $video["file"] . '">' . $video["channel"] . ' - ' . $video["datetime"] . '</a></h3>' . "\r\n";
									echo '		</div>' . "\r\n";
									echo '	</div>' . "\r\n";
									echo '</div>' . "\r\n";
								}
								?>
							</div>
							<div class="row list">
								<table class="table videos">
									<thead>
										<tr>
											<th>Channel</th>
											<th>Date</th>
											<th class="hidden-xs">Duration</th>
											<th class="hidden-xs">Definition</th>
											<th class="hidden-xs">Size</th>
											<th class="text-center" style="width:100px">Download</th>
											<th class="text-center" style="width:100px">Play</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($videos as $video) {
											$seed = uniqid();
											echo '<tr>' . "\r\n";
											echo '	<td>' . $video["channel"] . '</td>' . "\r\n";
											echo '	<td><span data-timestamp="' . $video["timestamp"] . '000"></span></td>' . "\r\n";
											echo '	<td class="hidden-xs"><span data-duration="' . $video["mediainfo"]["format"]["duration"] . '"></span></td>' . "\r\n";
											echo '	<td class="hidden-xs">' . "\r\n";
											echo '		<em data-toggle="popover" data-seed="' . $seed . '" data-trigger="hover" data-placement="top" data-title="Meta Data">' . $video["mediainfo"]["streams"][0]["height"] . 'p@' . $video["mediainfo"]["streams"][0]["r_frame_rate"] . 'fps</em>' . "\r\n";
											echo '	</td>' . "\r\n";
											echo '	<td class="hidden-xs"><span data-size="' . $video["mediainfo"]["format"]["size"] . '"></span>B</td>' . "\r\n";
											echo '	<td class="text-center"><a href="?download=' . $video["file"] . '" class="btn btn-success"><i class="fa fa-download"></i></a></td>' . "\r\n";
											echo '	<td class="text-center"><a href="?video=' . $video["file"] . '" class="btn btn-primary"><i class="fa fa-play"></i></a></td>' . "\r\n";
											echo '</tr>';
											echo '<div id="popover-' . $seed . '" class="hidden">';
											echo '	<h4>Video</h4>' . "\r\n";
											echo '	<ul>' . "\r\n";
											echo '		<li>Codec: ' . $video["mediainfo"]["streams"][0]["codec_name"] . ' ' . $video["mediainfo"]["streams"][0]["profile"] . '</li>' . "\r\n";
											echo '		<li>Bitrate: <span data-size="' . $video["mediainfo"]["streams"][0]["bit_rate"] . '"></span>b/s</li>' . "\r\n";
											echo '		<li>Definition: ' . $video["mediainfo"]["streams"][0]["width"] . '*' . $video["mediainfo"]["streams"][0]["height"] . '</li>' . "\r\n";
											echo '		<li>Framerate: ' . $video["mediainfo"]["streams"][0]["r_frame_rate"] . ' fps</li>' . "\r\n";
											echo '	</ul>' . "\r\n";
											echo '	<h4>Audio</h4>' . "\r\n";
											echo '	<ul>' . "\r\n";
											echo '		<li>Codec: ' . $video["mediainfo"]["streams"][1]["codec_name"] . '</li>' . "\r\n";
											echo '		<li>Bitrate: <span data-size="' . $video["mediainfo"]["streams"][1]["bit_rate"] . '"></span>b/s</li>' . "\r\n";
											echo '		<li>Sample Rate: ' . $video["mediainfo"]["streams"][1]["sample_rate"] . ' Hz</li>' . "\r\n";
											echo '		<li>Channels: ' . $video["mediainfo"]["streams"][1]["channels"] . '</li>' . "\r\n";
											echo '	</ul>' . "\r\n";
											echo '</div>' . "\r\n";
										}
										?>
									</tbody>
								</table>
							</div>
							<?php
						} else {
							?>
							<div class="col-md-12">
								<p class="text-center">No video available.</p>
								<br />
								<p class="text-center"><a class="btn btn-lg btn-primary" href="/?videos"><i class="fa fa-refresh"></i> Refresh</a></p>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				} elseif (isset($_GET["stats"])) {
					?>
					<div class="jumbotron">
						<div class="container">
							<h1><i class="fa fa-film"></i> Stream Stats</h1>
						</div>
					</div>
					<div class="container" id="statscontainer">
						<br />
						<div id="statsdiv"><iframe id="statspage" src="<?= $furl ?>/stat"></iframe></div>
					</div>
					<?php
				} elseif (isset($_GET["account"])) {
					?>
					<div class="jumbotron">
						<div class="container">
							<h1><i class="fa fa-film"></i> Account Settings</h1>
						</div>
					</div>
					<div class="container" id="statscontainer">
						<br />
						<div id="statsdiv"><?php include 'inc/account.php'; ?></div>
					</div>
					<?php
				} else {
					?>
					<div class="jumbotron">
						<div class="container">
							<h1><i class="fa fa-video-camera"></i> Live Channels</h1>
						</div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-md-12 text-right">
								<div class="btn-group">
									<a href="#" class="display-grid btn btn-default"><span class="fa fa-th-large"></span> Grid</a>
									<a href="#" class="display-list btn btn-default"><span class="fa fa-th-list"></span> List</a>
								</div>
							</div>
						</div>
						<br />
						<?php
						if (count($_SESSION["rtmp"]["channels"]) > 0) {
							$channels = array();
							foreach ($_SESSION["rtmp"]["channels"] as $channelName => $streamkey) {
								$channels[$channelName] = $streamkey;
								$channels[$channelName]["screenshot"] = 'img/channel_' . $channelName . '.jpg';
								if (!file_exists($channels[$channelName]["screenshot"])) {
									$channels[$channelName]["screenshot"] = 'img/no-preview.jpg';
								}

								$mediainfo = array();
								try {

									// Deactivated for now: too slow to fetch RTMP channel...
									// $mediainfo = MediaInfo::fetchChannel($channelName);
								} catch (Exception $e) {
									print 'Cauth Exception with message ' . $e->getMessage();
								}

								$channels[$channelName]["mediainfo"] = $mediainfo;
							}
							?>
							<div class="row grid">
								<?php
								foreach ($channels as $channelName => $streamkey) {
									$viewcount = file_get_contents($furl.'/nclients?app=live&name=' . $channelName);
									$col = 'col-md-6';
									if (count($_SESSION["rtmp"]["channels"]) === 1) {
										$col.= ' col-md-offset-3';
									}

									echo '<div class="' . $col . '">' . "\r\n";
									echo '	<div class="thumbnail">' . "\r\n";
									echo '		<a href="?channel=' . $channelName . '">' . "\r\n";
									echo '			<label class="status live"><i class="circle"></i> LIVE</label>' . "\r\n";
									echo '			<label class="viewcount">Viewers: ' . $viewcount . "</label>\r\n";
									echo '			<img class="img-responsive" src="' . $streamkey["screenshot"] . '" alt="' . updateStreamkey($channelName, 'channel') . '">' . "\r\n";
									echo '		</a>' . "\r\n";
									echo '		<div class="caption">' . "\r\n";
									echo '			<div class="channel-display"><h3><a href="?channel=' . $channelName . '">' . updateStreamkey($channelName, 'channel') . '</a> </h3></div>';
									echo '				<div class="channel-title">' . \updateStreamkey($channelName, 'title') . '</div>';
									echo '		</div>' . "\r\n";
									echo '	</div>' . "\r\n";
									echo '</div>' . "\r\n";
								}
								?>
							</div>
							<div class="row list">
								<table class="table channels">
									<thead>
										<tr>
											<th>Channel</th>
											<th>Duration</th>
											<th>Viewers</th>
											<th class="hidden-xs">Definition</th>
											<th class="text-center" style="width:100px">Record</th>
											<th class="text-center" style="width:100px">Watch</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach ($channels as $channelName => $streamkey) {
											$seed = uniqid();
											$viewcount = file_get_contents($furl.'/nclients?app=live&name=' . $channelName);
											echo '<tr>' . "\r\n";
											echo '	<td><a href="?channel=' . $channelName . '">' . updateStreamkey($channelName, 'channel') . '</a></td>' . "\r\n";
											echo '	<td>' . gmdate("H:i:s", ($streamkey["time"] / 1000)) . '</td>' . "\r\n";
											echo '	<td>' . $viewcount . ' watching</td>';
											echo '	<td class="hidden-xs">' . "\r\n";
											echo '		<em data-toggle="popover" data-seed="' . $seed . '" data-trigger="hover" data-placement="top" data-title="Meta Data">' . $streamkey["meta"]["video"]["height"] . 'p@' . $streamkey["meta"]["video"]["frame_rate"] . 'fps</em>' . "\r\n";
											echo '	</td>' . "\r\n";
											echo '	<td class="text-center"><a class="btn btn-record btn-danger" data-channel="' . $channelName . '"><i class="fa fa-circle"></i><i class="fa fa-stop"></i></a></td>' . "\r\n";
											echo '	<td class="text-center"><a class="btn btn-play btn-primary" href="?channel=' . $channelName . '"><i class="fa fa-play"></i></a></td>' . "\r\n";
											echo '</tr>' . "\r\n";
											echo '<div id="popover-' . $seed . '" class="hidden">';
											echo '	<h4>Video</h4>';
											echo '	<ul>';
											echo '		<li>Codec: ' . $streamkey["meta"]["video"]["codec"] . ' ' . $streamkey["meta"]["video"]["profile"] . '</li>';
											echo '		<li>Bitrate: <span data-size="' . $streamkey["bw_video"] . '"></span>b/s</li>';
											echo '		<li>Definition: ' . $streamkey["meta"]["video"]["width"] . '*' . $streamkey["meta"]["video"]["height"] . '</li>';
											echo '		<li>Framerate: ' . $streamkey["meta"]["video"]["frame_rate"] . ' fps</li>';
											echo '	</ul>';
											echo '	<h4>Audio</h4>';
											echo '	<ul>';
											echo '		<li>Codec: ' . $streamkey["meta"]["audio"]["codec"] . ' ' . $streamkey["meta"]["audio"]["profile"] . '</li>';
											echo '		<li>Bitrate: <span data-size="' . $streamkey["bw_audio"] . '"></span>b/s</li>';
											echo '		<li>Sample Rate: ' . $streamkey["meta"]["audio"]["sample_rate"] . ' Hz</li>';
											echo '		<li>Channels: ' . $streamkey["meta"]["audio"]["channels"] . '</li>';
											echo '	</ul>';
											echo '</div>' . "\r\n";
										}
										?>
									</tbody>
								</table>
							</div>
							<?php
						} else {
							?>
							<div class="col-md-12">
								<p class="text-center">No channel available.</p>
								<br />
								<p class="text-center"><a class="btn btn-lg btn-primary" href="/?channels"><i class="fa fa-refresh"></i> Refresh</a></p>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
				?>
			</div><!-- /.wrap -->
			<script>var settings = {player: false}</script>
			<?php
		}
		?>
		<script src="js/app.js"></script>
    </body>
</html>
