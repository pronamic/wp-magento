<?php 
class Magento_Product{
	private $magento_products;
	private $i;
	private $count;
	
	public function __construct($magento_products){
		$this->magento_products = $magento_products;
		$this->i = -1;
		$this->count = count($magento_products);		
	}
	
	public function have_products(){
		$this->i++;
		if($this->i < $this->count){
			return true;
		}
		return false;
	}
	
	public function product_title(){
		echo $this->magento_products[$this->i]['result']['name'];
	}
	
	public function product_price(){
		echo number_format($this->magento_products[$this->i]['result']['price'], 2);
	}
	
	public function product_url(){
		echo $this->magento_products[$this->i]['result']['url_path'];
	}
	
	public function has_image(){
		if(isset($this->magento_products[$this->i]['image']) && !empty($this->magento_products[$this->i]['image']))	return true;
		return false;
	}
	
	public function product_image_url(){
		echo $this->magento_products[$this->i]['image'];
	}
}
?>