<?php
/*
Plugin Name: Magento.
Plugin URI: http://pronamic.eu/wordpress/magento/
Description: Integrate Magent content into your WordPress website. 
Version: 0.1-beta
Requires at least: 3.0
Author: Pronamic
Author URI: http://pronamic.eu/
License: GPL
*/

class Init {
	public static function bootstrap() {		
		self::magento_auto_include(); 
		
		add_action('init', array(__CLASS__, 'initialize'));
		
		add_action('admin_init', array(__CLASS__, 'adminInitialize'));
		
		add_action('admin_menu', array(__CLASS__, 'adminMenu'));
		
		add_action('widgets_init', array(__CLASS__, 'Magento_Widgets'));
	}

	public static function initialize() {
		// Translations
		$relPath = dirname(plugin_basename(__FILE__)) . '/languages/';
		load_plugin_textdomain('pronamic-magento-plugin', false, $relPath);	

		// Stylesheet
		self::setStyleSheet();
		
		Magento::addShortcode();
	}
	
	/**
	 * This function will set the stylesheet (enqueue it in WP header).
	 */
	public static function setStyleSheet(){
		$stylesheet = plugins_url('css/style.css', __FILE__);
		wp_register_style('pronamic-magento-plugin-stylesheet', $stylesheet);
	}

	public static function adminInitialize() {
		// Settings
		register_setting('magento', 'magento-api-wsdl');
		register_setting('magento', 'magento-store-url');
		register_setting('magento', 'magento-api-username');
		register_setting('magento', 'magento-api-key');
		register_setting('magento', 'magento-caching-option');
		register_setting('magento', 'magento-caching-time');

		// Styles
		wp_enqueue_style(
			'magento-admin' , 
			plugins_url('css/admin.css', __FILE__)
		);
	}

	public static function adminMenu() {
		add_menu_page(
			$pageTitle = __('Magento', 'pronamic-magento-plugin') , 
			$menuTitle = __('Magento', 'pronamic-magento-plugin') , 
			$capability = 'manage_options' , 
			$menuSlug = __FILE__ , 
			$function = array(__CLASS__, 'page') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);
		
		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = __FILE__ , 
			$pageTitle = __('Settings', 'pronamic-magento-plugin') , 
			$menuTitle = __('Settings', 'pronamic-magento-plugin') , 
			$capability = 'manage_options' , 
			$menuSlug = 'magento-settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);
	}
	
	public static function magento_auto_include(){
		if(function_exists('spl_autoload_register')){
			function magento_file_autoloader($name) {
				$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
	
				$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $name . '.php';
	
				if(is_file($file)) {
					require_once $file;
				}
			}
			spl_autoload_register('magento_file_autoloader');			
		}
	}
	
	public static function Magento_Widgets(){
		register_widget('Magento_Products_Widget');
		register_widget('Magento_Latest_Products_Widget');
	}

	public static function page() {
		include('page-magento.php');
	}

	public static function pageSettings() {
		include('page-settings.php');
	}
}

Init::bootstrap();
?>