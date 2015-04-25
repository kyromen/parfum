<?php
/*
 * @version 5.1.0
 * @package JotCachePlugins
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
  table.adminlist tr th{
    padding-left: 0px;
    width:2em;
  }
  table.adminlist tr td{
    padding-left: 6em;
  }
  table.adminlist2 tr td{
    padding-left: 6em;
  }
  table.adminlist2 div span{
    padding-left: 1em;
  }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_jotcache'); ?>" method="post" name="adminForm_recache" id="adminForm_Recache">
  <h3>Overview of selected options for recache process</h3>
  <table class="adminlist" style="width:40%;">
    <tr>
      <th nowrap="nowrap"><?php echo JText::_('JENABLED'); ?></th>
      <th nowrap="nowrap"><?php echo JText::_('JOPTIONS'); ?></th>
      <th><?php echo JText::_('JOTCACHE_RS_SELECTION'); ?></th>
    </tr>
    <tr>
      <td nowrap="nowrap"><input type="checkbox" name="search" value="<?php echo $this->filter['search']; ?>" <?php echo $this->filter['search'] ? 'checked' : 'disabled'; ?> /></td>
      <td><?php echo JText::_('JOTCACHE_RS_SEARCH'); ?></td>
      <td class="sel"><?php echo $this->filter['search']; ?></td>
    </tr>
    <tr>
      <td nowrap="nowrap"><input type="checkbox" name="com" value="<?php echo $this->filter['com']; ?>" <?php echo $this->filter['com'] ? 'checked' : 'disabled'; ?>/></td>
      <td><?php echo JText::_('JOTCACHE_RS_COMP'); ?></td>
      <td class="sel"><?php echo $this->filter['com']; ?></td>
    </tr>
    <tr>
      <td nowrap="nowrap"><input type="checkbox" name="pview" value="<?php echo $this->filter['view']; ?>" <?php echo $this->filter['view'] ? 'checked' : 'disabled'; ?>/></td>
      <td><?php echo JText::_('JOTCACHE_RS_VIEW'); ?></td>
      <td class="sel"><?php echo $this->filter['view']; ?></td>
    </tr>
    <tr>
      <td nowrap="nowrap"><input type="checkbox" name="mark" value="1" <?php echo $this->filter['mark'] ? 'checked' : 'disabled'; ?>/></td>
      <td><?php echo JText::_('JOTCACHE_RS_MARK'); ?></td>
      <td class="sel"><?php echo $this->filter['mark']; ?></td>
    </tr>
  </table>
  <br/>
  <h3>Scope of cached pages for recaching</h3>
  <table class="scope adminlist2">
    <tr>
      <td><div <?php echo ($scope == 'chck') ? '' : 'style="color:silver;"'; ?>><input type="radio" name="scope" value="chck"  <?php echo ($scope == 'chck') ? 'checked' : 'disabled'; ?>/><span><?php echo JText::_('JOTCACHE_RECACHE_CHECKED'); ?></span></div></td>
      <td><div <?php echo ($scope == 'sel') ? '' : 'style="color:silver;"'; ?>><input type="radio" name="scope" value="sel"  <?php echo ($scope == 'sel') ? 'checked' : 'disabled'; ?>/><span><?php echo JText::_('JOTCACHE_RECACHE_SEL'); ?></span></div></td>
      <td><div><input type="radio" name="scope" value="all"  <?php echo ($scope == 'all') ? 'checked' : ''; ?>/><span><?php echo JText::_('JOTCACHE_RECACHE_ALL'); ?></span></div></td>
    </tr>
  </table>
  <input type="hidden" name="view" value="recache" />
  <input type="hidden" name="task" value="display" />
  <input type="hidden" name="jotcacheplugin" value="recache" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHtml::_('form.token'); ?>
</form>