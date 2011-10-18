<?php /**
 * This function is used to output Magento products. You may customize the output to your likings, using the following preset functions.
 * 
 * $Mage->have_products()			--	Checks wether there's a product to be displayed or not. We recommend you to put it in a 'while($Mage->have_products()):' loop. Also, all functions below should be within this loop. They will not work when not in this loop.
 * $Mage->product_title()			--	Outputs the product title.
 * $Mage->product_url()				--	Outputs the product url.
 * $Mage->product_price($currency, [$behind, [$decimals, [$decimalseparator, [$thousandsseparator]]]])	--	Outputs the product price in format 0.00. This function shows the price or when there's a discount, it shows the price after discount. It needs to have the currency logo parsed to show it in front of the discount price. For euros this would be: &euro; The second argument is an optional boolean (true or false) and should be set to true when the currency logo or name should be put after the price, it defaults to false when left empty. The other three arguments are optional as well: The first of the three optional arguments takes an int (number) which will set the number of decimal numbers of the price, this defaults to two if left empty. The next argument is the separator between the non-decimal and the decimal numbers. By default the separator is a period. The last argument sets the separator for the thousands of a number. This defaults to a comma when left empty.
 * $Mage->product_default_price()	--	Outputs the products default price in format 0.00. This is the price without discount.
 * $Mage->product_special_price()	--	Outputs the discount price in format 0.00.
 * $Mage->has_image()				--	Checks wether there's an image to display or not.
 * $Mage->product_thumbnail_url()	--	Outputs the url to the first image of a product. A clever thing to do, would be putting this inside a 'if($Mage->has_image()):' check.
 * $Mage->have_images()				--	Check wether there's an image to be displayed or not. We recommend you to put it in a 'while($Mage->have_images())' loop.
 * $Mage->product_image_url()		--	Outputs the url to the image. This function should be placed within a $Mage->have_images() loop otherwise it won't work.
 * 
 * @return String $content
 */?>

<ul class="pronamic-magento-items-grid">

	<?php while($Mage->have_products()): ?>

	<li class="pronamic-magento-item">
		
		<?php if($Mage->has_image()): ?>
			<a href="<?php $Mage->product_url(); ?>" target="_blank"><img src="<?php $Mage->product_thumbnail_url(); ?>" alt="" /></a>
		<?php else: ?>
			<a href="<?php $Mage->product_url(); ?>" target="_blank"><span class="magento-no-image"></span></a>
		<?php endif; ?>
		
		<h2><a href="<?php $Mage->product_url(); ?>" target="_blank">
			<?php $Mage->product_title(); ?>
		</a></h2>
		
		<span class="pronamic-magento-price-box">
			<span class="pronamic-magento-price"><?php $Mage->product_price('&euro;', false, 2, '.', ','); ?></span>
		</span>

	</li>

	<?php endwhile; ?>

</ul>