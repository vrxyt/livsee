<?php
/*
 * ---------------------------------------------------------------
 * Account info/update page
 * ---------------------------------------------------------------
 *
 * This page is used for loading the account information,
 * as well as allowing updates to channel name, channel title,
 * and display name.
 * 
 * TODO:
 *
 *		-Password change/reset
 *		-Custom channel page images
 *		-Custom profile images
 * 		-Better admin function
 *
 */

// run account info update if data was posted
if (!empty($_POST)) {
	$channelname = filter_input(INPUT_POST, 'channelname', FILTER_SANITIZE_STRING);
	$channeltitle = filter_input(INPUT_POST, 'channeltitle', FILTER_SANITIZE_STRING);
	$displayname = filter_input(INPUT_POST, 'displayname', FILTER_SANITIZE_STRING);
	$chatjp = filter_input(INPUT_POST, 'chatjp', FILTER_VALIDATE_BOOLEAN);
	$chatjp = $chatjp ? 't':'f';
	$status = $user->update($email, $channelname, $channeltitle, $displayname, $chatjp);
	// get new account info after the update
	$accountinfo = $user->info($email);
}
?>
<div class="mdl-grid mdl-grid--no-spacing">
	<div class="mdl-content__full-height">
		<div class="mdl-card mdl-shadow--2dp employer-form">
			<div class="mdl-card__title">
				<span>Account Settings</span>
			</div>
			<div class="mdl-card__supporting-text">
				<form action="" method="POST" class="form" id="settingsForm">
					<div class="form__article">
						
						<div class="mdl-grid">
							<ul class="mdl-list fill">
								<li class="mdl-list__item mdl-list__item--two-line">
									<span class="mdl-list__item-primary-content">
										<i class="material-icons mdl-list__item-icon">email</i>
										<span>Email</span>
										<span class="mdl-list__item-sub-title"><?= $accountinfo['email']; ?></span>
									</span>
								</li>
								<li class="mdl-list__item mdl-list__item--two-line">
									<span class="mdl-list__item-primary-content">
										<i class="material-icons mdl-list__item-icon">vpn_key</i>
										<span>Stream Key <a href="/guide">(click here for guide)</a></span>
										<span class="mdl-list__item-sub-title"><?= $accountinfo['stream_key']; ?></span>
									</span>
								</li>
								<li class="mdl-list__item mdl-list__item--two-line">
									<span class="mdl-list__item-primary-content">
										<i class="material-icons mdl-list__item-icon">vpn_key</i>
										<span>API Key <a href="https://github.com/Fenrirthviti/stream-site/blob/master/api/API_DOCS" target="_blank">(click here for docs)</a></span>
										<span class="mdl-list__item-sub-title"><?= $accountinfo['api_key']; ?></span>
									</span>
								</li>
							</ul>
						</div>

						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="text" name="displayname" id="displayName" value="<?= $accountinfo['display_name'] ?>"/>
								<label class="mdl-textfield__label" for="displayName">Display Name</label>
							</div>
						</div>

						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="text" name="channelname" id="channelName" value="<?= $accountinfo['channel_name'] ?>"/>
								<label class="mdl-textfield__label" for="channelName">Channel Name</label>
							</div>
						</div>

						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
								<input class="mdl-textfield__input" type="text" name="channeltitle" id="channelTitle" value="<?= $accountinfo['channel_title'] ?>"/>
								<label class="mdl-textfield__label" for="channelTitle">Channel Title</label>
							</div>
						</div>

						<div class="mdl-grid">
							<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="chatjp">
								<input type="checkbox" id="chatjp" class="mdl-switch__input" name="chatjp" value=true <?php if ($accountinfo['chat_jp_setting'] === 't') { echo 'checked'; }?>>
								<span class="mdl-switch__label">Show Join/Part in Chats</span>
							</label>
						</div>

						<div class="form__action">
							<button type="submit" name="Submitted" value="Submit" form="settingsForm" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">
								Update
							</button>
						</div>
						
						<div class="mdl-grid">
							<div class="mdl-cell mdl-cell--12-col mdl-typography--text-center">
								<?php
								if (!empty($status)) {
									echo '<br />' . $status;
								}
								?>
							</div>
						</div>						
					</div>
				</form>
			</div>
		</div>

		<?php
		if ($accountinfo['email'] === $admin_account) {
			$results = $user->admindata($accountinfo['email'])
			?>
			<div class="mdl-grid">
				<div class="mdl-cell mdl-cell--12-col">
					<table class="mdl-data-table mdl-js-data-table">
						<thead>
							<tr>
								<?php
								$arraykeys = array_keys($results[0]);
								foreach ($arraykeys as $cell):
									?>
									<th class="mdl-data-table__cell--non-numeric"><?= $cell ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($results as $row): ?>
								<tr>
									<?php foreach ($row as $cell): ?>
										<td class="mdl-data-table__cell--non-numeric"><?= $cell ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php } ?>
	</div>
</div>