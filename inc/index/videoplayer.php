<div class="live-player">
	<video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
		   controls data-setup='{}'
		   id="player"
		   preload="auto"
		   width="100%" height="100%"
		   poster="//<?= $surl ?>/img/video_<?php echo str_replace(".mp4", ".png", $video); ?>">
		<source src="//<?= $surl ?>/rec/<?= $video ?>" type="video/mp4"/>
	</video>

	<script>var settings = {player: true, video: "<?= $video ?>"};</script>
</div>