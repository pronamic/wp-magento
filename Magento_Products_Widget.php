<?php 
class Magento_Products_Widget extends WP_Widget{
	
	/**
	 * Initializes the sidebarWidget class
	 */
	public function Magento_Products_Widget(){
		// Settings
		$widget_ops = array('classname' => 'Magento-Sidebar-Widget', 'description' => __('The widget version of the Pronamic Magento plugin.', 'pronamic-magento-plugin'));

		/* Widget control settings. */
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'pronamic-magento-plugin');

		/* Create the widget. */
		$this->WP_Widget('pronamic-magento-plugin', __('Pronamic Magento Plugin Sidebar Widget', 'pronamic-magento-plugin'), $widget_ops, $control_ops);
	}
	
	/**
	 * The form shown on the admins widget page. Here settings can be changed.
	 * 
	 * @param mixed arrat $instance
	 */
	public function form($instance) {
		// outputs the options form on admin
		$defaults = array('showlatest' => '0', 'pid' => '', 'cat' => '', 'maxlatestproducts' => 3, 'septemp' => 1);
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
			<label for="<?php echo $this->get_field_id('showlatest'); ?>"><?php _e('Show latest products', 'pronamic-magento-plugin'); ?>
			<input value="1" <?php checked($instance['showlatest'], 1); ?> type="checkbox" id="<?php echo $this->get_field_id('showlatest'); ?>" name="<?php echo $this->get_field_name('showlatest'); ?>" class="fat" /></label>
		</p>
		
		<?php if($instance['showlatest']): ?>
		<p>
			<label for="<?php echo $this->get_field_id('maxlatestproducts'); ?>"><?php _e('Number of latest products shown', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('maxlatestproducts'); ?>" name="<?php echo $this->get_field_name('maxlatestproducts'); ?>" value="<?php echo $instance['maxlatestproducts']; ?>" style="width:100%" />
		</p>
		<?php endif; ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('septemp'); ?>"><?php _e('Use separate template', 'pronamic-magento-plugin'); ?>
			<input value="1" <?php checked($instance['septemp'], 1); ?> type="checkbox" id="<?php echo $this->get_field_id('septemp'); ?>" name="<?php echo $this->get_field_name('septemp'); ?>" class="fat" /></label>
		</p>
		
		<?php
	}

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
		$instance['showlatest'] = $new_instance['showlatest'];
		
		if(!is_numeric(strip_tags($new_instance['maxlatestproducts']))) $new_instance['maxlatestproducts'] = 3;
		$instance['maxlatestproducts'] = strip_tags($new_instance['maxlatestproducts']);
		$instance['septemp'] = $new_instance['septemp'];

		return $instance;
	}

	/**
	 * The widget as show to the user.
	 * 
	 * @param mixed array $args
	 * @param mixed array $instance
	 */
	public function widget($args, $instance) {
		// Determine wether the widget or the plugin template should be used.
		$templatemode = null;
		if($instance['septemp']) $templatemode = 'widget';
		
		// Different mode is different outcome
		$content = '';
		if($instance['showlatest']){
			$content .= magento::getProductOutput(array('pid'=>$instance['pid'], 'cat'=>$instance['cat'], 'latest'=>$instance['maxlatestproducts']), 0, $templatemode);		
		}else{
			$content .= magento::getProductOutput(array('pid'=>$instance['pid'], 'cat'=>$instance['cat']), 0, $templatemode);
		}
		
		echo $content;
	}
	
}
?>