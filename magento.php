<?php
/*
Plugin Name: Magento
Plugin URI: http://pronamic.eu/wordpress/magento/
Description: Integrate Magent content into your WordPress website. 
Version: 0.1-beta
Requires at least: 3.0
Author: Pronamic
Author URI: http://pronamic.eu/
License: GPL
*/

class Magento {
	public static function bootstrap() {
		add_action('init', array(__CLASS__, 'initialize'));

		add_action('admin_init', array(__CLASS__, 'adminInitialize'));

		add_action('admin_menu', array(__CLASS__, 'adminMenu'));
	}

	public static function initialize() {
		add_shortcode('magento', array(__CLASS__, 'shortcode'));
	}
	
	public static function shortcode($atts) {
		$wsdl = get_option('magento-api-wsdl');
		$username = get_option('magento-api-username');
		$apiKey = get_option('magento-api-key');

		$client = new SoapClient($wsdl);
		$session = $client->login($username, $apiKey);

		if(isset($atts['pid'])) {
			$productId = $atts['pid'];

			$result = $client->call($session, 'catalog_product.info', $productId);
	
			$content = '';
			if($result) {
				$content .= '<dl>';
				$content .= '    <dt>Id</dt>';
				$content .= '    <dd>' . $result['product_id'] . '</dd>';
				
				$content .= '    <dt>SKU</dt>';
				$content .= '    <dd>' . $result['sku'] . '</dd>';
				
				$content .= '    <dt>Name</dt>';
				$content .= '    <dd>' . $result['name'] . '</dd>';
				
				$content .= '    <dt>Description</dt>';
				$content .= '    <dd>' . $result['description'] . '</dd>';
				
				$content .= '    <dt>Price</dt>';
				$content .= '    <dd>' . $result['price'] . '</dd>';
				$content .= '</dl>';
			}
		}

		return $content;
	}

	public static function adminInitialize() {
		// Settings
		register_setting('magento', 'magento-api-wsdl');
		register_setting('magento', 'magento-api-username');
		register_setting('magento', 'magento-api-key');

		// Styles
		wp_enqueue_style(
			'magento-admin' , 
			plugins_url('css/admin.css', __FILE__)
		);
	}

	public static function adminMenu() {
		add_menu_page(
			$pageTitle = 'Magento' , 
			$menuTitle = 'Magento' , 
			$capability = 'manage_options' , 
			$menuSlug = __FILE__ , 
			$function = array(__CLASS__, 'page') , 
			$iconUrl = plugins_url('images/icon-16x16.png', __FILE__)
		);

		// @see _add_post_type_submenus()
		// @see wp-admin/menu.php
		add_submenu_page(
			$parentSlug = __FILE__ , 
			$pageTitle = 'Settings' , 
			$menuTitle = 'Settings' , 
			$capability = 'manage_options' , 
			$menuSlug = 'magento-settings' , 
			$function = array(__CLASS__, 'pageSettings')
		);
	}

	public static function page() {
		include 'page-magento.php';
	}

	public static function pageSettings() {
		include 'page-settings.php';
	}
}

Magento::bootstrap();
