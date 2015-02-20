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
$Revision: 12 $
$LastChangedDate: 2015-01-23 21:26:59 +0530 (Fri, 23 Jan 2015) $
$Id: default_confirm.php 12 2015-01-23 15:56:59Z abhishekdas $
----------------------------------------------------------------------------------------------------------*/
defined ('_JEXEC') or die('Restricted access');
?>
<div class="inner-wrap">
	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart', $this->useXHTML, $this->useSSL); ?>">
		<?php echo $this->loadTemplate ('cartfields'); ?>
		<?php if(!VmConfig::get('use_as_catalog')) : ?>
			<div class="proopc-row proopc-checkout-box">
				<button type="button" class="proopc-btn <?php echo ($this->params->get('color', 1) == 2) ? ' proopc-btn-danger' : ' proopc-btn-info'; ?>" id="proopc-order-submit" onclick="return ProOPC.submitOrder();"><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></button>
			</div>
		<?php endif; ?>
	</form>
</div>
<?php 
// We have intentionally kept important hidden input fields outside the checkout form.
// They will be moved within the form by JavaScript when the cart is verified.
?>
<div id="poropc-hidden-confirm">
	<input type="hidden" name="order_language" value="<?php echo $this->order_language; ?>"/>
	<input type="hidden" name="task" value="confirm"/>
	<input type="hidden" name="option" value="com_virtuemart"/>
	<input type="hidden" name="view" value="cart"/>
</div>