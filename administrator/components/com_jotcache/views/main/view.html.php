<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
class MainViewMain extends JViewLegacy {
protected $app;
protected $data;
protected $fnameExt;
protected $lists;
protected $status;
protected $filters;
  protected $pars;
protected $url = "http://kbase.jotcomponents.net/jotcache:help:direct50j3x:";
protected $sidebar;
protected $canManage;
protected $statusPlugin;
protected $statusGlobal;
protected $statusClear;
protected $showcookies;
public function __construct($config = array()) {
parent::__construct($config);
$this->app = JFactory::getApplication();
$document = JFactory::getDocument();
$document->addScript('components/com_jotcache/assets/jotcache510.js');
$document->addStyleSheet('components/com_jotcache/assets/jotcache.css');
$user = JFactory::getUser();
$this->canManage = $user->authorise('core.manage', 'com_jotcache') && $user->authorise('jotcache.manage', 'com_jotcache');
}function display($tpl = null) {
$this->data = $this->get('Data');
$this->lists = $this->get('Lists');
$this->status = $this->get('Status');
    $model = $this->getModel();
$search = $this->app->getUserStateFromRequest('com_jotcache.search', 'filter_search', '', 'string');
$search = JString::strtolower($search);
$this->lists['search'] = $search;
$this->lists['order_Dir'] = $model->fileOrderDir;
$this->lists['order'] = $model->fileOrder;
$this->sidebar = $this->renderSidebar();
$this->addToolbar();
parent::display();
}protected function addToolbar() {
if ($this->pars->enabled) {
if (isset($this->pars->cachecookies)) {
$this->showcookies = $this->pars->cachecookies && $this->data->showcookies;
} else {
$this->showcookies = false;
}}$bar = JToolBar::getInstance('toolbar');
JToolBarHelper::title(JText::_('JOTCACHE_RS_TITLE'), 'jotcache-logo.gif');
$bar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/helpers');
if ($this->canManage) {
$this->showStatusButtons($bar);
}JToolBarHelper::spacer('20px');
$linkMark = JRoute::_('index.php?option=com_jotcache&view=main&task=mark');
$markid = $this->app->input->cookie->get('jotcachemark', '0', 'int');
$bar->appendButton('selector', 'markid', $markid, $linkMark);
JToolBarHelper::spacer('20px');
JToolBarHelper::custom('refresh', 'refresh.png', 'refresh.png', JText::_('JOTCACHE_RS_REFRESH'), false);
if ($this->data->fastdelete) {
JToolBarHelper::custom('delete', 'delete.png', 'delete.png', JText::_('JOTCACHE_RS_DELETE'), false);
} else {
JToolBarHelper::deleteList(JText::_('JOTCACHE_RS_DEL_CONFIRM'), 'delete');
}JToolBarHelper::custom('deleteall', 'deleteall.png', 'deleteall.png', JText::_('JOTCACHE_RS_DELETE_ALL'), false);
JToolBarHelper::custom('recache.display', 'recache.png', 'recache.png', JText::_('JOTCACHE_RS_RECACHE'), false);
JToolBarHelper::spacer('35px');
if ($this->canManage) {
JToolBarHelper::preferences('com_jotcache', '500');
}JToolbarHelper::help('Help', false, $this->url . 'intro');
}private function showStatusButtons($bar) {
$statusTitleP = JText::_('JOTCACHE_RS_PLUGIN_NORMAL');
$this->statusPlugin = JOTCACHE_STATUS_NORMAL;
$linkP = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id=' . $this->lists['plgid']);
if ($this->pars->enabled == 0) {
$this->statusPlugin = JOTCACHE_STATUS_WARNING;
$statusTitleP = JText::_('JOTCACHE_RS_PLUGIN_WARNING');
} else {
if ($this->lists['last']) {
$this->statusPlugin = JOTCACHE_STATUS_ATTENTION;
$statusTitleP = JText::_('JOTCACHE_RS_PLUGIN_ATTENTION');
} else {
$this->statusPlugin = JOTCACHE_STATUS_NORMAL;
}}$bar->appendButton('status', 'statplugin', 'P', $statusTitleP, $linkP);
$linkG = JRoute::_('index.php?option=com_config');
$this->statusGlobal = $this->status['gclass'];
$bar->appendButton('status', 'statglobal', 'G', $this->status['gtitle'], $linkG);
$linkC = JRoute::_('index.php?option=com_cache');
$storageType = isset($this->pars->storage->type) ? $this->pars->storage->type : 'file';
if ($storageType == 'file') {
$this->statusClear = JOTCACHE_STATUS_NORMAL;
$statusTitleC = JText::_('JOTCACHE_RS_CLEAR_NORMAL');
if ($this->status['clear'] === 0) {
$this->statusClear = JOTCACHE_STATUS_SPECIAL;
$statusTitleC = JText::_('JOTCACHE_RS_CLEAR_SPECIAL');
}$bar->appendButton('status', 'statclear', 'C', $statusTitleC, $linkC);
} else {
$statusTitleC = JText::_('JOTCACHE_RS_CLEAR_NONE');
$this->statusClear = '';
$bar->appendButton('status', 'statclear', 'C', $statusTitleC, $linkC);
}}protected function renderSidebar($task = 'display') {
$this->pars = $this->get('PluginParams', 'Main');
if ($task == 'display') {
$this->assignFilters();
}$sidebar = '<div id="sidebar"><div class="sidebar-nav">';
if ($this->canManage) {
$this->assignAdminLinks($sidebar, $task);
}if ($task == 'display') {
$sidebar .= '<div class="filter-select">'
. '<h4 class="page-header">' . JText::_("JSEARCH_FILTER_LABEL") . '</h4>';
foreach ($this->filters as $filter) {
$sidebar .= '<label for="' . $filter["name"] . '" class="element-invisible">'
. $filter['label'] . '</label>';
$sidebar .= '<select name="' . $filter["name"] . '" id="' . $filter['name'] . '" '
. 'class="span12 small" onchange="' . $filter["onchange"] . '">';
if (!$filter['noDefault']) {
$sidebar .= '<option value="">' . $filter["label"] . '</option>';
}$sidebar .= $filter['options']
. '</select><hr class="hr-condensed" />';
}$sidebar .='</div>';     }
$sidebar .='</div></div>';
return $sidebar;
}protected function assignFilters() {
if (!isset($this->pars->domain)) {
$this->pars->domain = '0';
}if (!isset($this->pars->urlselection)) {
$this->pars->urlselection = '0';
}$this->filters = array(array('name' => 'filter_com', 'label' => JText::_('JOTCACHE_RS_SEL_COMP'), 'options' => $this->lists['com'], 'noDefault' => '', 'onchange' => 'jotcache.resetSelect(1);'), array('name' => 'filter_view', 'label' => JText::_('JOTCACHE_RS_SEL_VIEW'), 'options' => $this->lists['view'], 'noDefault' => '', 'onchange' => 'jotcache.resetSelect(0);'), array('name' => 'filter_mark', 'label' => JText::_('JOTCACHE_RS_SEL_MARK'), 'options' => $this->lists['mark'], 'noDefault' => '', 'onchange' => 'jotcache.resetSelect(0);'));
if (isset($this->pars->domain) && $this->pars->domain == 1) {
$this->filters[] = array('name' => 'filter_domain', 'label' => JText::_('JOTCACHE_RS_SEL_DOMAIN'), 'options' => $this->lists['domain'], 'noDefault' => '', 'onchange' => 'jotcache.resetSelect(0);');
}}protected function assignAdminLinks(&$sidebar, $task) {
$sidebar .= '<ul class="nav nav-list" id="submenu">';
$sidebar .= '<li' . (($task == 'display') ? ' class="active"' : '') . '><a href="index.php?option=com_jotcache">' . JText::_('JOTCACHE_RS_OVERVIEW') . '</a></li>';
$sidebar .= '<li' . (($task == 'exclude') ? ' class="active"' : '') . '><a href="index.php?option=com_jotcache&amp;view=main&amp;task=exclude&amp;boxchecked=0">' . ($this->pars->urlselection ? JText::_('COM_JOTCACHE_RS_INCL') : JText::_('COM_JOTCACHE_RS_EXCL')) . '</a></li>';
$sidebar .= '<li' . (($task == 'tplex') ? ' class="active"' : '') . '><a href="index.php?option=com_jotcache&amp;view=main&amp;task=tplex&amp;boxchecked=0">' . JText::_('COM_JOTCACHE_RS_TPL_EXCL') . '</a></li>';
$sidebar .= '<li' . (($task == 'bcache') ? ' class="active"' : '') . '><a href="index.php?option=com_jotcache&amp;view=main&amp;task=bcache&amp;boxchecked=0">' . JText::_('COM_JOTCACHE_RS_BCACHE') . '</a></li>';
$sidebar .= '</ul><hr/>';
}protected function getSortFields() {
if ($this->data->mode) {
return array(
'm.uri' => JText::_('JOTCACHE_RS_UTITLE'),
'm.id' => JText::_('JOTCACHE_RS_ID'),
'm.ftime' => JText::_('JOTCACHE_RS_CREATED'),
'm.language' => JText::_('JOTCACHE_RS_LANG'),
'm.browser' => JText::_('JOTCACHE_RS_BROWSER')
);} else {
return array(
'm.title' => JText::_('JOTCACHE_RS_PTITLE'),
'm.id' => JText::_('JOTCACHE_RS_ID'),
'm.ftime' => JText::_('JOTCACHE_RS_CREATED'),
'm.language' => JText::_('JOTCACHE_RS_LANG'),
'm.browser' => JText::_('JOTCACHE_RS_BROWSER')
);}}function exclude() {
if ($this->canManage) {
$this->app->input->set('hidemainmenu', true);
$this->data = $this->get('ExList', 'Main');
$this->setLayout("exclude");
$this->sidebar = $this->renderSidebar('exclude');
parent::display();
}}function tplex() {
if ($this->canManage) {
$this->app->input->set('hidemainmenu', true);
$this->lists = $this->get('TplLists', 'Main');
$this->setLayout("tplex");
$this->sidebar = $this->renderSidebar('tplex');
parent::display();
}}function bcache() {
if ($this->canManage) {
$this->app->input->set('hidemainmenu', true);
$this->data = $this->get('BcData', 'Main');
$this->setLayout("bcache");
$this->sidebar = $this->renderSidebar('bcache');
parent::display();
}}function debug() {
$this->pars = $this->get('PluginParams', 'Main');
$this->data = $this->get('CachedContent', 'main');
$this->data->mode = $this->app->input->getWord('mode');
$this->fnameExt = $this->app->input->getCmd('fname') . '.php';
$this->setLayout("debug");
parent::display();
}}