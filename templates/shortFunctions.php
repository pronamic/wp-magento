<?php 
class Mage{
	private static $result;
	private static $image;
	
	public function __construct($result, $image){
		self::$result = $result;
		self::$image = $image;
	}
	
	public function product_title(){
		return self::$result['name'];
	}
	
	public function product_price(){
		return number_format(self::$result['price'], 2);
	}
	
	public function product_url(){
		return self::$result['url_path'];
	}
	
	public function has_image(){
		if(isset(self::$image) && !empty(self::$image)){
			return true;
		}
		return false;
	}
	
	public function product_image_url(){
		return self::$image;
	}
}
?>