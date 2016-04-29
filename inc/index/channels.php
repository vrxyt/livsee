<div class="mdl-grid mdl-grid--no-spacing">
	<div class="mdl-grid mdl-cell mdl-cell--12-col-desktop mdl-cell--12-col-tablet mdl-cell--4-col-phone mdl-cell--top mdl-cell--stretch">
		<?php
		if (count($rtmpinfo["rtmp"]["channels"]) > 0) {
			$channels = array();
			foreach ($rtmpinfo["rtmp"]["channels"] as $channelName => $skey) {
				$channels[$channelName] = $skey;
				$screenshotThumbFilename = 'thumb_' . $channelName . '.png';
				$channels[$channelName]["screenshot"] = 'profiles/' . $channelName . '/' . $screenshotThumbFilename;
				if (file_exists($channels[$channelName]["screenshot"])) {
					$channels[$channelName]["screenshot"] = '/' . $channels[$channelName]["screenshot"];
				}
				elseif (file_exists('img/thumbs/' . $screenshotThumbFilename)) {
					$channels[$channelName]["screenshot"] = '/img/thumbs/' . $screenshotThumbFilename;
				}
				else {
					$channels[$channelName]["screenshot"] = '/img/no-preview.jpg';
				}
				$mediainfo = array();
				$channels[$channelName]["mediainfo"] = $mediainfo;
			}
			?>
			<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
				<div class="mdl-tabs__tab-bar">
					<a href="#grid-view" class="mdl-tabs__tab is-active">Grid View</a>
					<a href="#list-view" class="mdl-tabs__tab">List View</a>
				</div>

				<div class="mdl-tabs__panel is-active" id="grid-view">
					<div class="mdl-grid">
						<?php
						foreach ($channels as $channelName => $skey) {
							$viewcount = trim(file_get_contents($furl . '/nclients?app=live&name=' . $channelName));

							$cname = $user->updateStreamkey($channelName, 'channel');
							$ctitle = $user->updateStreamkey($channelName, 'title');
							echo '
							<div class="mdl-cell mdl-cell--4-col">
								<div class="grid">
									<a href="/watch/' . $channelName . '">
										<figure class="effect-sarah">
											<img src="' . $skey['screenshot'] . '" alt="' . $cname . '" />
											<figcaption>
												<h2>' . $cname . '</h2>
												<p>' . $ctitle . '</p>
												<label><i class="material-icons">visibility</i> <span>' . $viewcount . '</span></label>
											</figcaption>
										</figure>
									</a>
								</div>
							</div>
							';
						}
						?>


					</div>
				</div>
				<div class="mdl-tabs__panel" id="list-view">
					<div class="mdl-grid">
						<div class="mdl-cell mdl-cell--4-col"></div>
						<div class="mdl-cell mdl-cell--4-col">
							<table class="mdl-data-table mdl-js-data-table full-width">
								<thead>
									<tr>
										<th class="mdl-data-table__cell--non-numeric part-width">Channel</th>
										<th class="mdl-data-table__cell--non-numeric part-width">Duration</th>
										<th class="mdl-data-table__cell--non-numeric">Viewers</th>
										<th class="mdl-data-table__cell--non-numeric">Definition</th>
										<th class="mdl-data-table__cell--non-numeric">Record</th>
										<th class="mdl-data-table__cell--non-numeric">Watch</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($channels as $channelName => $skey) {
										$seed = uniqid();
										$viewcount = file_get_contents($furl . '/nclients?app=live&name=' . $channelName);
										
										$cname = $user->updateStreamkey($channelName, 'channel');
										echo '
										<tr>
											<td class="mdl-data-table__cell--non-numeric"><a href="/watch/' . $channelName . '">' . $cname . '</a></td>
											<td class="mdl-data-table__cell--non-numeric">' . gmdate("H:i:s", ($skey["time"] / 1000)) . '</td>
											<td>' . $viewcount . ' watching</td>
											<td class="mdl-data-table__cell--non-numeric" id="stream-detail-' . $seed . '">' . $skey["meta"]["video"]["height"] . 'p@' . $skey["meta"]["video"]["frame_rate"] . 'fps</td>
											<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="api/call/goes/here"><i class="material-icons" role="presentation">videocam</i></a></td>
											<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="/watch/' . $channelName . '"><i class="material-icons" role="presentation">play_circle_filled</i></a></td>
										</tr>
										<div class="mdl-tooltip mdl-tooltip--large" for="stream-detail-' . $seed . '">
											<p>Video</p>
											<ul class="mdl-list">
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . $skey["meta"]["video"]["codec"] . ' ' . $skey["meta"]["video"]["profile"] . '</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Bitrate: ' . bitsConvert($skey["bw_video"]) . 'b/s</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Definition: ' . $skey["meta"]["video"]["width"] . '*' . $skey["meta"]["video"]["height"] . '</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Framerate: ' . $skey["meta"]["video"]["frame_rate"] . ' fps</li>
											</ul>
											<br />
											<p>Audio</p>
											<ul class="mdl-list">
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . $skey["meta"]["audio"]["codec"] . ' ' . $skey["meta"]["audio"]["profile"] . '</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Bitrate: ' . bitsConvert($skey["bw_audio"]) . 'b/s</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Sample Rate: ' . $skey["meta"]["audio"]["sample_rate"] . ' Hz</li>
												<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Channels: ' . $skey["meta"]["audio"]["channels"] . '</li>
											</ul>
										</div>';
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="mdl-cell mdl-cell--4-col"></div>
					</div>
				</div>
	<?php
} else {
	?>
				<div class="mdl-grid">
					<div class="mdl-grid mdl-cell--4-col">
						<div class="mdl-card mdl-shadow--2dp">
							<div class="mdl-card__title mdl-card--expand">
								<h2 class="mdl-card__title-text">No channels available.</h2>
							</div>
							<div class="mdl-card__supporting-text">
								There is currently no one streaming. This page will (eventually when I fix it!) automatically refresh when someone goes live, or you can click the refresh button below to check manually.
							</div>
							<div class="mdl-card__actions mdl-card--border">
								<a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"href="/channels"><i class="material-icons">refresh</i> Refresh</a>
								</a>
							</div>
						</div>
					</div>
				</div>
	<?php
}
?>
		</div>
	</div>
</div>
