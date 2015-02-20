<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Kohl Patrick, Eugen Stranz
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2701 2011-02-11 15:16:49Z impleri $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Category and Columns Counter
$iColumn = 1;
$iRow = 0;
$iManufacturer = 1;

// Calculating Categories Per Row
$manufacturerBlocksPerRow = 4;
if ($manufacturerBlocksPerRow != 1) {
	$manufacturerCellWidth = ' width'.floor ( 100 / $manufacturerBlocksPerRow );
} else {
	$manufacturerCellWidth = '';
}

// Separator
$verticalSeparator = " vertical-separator";
$horizontalSeparator = '<div class="horizontal-separator"></div>';

// Lets output the categories, if there are some
if (!empty($this->manufacturers)) { ?>

<div class="manufacturer-view-default">

	<?php // Start the Output
    $indexes = array();
    $brands = array();
    $block = 'A';
    $i = 0;
    $j = 0;
	foreach ( $this->manufacturers as $manufacturer ) {
        if ($block != $manufacturer->mf_name[0]) {
            $indexes[$i] = $brands;
            $brands = array();
            $block = $manufacturer->mf_name[0];
            $i += 1;
            $j = 0;
        } else {
            $brands[$j] = $manufacturer;
            $j += 1;
        }
    }
    $brands[$j] = $manufacturer;
    $indexes[$i] = $brands;

    foreach ( $indexes as $brands ) {

        if ( !isset($brand) ) {
            echo "<div><h2>Бренды</h2></div>";
        } else {
            echo $horizontalSeparator;
        }

        echo "<div class=\"index\">".$brands[0]->mf_name[0]."</div>";
        echo "<div class=\"row\">";

        if (count($brands) % 4 >= 1) {
            $brandsPerColumn = count($brands) / 4 + 1;
        } else {
            $brandsPerColumn = count($brands) / 4;
        }

        $iRow = 0;
        $iColumn = 1;
        foreach ( $brands as $brand ) {
            if ($iRow % $brandsPerColumn == 0) {
                echo "<div class=\"column\">";
            }

//            $manufacturerURL = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=' . $brand->virtuemart_manufacturer_id, FALSE);
            $manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $brand->virtuemart_manufacturer_id, FALSE);

            ?>

            <div class="manufacturer">
                <a title="<?php echo $brand->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $brand->mf_name; ?></a>
            </div>

            <?php

            $iRow += 1;
            if ($iColumn == $manufacturerBlocksPerRow and $iRow % $brandsPerColumn == 0) {
                echo '</div><div class="clear"></div>';
                $iColumn = 1;
            } else if ($iRow % $brandsPerColumn == 0) {
                echo '</div>';
                $iColumn ++;
            }
        }

        if (!($iColumn == 1 and $iRow % $brandsPerColumn == 0)) {
            echo "</div><div class=\"clear\"></div>";
        } else {
            echo "</div>";
        }
    }
    echo $horizontalSeparator;
?>
<div class="clear"></div>
</div>
<?php
}
?>