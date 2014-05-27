<div class="wrap">
    <h3>Prophet Regwall Settings</h3>
	<form method="post" action="options.php">
		<?php settings_fields( 'pbs_regwall_option_group' ); ?>
		<?php do_settings_sections( 'pbs_regwall_option_group' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Cookie Name</th>
				<td>
					<input type="text" name="cookie_name" value="<?php echo get_option("cookie_name") ?>">
					<small>Make sure these match your HTML/JS implementation. Leave empty for default value (<i>regflag</i>).</small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Cookie Value</th>
				<td>
					<input type="text" name="cookie_value" value="<?php echo get_option("cookie_value") ?>">
					<small>Make sure these match your HTML/JS implementation. Leave empty for default value (<i>ok</i>).</small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Force Regwall?</th>
				<td>
					<input type="checkbox" name="force_regwall_for_loggedin_users" id="force_regwall_for_loggedin_users" value="1"<?php echo (get_option('force_regwall_for_loggedin_users')) == 1 ? " checked='checked'" : ""; ?>" />
					<label for="force_regwall_for_loggedin_users">Force Regwall for users that are logged in (for testing purposes)</label>
				</td>
			</tr>
			<tr>
				<th scope="row">Regwall HTML (leave empty for default)</th>
				<td>
					<?php wp_editor( get_option("regwall_html"), "regwall_html"); ?>
					<br><small><strong>Note:</strong> You can also directly modify the HTML/CSS/JS files in the plugins sub-folders <code>includes/</code> and <code>static/</code>.</small>
				</td>
			</tr>
    	</table>
		<?php submit_button() ?>
	</form>
</div>