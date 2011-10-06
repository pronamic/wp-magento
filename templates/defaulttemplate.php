<?php
function wrapTemplateStart(){
	$content = '';
	
	// Start of the default template wrapper
	$content .= '<ul class="pronamic-magento-items-grid">';
	
	return $content;
}

/**
 * This function is used to output Magento products. You may customize the output to your likings, using the following preset functions.
 * 
 * Mage::product_title()			--	Outputs the product title
 * Mage::product_url()				--	Outputs the product url
 * Mage::product_price()			--	Outputs the product price in format 0.00
 * Mage::has_image()				--	Checks wether there's an image to display or not
 * Mage::product_image_url()		--	Outputs the url to the image
 * 
 * @return String $content
 */
function templateBody(){
	$content = '';
	
	$content .= '<li class="pronamic-magento-item">';
		
		if(Mage::has_image()){
			$content .= '<a href="'. Mage::product_url() .'" target="_blank"><img src="'. Mage::product_image_url() .'" alt="" /></a>';
		}
		
		$content .= '<h2><a href="'. Mage::product_url() .'" target="_blank">' . Mage::product_title() . '</a></h2>';
				
		$content .= '<span class="pronamic-magento-price-box">
			<span class="pronamic-magento-price">&euro;'. Mage::product_price() .'</span>
		</span>
	</li>';
	
	return $content;
}

function wrapTemplateEnd(){
	$content = '';
	
	// End of the default template wrapper
	$content .= '</ul>';
	
	return $content;
}	
?>