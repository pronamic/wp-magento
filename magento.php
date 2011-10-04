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
		add_shortcode('magento', array(__CLASS__, 'shortcode'));
	}
	
	/**
	 * Function shortcode accepts the $atts array in which shortcode words will be parsed.
	 * 
	 * @param array $atts
	 * @return String $content
	 */
	public static function shortcode($atts) {
		$wsdl = get_option('magento-api-wsdl');
		$username = get_option('magento-api-username');
		$apiKey = get_option('magento-api-key');
		$url = get_option('magento-store-url');

		$client = new SoapClient($wsdl);
		$session = $client->login($username, $apiKey);
		
		$content = '';
		
		// Get a template ready, if there's no custom template in the current theme's stylesheet directory, get the default one.
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
		
		// Get the stylesheet, like the template, this plugin accepts custom css files as well.
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
			echo $cat . '<br /><br />';
			// Get all categories so we can search for the wanted one.
			try{
				$result = $client->call($session, 'catalog_category.tree');	
			}catch(Exception $e){
				$content .= 'We\'re sorry, we were unable to obtain any catagories.';
			}

			
			/*function array_flatten($result) { 
				if (!is_array($result)) { 
					return FALSE; 
				} 
				$flattened = array(); 
				foreach ($result as $key => $value) { 
					if (is_array($value)) { 
						$flattened = array_merge($flattened, array_flatten($value)); 
					}else{ 
						$flattened[$key] = $value; 
					} 
				} 
				return $flattened; 
			}*/
			
			function array_flatten($a) {
				
				
				
				
				return $flattened
			}
			
			
			
			
			
			
			$flattened = array_flatten($result);			
			echo '<br/> <br/>';
						
			foreach($flattened as $key => $value){
				echo $key . ' => ' . $value . '<br />';
			}
			
			var_dump($flattened);
			
			
			
			
			
			
			// 
			$category_id = '100';
			function arrayflattener($value, $key){
				/*if($key == 'category_id'){
					$cat_id = $value;
				}
				if($value == $cat){
					return $cat_id; 
				}*/
				
				//echo $category_id;
				
				//$results[$key] = $value;
				//echo $key . ' => ' . $value . '<br />';
			}			
			array_walk_recursive($result, 'arrayflattener');
			
			
			
			
			
			
			
			// Put $results in reverse and walk through it. This will allow us to, when we hit the keyword 
			// we're looking for, instantly pick the right category id. Very clever. Very clever indeed.
			/*$results = array_reverse($results);
			foreach($results as $key=>$value){
				echo $key . ' => '. $value;
				
				if(strtolower(trim($value)) == $cat){
					echo 'HOERA, GEVONDEN!';
					$getnextID = true;
				}
				if($key == 'category_id' && $getnextID){
					echo 'Categorie ID: ' . $value;
				}
			}*/
			
		} // Finished walking through parsed catagories.
		
		// End of list
		$content .= '</ul>';
		
		return $content;
	}
	
	/**
	 * This function will get products and their information by ID
	 * 
	 * @param int $productId
	 * @param Object $client
	 * @param String $session
	 * @param String $url
	 * @param String $template
	 */
	public static function getProductByID($productId, $client, $session, $url, $template) {
		$content = '';
		
		// Get product information and images from specified product ID.
		try{
			$result = $client->call($session, 'catalog_product.info', $productId);	
			try{
				$images = $client->call($session, 'product_media.list', $productId);
			}catch(Exception $e){	}
		}catch(Exception $e){
			$content .= 'Unable to obtain any products.';
		}
		
		// Build up the obtained information (if any) and pass them on in the $content variable which will be returned.
		if($result){
			if($images){
				$image = $images[1];
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

/////////////////////////////////			OLD CODE			/////////////////////////////////////////////////


			
			/*if($result) {
				$content .= '<dl>';
				//include("templates/defaulttemplate.php");
				
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
			}*/
