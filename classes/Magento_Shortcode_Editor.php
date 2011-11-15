<?php
/**
 * This class makes it easy for the user to
 * add custom shortcode, so he or she doesn't 
 * have to write it themselfs.
 * Based on the Gravity Forms script.
 * 
 * @author Stefan Boonstra
 * @version 2011
 */
class Magento_Shortcode_Editor {
	/**
	 * Adds an edit shortcode button to the mediabar
	 * 
	 * @param $context
	 */
	public static function addMagentoShortcodeButton($context){
		$imagebutton = plugins_url('images/icon-16x16.png', __DIR__);
		$out = '<a href="#TB_inline?width=450&inlineId=createsmagentoshortcode" class="thickbox" title="'.__("Add Magento Shortcode", 'pronamic-magento-plugin').'"><img src="'.$imagebutton.'" alt="'.__("Add Magento Shortcode", 'pronamic-magento-plugin').'" /></a>';
		return $context . $out;
	}
	
	/**
	 * Builds the shortcode
	 */
	function addMagentoShortcodeButtonForm(){
		?>
		<script>
			function addMagentoShortcodeButtonForm(){
				var magento_pid = jQuery('#magento_pid').val();
				var magento_cat = jQuery('#magento_cat').val();
				var magento_cat_num = jQuery('#magento_cat_num').val();
				var magento_latest = jQuery('#magento_latest').is(':checked');
				var magento_latest_num = jQuery('#magento_latest_num').val();
				var magento_search = jQuery('#magento_search').val();
				var magento_search_num = jQuery('#magento_search_num').val();
				
				if(magento_pid == '' && magento_cat == '' && !magento_latest && magento_search == ''){
					alert('No field was filled, please fill at least one field.', 'pronamic_magento_plugin');
					return;
				}
				
				if(magento_cat_num == '' || magento_cat_num < 0) var magento_cat_num = 0;
				if(magento_latest_num == '' || magento_latest_num < 0) var magento_latest_num = 0;
				if(magento_search_num == '' || magento_search_num < 0) var magento_search_num = 0;
				
				if(magento_pid != '') var magento_pid = " pid='"+magento_pid+"'";
				if(magento_cat != '') var magento_cat = " cat='"+magento_cat+", "+magento_cat_num+"'";
				if(magento_latest) var magento_latest = " latest='"+magento_latest_num+"'"; else magento_latest = '';
				if(magento_search != '') var magento_search = " name_like='"+magento_search+", "+magento_search_num+"'";
				
				window.send_to_editor("[magento"+magento_pid+magento_cat+magento_latest+magento_search+"]");
			}
		</script>
		
		<div id="createsmagentoshortcode" style="display:none;">
			<div class="wrap">
				<div>
					<div style="padding:15px 15px 0 15px;">
						<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e('Magento Shortcode Editor', 'pronamic-magento-plugin'); ?></h3>
						<span>
							<?php _e('Feel free to leave fields empty. Fields left empty are not added to the shortcode.', 'pronamic-magento-plugin'); ?>
						</span>
					</div>
					<div style="padding:15px 15px 0 15px;">						
						<hr />
						<input id="magento_pid" type="text" />
						<label for="magento_pid"><?php _e('Product ID\'s or SKU\'s. Comma separated when adding multiple.', 'pronamic_magento_plugin'); ?></label>
						<hr />
						<input id="magento_cat" type="text" />
						<label for="magento_cat"><?php _e('Category name or ID.', 'pronamic_magento_plugin'); ?></label><br />
						<input id="magento_cat_num" type="text" />
						<label for="magento_cat_num"><?php _e('Number of products shown in the category.', 'pronamic_magento_plugin'); ?></label>
						<hr />
						<input id="magento_latest" type="checkbox" />
						<label for="magento_latest"><?php _e('Show latest products.', 'pronamic_magento_plugin'); ?></label><br />
						<input id="magento_latest_num" type="text" />
						<label for="magento_latest_num"><?php _e('Number of latest products shown.', 'pronamic_magento_plugin'); ?></label>
						<hr />
						<input id="magento_search" type="text" />
						<label for="magento_search"><?php _e('Search for a products with a similar name.', 'pronamic_magento_plugin'); ?></label><br />
						<input id="magento_search_num" type="text" />
						<label for="magento_seacrh_num"><?php _e('Number of found products shown.', 'pronamic_magento_plugin'); ?></label>
						<hr />
						
						<div style="padding:15px;">
							<input type="button" class="button-primary" value="Insert Form" onclick="addMagentoShortcodeButtonForm();"/>&nbsp;&nbsp;&nbsp;
							<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e('Cancel', 'pronamic-magento-plugin'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
?>