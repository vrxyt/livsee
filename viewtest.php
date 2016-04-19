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
		<script src="js/vjs/video-js.js"></script>
		<script src="js/vjs/videojs-resolution-switcher.js"></script>
		<script src="js/vjs/videojs-contrib-hls.min.js"></script>
		<script src="js/vjs/dash.all.min.js"></script>
		<script src="js/vjs/videojs-dash.new.js"></script>
        </head>
        <body><div class="live-player">
				<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
					   controls data-setup='{}'
					   id="player"
					   preload="auto"
					   width="100%" height="100%"
					   poster="https://stream.rirnef.net/img/channel_<?php echo $streamkey; ?>.jpg"
					   >
					<source src="http://69.65.48.19:8081/live/testing/playlist.m3u8" type="application/x-mpegurl" label='HLS'/>
				</video>
		

				<script>
					videojs('player').videoJsResolutionSwitcher();
				</script>   
				<script>var settings = {player: true, channel: "<?php echo $streamkey; ?>"};</script>

				<!--<video id="videoPlayer" controls data-setup='{}'></video>

				<script>
					(function () {
						var url = "http://stream.rirnef.net/dash/<?php echo $streamkey; ?>.mpd";
						var player = dashjs.MediaPlayer().create();
						player.initialize(document.querySelector("#videoPlayer"), url, true);
					})();
				</script>
				<script>var settings = {player: true, channel: "<?php echo $streamkey; ?>"};</script>-->
			</div>
    </body>
</html>
