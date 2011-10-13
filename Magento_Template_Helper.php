<?php 
class Magento_Template_Helper{
	private $magento_products;
	private $i;
	private $count;
	
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
	}
	
	/**
	 * Tests if there are still products to show
	 * 
	 * @return true when there is a product next
	 */
	public function have_products(){
		$this->i++;
		if($this->i < $this->count){
			return true;
		}
		return false;
	}
	
	/**
	 * Prints the product title
	 */
	public function product_title(){
		echo $this->magento_products[$this->i]['result']['name'];
	}
	
	/**
	 * Prints the price, when there's a discount it prints the price
	 * striped out, with the discount price behind it.
	 */
	public function product_price(){
		if(isset($this->magento_products[$this->i]['result']['special_price'])){
			echo '<del>'; $this->product_default_price(); echo '</del> <b>'; $this->product_special_price(); echo '</b>'; 
		}else{
			echo $this->product_default_price();
		}
	}
	
	/**
	 * Prints the discount price of a product.
	 */
	public function product_special_price(){
		echo number_format($this->magento_products[$this->i]['result']['special_price'], 2);
	}
	
	/**
	 * Prints the price of a product, without discount.
	 */
	public function product_default_price(){
		echo number_format($this->magento_products[$this->i]['result']['price'], 2);
	}
	
	/**
	 * Prints the url to a product
	 */
	public function product_url(){
		echo $this->magento_products[$this->i]['result']['url_path'];
	}
	
	/**
	 * Tests if the product has an image.
	 * 
	 * @return true when there's an image to show
	 */
	public function has_image(){
		if(isset($this->magento_products[$this->i]['image']) && !empty($this->magento_products[$this->i]['image']))	return true;
		return false;
	}

	/**
	 * Prints the url to an image
	 */
	public function product_image_url(){
		echo $this->magento_products[$this->i]['image'];
	}
}
?>