<?php
$currentstreams = array_keys($rtmpinfo["rtmp"]["channels"]);
if (count($currentstreams) > 1) {
	?>
	<select class="channel">
		<?php
		foreach ($currentstreams as $channelName) {
			echo "\t\t";
			echo '<option value="' . $channelName . '"' . ($channelName === $getchannel ? ' selected' : '') . '>' . $channelName . '</option>';
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
		   poster="//<?= $surl ?>/img/channel_<?= $streamkey ?>.png"
		   >
		<source src="//<?= $surl ?>/hls/<?= $streamkey ?>.m3u8" type="application/x-mpegurl" label='HLS'/>
		<source src="rtmp://<?= $surl ?>/live/<?= $streamkey ?>" type="rtmp/flv" label='Flash'/>
	</video>
	<script>
		videojs('player').videoJsResolutionSwitcher();
		var settings = {player: true, channel: "<?= $streamkey ?>"};
	</script>

</div>