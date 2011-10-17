<?php 
class Magento_Latest_Products_Widget extends WP_Widget{
	
	/**
	 * Initializes the sidebarWidget class
	 */
	public function Magento_Latest_Products_Widget(){
		// Settings
		$widget_ops = array('classname' => 'Magento-Latest-Products-Widget', 'description' => __('Shows the latest products in your Magento store.', 'pronamic-magento-plugin'));

		// Widget control settings. 
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'magento_latest_products');
		
		// Create the widget.
		$this->WP_Widget('magento_latest_products', __('Magento Latest Products', 'pronamic-magento-plugin'), $widget_ops, $control_ops);
	}
	
	/**
	 * The form shown on the admins widget page. Here settings can be changed.
	 * 
	 * @param mixed arrat $instance
	 */
	public function form($instance) {
		// outputs the options form on admin
		$defaults = array('maxlatestproducts' => 3);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('maxlatestproducts'); ?>"><?php _e('Number of latest products shown', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('maxlatestproducts'); ?>" name="<?php echo $this->get_field_name('maxlatestproducts'); ?>" value="<?php echo $instance['maxlatestproducts']; ?>" style="width:100%" />
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
		
		if(!is_numeric(strip_tags($new_instance['maxlatestproducts']))) $new_instance['maxlatestproducts'] = 3;
		$instance['maxlatestproducts'] = strip_tags($new_instance['maxlatestproducts']);

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
		$content .= magento::getProductOutput(array('latest'=>$instance['maxlatestproducts']), 0, $templatemode);
		
		echo $content;
	}	
}
?>