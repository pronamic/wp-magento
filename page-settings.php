<div class="wrap">
	<?php screen_icon('magento'); ?>

	<h2>
		<?php echo esc_html('Magento'); ?>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields('magento'); ?>

		<h3 class="title">API</h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wsdl-field">WSDL</label>
				</th>
				<td>
					<input id="wsdl-field" name="magento-api-wsdl" value="<?php echo get_option('magento-api-wsdl'); ?>" type="text" class="regular-text" />

					<span class="description">
						http://domain.tld/api/?wsdl
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="store-url-field">Store URL</label>
				</th>
				<td>
					<input id="store-url-field" name="magento-store-url" value="<?php echo get_option('magento-store-url'); ?>" type="text" class="regular-text" />

					<span class="description">
						http://storedomain.ext/
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="username-field">Username</label>
				</th>
				<td>
					<input id="username-field" name="magento-api-username" value="<?php form_option('magento-api-username'); ?>" type="text" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="api-key-field">API Key</label>
				</th>
				<td>
					<input id="api-key-field" name="magento-api-key" value="<?php form_option('magento-api-key'); ?>" type="password" class="regular-text" />
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>