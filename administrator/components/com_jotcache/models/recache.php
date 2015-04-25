<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR . '/components/com_jotcache/helpers/recacherunner.php';
class MainModelRecache extends JModelLegacy {
var $_db;
var $_sql = "";
var $stopped = true;
function __construct() {
parent::__construct();
}function runRecache() {
$app = JFactory::getApplication();
$params = JComponentHelper::getParams("com_jotcache");
$timeout = (int) $params->get('recachetimeout', 300);
register_shutdown_function(array($this, 'recacheShutdown'));
ini_set('max_execution_time', $timeout);
$scopeAllow = array('none', 'chck', 'sel', 'all', 'direct');
$scope = $app->input->getWord('scope', '');
if (in_array($scope, $scopeAllow, TRUE)) {
$this->_sql = $this->_db->getQuery(true);
switch ($scope) {
case 'none':
return;
case 'chck':
$this->checkedRecache();
break;
case 'sel':
$this->selectedRecache($app);
break;
default:
break;
}if ($scope != 'direct') {
$this->_sql->update($this->_db->quoteName('#__jotcache'))
->set($this->_db->quoteName('recache') . ' = ' . $this->_db->quote(1));
$sql = $this->_db->setQuery($this->_sql);
        $sql->query();
}$this->controlRecache(1);
define('JOTCACHE_RECACHE_BROWSER', true);
$this->executeRunner($app);
}$this->stopped = false;
}private function checkedRecache() {
$this->_sql->update($this->_db->quoteName('#__jotcache'))
->set($this->_db->quoteName('recache') . ' = ' . $this->_db->quote(1))
->set($this->_db->quoteName('recache_chck') . ' = ' . $this->_db->quote(0))
->where("recache_chck='1'");
$sql = $this->_db->setQuery($this->_sql);
    $sql->query();
}private function selectedRecache($app) {
    $input = $app->input;
$search = $input->getString('search');
$com = $input->getCmd('com');
$view = $input->getCmd('pview');
$mark = $input->getInt('mark');
$params = JComponentHelper::getParams("com_jotcache");
$mode = (bool) $params->get('mode');
if ($com) {
$this->_sql->where("com='$com'");
}if ($view) {
$part = $this->_db->quoteName('view');
$this->_sql->where("$part='$view'");
}if ($mark) {
$this->_sql->where("mark='$mark'");
}if ($search) {
if ($mode) {
$this->_sql->where('LOWER(uri) LIKE ' . $this->_db->quote('%' . $this->_db->escape($search, true) . '%', false));
} else {
$this->_sql->where('LOWER(title) LIKE ' . $this->_db->quote('%' . $this->_db->escape($search, true) . '%', false));
}}}private function executeRunner($app) {
$main = new RecacheRunner();
$input = $app->input;
$jcplugin = strtolower($input->getWord('jotcacheplugin'));
$jcparams = $input->get->get('jcparams', array(), 'array');
$states = $input->post->get('jcstates', array(), 'array');
$jcstates = array();
if (count($states) > 0) {
foreach ($states as $key => $value) {
$curState = $app->getUserState('jotcache.' . $jcplugin . '.' . $key, null);
$newState = $states[$key];
if ($curState !== $newState) {
$jcstates[$key] = $newState;
$app->setUserState('jotcache.' . $jcplugin . '.' . $key, $newState);
} else {
$jcstates[$key] = $curState;
}}}$starturl = JURI::root();
if (substr($starturl, -1) == '/') {
$starturl = substr($starturl, 0, strlen($starturl) - 1);
}$main->doExecute($starturl, $jcplugin, $jcparams, $jcstates);
}function recacheShutdown() {
if ($this->stopped) {
echo JText::_('JOTCACHE_RECACHE_SHUTDOWN'), PHP_EOL;
} else {
echo JText::_('JOTCACHE_RECACHE_NORMAL'), PHP_EOL;
}}function flagRecache($cids) {
$list = implode("','", $cids);
$this->_sql = $this->_db->getQuery(true);
$this->_sql->update($this->_db->quoteName('#__jotcache'))
->set($this->_db->quoteName('recache_chck') . ' = ' . $this->_db->quote(1))
->where("fname IN ('$list')");
$this->_db->setQuery($this->_sql)->query();
}function controlRecache($flag) {
$cacheDir = JPATH_ROOT . '/cache/page';
if (!file_exists($cacheDir)) {
mkdir($cacheDir, 0755);
}$flagPath = $cacheDir . '/jotcache_recache_flag_tmp.php';
if ($flag) {
file_put_contents($flagPath, "defined('_JEXEC') or die;", LOCK_EX);
} else {
if (file_exists($flagPath)) {
unlink($flagPath);
$this->_sql = $this->_db->getQuery(true);
$this->_sql->update($this->_db->quoteName('#__jotcache'))
->set($this->_db->quoteName('recache') . ' = ' . $this->_db->quote(0));
$this->_db->setQuery($this->_sql)->query();
}}}function getPlugins() {
$query = $this->_db->getQuery(true);
$query->select('p.*')
->from('#__extensions AS p')
->where('p.enabled = 1')
->where('p.type = ' . $this->_db->quote('plugin'))
->where('p.folder = ' . $this->_db->quote('jotcacheplugins'))
->order('p.ordering');
$this->_db->setQuery($query);
$plugins = $this->_db->loadObjectList();
return $plugins;
}}