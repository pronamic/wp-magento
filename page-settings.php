<div class="wrap">
	<?php screen_icon('magento'); ?>

	<h2>
		<?php echo esc_html('Magento'); ?>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields('magento'); ?>

		<h3 class="title"><?php _e('API', 'pronamic-magento-plugin') ?></h3>

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
					<select id="caching-field" name="magento-caching-option" class="regular-text">
						<option value="0" <?php if(!get_option('magento-caching-option')) echo 'selected="selected"'; ?>><?php _e('No', 'pronamic-magento-plugin') ?></option>
						<option value="1" <?php if(get_option('magento-caching-option')) echo 'selected="selected"'; ?>><?php _e('Yes', 'pronamic-magento-plugin') ?></option>
					</select>
					
					<span class="description">
						<?php _e('Should we save product results returned by the Magento API? Saving results increases loading performance.', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<?php if(get_option('magento-caching-option')): ?>
			<tr valign="top">
				<th scope="row">
					<label for="caching-field"><?php _e('Cache lifetime', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<select id="caching-field" name="magento-caching-time" class="regular-text">
						<option value="15" <?php if(get_option('magento-caching-time') == '15') echo 'selected="selected"'; ?>><?php _e('Fifteen seconds', 'pronamic-magento-plugin') ?></option>
						<option value="60" <?php if(get_option('magento-caching-time') == '60') echo 'selected="selected"'; ?>><?php _e('One minute', 'pronamic-magento-plugin') ?></option>
						<option value="600" <?php if(get_option('magento-caching-time') == '600') echo 'selected="selected"'; ?>><?php _e('Ten minutes', 'pronamic-magento-plugin') ?></option>
						<option value="1800" <?php if(get_option('magento-caching-time') == '1800') echo 'selected="selected"'; ?>><?php _e('Thirty minutes', 'pronamic-magento-plugin') ?></option>
						<option value="3600" <?php if(get_option('magento-caching-time') == '3600') echo 'selected="selected"'; ?>><?php _e('One hour', 'pronamic-magento-plugin') ?></option>
						<option value="7200" <?php if(get_option('magento-caching-time') == '7200') echo 'selected="selected"'; ?>><?php _e('Two hours', 'pronamic-magento-plugin') ?></option>
						<option value="14400" <?php if(get_option('magento-caching-time') == '14400') echo 'selected="selected"'; ?>><?php _e('Four hours', 'pronamic-magento-plugin') ?></option>
						<option value="28800" <?php if(get_option('magento-caching-time') == '28800') echo 'selected="selected"'; ?>><?php _e('Eight hours', 'pronamic-magento-plugin') ?></option>
						<option value="57600" <?php if(get_option('magento-caching-time') == '57600') echo 'selected="selected"'; ?>><?php _e('Sixteen hours', 'pronamic-magento-plugin') ?></option>
						<option value="86400" <?php if(get_option('magento-caching-time') == '86400') echo 'selected="selected"'; ?>><?php _e('One day', 'pronamic-magento-plugin') ?></option>
						<option value="259200" <?php if(get_option('magento-caching-time') == '259200') echo 'selected="selected"'; ?>><?php _e('Three days', 'pronamic-magento-plugin') ?></option>
						<option value="640800" <?php if(get_option('magento-caching-time') == '640800') echo 'selected="selected"'; ?>><?php _e('One week', 'pronamic-magento-plugin') ?></option>
					</select>
					
					<?php include_once('CacheClass.php'); CacheClass::setReset(); ?>
					
					<span class="description">
						<?php _e('Timespan to save cache entry.', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<?php endif; ?>
		</table>
		<?php submit_button(); ?>
	</form>
</div>