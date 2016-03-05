<?php
require('lib/auth.php');
require('lib/dbconnect.php');
// Check if there is a channel to display
if (isset($_GET["channel"])) {
    $channel = $_GET["channel"];
    $result = pg_query($pglink, "SELECT * FROM users WHERE stream_key = '$channel'");
    $row_cnt = pg_num_rows($result);
    if ($row_cnt >= 1) {
	$check = pg_fetch_assoc(pg_query($pglink, "SELECT * FROM users WHERE stream_key = '$channel'"));
	$channel_name = $check['channel_name'];
	$channel_title = $check['channel_title'];
	echo 'name: '.$channel_name.' title: '.$channel_title;
	}
}
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<title>DM Stream</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.test.css">
		<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="css/app.css">
		<link rel="stylesheet" type="text/css" href="css/regform.css">
		<link rel="stylesheet" type="text/css" href="js/vjs/video-js.css">
		<link rel="stylesheet" type="text/css" href="js/vjs/videojs-resolution-switcher.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/vjs/dash.all.min.js"></script>
        </head>
        <body><div class="live-player">
    					<video id="videoPlayer" controls data-setup='{}'></video>

				<script>
					(function () {
						var url = "http://stream.rirnef.net/dash/<?php echo $channel; ?>.mpd";
						var player = dashjs.MediaPlayer().create();
						player.initialize(document.querySelector("#videoPlayer"), url, true);
					})();
				</script>
				<script>var settings = {player: true, channel: "<?php echo $streamkey; ?>"};</script>
			</div>
    </body>
</html>
