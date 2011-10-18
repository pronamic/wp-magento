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

class Magento {
	//const CACHETIME = 86400; // 24hrs (60*60*24);
	const CACHETIME = 300; // 5 minutes.
	private static $soapClient;
	private static $session;
	
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
				
		add_shortcode('magento', array(__CLASS__, 'shortcode'));
	}
	
	/**
	 * Function shortcode accepts the $atts array in which shortcode words will be parsed.
	 * 
	 * @param array $atts
	 * @return String $content
	 */
	public static function shortcode($atts) {
		$content = '';
		$content .= self::getProductOutput($atts, 0, 'shortcode');
		
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
		
		// Style
		if(!wp_style_is('pronamic-magento-plugin-stylehseet', 'queue')){
			wp_print_styles(array('pronamic-magento-plugin-stylesheet'));
		}
		
		// Will always run, unless caching has not been enabled. If any step in this proces fails, e.g.: Outdated cache or No cache found, we will run the API calls.
		if(get_option('magento-caching-option')){
			// Create the class
			$CC = new Magento_Cache($atts, $maxproducts, self::CACHETIME);
			
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
			
			$productIds = self::getProductIDsFromAtts($atts, $client, $session, $maxproducts);
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
	 * @param int $maxproducts
	 * @return array of ints $productIds
	 */
	private static function getProductIDsFromAtts($atts, $client, $session, $maxproducts){						
		$productIds = array();
		
		// If there are ID's being parsed, do these actions.
		if(isset($atts['pid'])){
			if(!empty($atts['pid'])){
				// Making sure no more than the wanted product id's are parsed.
				$pids = explode(',', $atts['pid']);
				if($maxproducts > 0){				
					$pids = array_slice($pids, -$maxproducts);
				}
				foreach($pids as $value){
					$productIds[] = $value;
				}
			}
		}

		// Whenever shortcode 'cat' is parsed, these actions will happen.
		if(isset($atts['cat'])){
			if(!empty($atts['cat'])){
				$settings = explode(',', $atts['cat']);
				$cat = strtolower(trim($settings[0]));
				$maxproducts = 0;
				if(isset($settings[1]))	$maxproducts = strtolower(trim($settings[1]));
				if(!is_numeric($maxproducts)){
					$maxproducts = 0;
				}elseif(empty($maxproducts)){
					$maxproducts = 0;
				}
				$result = '';
				$cat_id = '';
				
				// Check if the inputted shortcode cat is numeric or contains a string.
				if(is_numeric($cat)){
					$cat_id = $cat;
				}else{			
					$result = self::getCategoryList($client, $session);
					
					// Magento passes a wrapper array, to make it easier on the getCatagories function
					// we throw that wrapper away here and then call the function, so we get a flat array.
					$result = $result['children'];
					$result = self::flattenCategories($result);
					
					// Loop through the flattened array to match the catagory name with the given shortcode name.
					// When there is a mach, we need not look further so we break.
					foreach($result as $key=>$value){
						$tmp_id = '';
						$break = false;
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
					$result = ''; 
					$result = self::getProductsByCategoryID($client, $session, 0, $cat_id);
					
					if(!empty($result)){
						$i = 0;
						foreach($result as $value){
							$productIds[] = $value['product_id'];
							$i++;
							if($i >= $maxproducts && $maxproducts > 0){
								break;
							}
						}
					}
				}
			}
		} // Finished walking through parsed catagories.
		
		// Sort products by date
		if(isset($atts['latest'])){
			if(empty($atts['latest'])){
				$atts['latest'] = 0;
			}
			$maxproducts = strtolower(trim($atts['latest']));
			$result = '';
			
			// Get products
			if($maxproducts > 0 && is_numeric($maxproducts)){
				$count = 0;
				for($i=1; $count<$maxproducts; $i++){
					$filter = array('created_at' => array('from' => date('o-m-d H:i:s', strtotime('-'.$i.' months'))));
					$result = self::getProductList($client, $session, $filter);
					$count = count($result);
					if($i > 100){
						//break;
					}
				}
				$result = array_slice($result, -$maxproducts);
			}else{
				$result = self::getProductList($client, $session, '');
			}
			
			// Put latest products first
			$result = array_reverse($result);
			
			// Get all product ids and give them to the $productIds array
			if(!empty($result)){
				foreach($result as $value){
					$productIds[] = $value['product_id'];
				}
			}
		}// Finished walking through latest products
		
		if(isset($atts['name_like'])){
			if(!empty($atts['name_like'])){
				$settings = explode(',', $atts['name_like']);
				$needle = $settings[0];
				if(isset($settings[1])) $maxproducts = strtolower(trim($settings[1]));
				else $maxproducts = 0;
				if(!is_numeric($maxproducts)) $maxproducts = 0;
				$result = '';
				
				// Get results, if the user added %%%'s he probably knows what he's doing, so let him do his bussines. If he didn't, we should probably help him in his quest to find products.
				unset($tmp);
				$tmp = strpos(' '.$needle, '%');
				if(!empty($tmp)){
					$filter = array('name' => array('like' => $needle));
					$result = self::getProductList($client, $session, $filter);
				}else{
					function arrayAdder($existingarray, $values){
						if(is_array($existingarray) && is_array($values)){
							foreach($values as $value){
								$existingarray[] = $value;
							}
						}
						return $existingarray;
					}
					
					// Walk through the search posibilities getting the results while there are not enough products found yet.
					$keywords = array($needle, $needle.'%', '%'.$needle, '%'.$needle.'%');
					$array = array();
					foreach($keywords as $keyword){
						$filter = array('name' => array('like' => $keyword));
						$result = self::getProductList($client, $session, $filter);
						$array = arrayAdder($array, $result);
						
						if(count($array) >= $maxproducts && $maxproducts > 0){
							break;
						}
					}				
					$result = $array;
				}
				
				// When products were found
				if(!empty($result)){
					$count = count($result);
					if($count > $maxproducts){
						$result = array_slice($result, -$maxproducts);
					}
					
					foreach($result as $value){
						$productIds[] = $value['product_id'];
					}
				}
			}
		}// Finished $atts['name_like'];
		
		/*
		/**
		 * Test attribute.
		 */		
		if(isset($atts['test'])){
			//$result = self::getProductList($client, $session, '');
			//$result = self::getProductByID(18, $client, $session);
			//var_dump($result);
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
			$result = self::getProductByID($productId, $client, $session);
			$images = self::getImagesByProductID($productId, $client, $session);
			
			// Build up the obtained information (if any) and pass them on in the $content variable which will be returned.
			if($result){
				if(!$images){
					unset($images);
					$images = '';
				}
				
				// Check if base url ends correctly (with a /)
				if($url[strlen($url)-1] != '/'){
					$url .= '/';
				}
				
				// Adjust resul's url path
				$result['url_path'] = $url . $result['url_path'];
				
				// Place the result and the image in an array that will be looped through in the template. Format: array('1' => array('result' => $result, 'images' => $images))
				$magento_products[] = array('result' => $result, 'images' => $images);
			}
		}
		
		// The template
		if(!empty($magento_products)){
			// Included functions to make template use more easy on the user
			global $Mage;
			$Mage = new Magento_Template_Helper($magento_products);
			
			try{
				include($template);
			}catch(Exception $e){
				$content .= __('Detected an error in the template file, actions have been interupted.', 'pronamic-magento-plugin');
			}
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
	 * Returns information about a product gotten from API by using the parsed ProductID, uses caching.
	 * 
	 * @param uint $productID
	 * @param Object $client
	 * @param String $session
	 */
	public static function getProductByID($productId, $client, $session){
		$result = '';
		$result = self::getAPICacheResults('magento-CachedProduct'.$productId);
		
		if(empty($result)){
			try{
				$result = $client->call($session, 'catalog_product.info', $productId);
				self::setAPICacheResults('magento-CachedProduct'.$productId, $result);
			}catch(Exception $e){	}	
		}
		
		return $result;
	}
	
	/**
	 * Returns information about the image with $productId, uses caching.
	 * 
	 * @param int $productId
	 * @param Object $client
	 * @param String $session
	 */
	public static function getImagesByProductID($productId, $client, $session){
		$image = '';		
		$image = self::getAPICacheResults('magento-CachedImage'.$productId);
		
		if(empty($image)){
			try{
				$image = $client->call($session, 'product_media.list', $productId);
				self::setAPICacheResults('magento-CachedImage'.$productId, $image);
			}catch(Exception $e){	}
		}
		
		return $image;
	}
	
	/**
	 * Returns a list of all products. Uses caching
	 * 
	 * @param String $apiKey
	 * @param Object $client
	 */
	private static function getProductList($client, $session, $filter){
		$result = '';
		$result = self::getAPICacheResults('magento-getProductList');
		
		if(empty($result)){
			try{
				if(!empty($filter)){
					$result = $client->call($session, 'catalog_product.list', array($filter));
				}else{
					$result = $client->call($session, 'catalog_product.list');
					self::setAPICacheResults('magento-getProductList', $result);
				}
			}catch(Exception $e){	}
		}
		
		return $result;
	}
	
	/**
	 * Accepts an array of ints, which it passes one at a time
	 * to get all products into a new array. Does not work with 
	 * multidimensional arrays. Uses caching
	 * 
	 * @param array of ints $productids
	 * @param Object $client
	 * @param String $session
	 */
	private static function getProductListByIDs($productIds, $client, $session){
		$result = '';
		$array = array();
		
		$cachename = '';
		foreach($productIds as $value){
			$cachename .= $value;
		}
		
		$array = self::getAPICacheResults('magento-getProductListByIDs'.$cachename);
		
		if(empty($array)){
			$error = '';
			foreach($productIds as $productId){
				try{
					$result = $client->call($session, 'catalog_product.info', $productId);
					$array[] = $result;					
				}catch(Exception $e){
					$error .= 'An error occured <br />';
				}
			}
			if(empty($error)){
				self::setAPICacheResults('magento-getProductListByIDs'.$cachename, $array);
			}
		}
		
		return $array;
	}
	
	/**
	 * Function which returns the catagory tree. Uses caching
	 * 
	 * @param Object $client
	 * @param String $session
	 */
	private static function getCategoryList($client, $session){
		$result = '';
		$result = self::getAPICacheResults('magento-getCategoryList');

		if(empty($result)){
			try{
				$result = $client->call($session, 'catalog_category.tree');
				self::setAPICacheResults('magento-getCategoryList', $result);
			}catch(Exception $e){	}
		}
		
		return $result;
	}
	
	/**
	 * Returns products assigned to a certain category id
	 *  
	 * @param Object $client
	 * @param String $session
	 * @param int $storeID
	 * @param int $categoryID
	 */
	public static function getProductsByCategoryID($client, $session, $storeID, $categoryID){
		$result = '';
		$result = self::getAPICacheResults('magento-getProductsByCategoryID');
				
		if(empty($result) && isset($storeID) && isset($categoryID)){
			try{
				$result = $client->call($session, 'category.assignedProducts', array($categoryID, $storeID));
				self::setAPICacheResults('magento-getProductsByCategoryID', $result);
			}catch(Exception $e){	echo 'wrong';}
		}
		
		return $result;
	}
	
	/**
	 * Save API call results in a transient
	 * 
	 * @param String $cachename
	 * @param Any variable $result
	 * @return true on succes
	 */
	private static function setAPICacheResults($cachename, $result){
		if(get_option('magento-caching-option')){
			set_transient($cachename, $result, self::CACHETIME);
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the cached results of a saved API call if this fails, returns an empty string.
	 * 
	 * @param String $cachename
	 * @return $result on succes, empty string on failure
	 */
	private static function getAPICacheResults($cachename){
		$result = '';
		if(get_option('magento-caching-option')){
			$result = get_transient($cachename);
			if(!empty($result)){
				return $result;
			}
		}
		return '';
	}
	
	/**
	 * Get a template ready, if there's no custom template in the current theme's stylesheet directory, get the default one.
	 * 
	 * @return String $template (Location to template file, custom or default)
	 */
	private static function getTemplate($templatemode){
		if(empty($templatemode)) $templatemode = 'default';
		$templates = array('magento-products-'.$templatemode.'.php');
		$template = locate_template($templates);
		if(!$template){
			if($templatemode != 'shortcode'){ // This is used for the collective custom widget template file.
				$templates = array('magento-products-widget.php');
				$template = locate_template($templates);
			}
			if(!$template){
				$template = 'templates/magento-products-default.php';
			}
		}
		
		return $template;
	}
	
	/**
	 * This function will set the stylesheet (enqueue it in WP header).
	 */
	private static function setStyleSheet(){
		$stylesheet = plugins_url('css/style.css', __FILE__);
		wp_register_style('pronamic-magento-plugin-stylesheet', $stylesheet);
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
				if(isset($value['children']) && is_array($value['children'])){
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
	
	public static function magento_auto_include(){
		if(function_exists('spl_autoload_register')){
			function magento_file_autoloader($name) {
				$name = str_replace('\\', DIRECTORY_SEPARATOR, $name);
	
				$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . $name . '.php';
	
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
		include 'page-magento.php';
	}
	
	public static function blocks(){
		
	}

	public static function pageSettings() {
		include 'page-settings.php';
	}
}

Magento::bootstrap();