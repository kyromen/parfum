<?php // no direct access
defined ('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();

$product_model = VmModel::getModel('product');

$col = 1;
$pwidth = ' width' . floor (100 / $products_per_row);
if ($products_per_row > 1) {
	$float = "floatleft";
} else {
	$float = "center";
}
if($params->get ('moduleclass_sfx') == '_color') {
	$float = "";
}
?>

<div class="vmgroup<?php echo $params->get ('moduleclass_sfx') ?> jcarousel-wrapper">

	<?php if ($headerText) {
		if($params->get('product_group') == 'recent') { ?>
			<p class="vmheader header"><span><?php echo $headerText ?> (<span class="recentCount"><?php echo count($products)-1; ?></span>)</span></p>
		<?php } else { ?>
			<p class="vmheader header"><span><?php echo $headerText ?></span></p>
		<?php }
	}
	if ($display_style == "div") {
		?>
		<div class="vmproduct<?php echo $params->get ('moduleclass_sfx'); ?> productdetails">
			<?php foreach ($products as $product) { ?>
				<div class="<?php echo $pwidth ?> <?php echo $float ?>">
					<div class="spacer">
						<?php
						if (!empty($product->images[0])) {
							$image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
						} else {
							$image = '';
						}
						echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
						echo '<div class="clear"></div>';
						$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
							$product->virtuemart_category_id); ?>
						<a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';

						if ($show_price) {
							// 		echo $currency->priceDisplay($product->prices['salesPrice']);
							if (!empty($product->prices['salesPrice'])) {
								echo $currency->createPriceDiv ('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
							}
							// 		if ($product->prices['salesPriceWithDiscount']>0) echo $currency->priceDisplay($product->prices['salesPriceWithDiscount']);
							if (!empty($product->prices['salesPriceWithDiscount'])) {
								echo $currency->createPriceDiv ('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
							}
						}
						if ($show_addtocart) {
							echo mod_virtuemart_product::addtocart ($product);
						}
						?>
					</div>
				</div>
				<?php
				if ($col == $products_per_row && $products_per_row && $col < $totalProd) {
					echo "	</div><div style='clear:both;'>";
					$col = 1;
				} else {
					$col++;
				}
			} ?>
		</div>
		<br style='clear:both;'/>

	<?php
	} else {
		$rows = (int)(count ($products) / $products_per_row);
		?>
		<span><?php echo $module->title; ?></span>
		<div class="jcarousel">
			<ul class="vmproduct<?php echo $params->get ('moduleclass_sfx'); ?> productdetails">
				<?php foreach ($products as $product) : ?>
					<li class="<?php echo $float ?>">
						<?php
						if (!empty($product->images[0])) {
							$image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
						} else {
							$image = '';
						}

						if ($product->product_parent_id != 0) {
							$p_product = $product_model->getProduct($product->product_parent_id);
							echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $p_product->virtuemart_product_id . '&virtuemart_category_id=' . $p_product->virtuemart_category_id), $image, array('title' => $p_product->product_name));
							echo '<div class="clear"></div>';
							$url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $p_product->virtuemart_product_id . '&virtuemart_category_id=' .
								$p_product->virtuemart_category_id);
						} else {
							echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
							echo '<div class="clear"></div>';
							$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
								$product->virtuemart_category_id);
						}

						$manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $product->virtuemart_manufacturer_id[0], FALSE);
						?>
						<div class="manufacture">
							<a title="<?php echo $product->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $product->mf_name; ?></a>
						</div>
						<h2 class="pr_name">
							<a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>
						</h2>

						<?php    echo '<div class="clear"></div>';
						// $product->prices is not set when show_prices in config is unchecked
						if ($show_price and  isset($product->prices)) {
							echo '<div class="product-price">'.$currency->createPriceDiv ('salesPrice', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
							if ($product->prices['salesPriceWithDiscount'] > 0) {
								echo $currency->createPriceDiv ('salesPriceWithDiscount', '', $product->prices, FALSE, FALSE, 1.0, TRUE);
							}
							echo '</div>';
						}
						if ($show_addtocart) {
							echo mod_virtuemart_product::addtocart ($product);
						}
						?>
					</li>
					<?php
					if ($col == $products_per_row && $products_per_row) {
						echo '
		</ul><div class="clear"></div>
			<ul  class="vmproduct' . $params->get ('moduleclass_sfx') . ' productdetails">';
						$col = 1;
					} else {
						$col++;
					}
				endforeach; ?>
			</ul>
			</div>

			<a href="#" class="jcarousel-control-prev"></a>
			<a href="#" class="jcarousel-control-next"></a>

<!--			<p class="jcarousel-pagination"></p>-->
			<div class="clear"></div>

			<?php
			}
			if ($footerText) : ?>
				<div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
					<?php echo $footerText ?>
				</div>
			<?php endif; ?>
		</div>

<script src="/templates/zvv/js/jquery.jcarousel.min.js"></script>
<script src="/templates/zvv/js/jcarousel.responsive.js"></script>