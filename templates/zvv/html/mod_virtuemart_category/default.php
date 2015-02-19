<?php // no direct access
defined('_JEXEC') or die('Restricted access');
//JHTML::stylesheet ( 'menucss.css', 'modules/mod_virtuemart_category/css/', false );

/* ID for jQuery dropdown */
$ID = str_replace('.', '_', substr(microtime(true), -8, 8));
$js="
//<![CDATA[
jQuery(document).ready(function() {
		jQuery('#VMmenu".$ID." li.VmClose ul').hide();
		jQuery('#VMmenu".$ID." li .VmArrowdown').click(
		function() {

			if (jQuery(this).parent().next('ul').is(':hidden')) {
				jQuery('#VMmenu".$ID." ul:visible').delay(500).slideUp(500,'linear').parents('li').addClass('VmClose').removeClass('VmOpen');
				jQuery(this).parent().next('ul').slideDown(500,'linear');
				jQuery(this).parents('li').addClass('VmOpen').removeClass('VmClose');
			}
		});
	});
//]]>
" ;

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);?>

<ul class="VMmenu<?php echo $class_sfx ?>" id="<?php echo "VMmenu".$ID ?>" >
<?php foreach ($categories as $category) {
		 $active_menu = 'class="VmClose"';
		$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
		$cattext = $category->category_name;
		//if ($active_category_id == $category->virtuemart_category_id) $active_menu = 'class="active"';
		if (in_array( $category->virtuemart_category_id, $parentCategories)) $active_menu = 'class="VmOpen"';


		$manuM = VmModel::getModel('manufacturer');
		$mlang=(!VmConfig::get('prodOnlyWLang',false) and VmConfig::$defaultLang!=VmConfig::$vmlang and Vmconfig::$langCount>1);
		$manufacturers = $manuM ->getManufacturersOfProductsInCategory($category->virtuemart_category_id,VmConfig::$vmlang,$mlang);

		$indexes = array($manufacturers[0]->mf_name[0]);
		$block = $manufacturers[0]->mf_name[0];

		$i = 1;
		foreach ( $manufacturers as $manufacturer ) {
			if ($block != $manufacturer->mf_name[0]) {
				$indexes[$i] = $block = $manufacturer->mf_name[0];
				$i += 1;
			}
		}
		?>



		<li <?php echo $active_menu ?>>
			<?php echo JHTML::link($caturl, $cattext); ?>
			<div class="indexes">
				<div class="wrapper">
					<div>Бренд: </div>
					<? foreach($indexes as $i)
						echo "<div><a href=\"". $caturl . "?index=" . $i . "\">" . $i ."</a></div>";
					?>
					<div><a href="<?php echo $caturl ?>">#</a></div>
				</div>
			</div>
		</li>
<?php
	} ?>
</ul>
