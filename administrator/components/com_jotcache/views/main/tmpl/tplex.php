<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
$site_url = JURI::root();
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JToolBarHelper::title(JText::_('JOTCACHE_TPLEX_TITLE'), 'jotcache-logo.gif');
$msg = JText::_('JOTCACHE_RS_REFRESH_DESC');
JToolBarHelper::custom('tplapply', 'apply.png', 'apply.png', 'Apply', false);
JToolBarHelper::spacer();
JToolBarHelper::custom('tplsave', 'save.png', 'save.png', 'Save', false);
JToolBarHelper::spacer();
JToolBarHelper::cancel('close', JText::_('CLOSE'));
JToolBarHelper::spacer();
JToolbarHelper::help('JotCacheHelp', false, $this->url . 'exclusion');
$rows = $this->lists['pos'];
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
      <table class="table table-striped span6">
        <thead>
          <tr>
            <th class="span2"><input type="checkbox" name="toggle" value=""  onclick="Joomla.checkAll(this);" />&nbsp;<?php echo JText::_('JOTCACHE_EXCLUDE_EXCLUDED'); ?></th>
            <th  class="span4"><?php echo JText::_('JOTCACHE_TPLEX_POS'); ?></th>
          </tr>
        </thead>
        <?php
        $k = 0;
for ($i = 0, $n = count($rows); $i < $n; $i++) {
$row = &$rows[$i];
$checking = array_key_exists($row, $this->lists['value']) ? "checked" : "";
$checked = '<input type="checkbox" id="cb' . $i . '" name="cid[]" value="' . $row . '" ' . $checking . ' onclick="jotcache.valoff(this);isChecked(this.checked);" />';
?>
          <tr class="<?php echo "row$k"; ?>">
            <td align="center"><?php echo $checked; ?></td>
            <td><?php echo $row; ?></td>
          </tr>
          <?php
          $k = 1 - $k;
}?>
      </table>
      <br/>
      <input type="hidden" name="option" value="com_jotcache" />
      <input type="hidden" name="view" value="main" />
      <input type="hidden" name="task" value="tplex" />
      <input type="hidden" name="boxchecked" value="0" />
      </form>