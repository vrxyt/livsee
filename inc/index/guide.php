<div class="mdl-grid mdl-grid--no-spacing">
	<div class="mdl-content">
		<h1>How to Configure OBS Studio.</h1>

		<ul class='mdl-list'>
			<li class="mdl-list__item">
				<span class="mdl-list__item-primary-content">Open OBS Studio, and click on Settings.</span>
			</li>
			<li class="mdl-list__item">
				<span class="mdl-list__item-primary-content">Click on the Stream tab.</span>
			</li>
			<li class="mdl-list__item">
				<span class="mdl-list__item-primary-content">For Stream Type, select Custom Streaming Server from the dropdown.</span>
			</li>
			<li class="mdl-list__item">
				<span class="mdl-list__item-primary-content">For URL, enter: rtmp://<?= $surl ?>/live</span>
			</li>
			<li class="mdl-list__item mdl-list__item--two-line">
				<span class="mdl-list__item-primary-content">
					<span>for <b>Stream Key</b>, you must enter your current Display Name and Stream Key as follows:</span>
					<span class="mdl-list__item-sub-title mdl-color-text--cyan-500"><?= $accountinfo['display_name'] ?>?key=<?= $accountinfo['stream_key'] ?></span>
				</span>
			</li>
			<li class="mdl-list__item">
				<span class="mdl-list__item-primary-content">Note: if you update your Display Name, you will need to update it in the stream key in OBS.</span>
			</li>
		</ul>
	</div>
</div>