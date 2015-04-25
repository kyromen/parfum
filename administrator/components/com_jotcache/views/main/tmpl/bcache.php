<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JToolBarHelper::title(JText::_('JOTCACHE_BCACHE_TITLE'), 'jotcache-logo.gif');
$bar = JToolBar::getInstance('toolbar');
$msg = JText::_('JOTCACHE_RS_REFRESH_DESC');
JToolBarHelper::custom('bcapply', 'apply.png', 'apply.png', 'Apply', false);
JToolBarHelper::spacer();
JToolBarHelper::custom('bcsave', 'save.png', 'save.png', 'Save', false);
JToolBarHelper::spacer();
JToolBarHelper::deleteList(JText::_('JOTCACHE_RS_DEL_CONFIRM'), 'bcdelete');
JToolBarHelper::spacer();
JToolBarHelper::cancel('display', JText::_('CLOSE'));
JToolBarHelper::spacer();
JToolbarHelper::help('JotCacheHelp', false, $this->url . 'browser_caching');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <table class="table table-striped">
        <thead>
          <tr>
            <th nowrap="nowrap" width="120"><input type="checkbox" name="toggle" value=""  onclick="Joomla.checkAll(this);" />&nbsp;<?php echo JText::_('JOTCACHE_BCACHE_INCLUDED'); ?></th>
            <th align="center"><?php echo JText::_('JOTCACHE_BCACHE_URI'); ?></th>
            <th title="<?php echo JText::_('JOTCACHE_BCACHE_TIME'); ?>"><?php echo JText::_('JOTCACHE_BCACHE_TIME'); ?></th>
          </tr>
        </thead>
        <?php
        $rows = $this->data;
$stop = count($rows);
$k = 0;
for ($i = 0, $n = 20; $i < $n; $i++) {
if ($i >= $stop) {
$id = 0;
$uri = '';
$time = '';
$pfx = "ix" . $i;
$pfy = "iy" . $i;
} else {
$row = &$rows[$i];
$id = $row->id;
$uri = $row->value;
$time = $row->name;
$pfx = "ux" . $id;
$pfy = "uy" . $id;
}$checked = '<input type="checkbox" id="cb' . $i . '" name="cid[]" value="' . $id . '" onclick="Joomla.isChecked(this.checked);" />';
?>
          <tr class="<?php echo "row$k"; ?>">
            <td align="center" width="10%"><?php echo $checked; ?></td>
            <td width="70%"><input id="<?php echo "$pfx"; ?>" name="<?php echo "$pfx"; ?>" style="width: 100%;" value="<?php echo $uri; ?>" ></td>
            <td width="20%"><input id="<?php echo "$pfy"; ?>" name="<?php echo "$pfy"; ?>" style="width:20%;" value="<?php echo $time; ?>" ></td>
          </tr>
          <?php
          $k = 1 - $k;
}?>
      </table>
      <br/>
      <input type="hidden" name="option" value="com_jotcache" />
      <input type="hidden" name="view" value="main" />
      <input type="hidden" name="task" value="bcache" />
      <input type="hidden" name="boxchecked" value="0" />
      </form>