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
	private static $soapClient;
	private static $session;
	
	public static function bootstrap() {
		add_action('init', array(__CLASS__, 'initialize'));

		add_action('admin_init', array(__CLASS__, 'adminInitialize'));

		add_action('admin_menu', array(__CLASS__, 'adminMenu'));
		
		add_action('widgets_init', array(__CLASS__, 'sidebarWidget'));
	}

	public static function initialize() {
		// Translations
		$relPath = dirname(plugin_basename(__FILE__)) . '/languages/';
		load_plugin_textdomain('pronamic-magento-plugin', false, $relPath);	

		// Stylesheet
		self::setStyleSheet('plugin');
		self::setStyleSheet('widgets');
		
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
		$content .= self::getProductOutput($atts, 0, 'plugin');
		
		return $content;
	}
	
	/**
	 * This function will take care of extracting productIDs from $atts
	 * 
	 * @param mixed array $atts
	 */
	public static function getProductOutput($atts, $maxproducts, $templatemode){
		$content = '';
		$runApiCalls = true;
		
		// Will always run, unless caching has not been enabled. If any step in this proces fails, e.g.: Outdated cache or No cache found, we will run the API calls.
		if(get_option('magento-caching-option')){
			// Create the class
			include_once('CacheClass.php');
			$CC = new CacheClass($atts, $maxproducts);
			
			try{
				$content .= $CC->getCache();
				$runApiCalls = false;
			}catch(Exception $e){
				$runApiCalls = true;
			}
		}
		
		// Only runs if no succesful cache call was made in any way.
		if($runApiCalls){
			// Output buffer, mostly there for caching
			ob_start();
			
			$content .= self::getAPIResults($atts, $maxproducts, $templatemode);
			
			// End of outer output buffer. This could be saved to the cachefiles.
			$bufferoutput = ob_get_clean();
			$content .= $bufferoutput;
			
			if(get_option('magento-caching-option')){
				$CC->storeCache($bufferoutput);
			}
		}// End of API calls.
		
		return $content;
	}
	
	/**
	 * No usable cache files were found, get the results from the API
	 * 
	 * @param mixed array $atts
	 * @param int $maxproducts
	 * @param String $templatemode
	 */
	public static function getAPIResults($atts, $maxproducts, $templatemode){
		$content = '';
		
		// If no cache record is found
		$connection = false;
		try{
			$wsdl = get_option('magento-api-wsdl');
			$client = self::getSoapClient($wsdl);
			try{
				$username = get_option('magento-api-username');
				$apiKey = get_option('magento-api-key');
				$session = self::getSession($username, $apiKey, $client);				
				$connection = true;
			}catch(Exception $e){
				$content .= __('Unable to login to host with that username/password combination.', 'pronamic-magento-plugin');
			}
		}catch(Exception $e){
			$content .= __('Unable to connect to host.', 'pronamic-magento-plugin');
			$connection = false;
		}
		
		if($connection){
			// Magento store url
			$url = get_option('magento-store-url');
			
			// Template
			$template = self::getTemplate($templatemode);
			
			// Style
			if(!wp_style_is('pronamic-magento-plugin-stylehseet', 'queue')){
				wp_print_styles(array('pronamic-magento-plugin-stylesheet'));
			}
			
			$productIds = self::getProductIDsFromAtts($atts, $client, $session);
			if(!empty($productIds)){
				$content .= self::getProductsByID($productIds, $client, $session, $url, $template);
			}
		}
		return $content;
	}
	
	/**
	 * Gets the productIds belonging to the requested products in the $atts variable.
	 * Returns them in an array that is suitable for use in self::getProductsByID()
	 * 
	 * @param mixed array $atts
	 * @param Object $client
	 * @param String $session
	 * @return array of ints $productIds
	 */
	private static function getProductIDsFromAtts($atts, $client, $session){						
		$productIds = array();
		
		// If there are ID's being parsed, do these actions.
		if(isset($atts['pid'])){
			// Making sure no more than the wanted product id's are parsed.
			$pids = explode(',', $atts['pid']);
			if($maxproducts > 0){				
				$pids = array_slice($pids, -$maxproducts);
			}
			foreach($pids as $value){
				$productIds[] = $value;
			}
		}

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
			
			// If there's a result on our query. (or just a numeric string was parsed)
			if(!empty($cat_id)){
				// Get list of all products so we can filter out the required ones.
				try{
					$productlist = $client->call($session, 'catalog_product.list');
				}catch(Exception $e){
					$content .= __('We\'re sorry, we weren\'t able to find any products with the queried category id.', 'pronamic-magento-plugin');
				}
				
				// Extract the productIds from the productlist where the category_ids are cat_id. Put them in productIds array.
				if($productlist){
					$productId = '';
					$i = 0;
					$break = false;
					foreach($productlist as $key=>$value){
						foreach($value as $key2=>$value2){
							if($key2 == 'product_id'){
								$productId = $value2;
							}
							if($key2 == 'category_ids'){
								foreach($value2 as $value3){
									if($value3 == $cat_id){
										$productIds[] = $productId;
										$i++;
										if($maxproducts > 0 && $i >= $maxproducts) $break = true;
									}
									if($break) break;
								}
							}
							if($break) break;
						}
						if($break) break;
					}
				}
			}
		} // Finished walking through parsed catagories.
		
		// Sort products by date
		if(isset($atts['latest'])){
			$result = self::getProductList($client, $session);
			if(!empty($result)){
				$pids = array();
				foreach($result as $value){
					$pids[] = $value['product_id'];
				}
				
				$products = self::getProductListByIDs($pids, $client, $session);
				if(!empty($products)){
					$datearray = array();
					foreach($products as $value){
						$datearray[$value['product_id']] = preg_replace('/[^a-z0-9]/', '', $value['created_at']);
					}
					
					$tmp = $datearray;
					sort($tmp);
					$tmp = array_reverse($tmp);
					
					$maxproducts = $atts['latest'];
					if(is_numeric($maxproducts)){
						if($maxproducts == 0){
							$neededdates = $tmp;
						}else{
							$neededdates = array_slice($tmp, -$maxproducts);
						}
						
						foreach($datearray as $key=>$value){
							foreach($neededdates as $value2){
								if($value2 == $value){
									$productIds[] = $key;
								}
							}
						}
					}
				}
			}
		}
		return $productIds;
	}
	
	/**
	 * This function will get products and their information by ID or SKU
	 * 
	 * @param array[int] $productIds
	 * @param Object $client
	 * @param String $session
	 * @param String $url
	 * @param String $template
	 * @return String $content
	 */
	public static function getProductsByID($productIds, $client, $session, $url, $template) {		
		$content = '';
		$result = '';
		global $magento_products;
		$magento_products = array();
		
		foreach($productIds as $value){
			// Clean up messy input.
			$productId = strtolower(trim($value));
			
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
					$image = $image['url'];
				}else{
					unset($image);
					$image = plugins_url('images/noimg.gif', __FILE__);
				}
								
				// Check if base url ends correctly (with a /)
				if($url[strlen($url)-1] != '/'){
					$url .= '/';
				}
				
				// Adjust resul's url path
				$result['url_path'] = $url . $result['url_path'];
				
				// Place the result and the image in an array that will be looped through in the template. Format: array('1' => array('result' => $result, 'image' => $image))
				$magento_products[] = array('result' => $result, 'image' => $image);
			}
		}
		
		// Included functions to make template use more easy on the user
		include_once('templates/shortFunctions.php');
		new Mage();
		
		// The template
		try{
			include($template);
		}catch(Exception $e){
			$content .= __('Detected an error in the template file, actions have been interupted.', 'pronamic-magento-plugin');
		}
	
		return $content;
	} // End of getProductByID($productId, $client, $session, $url, $template)
	
	/**
	 * Singleton function, will check if the soapClient hasn't already
	 * been created before. If it has, return the previously saved Object.
	 * Otherwise, create a new soapClient Object and save it for a next time.
	 * 
	 * @param String $wsdl
	 */
	private static function getSoapClient($wsdl){		
		if(!isset(self::$soapClient)){			
			self::$soapClient = new SoapClient($wsdl);
		}
		return self::$soapClient;
	}
	
	/**
	 * Also a Singleton function, it works exaclty like the getSoapClient() function
	 * 
	 * @param String $username
	 * @param String $apiKey
	 * @param Object $client
	 */
	private static function getSession($username, $apiKey, $client){
		if(!isset(self::$session)){
			self::$session = $client->login($username, $apiKey);
		}
		return self::$session;
	}
	
	/**
	 * Returns a list of all products.
	 * 
	 * @param String $apiKey
	 * @param Object $client
	 */
	private static function getProductList($client, $session){
		$result = '';
		try{
			$result = $client->call($session, 'catalog_product.list');
		}catch(Exception $e){	}
		
		return $result;
	}
	
	/**
	 * Accepts an array of ints, which it passes one at a time
	 * to get all products into a new array.
	 * 
	 * @param array of ints $productids
	 * @param Object $client
	 * @param String $session
	 */
	private static function getProductListByIDs($productIds, $client, $session){
		$result = '';
		$array = array();
		foreach($productIds as $productId){
			try{
				$result = $client->call($session, 'catalog_product.info', $productId);
				$array[] = $result;
			}catch(Exception $e){	}
		}
		
		return $array;
	}
	
	/**
	 * Function which returns the catagory tree.
	 * 
	 * @param Object $client
	 * @param String $session
	 */
	private static function getCatagoryList($client, $session){
		$result = '';
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
	private static function getTemplate($templatemode){
		if(empty($templatemode)) $templatemode = 'plugin';
		$templates = array('pronamic-magento-'.$templatemode.'template.php');
		$template = locate_template($templates);
		if(!$template){
			$template = 'templates/pronamic-magento-'.$templatemode.'template.php';
		}
		
		return $template;
	}
	
	/**
	 * This function will set the stylesheet (enqueue it in WP header).
	 */
	private static function setStyleSheet($templatemode){
		if(empty($templatemode)) $templatemode = 'plugin';
		$stylesheet = '';
		$stylesheet = get_bloginfo('stylesheet_directory') . '/' . 'pronamic-magento-'.$templatemode.'-stylesheet.css';
		if(!file_exists($stylesheet)){
			$stylesheet = plugins_url('css/pronamic-magento-'.$templatemode.'-stylesheet.css', __FILE__);
		}
		
		wp_register_style('pronamic-magento-'.$templatemode.'-stylesheet', $stylesheet);
		wp_enqueue_style( 'pronamic-magento-'.$templatemode.'-stylesheet');
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
	
	public static function sidebarWidget(){
		include_once('sidebarWidget.php');
		register_widget('sidebarWidget');
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