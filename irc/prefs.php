<?php
	set_include_path(get_include_path() . PATH_SEPARATOR .
		dirname(__FILE__) ."/include");

	require_once "functions.php";

	function css_editor($link) {
		$user_css = get_pref($link, "USER_STYLESHEET");
	?>
	<div class="modal-header">
		<button type="button" onclick="close_infobox()" class="close">&times;</button>
		<h4 class="modal-title"><?php echo __("Customize Theme") ?></h4>
	</div>
	<div class="modal-body">
		<div class="alert alert-info" id="mini-notice" style='display : none'>&nbsp;</div>

		<form class="form" id="prefs_css_form" onsubmit="return false;">

		<input type="hidden" name="op" value="prefs-save-css"/>

		<div class="alert alert-info"><?php echo T_sprintf("You can override colors, fonts and layout of your currently selected theme with custom CSS declarations here. <a target=\"_blank\" href=\"%s\">This file</a> can be used as a baseline.", "tt-irc.css") ?></div>

		<textarea name="user_css" rows="10" class="form-control"><?php echo $user_css ?></textarea>

		</form>

	</div>

	<div class="modal-footer">
		<button class="btn btn-primary" type="submit" onclick="save_css()"><?php echo __('Save & Reload') ?></button>
		<button class="btn btn-default" type="submit" onclick="show_prefs()"><?php echo __('Go back') ?></button></div>
	</div>

	</div>
	<?php

	}

	function print_servers($link, $id) {
		$result = db_query($link, "SELECT ttirc_servers.*,
				status,active_server
			FROM ttirc_servers,ttirc_connections
			WHERE connection_id = '$id' AND
			connection_id = ttirc_connections.id AND
			owner_uid = " . $_SESSION["uid"]);

		$lnum = 1;

		while ($line = db_fetch_assoc($result)) {

			$id = $line['id'];

			if ($line['status'] != CS_DISCONNECTED &&
					$line['server'] . ':' . $line['port'] == $line['active_server']) {
				$connected = __("(connected)");
			} else {
				$connected = '';
			}

			print "<li class='row' id='S-$id' server_id='$id'>";
			print "<div class='checkbox'><label>";
			print "<input type='checkbox' onchange='select_row(this)'
				row_id='S-$id'>";
			print $line['server'] . ":" . $line['port'] . " $connected";
			print "</label></div>";
			print "</li>";

			++$lnum;
		}
	}

	function notification_editor($link) {

		$notify_on = json_decode(get_pref($link, "NOTIFY_ON"));

		if (!is_array($notify_on)) $notify_on = array();

		$nev_checked = array();

		foreach ($notify_on as $no) {
			$nev_checked[$no] = "checked";
		}
	?>

	<div class="modal-header">
		<button type="button" onclick="close_infobox()" class="close">&times;</button>
		<h4 class="modal-title"><?php echo __("Notifications") ?></h4>
	</div>

	<div class="modal-body">
		<div id="mini-notice" class="alert alert-info" style='display : none'>&nbsp;</div>

		<div class="alert alert-info"><?php echo T_sprintf("Desktop notifications are only shown for events happening in background channels or when your Tiny Tiny IRC window is unfocused.") ?></div>


		<form class="form" id="prefs_notify_form" onsubmit="return false;">

		<input type="hidden" name="op" value="prefs-save-notify"/>

		<h5><?php echo __('Notify events') ?></h5>

		<div class="form-group">
			<div class="col-sm-6">

				<div class="checkbox"><label for="n_highlight">
					<input name="notify_event[]" <?php echo $nev_checked[1] ?>
						id="n_highlight" type="checkbox" value="1">
					<?php echo __('Channel highlight') ?>
				</label></div>

				<div class="checkbox"><label for="n_privmsg">
					<input name="notify_event[]" <?php echo $nev_checked[2] ?>
						id="n_privmsg" type="checkbox" value="2">
					<?php echo __('Private message') ?>
					</label></div>

				<div class="checkbox"><label for="n_connstat">
					<input name="notify_event[]" <?php echo $nev_checked[3] ?>
						id="n_connstat" type="checkbox" value="3">
					<?php echo __('Connection status change') ?>
				</label></div>

				<div class="checkbox"><label for="n_chanmsg">
					<input name="notify_event[]" <?php echo $nev_checked[4] ?>
						id="n_chanmsg" type="checkbox" value="4">
					<?php echo __('Channel message') ?>
				</label></div>
			</div>
		</div>

		</form>
	</div>

	<div class="modal-footer">
		<div style='float : left'>
			<button class="btn btn-default" type="submit" onclick="notify_enable()"><?php echo __('Enable notifications') ?></button></div>

			<button class="btn btn-primary" type="submit" onclick="save_notifications()"><?php echo __('Save') ?></button>
			<button class="btn btn-default" type="submit" onclick="show_prefs()"><?php echo __('Go back') ?></button></div>
		</div>

	</div>
	<?php

	}

	function connection_editor($link, $id) {
		$result = db_query($link, "SELECT * FROM ttirc_connections
			WHERE id = '$id' AND owner_uid = " . $_SESSION["uid"]);

		$line = db_fetch_assoc($result);

		if (sql_bool_to_bool($line['auto_connect'])) {
			$auto_connect_checked = 'checked';
		} else {
			$auto_connect_checked = '';
		}

		if (sql_bool_to_bool($line['visible'])) {
			$visible_checked = 'checked';
		} else {
			$visible_checked = '';
		}

		if (sql_bool_to_bool($line['permanent'])) {
			$permanent_checked = 'checked';
		} else {
			$permanent_checked = '';
		}

		if (sql_bool_to_bool($line['use_ssl'])) {
			$use_ssl_checked = 'checked';
		} else {
			$use_ssl_checked = '';
		}

	?>
	<div class="modal-header">
		<button type="button" onclick="close_infobox()" class="close">&times;</button>
		<h4 class="modal-title"><?php echo __("Edit Connection") ?></h4>
	</div>
	<div class="modal-body">
		<div class="alert alert-info" id="mini-notice" style='display : none'>&nbsp;</div>

		<form class="form form-horizontal" id="prefs_conn_form" onsubmit="return false;">

		<input type="hidden" name="connection_id" value="<?php echo $id ?>"/>
		<input type="hidden" name="op" value="prefs-conn-save"/>

 		<div class="form-group">
			<label class='col-sm-4 control-label'><?php echo __('Title:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="title" required="1" size="30" value="<?php echo $line['title'] ?>">
			</div>
		</div>

		<div class="form-group">
			<label class='col-sm-4 control-label'><?php echo __('Server password:') ?></label>
			<div class="col-sm-6">
				<input class="form-control" name="server_password" size="30" type="password"
					value="<?php echo $line['server_password'] ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Nickname:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="nick" size="30" value="<?php echo $line['nick'] ?>">
			</div>
		</div>

		<div class="form-group">
			<label class='col-sm-4 control-label'><?php echo __('Favorite channels:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="autojoin" size="30" value="<?php echo $line['autojoin'] ?>">
			</div>
		</div>

		<div class="form-group">
			<label class='col-sm-4 control-label'><?php echo __('Connect command:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="connect_cmd" size="30" value="<?php echo $line['connect_cmd'] ?>">
			</div>
		</div>

		<div class="form-group">
			<label class='col-sm-4 control-label'><?php echo __('Character set:') ?></label>
			<div class="col-sm-6">
				<?php print_select('encoding', $line['encoding'], get_iconv_encodings()) ?>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-4"></div>
			<div class="col-sm-6">

				<div class="checkbox"><label for="pr_visible">
					<input name="visible" <?php echo $visible_checked ?>
						id="pr_visible" type="checkbox" value="1">
					<?php echo __('Enable connection') ?>
				</label></div>

				<div class="checkbox"><label for="pr_auto_connect">
					<input name="auto_connect" <?php echo $auto_connect_checked ?>
						id="pr_auto_connect" type="checkbox" value="1">
					<?php echo __('Automatically connect') ?>
				</label></div>

				<div class="checkbox"><label for="pr_permanent">
					<input name="permanent" <?php echo $permanent_checked ?>
						id="pr_permanent" type="checkbox" value="1">
					<?php echo __('Stay connected permanently') ?>
				</label></div>

				<div class="checkbox"><label for="pr_use_ssl">
					<input name="use_ssl" <?php echo $use_ssl_checked ?>
						id="pr_use_ssl" type="checkbox" value="1">
					<?php echo __('Connect using SSL') ?>
				</label></div>
			</div>
		</div>

		<button type="submit" style="display : none" onclick="save_conn()"></button>

		<h5><?php echo __('Servers') ?></h5>

		<ul id="servers-list" class="list-unstyled scrollable">
			<?php print_servers($link, $id); ?>
		</ul>

		</form>

	</div>

	<div class="modal-footer">
			<div style='float : left'>
			<button class="btn btn-default" onclick="create_server()"><?php echo __('Add server') ?></button>
			<button class="btn btn-danger" onclick="delete_server()"><?php echo __('Delete') ?></button>
			</div>
			<button class="btn btn-primary" type="submit" onclick="save_conn()"><?php echo __('Save') ?></button>
			<button class="btn btn-default" type="submit" onclick="show_prefs()"><?php echo __('Go back') ?></button></div>
		</div>
	</div>

	<?php
	}

	function print_connections($link) {
		$result = db_query($link, "SELECT * FROM ttirc_connections
			WHERE owner_uid = " . $_SESSION["uid"]);

		$lnum = 1;

		while ($line = db_fetch_assoc($result)) {

			$id = $line['id'];

			if ($line["status"] != "0") {
				$connected = __("(active)");
			} else {
				$connected = "";
			}

			print "<li class='row' id='C-$id' connection_id='$id'>";
			print "<div class='checkbox'><label>";
			print "<input type='checkbox' onchange='select_row(this)'
				row_id='C-$id'>";
			print "<a href=\"#\" title=\"".__('Click to edit connection')."\"
				onclick=\"edit_connection($id)\">".
				$line['title']." $connected</a>";
			print "</label></div>";
			print "</li>";

			++$lnum;
		}
	}

	function main_prefs($link) {

	$_SESSION["prefs_cache"] = false;

	$result = db_query($link, "SELECT * FROM ttirc_users WHERE
		id = " . $_SESSION["uid"]);

	$realname = db_fetch_result($result, 0, "realname");
	$nick = db_fetch_result($result, 0, "nick");
	$email = db_fetch_result($result, 0, "email");
	$quit_message = db_fetch_result($result, 0, "quit_message");

	if (sql_bool_to_bool(db_fetch_result($result, 0, "hide_join_part"))) {
		$hide_join_part_checked = 'checked';
	} else {
		$hide_join_part_checked = '';
	}

	$highlight_on = get_pref($link, "HIGHLIGHT_ON");
	$disable_image_preview_checked = get_pref($link, "DISABLE_IMAGE_PREVIEW") ? "checked" : "";
?>

	<div class="modal-header">
		<button type="button" onclick="close_infobox()" class="close">&times;</button>
		<h4 class="modal-title"><?php echo __("Preferences") ?></h4 class="modal-title"></div>
	<div class="modal-body">

		<form class="form-horizontal" id="prefs_form" onsubmit="return false;">

		<div id="mini-notice" class="alert alert-info" style='display : none'>&nbsp;</div>

		<input type="hidden" name="op" value="prefs-save"/>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Real name:') ?></label>
			<div class="col-sm-6">
			<input class="form-control" type="text" name="realname" required="1" size="30" value="<?php echo $realname ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Nickname:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="nick" required="1" size="30" value="<?php echo $nick ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('E-mail:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="email" required="1" size="30" value="<?php echo $email ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Quit message:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="quit_message" size="30" value="<?php echo $quit_message ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Highlight on:') ?></label>
			<div class="col-sm-6">
				<input type="text" class="form-control" name="highlight_on" size="30" value="<?php echo $highlight_on ?>">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Theme:') ?></label>
			<div class="col-sm-6">
				<div class="input-group">
					<?php print_theme_select($link); ?>
					<a class="input-group-addon btn btn-default" href="#" onclick="customize_css()">
						<?php echo __("Customize") ?></a>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-4"></div>
			<div class="col-sm-6">
				<a class="btn btn-default" href="#" onclick="configure_notifications()"><?php echo __('Configure desktop notifications') ?></a>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-4"></div>
			<div class="col-sm-6">
				<div class="checkbox">
					<label><input name="hide_join_part" <?php echo $hide_join_part_checked ?>
						id="pr_hide_join_part" type="checkbox" value="1">
					<?php echo __('Do not highlight tabs on auxiliary messages') ?></label></div>

				<div class="checkbox">
					<label><input name="disable_image_preview" <?php echo $disable_image_preview_checked ?>
						id="pr_disable_image_preview" type="checkbox" value="1">
					<?php echo __('Disable inline image preview') ?></label></div>

			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo __('Change password:') ?></label>

			<div class="col-sm-6">
				<input placeholder="<?php echo __("New password") ?>" type="text" class="form-control" name="new_password" type="password" size="30" value="">
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-4"> </label>
			<div class="col-sm-6">
				<input placeholder="<?php echo __("Confirm password") ?>" type="text" class="form-control" name="confirm_password" type="password" size="30" value="">
			</div>
		</div>

		<h5><?php echo __('Connections') ?></h5>

		<ul class="scrollable list-unstyled" id="connections-list"><?php print_connections($link) ?></ul>

	</form>

	</div>

	<div class="modal-footer">
		<div style='float : left'>
			<button class="btn btn-default" onclick="create_connection()">
				<?php echo __('Create connection') ?></button class="btn">
			<button class="btn btn-danger" onclick="delete_connection()">
				<?php echo __('Delete') ?></button class="btn">
		</div>
		<button class="btn btn-primary" type="submit" onclick="save_prefs()">
			<?php echo __('Save') ?></button class="btn">
		<button class="btn btn-default" onclick="close_infobox()"><?php echo __('Close') ?></button>
	</div>

<?php } ?>
