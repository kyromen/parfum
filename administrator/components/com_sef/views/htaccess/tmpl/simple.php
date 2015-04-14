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

<?php $this->showInfoText('COM_SEF_INFOTEXT_HTACCESS', true); ?>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_SEF_301_REDIRECTS'); ?></legend>
<table class="adminlist table table-striped">
<thead>
    <tr>
        <th width="5">
            <?php echo JText::_('COM_SEF_NUM'); ?>
        </th>
        <th width="20">
            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
        </th>
        <th class="title">
            <?php echo JText::_('COM_SEF_REDIRECT_FROM'); ?>
        </th>
        <th class="title">
            <?php echo JText::_('COM_SEF_REDIRECT_TO'); ?>
        </th>
    </tr>
</thead>
<tbody>
    <?php
    $k = 0;
    $keys = array_keys($this->items);
    
    for( $i = 0, $n = count($keys); $i < $n; $i++ ) {
        $id = $i + 1;
        $row =& $this->items[$keys[$i]];
        ?>
        <tr class="<?php echo 'row'. $k; ?>">
            <td align="center">
                <?php echo $i+1; ?>
            </td>
            <td>
                <?php echo JHTML::_('grid.id', $i, $id ); ?>
            </td>
            <td>
                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>', 'edit')">
                <?php echo $row->from; ?>
                </a>
            </td>
            <td>
                <a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>', 'edit')">
                <?php echo $row->to; ?>
                </a>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
    }
    ?>
</tbody>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_SEF_REWRITEBASE'); ?></legend>
<table class="adminform table table-striped">
    <tr>
        <th colspan="2"><?php echo JText::_('COM_SEF_INFO_REWRITE_BASE'); ?></th>
    </tr>
    <tr>
        <td width="100"><?php echo JText::_('COM_SEF_ENABLED'); ?>:</td>
        <td><?php echo $this->lists['baseEnable']; ?></td>
    </tr>
    <tr>
        <td><?php echo JText::_('COM_SEF_VALUE'); ?>:</td>
        <td><?php echo $this->lists['baseValue']; ?></td>
    </tr>
</table>
</fieldset>

<fieldset class="adminform">
<legend><?php echo JText::_('COM_SEF_FOLLOWSYMLINKS'); ?></legend>
<table class="adminform table table-striped">
    <tr>
        <th colspan="2"><?php echo JText::_('COM_SEF_INFO_FOLLOW_SYMLINKS'); ?></th>
    </tr>
    <tr>
        <td width="100"><?php echo JText::_('COM_SEF_ENABLED'); ?>:</td>
        <td><?php echo $this->lists['symLinksEnable']; ?></td>
    </tr>
</table>
</fieldset>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="htaccess" />
</form>