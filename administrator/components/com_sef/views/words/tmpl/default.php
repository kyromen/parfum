<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.2
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2015 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php $this->showInfoText('COM_SEF_INFOTEXT_MANAGEWORDS'); ?>

<table>
    <tr>
        <td width="100%" valign="bottom">
        </td>
        <td nowrap="nowrap" align="right">
            <?php
            echo JText::_('COM_SEF_FILTER_WORDS') . ':';
            ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php echo $this->lists['filterWords']; ?>
        </td>
    </tr>
</table>

<table class="adminlist table table-striped">
<thead>
    <tr>
        <th width="5">
            <?php echo JText::_('COM_SEF_NUM'); ?>
        </th>
        <th width="20">
            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
        </th>
        <th class="title" width="40%">
            <?php echo JHTML::_('grid.sort', JText::_('COM_SEF_WORDS'), 'word', $this->lists['filter_order'] == 'word' ? $this->lists['filter_order_Dir'] : 'desc', $this->lists['filter_order']); ?>
        </th>
        <th class="title">
            <?php echo JText::_('COM_SEF_ASSOCIATED_SEF_URLS'); ?>
        </th>
    </tr>
</thead>
<tfoot>
    <tr>
        <td colspan="4">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
</tfoot>
<tbody>
    <?php
    $k = 0;
    $j = 0;
    foreach (array_keys($this->items) as $i) {
        $row = &$this->items[$i];
        ?>
        <tr class="<?php echo 'row'. $k; ?>">
            <td align="center">
                <?php echo $this->pagination->getRowOffset($j); ?>
            </td>
            <td>
                <?php echo JHTML::_('grid.id', $j, $row->id ); ?>
            </td>
            <td>
                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $j;?>', 'edit')">
                <?php echo $row->word; ?>
                </a>
            </td>
            <td>
                <?php
                if (count($row->urls) == 0) {
                    echo JText::_('(None)');
                }
                else {
                    echo implode('<br />', $row->urls);
                }
                ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
        $j++;
    }
    ?>
</tbody>
</table>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="words" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['filter_order_Dir']; ?>" />
</form>