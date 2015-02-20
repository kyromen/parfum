<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_manufacturer.php 8610 2014-12-02 18:53:19Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

function getVmMediaFile($virtuemart_media_id, $getthumb = false) {
    $db = JFactory::getDBO();
    if ($getthumb) $sql = "SELECT file_url_thumb FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
    else $sql = "SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
    $db->setQuery($sql);
    return $db->loadResult();
}

?>
<div class="manufacturer">
    <?php

    $manufacture_model = VmModel::getModel('manufacturer');
    $manufacture = $manufacture_model->getManufacturer($this->product->virtuemart_manufacturer_id[0]);

    $link = $manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $this->product->virtuemart_manufacturer_id[0], FALSE);
    $text = $this->product->mf_name;

    /* Avoid JavaScript on PDF Output */
    if (strtolower(vRequest::getCmd('output')) == "pdf") {
	echo JHtml::_('link', $link, $text);
    } else {
	?>
        <span class="bold"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') ?></span><a class="manuM" href="<?php echo $link ?>"><?php echo $text ?></a>
    <?php } ?>

    <?php if ( !empty($manufacture->virtuemart_media_id) ) { ?>
        <!--Manufacturer Image-->
        <div class="manufacturer-image">
            <img src="<?php echo getVmMediaFile($manufacture->virtuemart_media_id[0]);?>" \>
        </div>

    <?php } ?>
</div>