<div id="wrap">
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-menu">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"><i class="fa fa-youtube-play"></i> <?= $sitetitle ?> <span class="subtext"><?= $sitesubtitle ?></span></a>
			</div>

			<div id="navbar-collapse-menu" class="collapse navbar-collapse navbar-right">
				<ul class="nav navbar-nav">
					<li<?php
					if (!in_array($page, ['videos', 'stats', 'account'])) {
						echo ' class="active"';
					}
					?>><a href="?page=channels">Channels</a></li>
					<li<?php
					if (isset($_GET["videos"])) {
						echo ' class="active"';
					}
					?>><a href="?page=videos">Videos</a></li>
					<li<?php
					if (isset($_GET["stats"])) {
						echo ' class="active"';
					}
					?>><a href="?page=stats">Stats</a></li>
					<li<?php
					if (isset($_GET["account"])) {
						echo ' class="active"';
					}
					?>><a href="?page=account">Account</a></li>
					<li><a href="index.php?action=logout">Logout</a></li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</div>
	</nav>