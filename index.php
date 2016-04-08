<?php
// includes site vars
include 'inc/config.php';

// enable if error reporting is on
if ($debug === true) {error_reporting(E_ALL);ini_set('display_errors', 1);}

// includes
function __autoload($class) {
	include 'lib/' . $class . '.class.php';
}

require 'inc/auth.php';

// Load RTMP channels informations
$rtmpclass = new rtmp();
$rtmpinfo = $rtmpclass->checkStreams();

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

if (!empty($getdownload)) {
	$file = "/var/tmp/rec/" . $getdownload;
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



// Check if we're trying to watch a video or stream
$streamkey = filter_input(INPUT_GET, 'channel', FILTER_SANITIZE_STRING);
$video = filter_input(INPUT_GET, 'video', FILTER_SANITIZE_STRING);


include 'inc/index/main.php';

// Check if we're trying to watch a stream or video, otherwise load the header page.
// This keeps the player page clean of any extra junk.
if (!empty($streamkey)) {include 'inc/streamplayer.php';}
elseif (!empty($video)) {include 'inc/videoplayer.php';}
else { include 'inc/index/header.php'; }

// Check which page we're loading
if ($page === 'videos') { include 'inc/index/videos.php'; }
elseif ($page === 'stats') { include 'inc/index/stats.php'; }
elseif ($page === 'account') { include 'inc/index/account.php'; }
else { include 'inc/index/channels.php'; }
?>
			</div><!-- /.wrap -->	
			<script>var settings = {player: false}</script>
		<script src="js/app.js"></script>
    </body>
</html>
