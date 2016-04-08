<?php

	if (count(array_keys($rtmpinfo["rtmp"]["channels"])) > 1) {
		?>
		<select class="channel">
			<?php
			foreach (array_keys($rtmpinfo["rtmp"]["channels"]) as $channelName) {
				echo "\t\t";
				echo '<option value="' . $channelName . '"';
				if ($channelName === $getchannel) {
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
			   poster="//<?= $surl ?>/img/channel_<?php echo $streamkey; ?>.png"
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