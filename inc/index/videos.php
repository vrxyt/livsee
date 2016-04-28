<div class="mdl-grid mdl-grid--no-spacing">
	<div class="mdl-grid mdl-cell mdl-cell--9-col-desktop mdl-cell--12-col-tablet mdl-cell--4-col-phone mdl-cell--top mdl-cell--stretch">
		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
			<?php
			$videos = glob("/var/tmp/rec/*.mp4");
			if (count($videos) > 0) {
				foreach ($videos as $key => $video) {
					$file = substr($video, strrpos($video, "/") + 1);
					$skey = substr($file, 0, strrpos($file, "-"));
					$skey = $user->updateStreamkey($skey, 'channel');
					$timestamp = substr($file, strrpos($file, "-") + 1, -4);
					$datetime = date("Y-m-d H:i:s", $timestamp);
					$screenshot = 'img/video_' . str_replace('mp4', 'png', $file);
					if (!file_exists($screenshot)) {
						$screenshot = 'img/no-preview.jpg';
					}

					$mediainfo = array();
					try {
						$mediainfo = mediainfo::fetchVideo($video);

						// eval FPS to get rid of fractions:
						// - 30/1 becomes 30
						// - 2997/100 becomes 29.97

						eval('$mediainfo["streams"][0]["r_frame_rate"] = ' . $mediainfo["streams"][0]["r_frame_rate"] . ';');
					} catch (Exception $e) {
						print 'Cauth Exception with message ' . $e->getMessage();
					}

					$videos[$key] = ["file" => $file, "screenshot" => $screenshot, "channel" => $skey, "timestamp" => $timestamp, "datetime" => $datetime, "mediainfo" => $mediainfo];
				}
				?>
				<div class="mdl-tabs__tab-bar">
					<a href="#grid-view" class="mdl-tabs__tab is-active">Grid View</a>
					<a href="#list-view" class="mdl-tabs__tab">List View</a>
				</div>

				<div class="mdl-tabs__panel is-active" id="grid-view">
					<div class="mdl-grid">
						<?php
						foreach ($videos as $key => $video) {
							echo '<div class="mdl-cell mdl-cell--4-col">' . "\r\n";
							echo '	<div class="grid"><a href="/video/' . $video["file"] . '"><figure class="effect-sarah">' . "\r\n";
							echo '			<img src="' . $video["screenshot"] . '" alt="' . $video["channel"] . '">' . "\r\n";
							echo "			<figcaption>\r\n";
							echo "				<h2>" . $video['channel'] . "</h2>\r\n";
							echo "				<p>" . $video['datetime'] . "</p>\r\n";
							echo "			</figcaption>\r\n";
							echo "		</figure></a>\r\n";
							echo '</div></div>' . "\r\n";
						}
						?>


					</div></div>
				<div class="mdl-tabs__panel" id="list-view">
					<div class="mdl-grid">
						<div class="mdl-cell mdl-cell--4-col"></div>
						<div class="mdl-cell mdl-cell--4-col">
							<table class="mdl-data-table mdl-js-data-table full-width">
								<thead>
									<tr>
										<th class="mdl-data-table__cell--non-numeric">Channel</th>
										<th class="mdl-data-table__cell--non-numeric">Date</th>
										<th class="mdl-data-table__cell--non-numeric">Duration</th>
										<th class="mdl-data-table__cell--non-numeric">Definition</th>
										<th class="mdl-data-table__cell--non-numeric">Size</th>
										<th class="mdl-data-table__cell--non-numeric">Download</th>
										<th class="mdl-data-table__cell--non-numeric">Play</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($videos as $video) {
										$seed = uniqid();
										echo '<tr>' . "\r\n";
										echo '	<td class="mdl-data-table__cell--non-numeric">' . $video["channel"] . '</td>' . "\r\n";
										echo '	<td class="mdl-data-table__cell--non-numeric">' . date("Y-m-d H:i:s", $video["timestamp"]) . '</td>' . "\r\n";
										echo '	<td>' . date("H:i:s", $video["mediainfo"]["format"]["duration"]) . '</td>' . "\r\n";
										echo '	<td class="mdl-data-table__cell--non-numeric" id="stream-detail-' . $seed . '">' . "\r\n";
										echo '		' . $video["mediainfo"]["streams"][0]["height"] . 'p@' . $video["mediainfo"]["streams"][0]["r_frame_rate"] . 'fps' . "\r\n";
										echo '	</td>' . "\r\n";
										echo '	<td>' . bytesConvert($video["mediainfo"]["format"]["size"]) . 'B</td>' . "\r\n";
										echo '	<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="/download/' . $video["file"] . '"><i class="material-icons">file_download</i></a></td>' . "\r\n";
										echo '	<td class="mdl-data-table__cell--non-numeric mdl-typography--text-center"><a href="/video/' . $video["file"] . '"><i class="material-icons">play_circle_filled</i></a></td>' . "\r\n";
										echo '</tr>';
										echo '<div class="mdl-tooltip mdl-tooltip--large" for="stream-detail-' . $seed . '">';
										echo '	<p>Video</p>';
										echo '	<ul class="mdl-list">';
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . $video["mediainfo"]["streams"][0]["codec_name"] . ' ' . $video["mediainfo"]["streams"][0]["profile"] . '</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Bitrate: ' . bitsConvert($video["mediainfo"]["streams"][0]["bit_rate"]) . 'b/s</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Definition: ' . $video["mediainfo"]["streams"][0]["width"] . '*' . $video["mediainfo"]["streams"][0]["height"] . '</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-light">Framerate: ' . $video["mediainfo"]["streams"][0]["r_frame_rate"] . ' fps</li>' . "\r\n";
										echo '	</ul>' . "\r\n";
										echo '	<br /><p>Audio</p>';
										echo '	<ul class="mdl-list">';
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Codec: ' . $video["mediainfo"]["streams"][1]["codec_name"] . '</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Bitrate: ' . bitsConvert($video["mediainfo"]["streams"][1]["bit_rate"]) . 'b/s</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Sample Rate: ' . $video["mediainfo"]["streams"][1]["sample_rate"] . ' Hz</li>' . "\r\n";
										echo '		<li class="mdl-typography--caption mdl-typography--text-left mdl-typography--font-thin">Channels: ' . $video["mediainfo"]["streams"][1]["channels"] . '</li>' . "\r\n";
										echo '	</ul>' . "\r\n";
										echo '</div>' . "\r\n";
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
				<div class="mdl-card mdl-shadow--2dp full-height">
					<div class="mdl-card__title mdl-card--expand">
						<h2 class="mdl-card__title-text">No videos available.</h2>
					</div>
					<div class="mdl-card__supporting-text">
						There are currently no recorded videos. Check back later, maybe someone will record something!
					</div>
					<div class="mdl-card__actions mdl-card--border">
						<a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"href="/channels"><i class="material-icons">refresh</i> Refresh</a>
						</a>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>