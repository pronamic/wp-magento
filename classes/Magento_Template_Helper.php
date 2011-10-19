<?php 
class Magento_Template_Helper{
	private $magento_products;
	private $i;
	private $count;
	private $imageI;
	private $countImage;
	
	/**
	 * Constructor initializes the variables so a correct loop can be made to keep
	 * the template simple for the not so experienced users.
	 * 
	 * @param mixed array $magento_products
	 */
	public function __construct($magento_products){
		$this->magento_products = $magento_products;
		$this->i = -1;
		$this->count = count($magento_products);
		//var_dump($this->magento_products);
	}
	
	/**
	 * Tests if there are still products to show
	 * 
	 * @return true when there is a product next
	 */
	public function have_products(){
		$this->i++;
		if($this->i < $this->count){
			$this->imageI = -1;
			if(isset($this->magento_products[$this->i]['images'])) $this->countImage = count($this->magento_products[$this->i]['images']);
			return true;
		}
		return false;
	}
	
	/**
	 * Prints the product title
	 */
	public function product_title(){
		if($this->inside_product_loop()){
			echo $this->magento_products[$this->i]['result']['name'];
		}
	}
	
	/**
	 * Prints the price, when there's a discount it prints the price
	 * striped out, with the discount price behind it.
	 */
	public function product_price(){
		if($this->inside_product_loop()){
			// Currency settings
			$price = $this->magento_products[$this->i]['result']['price'];
			$specialprice = $this->magento_products[$this->i]['result']['special_price'];
			$currency = get_option('magento-currency-setting');
			$position = get_option('magento-currency-position');
			
			$currencies = array('USD', 'EUR', 'GBP', 'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NZD', 'NOK', 'PHP', 'PLN', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY');
			$replacements = array('&#36;', '&euro;', '&pound;', '&#36;', '&#36;', '&#36;', 'CZK', 'DKK', 'HKD', 'HUF', 'ILS', '&yen;', 'MYR', '&#36;', '&#36;', 'NOK', 'PHP', 'PLN', '&#36;', 'SEK', 'CHF', 'TWD', 'THB', 'TL');
			foreach($currencies as $key => $value){
				if($currency == $value){
					$currency = $replacements[$key];
				}
			}
			
			$left = ''; $right = ''; $leftspace = ''; $rightspace = '';
			switch($position){
				case 'left': $left = $currency; break;
				case 'right': $right = $currency; break; 
				case 'left_space': $leftspace = $currency . ' '; break;
				case 'right_space': $rightspace = ' ' . $currency; break;
				default: $left = $currency; break;
			}
			
			if(isset($specialprice)){				
				echo '<del>'.$left.$leftspace; echo $this->this_number_format($price); echo $right.$rightspace.'</del> <b>'.$left.$leftspace; echo $this->this_number_format($specialprice); echo $right.$rightspace.'</b>';
			}else{
				echo $left.$leftspace;  echo $this->this_number_format($price); echo $right.$rightspace;
			}
		}
	}
	
	/**
	 * Returns the formatted input. Uses the currency settings.
	 * 
	 * @param float $price
	 * @return float The number in a new format
	 */
	public function this_number_format($price){
		$decimals = get_option('magento-number-decimals');
		if(is_numeric($decimals)) $decimals = (int) $decimals; else $decimals = 2;
		$decimalseparator = get_option('magento-decimal-separator');
		$thousandsseparator = get_option('magento-thousands-separator');
		
		if(empty($decimalseparator)) $decimalseparator = '.';
		if(empty($thousandsseparator)) $thousandsseparator = ',';
		
		return number_format($price, $decimals, $decimalseparator, $thousandsseparator);
	}
	
	/**
	 * Prints the discount price of a product.
	 */
	public function product_special_price(){
		if($this->inside_product_loop()){
			echo $this->this_number_format($this->magento_products[$this->i]['result']['special_price']);
		}
	}
	
	/**
	 * Prints the price of a product, without discount.
	 */
	public function product_default_price(){
		if($this->inside_product_loop()){
			echo $this->this_number_format($this->magento_products[$this->i]['result']['price']);
		}
	}
	
	/**
	 * Prints the url to a product
	 */
	public function product_url(){
		if($this->inside_product_loop()){
			echo $this->magento_products[$this->i]['result']['url_path'];
		}
	}
	
	/**
	 * Test if there are still images to show, works good in a while loop
	 * 
	 * @return true if there is an image at this point in the array.
	 */
	public function have_images(){
		if($this->inside_product_loop()){	
			if($this->inside_product_loop()){
				$this->imageI++;
				if($this->imageI < $this->countImage){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Prints the url to an image, for use in a while loop with have_images();
	 */
	public function product_image_url(){
		if($this->inside_product_loop() && $this->inside_image_loop()){
			echo $this->magento_products[$this->i]['images'][$this->imageI]['url'];
		}
	}
	
	/**
	 * Tests if the product has an image
	 */
	public function has_image(){
		if($this->inside_product_loop() && isset($this->magento_products[$this->i]['images']) && !empty($this->magento_products[$this->i]['images'])) return true;
		return false;
	}
	
	/**
	 * Tests if the product has an image.
	 */
	public function product_thumbnail_url(){
		if($this->inside_product_loop() && isset($this->magento_products[$this->i]['images']) && !empty($this->magento_products[$this->i]['images'])) echo $this->magento_products[$this->i]['images'][0]['url'];
	}
	
	/**
	 * Returns true if the current function is called within the have_products() loop
	 * 
	 * @return boolean true if within have_products() loop 
	 */
	private function inside_product_loop(){
		if($this->i >= 0 && $this->i < $this->count) return true;
		return false;
	}
	
	private function inside_image_loop(){
		if($this->imageI >= 0 && $this->imageI < $this->countImage) return true;
		return false;
	}
}
?>