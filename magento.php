<?php
/*
Plugin Name: Magento
Plugin URI: http://pronamic.eu/wordpress/magento/
Description: Integrate Magent content into your WordPress website. 
Version: beta-0.2.1
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
		register_setting('magento-api', 'magento-api-wsdl');
		register_setting('magento-api', 'magento-store-url');
		register_setting('magento-api', 'magento-api-username');
		register_setting('magento-api', 'magento-api-key');
		register_setting('magento-api', 'magento-caching-option');
		register_setting('magento-api', 'magento-caching-time');
		
		// Currency Settings
		register_setting('magento-currency', 'magento-currency-setting');
		register_setting('magento-currency', 'magento-currency-position');
		register_setting('magento-currency', 'magento-number-decimals');
		register_setting('magento-currency', 'magento-decimal-separator');
		register_setting('magento-currency', 'magento-thousands-separator');

		// Styles
		wp_enqueue_style(
			'magento-admin' , 
			plugins_url('css/admin.css', __FILE__)
		);
		
		// Shortcode editor for rich text editor
		add_action('media_buttons_context', array('Magento_Shortcode_Editor', 'addMagentoShortcodeButton'));
		if(in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))) add_action('admin_footer',  array('Magento_Shortcode_Editor', 'addMagentoShortcodeButtonForm'));
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
			$pageTitle = __('API Settings', 'pronamic-magento-plugin') , 
			$menuTitle = __('API Settings', 'pronamic-magento-plugin') , 
			$capability = 'manage_options' , 
			$menuSlug = 'magento-settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);
		
		add_submenu_page(
			$parentSlug = __FILE__ , 
			$pageTitle = __('Currency Settings', 'pronamic-magento-plugin') , 
			$menuTitle = __('Currency Settings', 'pronamic-magento-plugin') , 
			$capability = 'manage_options' , 
			$menuSlug = 'magento-currency-settings' , 
			$function = array(__CLASS__, 'currencySettings')
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
		include('settings/page-magento.php');
	}

	public static function pageSettings() {
		include('settings/page-settings.php');
	}
	
	public static function currencySettings() {
		include('settings/currency-settings.php');
	}
}

Init::bootstrap();
?>