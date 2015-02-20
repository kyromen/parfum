<?php
defined('_JEXEC') or die('Restricted access');

$manufacturerBlocksPerRow = 4;

$verticalSeparator = " vertical-separator";
$horizontalSeparator = '<div class="horizontal-separator"></div>';

if ( !empty($this->manufacturers) ) { ?>

    <div class="manufacturer-view-default">

        <?php
        $indexes = array();
        $brands = array();
        $block = $this->manufacturers[0]->mf_name[0];
        $i = 0;
        $j = 0;
        foreach ( $this->manufacturers as $manufacturer ) {
            if ( $block != $manufacturer->mf_name[0] ) {
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
            if ( !isset($brands[0]) ){
                continue;
            }

            if ( !isset($brand) ) {
                echo "<div><h2>Бренды</h2></div>";
            } else {
                echo $horizontalSeparator;
            }

            echo "<div class=\"index\">".$brands[0]->mf_name[0]."</div>";
            echo "<div class=\"row\">";

            if ( count($brands) % $manufacturerBlocksPerRow >= 1 ) {
                $brandsPerColumn = count($brands) / $manufacturerBlocksPerRow + 1;
            } else {
                $brandsPerColumn = count($brands) / $manufacturerBlocksPerRow;
            }

            $iRow = 0;
            $iColumn = 1;
            foreach ( $brands as $brand ) {
                if ( $iRow % $brandsPerColumn == 0 ) {
                    echo "<div class=\"column\">";
                }
                $manufacturerIncludedProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $brand->virtuemart_manufacturer_id, FALSE);
                ?>

                <div class="manufacturer">
                    <a title="<?php echo $brand->mf_name; ?>" href="<?php echo $manufacturerIncludedProductsURL; ?>"><?php echo $brand->mf_name; ?></a>
                </div>

                <?php
                $iRow += 1;
                if ( $iColumn == $manufacturerBlocksPerRow and $iRow % $brandsPerColumn == 0 ) {
                    echo '</div><div class="clear"></div>';
                    $iColumn = 1;
                } else if ( $iRow % $brandsPerColumn == 0 ) {
                    echo '</div>';
                    $iColumn ++;
                }
            }
            if ( !($iColumn == 1 and $iRow % $brandsPerColumn == 0) ) {
                echo "</div><div class=\"clear\"></div>";
            } else {
                echo "</div>";
            }
        }
        ?>
        <div class="end-horizontal-separator"></div>
        <div class="clear"></div>
    </div>

<?php } ?>