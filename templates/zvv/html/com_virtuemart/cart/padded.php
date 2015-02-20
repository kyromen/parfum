<?php
/**
*
* Layout for the add to cart popup
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2013 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$db = JFactory::getDBO();

function getVmMediaFile($virtuemart_media_id, $getthumb = false) {
	$db = JFactory::getDBO();
	if ($getthumb) $sql = "SELECT file_url_thumb FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
	else $sql = "SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
	$db->setQuery($sql);
	return $db->loadResult();
}

if($this->products){
	foreach($this->products as $product){
		if($product->quantity>0){ ?>
			<?php
			// prdduct volume
			$sql= "SELECT value FROM #__virtuemart_product_custom_plg_param_values WHERE id = (SELECT val FROM #__virtuemart_product_custom_plg_param_ref AS t1 WHERE t1.virtuemart_product_id = " . $product->virtuemart_product_id . " and t1.virtuemart_custom_id = " . 26 . " LIMIT 1);";
			$db->setQuery($sql);
			$db->query();
			$p_volume = $db->loadAssocList();
			?>

			<div class="addtocart-popup">
				<span>Товар добавлен</span>
				<table>
					<tbody>
						<tr>
							<td></td>
							<td>Название</td>
							<td>Объём</td>
							<td>Кол-во</td>
							<td>Цена</td>
						</tr>
						<tr>
							<td style="width: 140px">
								<?php if (!empty($product->virtuemart_media_id)) { ?>
									<img style="width: 100px" src="/<?php echo getVmMediaFile($product->virtuemart_media_id[0]); ?>" />
								<?php } ?>
							</td>
							<td style="width: 280px"><?php echo $product->product_name; ?></td>
							<td style="width: 120px"><?php echo $p_volume[0]['value']; ?></td>
							<td style="width: 90px"><?php echo $product->quantity; ?></td>
							<td><?php echo (int)$product->allPrices[0]['product_price'] * $product->quantity  . " RUB"; ?></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo '<span><a class="continue_link" href="' . $this->continue_link . '" >' . vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a></span>'; ?></td>
							<td colspan="3"><?php echo '<span><a class="showcart" href="' . $this->cart_link . '">' . vmText::_('COM_VIRTUEMART_CART_SHOW') . '</a></span>'; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php } ?>
<!--			--><?//
//			echo '<h4>'.vmText::sprintf('COM_VIRTUEMART_CART_PRODUCT_ADDED',$product->product_name,$product->quantity).'</h4>';
//		else {
//			if(!empty($product->errorMsg)){
//				echo '<div>'.$product->errorMsg.'</div>';
//			}
//		}
	}
}

if(VmConfig::get('popup_rel',1)){
	//VmConfig::$echoDebug=true;
	if ($this->products and is_array($this->products) and count($this->products)>0 ) {

		$product = reset($this->products);

		$customFieldsModel = VmModel::getModel('customfields');
		$product->customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($product->allIds,'R');

		$customFieldsModel->displayProductCustomfieldFE($product,$product->customfields);
		if(!empty($product->customfields)){
			?>
			<div class="product-related-products">
			<h4><?php echo vmText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
			<?php
		}
		foreach($product->customfields as $rFields){

				if(!empty($rFields->display)){
				?><div class="product-field product-field-type-<?php echo $rFields->field_type ?>">
				<div class="product-field-display"><?php echo $rFields->display ?></div>
				</div>
			<?php }
		} ?>
		</div>
	<?php
	}
}

?><br style="clear:both">
