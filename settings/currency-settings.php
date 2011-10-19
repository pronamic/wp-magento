<div class="wrap">
	<?php screen_icon('magento'); ?>

	<h2>
		<?php echo esc_html('Magento'); ?>
	</h2>

	<form method="post" action="options.php">
		<?php settings_fields('magento-currency'); ?>

		<h3 class="title"><?php _e('Currency Settings', 'pronamic-magento-plugin') ?></h3>

		<table class="form-table">
			<?php 
				$currencies = array('' => __('Don\'t display a currency logo', 'pronamic-magento-plugin'),
					'USD' => __('US Dollars (&#36;)', 'pronamic-magento-plugin'),
					'EUR' => __('Euros (&euro;)', 'pronamic-magento-plugin'),
					'GBP' => __('Pounds Sterling (&pound;)', 'pronamic-magento-plugin'),
					'AUD' => __('Australian Dollars (&#36;)', 'pronamic-magento-plugin'),
					'BRL' => __('Brazilian Real (&#36;)', 'pronamic-magento-plugin'),
					'CAD' => __('Canadian Dollars (&#36;)', 'pronamic-magento-plugin'),
					'CZK' => __('Czech Koruna', 'pronamic-magento-plugin'),
					'DKK' => __('Danish Krone', 'pronamic-magento-plugin'),
					'HKD' => __('Hong Kong Dollar (&#36;)', 'pronamic-magento-plugin'),
					'HUF' => __('Hungarian Forint', 'pronamic-magento-plugin'),
					'ILS' => __('Israeli Shekel', 'pronamic-magento-plugin'),
					'JPY' => __('Japanese Yen (&yen;)', 'pronamic-magento-plugin'),
					'MYR' => __('Malaysian Ringgits', 'pronamic-magento-plugin'),
					'MXN' => __('Mexican Peso (&#36;)', 'pronamic-magento-plugin'),
					'NZD' => __('New Zealand Dollar (&#36;)', 'pronamic-magento-plugin'),
					'NOK' => __('Norwegian Krone', 'pronamic-magento-plugin'),
					'PHP' => __('Philippine Pesos', 'pronamic-magento-plugin'),
					'PLN' => __('Polish Zloty', 'pronamic-magento-plugin'),
					'SGD' => __('Singapore Dollar (&#36;)', 'pronamic-magento-plugin'),
					'SEK' => __('Swedish Krona', 'pronamic-magento-plugin'),
					'CHF' => __('Swiss Franc', 'pronamic-magento-plugin'),
					'TWD' => __('Taiwan New Dollars', 'pronamic-magento-plugin'),
					'THB' => __('Thai Baht', 'pronamic-magento-plugin'), 
					'TRY' => __('Turkish Lira (TL)', 'pronamic-magento-plugin')
				);
			?>		
			<tr valign="top">
				<th scope="row">
					<label for="currency-field"><?php _e('Currency', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<select id="currency-field" name="magento-currency-setting">
						<?php foreach($currencies as $key => $value): ?>
							<option value="<?php echo $key; ?>" <?php selected(get_option('magento-currency-setting'), $key); ?>><?php echo $value ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php 
				$positions = array('left' => __('Left', 'pronamic-magento-plugin'),
					'right' => __('Right', 'pronamic-magento-plugin'),
					'left_space' => __('Left (with space)', 'pronamic-magento-plugin'),
					'right_space' => __('Right (with space)', 'pronamic-magento-plugin')
				);
			?>
			<tr valign="top">
				<th scope="row">
					<label for="currency-position-field"><?php _e('Currency position', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<select id="currency-position-field" name="magento-currency-position">
						<?php foreach($positions as $key => $value): ?>
							<option value="<?php echo $key; ?>" <?php selected(get_option('magento-currency-position'), $key); ?>><?php echo $value ?></option>
						<?php endforeach; ?>
					</select>

					<span class="description">
						<?php _e('Set the currency\'s location towards the price.', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="number-decimals-field"><?php _e('Number of decimals', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<?php if(get_option('magento-number-decimals')): ?>
						<input id="number-decimals-field" name="magento-number-decimals" value="<?php form_option('magento-number-decimals'); ?>" type="text" class="" size=1 />
					<?php else: ?>
						<input id="number-decimals-field" name="magento-number-decimals" value="2" type="text" class="" size=1 />
					<?php endif; ?>
					<span class="description">
						<?php _e('Number of digits after the decimal separator.', 'pronamic-magento-plugin') ?>
					</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="decimal-separator-field"><?php _e('Decimal separator', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<?php if(get_option('magento-decimal-separator')): ?>
						<input id="decimal-separator-field" name="magento-decimal-separator" value="<?php form_option('magento-decimal-separator'); ?>" type="text" class="" size=1 maxlength=1 />
					<?php else: ?>
						<input id="decimal-separator-field" name="magento-decimal-separator" value="." type="text" class="" size=1 maxlength=1 />
					<?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="thousands-separator-field"><?php _e('Thousands separator', 'pronamic-magento-plugin') ?></label>
				</th>
				<td>
					<?php if(get_option('magento-thousands-separator')): ?>
						<input id="thousands-separator-field" name="magento-thousands-separator" value="<?php form_option('magento-thousands-separator'); ?>" type="text" class="" size=1 maxlength=1 />
					<?php else: ?>
						<input id="thousands-separator-field" name="magento-thousands-separator" value="," type="text" class="" size=1 maxlength=1 />
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>