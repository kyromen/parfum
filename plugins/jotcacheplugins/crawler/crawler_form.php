<?php
/*
 * @version 5.1.0
 * @package JotCachePlugins
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$app = JFactory::getApplication();
$depth = $app->getUserStateFromRequest('jotcache.crawler.depth', 'depth', JRequest::getInt('depth'), 'int');
$maxDepth = 5;
$lang = JFactory::getLanguage();
$lang->load('plg_jotcacheplugins_crawler', JPATH_ADMINISTRATOR, null, false, false);
$depthOptions = array();
for ($i = 1; $i < $maxDepth + 1; $i++) {
$depthOptions[$i] = $i;
}?>
<form action="<?php echo JRoute::_('index.php?option=com_jotcache'); ?>" method="post" name="adminForm_crawler" id="adminForm_Crawler">
  <h3><?php echo JText::_('PLG_JCPLUGINS_CRAWLER_TITLE'); ?></h3>
  <table class="adminlist" style="width:400px;">
    <tr>
      <td style="padding-left: 0px;" class="hasTip" title="<?php echo JText::_('PLG_JCPLUGINS_CRAWLER_DEPTH_DESC'); ?>"><?php echo JText::_('PLG_JCPLUGINS_CRAWLER_DEPTH'); ?> </td>
      <td style="padding-left: 0px;"><?php echo JHTML::_('select.genericlist', $depthOptions, 'jcstates[depth]', 'style="width:100px;"', 'value', 'text', $depth); ?></td>
    </tr>
  </table>
  <input type="hidden" name="view" value="recache" />
  <input type="hidden" name="task" value="display" />
  <input type="hidden" name="scope" value="direct" />
  <input type="hidden" name="jotcacheplugin" value="crawler" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="hidemainmenu" value="0" />
  <?php echo JHtml::_('form.token'); ?>
</form>