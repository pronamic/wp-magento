<?php /**
 * This function is used to output Magento products. You may customize the output to your likings, using the following preset functions.
 * 
 * Mage::product_title()			--	Outputs the product title
 * Mage::product_url()				--	Outputs the product url
 * Mage::product_price()			--	Outputs the product price in format 0.00
 * Mage::has_image()				--	Checks wether there's an image to display or not
 * Mage::product_image_url()		--	Outputs the url to the image
 * 
 * @return String $content
 */?>

<?php global $magento_products; if(!empty($magento_products)): ?>

<ul class="pronamic-magento-items-grid">

	<?php global $i; for($i = 0; $i < count($magento_products); $i++): ?>

	<li class="pronamic-magento-item">
		
		<?php if(Mage::has_image()): ?>
			<a href="<?php Mage::product_url(); ?>" target="_blank"><img src="<?php Mage::product_image_url(); ?>" alt="" /></a>
		<?php endif; ?>
		
		<h2><a href="<?php Mage::product_url(); ?>" target="_blank">
			<?php Mage::product_title(); ?>
		</a></h2>
		
		<span class="pronamic-magento-price-box">
			<span class="pronamic-magento-price">&euro;<?php Mage::product_price(); ?></span>
		</span>

	</li>

	<?php endfor; ?>

</ul>

<?php endif; ?>