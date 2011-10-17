<?php 
class Magento_Products_Widget extends WP_Widget{
	
	/**
	 * Initializes the sidebarWidget class
	 */
	public function Magento_Products_Widget(){
		// Settings
		$widget_ops = array('classname' => 'Magento-Product-Widget', 'description' => __('Shows magento products in a widget form.', 'pronamic-magento-plugin'));

		// Widget control settings. 
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'magento_products');
		
		// Create the widget.
		$this->WP_Widget('magento_products', __('Magento Products', 'pronamic-magento-plugin'), $widget_ops, $control_ops);
	}
	
	/**
	 * The form shown on the admins widget page. Here settings can be changed.
	 * 
	 * @param mixed arrat $instance
	 */
	public function form($instance) {
		// outputs the options form on admin
		$defaults = array('pid' => '', 'cat' => '', 'name_like' => '');
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('pid'); ?>"><?php _e('Product ID\'s or SKU\'s (Comma separated):', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('pid'); ?>" name="<?php echo $this->get_field_name('pid'); ?>" value="<?php echo $instance['pid']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Category name or ID:', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" value="<?php echo $instance['cat']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('name_like'); ?>"><?php _e('Find products with a name like:', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('name_like'); ?>" name="<?php echo $this->get_field_name('name_like'); ?>" value="<?php echo $instance['name_like']; ?>" style="width:100%;" />
		</p>
		
	<?php }

	/**
	 * Updates widget's settings. New settings are parsed by the form function
	 * 
	 * @param mixed array $new_instance
	 * @param mixed array $old_instance
	 */
	public function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;
		
		$instance['pid'] = strip_tags($new_instance['pid']);
		$instance['cat'] = strip_tags($new_instance['cat']);
		$instance['name_like'] = $new_instance['name_like'];

		return $instance;
	}

	/**
	 * The widget as shown to the user.
	 * 
	 * @param mixed array $args
	 * @param mixed array $instance
	 */
	public function widget($args, $instance) {
		// Give users the widget_id
		echo '<!-- -- -- The Widget ID for this widget is: "'.$args['widget_id'].'" -- -- -->';
		
		// Content
		$content = '';
		$templatemode = $args['widget_id'];
		$content .= magento::getProductOutput(array('pid'=>$instance['pid'], 'cat'=>$instance['cat'], 'name_like'=>$instance['name_like']), 0, $templatemode);
				
		echo $content;
	}	
}
?>