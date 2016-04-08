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
				$streamkey = $user->updateStreamkey($streamkey, 'channel');
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