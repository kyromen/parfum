<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license	GNU General Public License version 2
 */
defined('JPATH_BASE') or die;
require_once dirname(__FILE__) . '/JotcacheStorage.php';
require_once dirname(__FILE__) . '/JotcacheStorageBase.php';
class JotcacheMemcacheCache extends JotcacheStorage implements JotcacheStorageBase {
protected static $_db = null;
protected $_persistent = true;
protected $_compress = 0;
protected static $_lead = '';
protected $_clean = false;
public static $connected = false;
public function __construct($options = array(), $params) {
parent::__construct($options, $params);
if (self::$_db === null) {
$this->getConnection($params);
}}protected function getConnection($params) {
if ((extension_loaded('memcache') && class_exists('Memcache')) != true) {
return false;
}$this->_persistent = $params->get('persistent', true);
$this->_compress = $params->get('mcompress', false) == false ? 0 : MEMCACHE_COMPRESSED;
$storage = $params->get('storage', null);
if (!isset($storage)) {
$this->loadLanguage();
throw new RuntimeException(JText::_('JOTCACHE_MEMCACHED_NO_SETTINGS'), 404);
}    self::$_db = new Memcache;
self::$_db->addServer($storage->host, $storage->port, $this->_persistent);
$memcachetest = @self::$_db->connect($storage->host, $storage->port);
if ($memcachetest == false) {
$this->loadLanguage();
$msg = sprintf(JText::_('JOTCACHE_MEMCACHED_NO_CONNECT'), $storage->host, $storage->port);
throw new RuntimeException($msg, 404);
    }self::$connected = TRUE;
$config = JFactory::getConfig();
$hash = md5($config->get('secret'));
self::$_lead = 'jotcache-' . substr($hash, 0, 6) . '-';
return;
}protected function loadLanguage() {
$lang = JFactory::getLanguage();
$lang->load('plg_system_jotcache', JPATH_ADMINISTRATOR, null, false, true);
}public function get() {
$data = self::$_db->get(self::$_lead . $this->fname);
if ($data) {
$data = preg_replace('/^.*\n/', '', $data);
}return $data;
}public function store($data = null) {
if ($data) {
$cache_id = self::$_lead . $this->fname;
if (!self::$_db->replace($cache_id, $data, $this->_compress, $this->_lifetime)) {
self::$_db->set($cache_id, $data, $this->_compress, $this->_lifetime);
}return true;
}return false;
}public function remove($path) {
return self::$_db->delete(self::$_lead . $this->fname);
}public function autoclean() {
}public function _getFilePath() {
return '';
}}