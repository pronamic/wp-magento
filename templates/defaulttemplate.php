<?php
function wrapTemplateStart(){
	$content = '';
	
	// Start of the default template wrapper
	$content .= '<ul class="pronamic-magento-items-grid">';
	
	return $content;
}

function templateBody($result, $image){
	$content = '';
	
	// This is the default template for the Pronamic-Magento plugin
	$content .= '<li class="pronamic-magento-item">';
		
		if($image){
			$content .= '<a href="'. $url . $result['url_path'] .'" target="_blank"><img src="'. $image['url'] .'" alt="" /></a>';
		}
		
		$content .= '<h2><a href="'. $url . $result['url_path'] .'" target="_blank">'. $result['name'] .'</a></h2>';
				
		$content .= '<span class="pronamic-magento-price-box">
			<span class="pronamic-magento-price">&euro;'. number_format($result['price'], 2) .'</span>
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