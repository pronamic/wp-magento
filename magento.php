<?php
/*
Plugin Name: Magento.
Plugin URI: http://pronamic.eu/wordpress/magento/
Description: Integrate Magent content into your WordPress website. 
Version: beta-0.1
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
		echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/languages/';
		load_plugin_textdomain('pronamic-magento-plugin', false, WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/languages/');
		
		add_shortcode('magento', array(__CLASS__, 'shortcode'));
	}
	
	/**
	 * Function shortcode accepts the $atts array in which shortcode words will be parsed.
	 * 
	 * @param array $atts
	 * @return String $content
	 */
	public static function shortcode($atts) {
		error_reporting(E_ALL ^ E_NOTICE);
		
		$content = '';
		
		$wsdl = get_option('magento-api-wsdl');
		$username = get_option('magento-api-username');
		$apiKey = get_option('magento-api-key');
		$url = get_option('magento-store-url');

		$connection = false;
		try{
			$client = new SoapClient($wsdl);
			$session = $client->login($username, $apiKey);
			$connection = true;
		}catch(Exception $e){
			$content .= __('Unable to connect to host.', 'pronamic-magento-plugin');
			$connection = false;
		}
		
		if($connection){
			// Template and stylesheet
			$template = self::getTemplate();
			$stylesheet = self::getStyleSheet();
			include($stylesheet);
			
			// Start of list
			$content .= '<ul class="pronamic-magento-items-grid">';
			
			// If there are ID's being parsed, do these actions.
			if(isset($atts['pid'])) {			
				$productIds = explode(',', $atts['pid']);
				if(count($productIds) > 1){
					// Multiple id's parsed, loop through them.
					foreach($productIds as $value){
						if(!empty($value)){
							$content .= self::getProductByID(trim($value), $client, $session, $url, $template);
						}
					}
				}else{
					// Single id parsed, pass first item in array.
					$productId = trim($productIds[0]);
					if(!empty($productId)){
						$content .= self::getProductByID(trim($productId), $client, $session, $url, $template);
					}
				}
			} // Finished looping through parsed ID's
	
			// Whenever shortcode 'cat' is parsed, these actions will happen.
			if(isset($atts['cat'])){
				$cat = strtolower(trim($atts['cat']));
				$result = '';
				$cat_id = '';
				
				// Check if the inputted shortcode cat is numeric or contains a string.
				if(is_numeric($cat)){
					$cat_id = $cat;
				}else{			
					$result = self::getCatagoryList($client, $session);
					
					// Magento passes a wrapper array, to make it easier on the getCatagories function
					// we throw that wrapper away here and then call the function, so we get a flat array.
					$result = $result['children'];
					$result = self::flattenCategories($result);
					
					// Loop through the flattened array to match the catagory name with the given shortcode name.
					// When there is a mach, we need not look further so we break.
					foreach($result as $key=>$value){
						$tmp_id = '';
						foreach($value as $key2=>$value2){
							if($key2 == 'category_id'){
								$tmp_id = $value2;
							}							
							if($key2 == 'name' && strtolower(trim($value2)) == $cat){
								$cat_id = $tmp_id;
								$break = true;
								break;
							}
						}
						if($break){
							break;
						}
					}
				}
				
				// If there's a result on our query.
				if(!empty($cat_id)){
					// Get list of all products so we can filter out the required ones.
					try{
						$productlist = $client->call($session, 'catalog_pro duct.list');
					}catch(Exception $e){
						$content .= __('We\'re sorry, we weren\'t able to find any products with the queried category id.', 'pronamic-magento-plugin');
					}
					
					// Extract the productIds from the productlist where the category_ids are cat_id. Put them in productIds array.
					if($productlist){
						$productId = '';
						$productIds = array();
						foreach($productlist as $key=>$value){
							foreach($value as $key2=>$value2){
								if($key2 == 'product_id'){
									$productId = $value2;
								}
								if($key2 == 'category_ids'){
									foreach($value2 as $value3){
										if($value3 == $cat_id){
											$count = count($productIds);
											$productIds[$count] = $productId;
										}
									}
								}
							}
						}
						// Get the values from productIds in random order, then output them with getProductID()
						$i = 0;
						foreach($productIds as $value){					
							$rand = array_rand($productIds);
							$content .= self::getProductByID($productIds[$rand], $client, $session, $url, $template);
							unset($productIds[$rand]);
							$i++;
							if($i >= 3){
								break;
							}
						}
					}
				}
			} // Finished walking through parsed catagories.
			
			// End of list
			$content .= '</ul>';
		}
		
		return $content;
	}
	
	/**
	 * This function will get products and their information by ID or SKU
	 * 
	 * @param int $productId
	 * @param Object $client
	 * @param String $session
	 * @param String $url
	 * @param String $template
	 */
	public static function getProductByID($productId, $client, $session, $url, $template) {
		$content = '';
		$result = '';
		
		// Get product information and images from specified product ID.
		try{
			$result = $client->call($session, 'catalog_product.info', $productId);	
			try{
				$images = $client->call($session, 'product_media.list', $productId);
			}catch(Exception $e){	}
		}catch(Exception $e){
			$content .= __('Unable to obtain any products.', 'pronamic-magento-plugin');
		}
		
		// Build up the obtained information (if any) and pass them on in the $content variable which will be returned.
		if($result){
			if($images){
				$image = $images[0];
			}else{
				unset($image);
				$image['url'] = plugins_url('images/noimg.gif', __FILE__);
			}
							
			// Check if base url ends correctly (with a /)
			if($url[strlen($url)-1] != '/'){
				$url .= '/';
			}
			
			// Build a list item
			include($template);
		}
		
		return $content;
	} // End of getProductByID($productId, $client, $session, $url, $template)
	
	/**
	 * Function which returns the catagory tree.
	 * 
	 * @param Object $client
	 * @param String $session
	 */
	private static function getCatagoryList($client, $session){
		// Get all categories so we can search for the wanted one.
		try{
			$result = $client->call($session, 'catalog_category.tree');	
		}catch(Exception $e){
			$content .= __('We\'re sorry, we were unable to obtain any categories.', 'pronamic-magento-plugin');
		}
		
		return $result;
	}
	
	/**
	 * Get a template ready, if there's no custom template in the current theme's stylesheet directory, get the default one.
	 * 
	 * @return String $template (Location to template file, custom or default)
	 */
	private static function getTemplate(){
		$template = '';
		$templates = array('pronamic-magento-plugintemplate.php');
		$template = locate_template($templates);
		if($template){
			$template = explode('/', $template);
			$count = count($template);
			$template = get_bloginfo('stylesheet_directory') . '/' . $template[$count-1];
		}else{
			$template = 'templates/defaulttemplate.php';
		}
		
		return $template;
	}
	
	/**
	 * Get the stylesheet, like the template, this plugin accepts custom css files as well.
	 * 
	 * @return String $stylesheet (Location to stylesheet file, custom or default)
	 */
	private static function getStyleSheet(){
		$stylesheet = '';
		$stylesheets = array('pronamic-magento-plugin-stylesheet.css');
		$stylesheet = locate_template($stylesheets);
		if($stylesheet){
			$stylesheet = explode('/', $stylesheet);
			$count = count($stylesheet);
			$stylesheet = get_bloginfo('stylesheet_directory') . '/' . $stylesheet[$count-1];
		}else{
			$stylesheet = 'css/default.css';
			//$stylesheet = plugins_url('css/default.css', __FILE__);
		}
		
		return $stylesheet;
	}	
	
	/**
	 * Function to flatten the multidemensional array given by the Magento API
	 * This is not a very dynamic function, for it is created specifically 
	 * to break down the Magento catagory hierarchy.
	 * 
	 * @param array $array
	 */
	private static function flattenCategories($array){
		$loop = false;
		$newarray = array();
		foreach($array as $key=>$value){
			if(is_array($value)){
				if(is_array($value['children'])){
					$count = count($newarray);
					$newarray[$count] = $value['children'];
					$array[$key]['children'] = '';
				}else{
					foreach($value as $key2=>$value2){
						$count = count($newarray);
						if(is_array($value2)){
							$newarray[$count] = $value2;
							$array[$key][$key2] = '';
						}
					}
				}
			}
		}				
		if(!empty($newarray)){
			foreach($newarray as $value){
				$count = count($array);
				$array[$count] = $value;
			}
			$loop = true;
		}
		if($loop){
			$array = self::flattenCategories($array);
		}
		
		return $array;
	}

	public static function adminInitialize() {
		// Settings
		register_setting('magento', 'magento-api-wsdl');
		register_setting('magento', 'magento-api-username');
		register_setting('magento', 'magento-api-key');
		register_setting('magento', 'magento-store-url');

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
		
		add_submenu_page(
			$parentSlug = __FILE__ ,
			$pageTitle = 'Blokken' , 
			$menuTitle = 'Blokken' , 
			$capability = 'manage_options' , 
			$menuSlug = 'magento-blokken' , 
			$function = array(__CLASS__, 'blocks')
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
	
	public static function blocks(){
		
	}

	public static function pageSettings() {
		include 'page-settings.php';
	}
}

Magento::bootstrap();