<?php
/**
 * sublayout products
 *
 * @package	VirtueMart
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
 * @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
 */

defined('_JEXEC') or die('Restricted access');
$products_per_row = $viewData['products_per_row'];
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";

$product_model = VmModel::getModel('product');

$db = JFactory::getDBO();

function getCustomField($db, $pr_id, $custom_id) {
	$sql= "SELECT value FROM #__virtuemart_product_custom_plg_param_values WHERE id = (SELECT val FROM #__virtuemart_product_custom_plg_param_ref AS t1 WHERE t1.virtuemart_product_id = " . $pr_id . " and t1.virtuemart_custom_id = " . $custom_id . " LIMIT 1);";
	$db->setQuery($sql);
	$db->query();
	$out = $db->loadAssocList();
	if ( !empty($out[0]['value']) ) {
		return $out[0]['value'];
	} else {
		return "";
	}
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs');

$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

foreach ($viewData['products'] as $type => $products ) {

	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

	if(!empty($type) and count($products)>0){
		$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
<div class="<?php echo $type ?>-view">
  <h4><?php echo $productTitle ?></h4>
		<?php // Start the Output
    }

	// Calculating Products Per Row
	$cellwidth = ' width'.floor ( 100 / $products_per_row );

	$BrowseTotalProducts = count($products);

	$col = 1;
	$nb = 1;
	$row = 1;

	foreach ( $products as $product ) {

		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
<!--	<div class="horizontal-separator"></div>-->
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
	<div class="row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

    // Show Products ?>
	<div class="product vm-col<?php echo ' vm-col-' . $products_per_row . $show_vertical_separator ?>">
		<div class="spacer">
			<div class="vm-product-media-container">

				<?php if ($product->product_parent_id != 0) {
					$p_product = $product_model->getProduct($product->product_parent_id);
					$link = $p_product->link;
				} else {
					$link = $product->link;
				}
				?>

				<a title="<?php echo $product->product_name ?>" href="<?php echo $link.$ItemidStr; ?>">
					<?php
					echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
					?>
				</a>
			</div>

			<div class="vm-product-rating-container">
				<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
				if ( VmConfig::get ('display_stock', 1)) { ?>
					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
				<?php }
				echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
				?>
			</div>


				<div class="quantity-box-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
					<?php if ( !(empty($product->virtuemart_manufacturer_id[0])) ) { ?>
						<?php $manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $product->virtuemart_manufacturer_id[0], FALSE); ?>
						<div class="manufacture">
							<a title="<?php echo $product->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $product->mf_name; ?></a>
						</div>
					<?php } ?>

					<?php if ($product->product_parent_id != 0) {
						$p_product = $product_model->getProduct($product->product_parent_id);
						?>
						<h2 class="pr_name"><?php echo JHtml::link ($p_product->link.$ItemidStr, $product->product_name); ?></h2>
					<?php } else { ?>
						<h2 class="pr_name"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h2>
					<?php } ?>


					<?php if(!empty($rowsHeight[$row]['product_s_desc'])){
					?>
<!--					<p class="product_s_desc">-->
<!--						--><?php //// Product Short Description
//						if (!empty($product->product_s_desc)) {
//							echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ..  .') ?>
<!--						--><?php //} ?>
<!--					</p>-->
			<?php  } ?>
				</div>
			<?php //echo $rowsHeight[$row]['price'] ?>

			<?php
			$ids = array();
			if ($product->product_parent_id != 0) {
				$ids[] = $product->product_parent_id;;
				$ids = array_merge($ids, $product_model->getProductChildIds($product->product_parent_id));
			}
			else {
				$ids[] = $product->virtuemart_product_id;
				$ids = array_merge($ids, $product_model->getProductChildIds($product->virtuemart_product_id));
			}
			if ( !empty($ids) ) {
				$child_products = array();
				$output = $product_model->getProducts($ids);
				while (count($output)) {
					$min_volume = $output[0];
					$ind = 0;
					for ($i = 0; !empty($output[$i]); $i++) {
						if (getCustomField($db, $output[$i]->virtuemart_product_id, 23) > $min_volume) {
							$min_volume = $output[$i];
							$ind = $i;
						}
					}
					$child_products[] = $output[$ind];
					unset($output[$ind]);
					$output = array_values($output);
				}
			}
			?>

			<?php
			$p_volume = getCustomField($db,$child_products[0]->virtuemart_product_id, 23);
			$p_type = getCustomField($db,$child_products[0]->virtuemart_product_id, 2);
			?>

			<?php if ( !empty($p_type) ) { ?>
				<span class="pr_type"><?php echo ucfirst($p_type); ?></span>
			<?php } ?>

			<?php for ($i = 0; $i < count($child_products); $i++) { ?>
				<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?>" <?php if ($i != 0) echo 'style="display: none;"'?>> <?php
					echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$child_products[$i],'currency'=>$currency)); ?>
				</div>
			<?php } ?>

			<?php if ( count($child_products) > 1 ) {
				$op_value = 0;
				?>

				<?php if ( !empty($p_volume) ) { ?>
					<select class="custom-volume">
						<?php foreach ($child_products as $pr) { ?>
							<?php
							$p_volume = getCustomField($db,$pr->virtuemart_product_id, 23)
							?>
							<?php if ( !empty($p_volume) ) { ?>
								<option value="<?php $op_value++; echo $op_value; ?>"><?php echo $p_volume; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
				<?php } ?>
			<?php } else if ( !empty($p_volume) ) { ?>
				<span class="pr_volume"><?php echo JHtml::link ($child_products[0]->link.$ItemidStr, 'Объём: ' . $p_volume); ?></span>
			<?php } ?>

<!--			--><?php ////echo $rowsHeight[$row]['customs'] ?>
<!--			<div class="vm3pr---><?php //echo $rowsHeight[$row]['customfields'] ?><!--"> --><?php
//				echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row])); ?>
<!--			</div>-->

			<?php for ($i = 0; $i < count($child_products); $i++) { ?>
				<div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>" <?php if ($i != 0) echo 'style="display: none;"'?>> <?php
					echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$child_products[$i],'rowHeights'=>$rowsHeight[$row])); ?>
				</div>
			<?php } ?>

<!--			<div class="vm-details-button">-->
<!--				--><?php //// Product Details Button
//				$link = empty($product->link)? $product->canonical:$product->link;
//				echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
//				//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
//				?>
<!--			</div>-->

		</div>
	</div>

	<?php
    $nb ++;

      // Do we need to close the current row now?
      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
    <div class="clear"></div>
  </div>
      <?php
      	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

      if(!empty($type)and count($products)>0){
        // Do we need a final closing row tag?
        //if ($col != 1) {
      ?>
    <div class="clear"></div>
  </div>
    <?php
    // }
    }
  }
