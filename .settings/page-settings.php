<div class="wrap">
	<?php screen_icon('magento'); ?>

	<h2>
		<?php echo esc_html('Magento'); ?>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields('magento'); ?>

		<h3 class="title"><?php _e('API Settings', 'pronamic-magento-plugin') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wsdl-field"><?php _e('WSDL', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<input id="wsdl-field" name="magento-api-wsdl" value="<?php echo get_option('magento-api-wsdl'); ?>" type="text" class="regular-text" />

					<span class="description">
						<?php _e('http://domain.tld/api/?wsdl', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="store-url-field"><?php _e('Store URL', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<input id="store-url-field" name="magento-store-url" value="<?php echo get_option('magento-store-url'); ?>" type="text" class="regular-text" />

					<span class="description">
						<?php _e('http://storedomain.ext/', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="username-field"><?php _e('Username', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<input id="username-field" name="magento-api-username" value="<?php form_option('magento-api-username'); ?>" type="text" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="api-key-field"><?php _e('API Key', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<input id="api-key-field" name="magento-api-key" value="<?php form_option('magento-api-key'); ?>" type="password" class="regular-text" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="caching-field"><?php _e('Caching', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>					
					<label><input value="1" <?php checked(get_option('magento-caching-option'), 1); ?> type="checkbox" id="caching-field" name="magento-caching-option" class="" />
					
					<span class="description">
						<?php _e('Should we save product results returned by the Magento API? Saving results increases loading performance.', 'pronamic-magento-plugin') ?>
					</span></label>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>