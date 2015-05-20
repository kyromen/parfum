<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 6556 2012-10-17 18:15:30Z kkmediaproduction $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
JHTML::_ ('behavior.modal');
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/

function getVmMediaFile($virtuemart_media_id, $getthumb = false) {
	$db = JFactory::getDBO();
	if ($getthumb) $sql = "SELECT file_url_thumb FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
	else $sql = "SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".intval($virtuemart_media_id);
	$db->setQuery($sql);
	return $db->loadResult();
}

$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";

$document = JFactory::getDocument ();
$document->addScriptDeclaration ($js);

$manuM = VmModel::getModel('manufacturer');
$mlang=(!VmConfig::get('prodOnlyWLang',false) and VmConfig::$defaultLang!=VmConfig::$vmlang and Vmconfig::$langCount>1);
$manufacturers = $manuM ->getManufacturersOfProductsInCategory($this->category->virtuemart_category_id,VmConfig::$vmlang,$mlang);

if ( strstr ( JURI::current(), "/brand/" ) ) $manufacturer = $manuM->getManufacturer();

$app = JFactory::getApplication();
$menu = $app->getMenu();
$current_page = $menu->getActive()->id;

//$categgories = $manuMC->getManufacturerCategories(true);

if (empty($this->keyword) and !empty($this->category)) { ?>
	<div class="category_description">
		<?php echo $this->category->category_description; ?>
	</div>
<?php
}

if ($menu->getActive()->id != 149) {
	if (VmConfig::get ('showCategory', 1) or empty($this->keyword)) { ?>
		<div class="category-block-left">
			<?php
			if (!empty($manufacturers)) {
				echo "<div class=\"category-manufacturers\">";
				echo "<span>Бренды</span>";
				echo "<ul class=\"mCustomScrollbar\">";
				$indexes = array();
				$brands = array();
				$block = $manufacturers[0]->mf_name[0];

				if ( isset($_GET['index']) ) {
					$index = $block = $_GET['index'];
				}

				$i = 0;
				$j = 0;
				foreach ( $manufacturers as $manuf ) {
					if ( isset($index) ) {
						if ($index != $manuf->mf_name[0]) continue;
					}
					if ($block != $manuf->mf_name[0]) {
						$indexes[$i] = $brands;
						$brands = array();
						$block = $manuf->mf_name[0];
						$i += 1;
						$j = 0;
						$brands[$j] = $manuf;
					} else {
						$brands[$j] = $manuf;
						$j += 1;
					}
				}
				$indexes[$i] = $brands;
				for ($i=0; isset($indexes[$i]); $i+=1) {
					$brands = $indexes[$i];

					if (!isset($brands[0]->mf_name[0])) { continue; }

					foreach ( $brands as $brand ) {
						if ($manufacturer->mf_name == $brand->mf_name) $li_class=" class=\"current\"";
						else $li_class = "";

						if ($this->category->virtuemart_category_id == 0) {
							$manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $brand->virtuemart_manufacturer_id, FALSE);
						} else {
							$manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $brand->virtuemart_manufacturer_id . "&virtuemart_category_id=" . $this->category->virtuemart_category_id, FALSE);
						}

						?>
						<li<?php echo $li_class; ?>><a title="<?php echo $brand->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $brand->mf_name; ?></a></li>
					<?php } ?>
				<?php } ?>
				</ul>
				</div>
				<div class="clear"></div>
			<?php } ?>
		</div>

		<div class="category-block-right">
			<?php
			if ( strstr ( JURI::current(), "/brand/" ) ) {
				?>
				<?php if ($manufacturer->mf_name) { ?>
					<div class="manufacturer-block">
						<div class="manuf-header">Парфюмерия <?php echo $manufacturer->mf_name;?></div>
						<?php if (isset($manufacturer->virtuemart_media_id[0])) {
							foreach ($manufacturer->virtuemart_media_id as $virtuemart_media_id) { ?>
								<div class="manuf-img"><img src="<?php echo getVmMediaFile($virtuemart_media_id);?>" \></div>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div style="padding: 10px;"></div>
				<?php } ?>
			<?php } ?>

			<?php
			if (empty($this->category->haschildren)) {
				$model = VmModel::getModel('category');
				$category_parent_id = $model->getParentCategory($this->category->virtuemart_category_id);
				$output = $model->getChildCategoryList($category_parent_id->virtuemart_vendor_id, $category_parent_id->virtuemart_category_id);
				$categories = $output;
			} else {
				$categories = $this->category->children;
			}

			if (!empty($categories)) {
				$iCol = 1;
				$iCategory = 1;

				$categories_per_row = count($categories);
				$category_cellwidth = ' width' . floor (100 / $categories_per_row);

				$verticalseparator = " vertical-separator";
				?>

				<div class="child-category-view">
					<?php
					$i = 0;
					$category_with_products = array();
					foreach ($categories as $category) {
						$manufacturers = $manuM->getManufacturersOfProductsInCategory($category->virtuemart_category_id, VmConfig::$vmlang, $mlang);

						$whithout_products = true;
						foreach ($manufacturers as $manuf) {
							if ( isset($manufacturer->virtuemart_manufacturer_id) ) {
								if ($manuf->virtuemart_manufacturer_id == $manufacturer->virtuemart_manufacturer_id) {
									$whithout_products = false;
									break;
								}
							} else {
								$whithout_products = false;
								break;
							}
						}
						if ($whithout_products) continue;

						$category_with_products[$i] = $category;
						$i++;
					}

					foreach ($category_with_products as $category) {
						if ($iCol == 1) { ?>
							<div class="row">
						<?php } ?>

						<?php
						if (isset($_GET['index'])) {
							$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id . '&index=' . $_GET['index'], FALSE);
						} else if (strstr(JURI::current(), "/brand/")) {
							$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $manufacturer->virtuemart_manufacturer_id . "&virtuemart_category_id=" . $category->virtuemart_category_id, FALSE);
						} else {
							$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);
						}

						if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
							$show_vertical_separator = ' ';
						} else {
							$show_vertical_separator = $verticalseparator;
						}

						$class = 0;
						if (($category->virtuemart_category_id == $this->category->virtuemart_category_id) or (count($category_with_products) == 1)) {
							$class = "class='active'";
						} ?>

						<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>" style="width: <?php echo 100 / count($category_with_products); ?>%">
							<div class="spacer">
								<h2>
									<a href="<?php echo $caturl; ?>" title="<?php echo $category->category_name ?>" <?php echo $class; ?>>
										<?php echo $category->category_name ?>
									</a>
								</h2>
							</div>
						</div>

						<?php
						$iCategory++;
						if ($iCol == $categories_per_row) { ?>
							<div class="clear"></div>
							</div>

							<?php
							$iCol = 1;
						} else {
							$iCol++;
						}
					}

					if ($iCol != 1) { ?>
						<div class="clear"></div>
						</div>
					<?php } ?>
			<?php } ?>
		</div>

		<div id="products-block" class="browse-view">
			<?php
			if (!empty($this->keyword)) { ?>
				<h3><?php echo $this->keyword; ?></h3>
			<?php } ?>

			<?php
			if (!empty($this->products)) {
				$products = array();
				$products[0] = $this->products;
				echo shopFunctionsF::renderVmSubLayout($this->productsLayout, array('products' => $products, 'currency' => $this->currency, 'products_per_row' => $this->perRow, 'showRating' => $this->showRating));
				?>

				<div class="vm-pagination" style="margin-top: 40px;"><?php echo $this->vmPagination->getPagesLinks(); ?>
					<span style="float:right">
				</div>
			<?php } ?>
		</div>

        <?php if ( !empty($manufacturer->mf_desc) ) { ?>
            <div class="manufacturer-block">
                <div class="manuf-desc">
                    <p><?php echo $manufacturer->mf_desc; ?></p>
                </div>
            </div>
        <?php } ?>
	<?php }	?>
<?php } ?>