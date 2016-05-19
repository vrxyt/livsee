<div class="mdl-grid mdl-grid--no-spacing fill">
	<div class="mdl-content fill">
		<div class="live-player">
			<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
				   data-setup='{"controls": true, "preload": "auto"}'
				   autoplay
				   id="streamPlayer"
				   width="100%" height="100%"
				   poster="//<?= $surl ?>/img/channel/channel_<?= $streamkey ?>.png"
				   >
				<source src="//<?= $surl ?>/hls/<?= $streamkey ?>.m3u8" type="application/x-mpegurl" label='HLS'/>
				<source src="rtmp://<?= $surl ?>/live/<?= $streamkey ?>" type="rtmp/flv" label='Flash'/>
			</video>
			<script>
				videojs('streamPlayer').videoJsResolutionSwitcher();
			</script>
		</div>
	</div>
</div>