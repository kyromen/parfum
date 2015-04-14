<?php 
/*--------------------------------------------------------------------------------------------------------
# VP One Page Checkout - Joomla! System Plugin for VirtueMart 3
----------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012 - 2015 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      http://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 6 $
$LastChangedDate: 2015-01-22 04:42:10 +0530 (Thu, 22 Jan 2015) $
$Id: default_pricelist.php 6 2015-01-21 23:12:10Z abhishekdas $
----------------------------------------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

$product_model = VmModel::getModel('product');
$db = JFactory::getDBO();

$total_colspan       = VmConfig::get('show_tax') ? 7 : 6;
$total_colspan       = $this->params->get('hide_discount', 0) ? ($total_colspan - 1) : $total_colspan;
$subheading_colspan  = $this->params->get('hide_sku', 0) ? 4 : 5;
// If resposive then set the dynamic css for the price list table layout
if($this->params->get('responsive', 1))
{
	$document = JFactory::getDocument();
	if(VmConfig::get('show_tax'))
	{ 
		$css = $this->helper->cleanCSS("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_NAME') . "';
				}
				.cart-p-list td:nth-of-type(2):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SKU') . "';
				}
				.cart-p-list td:nth-of-type(3):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_PRICE') . "';
				}
				.cart-p-list td:nth-of-type(4):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_QUANTITY') . "';
				}
				.cart-p-list td:nth-of-type(5):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') . "';
				}
				.cart-p-list td:nth-of-type(6):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-p-list td:nth-of-type(7):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
				.cart-sub-total td:nth-of-type(1):before,
				.cart-coupon-row td:nth-of-type(1):before,
				.discount-rule-per-bill td:nth-of-type(1):before,
				.tax-rule-per-bill td:nth-of-type(1):before,
				.shipping-row td:nth-of-type(1):before,
				.payment-row td:nth-of-type(1):before,
				.grand-total td:nth-of-type(1):before,
				.grand-total-p-currency td:nth-of-type(1):before {
					content: '';
				}
				.cart-sub-total td:nth-of-type(2):before,
				.cart-coupon-row td:nth-of-type(2):before,
				.discount-rule-per-bill td:nth-of-type(2):before,
				.tax-rule-per-bill td:nth-of-type(2):before,
				.shipping-row td:nth-of-type(2):before,
				.payment-row td:nth-of-type(2):before,
				.grand-total td:nth-of-type(2):before,
				.grand-total-p-currency td:nth-of-type(2):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') . "';
				}
				.cart-sub-total td:nth-of-type(3):before,
				.cart-coupon-row td:nth-of-type(3):before,
				.discount-rule-per-bill td:nth-of-type(3):before,
				.tax-rule-per-bill td:nth-of-type(3):before,
				.shipping-row td:nth-of-type(3):before,
				.payment-row td:nth-of-type(3):before,
				.grand-total td:nth-of-type(3):before,
				.grand-total-p-currency td:nth-of-type(3):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-sub-total td:nth-of-type(4):before,
				.cart-coupon-row td:nth-of-type(4):before,
				.discount-rule-per-bill td:nth-of-type(4):before,
				.tax-rule-per-bill td:nth-of-type(4):before,
				.shipping-row td:nth-of-type(4):before,
				.payment-row td:nth-of-type(4):before,
				.grand-total td:nth-of-type(4):before,
				.grand-total-p-currency td:nth-of-type(4):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
			}
		");
	} 
	else 
	{
		$css = $this->helper->cleanCSS("
			@media (max-width: 767px) {
				.cart-p-list td:nth-of-type(1):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_NAME') . "';
				}
				.cart-p-list td:nth-of-type(2):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SKU') . "';
				}
				.cart-p-list td:nth-of-type(3):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_PRICE') . "';
				}
				.cart-p-list td:nth-of-type(4):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_QUANTITY') . "';
				}
				.cart-p-list td:nth-of-type(5):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-p-list td:nth-of-type(6):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
				.cart-sub-total td:nth-of-type(1):before,
				.cart-coupon-row td:nth-of-type(1):before,
				.discount-rule-per-bill td:nth-of-type(1):before,
				.tax-rule-per-bill td:nth-of-type(1):before,
				.shipping-row td:nth-of-type(1):before,
				.payment-row td:nth-of-type(1):before,
				.grand-total td:nth-of-type(1):before,
				.grand-total-p-currency td:nth-of-type(1):before {
					content: '';
				}
				.cart-sub-total td:nth-of-type(2):before,
				.cart-coupon-row td:nth-of-type(2):before,
				.discount-rule-per-bill td:nth-of-type(2):before,
				.tax-rule-per-bill td:nth-of-type(2):before,
				.shipping-row td:nth-of-type(2):before,
				.payment-row td:nth-of-type(2):before,
				.grand-total td:nth-of-type(2):before,
				.grand-total-p-currency td:nth-of-type(2):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') . "';
				}
				.cart-sub-total td:nth-of-type(3):before,
				.cart-coupon-row td:nth-of-type(3):before,
				.discount-rule-per-bill td:nth-of-type(3):before,
				.tax-rule-per-bill td:nth-of-type(3):before,
				.shipping-row td:nth-of-type(3):before,
				.payment-row td:nth-of-type(3):before,
				.grand-total td:nth-of-type(3):before,
				.grand-total-p-currency td:nth-of-type(3):before {
					content: '" . vmText::_('COM_VIRTUEMART_CART_TOTAL') . "';
				}
			}
		");
	}
	if($this->params->get('hide_discount', 0))
	{
		$css .= $this->helper->cleanCSS("
			th.col-discount,
			td.col-discount {
				display: none !important;
			}
		");
	}
	if($this->params->get('hide_sku', 0))
	{
		$css .= $this->helper->cleanCSS("
			th.col-sku,
			td.col-sku {
				display: none !important;
			}
		");
	}
	$document->addStyleDeclaration($css);
}
?>
<table class="cart-summary proopc-table-striped" width="100%" border="0">

	<thead>
		<tr>
			<th class="col-image" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_IMAGE') ?></span>
			</th>
			<th class="col-name" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_NAME') ?></span>
			</th>
			<th class="col-sku" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_SKU') ?></span>
			</th>
			<th class="col-price" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_PRICE') ?></span>
			</th>
			<th class="col-qty" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_QUANTITY') ?></span>
			</th>
<!--			--><?php //if(VmConfig::get('show_tax')) : ?>
<!--				<th class="col-tax" align="left">-->
<!--					<span>--><?php // echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?><!--</span>-->
<!--				</th>-->
<!--			--><?php //endif; ?>
<!--			<th class="col-discount" align="left">-->
<!--				<span>--><?php //echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?><!--</span>-->
<!--			</th>-->
			<th class="col-total" align="left">
				<span><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL_COL') ?></span>
			</th>
			<th align="right">
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php // Show all products ?>
		<?php foreach($this->cart->products as $pkey => $prow) : ?>
			<tr valign="top" class="cart-p-list">
				<td class="col-image" align="left" >
					<?php if($prow->virtuemart_media_id && !empty($prow->images[0]) && VmConfig::get('oncheckout_show_images')) : ?>
						<div class="cart-product-description with-image clearfix">
							<div class="cart-images">
								<?php echo $prow->images[0]->displayMediaThumb('class="img-responsive"', false); ?>
							</div>
						</div>
					<?php endif; ?>
				</td>
				<td class="col-name" align="left" >
					<div class="cart-product-description">
						<?php $manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $prow->virtuemart_manufacturer_id[0], FALSE); ?>
						<div class="manufacture">
							<a title="<?php echo $prow->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $prow->mf_name; ?></a>
						</div>
						<?php
						echo JHtml::link($prow->url, $prow->product_name);
						echo $this->customfieldsModel->CustomsFieldCartDisplay($prow);
						?>


						<?php if ($prow->product_parent_id != 0) {
							// product volume
							$sql= "SELECT value FROM #__virtuemart_product_custom_plg_param_values WHERE id = (SELECT val FROM #__virtuemart_product_custom_plg_param_ref AS t1 WHERE t1.virtuemart_product_id = " . $prow->virtuemart_product_id . " and t1.virtuemart_custom_id = " . 26 . " LIMIT 1);";
							$db->setQuery($sql);
							$db->query();
							$p_volume = $db->loadAssocList();
							?>

							<?php if ( isset($p_volume[0]['value']) ) {  ?>
								<span class="pr_volume"><?php echo JHtml::link ($p_product->link, 'Объём: ' . $p_volume[0]['value']); ?></span>
							<?php } ?>
						<?php } ?>
					</div>
				</td>
				<td class="col-sku" align="left" >
					<span class="product-sku-text"><?php echo $prow->product_sku ?></span>
				</td>
				<td class="col-price nowrap" align="left" >
					<?php if ($prow->prices['discountedPriceWithoutTax']) : ?>
						<span class="PricediscountedPriceWithoutTax"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['discountedPriceWithoutTax']); ?></span>
					<?php else : ?>
						<span class="PricebasePriceVariant"><?php echo $this->currencyDisplay->priceDisplay($prow->prices['basePriceVariant']); ?></span>
					<?php endif; ?>
				</td>
				<td class="col-qty cart-p-qty nowrap" align="left" >
					<?php
					if($prow->step_order_level)
						$step = (int) $prow->step_order_level;
					else
						$step = 1;
					if($step == 0)
						$step = 1;
					$jsFunction = 'Virtuemart.checkQuantity(this, ' . $step . ', \'' . vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED') . '\');';
					?>
					<div class="proopc-input-append">
						<input type="text" onblur="<?php echo $jsFunction ?>" onclick="<?php echo $jsFunction ?>" onchange="<?php echo $jsFunction ?>" onsubmit="<?php echo $jsFunction ?>" title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox input-ultra-mini js-recalculate proopc-qty-input" size="1" maxlength="4" name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" data-quantity="<?php echo $prow->quantity ?>"/>
						<button class="proopc-btn<?php echo ($this->params->get('color', 1) == 2) ? ' proopc-btn-danger' : ''; ?>" name="updatecart.<?php echo $pkey ?>" title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" onclick="return ProOPC.updateproductqty(this);"><i class="proopc-icon-refresh"></i></button>
					</div>
				</td>
<!--				--><?php //if(VmConfig::get('show_tax')) : ?>
<!--					<td class="col-tax nowrap" align="left">-->
<!--						--><?php //if(!empty($prow->prices['taxAmount'])) : ?>
<!--							<span class="PricetaxAmount">--><?php //echo $this->currencyDisplay->priceDisplay($prow->prices['taxAmount']); ?><!--</span>-->
<!--						--><?php //endif; ?>
<!--					</td>-->
<!--				--><?php //endif; ?>
<!--				<td class="col-discount nowrap" align="left">-->
<!--					--><?php //if(!empty($prow->prices['discountAmount'])) : ?>
<!--						<span class="PricediscountAmount">--><?php //echo $this->currencyDisplay->priceDisplay($prow->prices['discountAmount']); ?><!--</span>-->
<!--					--><?php //endif; ?>
<!--				</td>-->
				<td class="col-total nowrap" align="left">
					<?php if (VmConfig::get('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceWithTax', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php elseif(VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) : ?>
						<span class="line-through"><?php echo $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false, $prow->quantity); ?></span><br/>
					<?php endif; ?>
					<?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, false, false, $prow->quantity); ?>
				</td>
				<td class="rmv_frm_crt">
					<button class="proopc-btn<?php echo ($this->params->get('color', 1) == 2) ? ' proopc-btn-danger' : ''; ?> remove_from_cart" name="delete.<?php echo $pkey ?>" title="<?php echo vmText::_('COM_VIRTUEMART_CART_DELETE') ?>" data-vpid="<?php echo $prow->cart_item_id  ?>" onclick="return ProOPC.deleteproduct(this);"></button>
				</td>
			</tr>
		<?php endforeach; ?>
		
		<?php // Show a blank row ?>
		<tr class="blank-row">
			<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
			<?php if(VmConfig::get('show_tax')) : ?>
				<td class="col-tax"></td>
			<?php endif; ?>
			<td class="col-discount"></td>
			<td class="col-total"></td>
		</tr>
		
		<?php // Show Product Total ?>
<!--		<tr class="cart-sub-total">-->
<!--			<td class="sub-headings" colspan="--><?php //echo $subheading_colspan ?><!--" align="left">-->
<!--				<span>--><?php //echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?><!--:</span>-->
<!--			</td>-->
<!--			--><?php //if(VmConfig::get('show_tax')) : ?>
<!--				<td class="col-tax nowrap" align="left">-->
<!--					--><?php //if(!empty($this->cart->cartPrices['taxAmount'])) : ?>
<!--						<span class="PricetaxAmount">--><?php //echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['taxAmount']); ?><!--</span>-->
<!--					--><?php //endif; ?>
<!--				</td>-->
<!--			--><?php //endif; ?>
<!--			<td class="col-discount nowrap" align="left">-->
<!--				--><?php //if(!empty($this->cart->cartPrices['discountAmount'])) : ?>
<!--					<span class="PricediscountAmount">--><?php //echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['discountAmount']); ?><!--</span>-->
<!--				--><?php //endif; ?>
<!--			</td>-->
<!--			<td class="col-total nowrap" align="left">-->
<!--				--><?php //if(!empty($this->cart->cartPrices['salesPrice'])) : ?>
<!--					<span class="PricesalesPrice">--><?php //echo $this->currencyDisplay->priceDisplay($this->cart->cartPrices['salesPrice']); ?><!--</span>-->
<!--				--><?php //endif; ?>
<!--			</td>-->
<!--		</tr>-->
		
		<?php // Do we need to show the rest of the price list table ?>
		<?php if($this->params->get('show_full_pricelist_firststage', 0) || $this->finalStage) : ?>
			
			<?php // Show applied discount coupon if enabled ?>
			<?php if (VmConfig::get('coupons_enable') && !empty($this->cart->cartData['couponCode'])) : ?>
				<tr class="cart-coupon-row">
					<td class="coupon-form-col" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo vmText::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?>:</span>
						<div class="coupon-details">
							<?php echo $this->cart->cartData['couponCode']; ?>
							<?php echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')' ): ''; ?>
						</div>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="left">
							<?php echo $this->currencyDisplay->createPriceDiv('couponTax', '', $this->cart->cartPrices['couponTax'], false); ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon', '', $this->cart->pricesUnformatted['salesPriceCoupon'], false); ?>
					</td>
				</tr>
			<?php endif; ?>
			
			<?php // Show Discount Before Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['DBTaxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill discount-rule-per-bill">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
					<td class="col-total nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<?php // Show Discount Before Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['taxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill tax-rule-per-bill">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="left">
							<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			
			<?php // Show Discount After Tax Rules per Bill if available ?>
			<?php foreach($this->cart->cartData['DATaxRulesBill'] as $rule) : ?>
				<tr class="tax-per-bill discount-rule-per-bill">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo $rule['calc_name'] ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax"></td>
					<?php endif; ?>
					<td class="col-discount nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
					<td class="col-total nowrap" align="left">
						<?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php // Show a blank row ?>
			<tr class="blank-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax"></td>
				<?php endif; ?>
				<td class="col-discount"></td>
				<td class="col-total"></td>
			</tr>

			<?php // Show selected Shipment Method ?>
<!--			<tr class="shipping-row">-->
<!--				<td class="shipping-payment-heading" colspan="--><?php //echo $subheading_colspan ?><!--" align="left">-->
<!--					--><?php //echo $this->cart->cartData['shipmentName']; ?>
<!--				</td>-->
<!--				--><?php //if(VmConfig::get('show_tax')) : ?>
<!--					<td class="col-tax nowrap" align="left">-->
<!--						--><?php //echo $this->currencyDisplay->createPriceDiv('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], false); ?>
<!--					</td>-->
<!--				--><?php //endif; ?>
<!--				<td class="col-discount nowrap" align="left">-->
<!--					--><?php //if($this->cart->cartPrices['salesPriceShipment'] < 0) : ?>
<!--						--><?php //echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
<!--					--><?php //endif; ?>
<!--				</td>-->
<!--				<td class="col-total nowrap" align="left">-->
<!--					--><?php //echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
<!--				</td>-->
<!--			</tr>-->
			
<!--			--><?php //// Show selected Payment Method ?>
<!--			<tr class="payment-row">-->
<!--				<td class="shipping-payment-heading" colspan="--><?php //echo $subheading_colspan ?><!--" align="left">-->
<!--					--><?php //echo $this->cart->cartData['paymentName']; ?>
<!--				</td>-->
<!--				--><?php //if (VmConfig::get('show_tax')) : ?>
<!--					<td class="col-tax nowrap" align="left">-->
<!--						--><?php //echo $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->cartPrices['paymentTax'], false) ?>
<!--					</td>-->
<!--				--><?php //endif; ?>
<!--				<td class="col-discount nowrap" align="left">-->
<!--					--><?php //if($this->cart->cartPrices['salesPricePayment'] < 0) : ?>
<!--						--><?php //echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
<!--					--><?php //endif; ?>
<!--				</td>-->
<!--				<td class="col-total nowrap" align="left">-->
<!--					--><?php //echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
<!--				</td>-->
<!--			</tr>-->
			
			<?php // Show a blank row ?>
			<tr class="blank-row">
				<td class="shipping-payment-heading" colspan="<?php echo $subheading_colspan ?>"></td>
				<?php if(VmConfig::get('show_tax')) : ?>
					<td class="col-tax"></td>
				<?php endif; ?>
				<td class="col-discount"></td>
				<td class="col-total"></td>
			</tr>
			
			<?php // Show cart total ?>
			<tr class="grand-total">
				<td class="delivery" colspan="3" align="left">
					<p>Доставка по Москве:<span>Бесплатно</span></p>
					<p>Доставка в регионы:<span>Уточните у оператора</span></p>
				</td>
				<td class="sub-headings" colspan="<?php echo $subheading_colspan - 3 ?>" align="right">
					<span><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL') ?>:</span>
				</td>
<!--				--><?php //if(VmConfig::get('show_tax')) : ?>
<!--					<td class="col-tax nowrap" align="left">-->
<!--						--><?php //echo $this->currencyDisplay->createPriceDiv('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false) ?>
<!--					</td>-->
<!--				--><?php //endif; ?>
<!--				<td class="col-discount nowrap" align="left">-->
<!--					--><?php //echo $this->currencyDisplay->createPriceDiv('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], false) ?>
<!--				</td>-->
				<td class="col-total nowrap" align="left">
					<?php echo $this->currencyDisplay->createPriceDiv('billTotal', '', $this->cart->cartPrices['billTotal'], false); ?>
				</td>
				<td></td>
			</tr>
			
			<?php // Show cart total in payment currency ?>
			<?php if(!empty($this->totalInPaymentCurrency)) : ?>
				<tr class="grand-total-p-currency">
					<td class="sub-headings" colspan="<?php echo $subheading_colspan ?>" align="left">
						<span><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</span>
					</td>
					<?php if(VmConfig::get('show_tax')) : ?>
						<td class="col-tax nowrap" align="left">
							<?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false) ?>
						</td>
					<?php endif; ?>
					<td class="col-discount"></td>
					<td class="col-total nowrap" align="left">
						<span class="PricesalesPrice"><?php echo $this->totalInPaymentCurrency; ?></span>
					</td>
				</tr>
			<?php endif; ?>
				
		<?php endif; ?>
		
		<?php // Show Checkout advertisements generated by coupon plugin, payment plugin and shipment plugin ?>
		<?php if(!empty($this->checkoutAdvertise) && !$this->finalStage) : ?>
			<?php foreach($this->checkoutAdvertise as $html) : ?>
				<tr class="checkout-advertise-row payment-advertise">
					<td class="col-advertisement" colspan="<?php echo $total_colspan ?>">
						<div id="proopc-payment-advertise-table">
							<div class="checkout-advertise">
								<?php echo $html; ?>
							</div>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
			
	</tbody>
</table>