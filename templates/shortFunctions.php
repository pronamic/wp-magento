<?php 
class Mage{
	//private static $result;
	//private static $image;
	
	public function __construct(){
		//self::$result = $result;
		//self::$image = $image;
	}
	
	public function product_title(){
		global $i;
		global $magento_products;
		echo $magento_products[$i]['result']['name'];
	}
	
	public function product_price(){
		global $i;
		global $magento_products;
		echo number_format($magento_products[$i]['result']['price'], 2);
	}
	
	public function product_url(){
		global $i;
		global $magento_products;
		echo $magento_products[$i]['result']['url_path'];
	}
	
	public function has_image(){
		global $i;
		global $magento_products;
		if(isset($magento_products[$i]['image']) && !empty($magento_products[$i]['image'])){
			return true;
		}
		return false;
	}
	
	public function product_image_url(){
		global $i;
		global $magento_products;
		echo $magento_products[$i]['image'];
	}
}
?>