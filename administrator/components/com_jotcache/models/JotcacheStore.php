<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
class JotcacheStore {
private $app;
private $db;
  private $root;
private $storage;
public $exclude = array();
function __construct($db, $storage) {
$this->app = JFactory::getApplication();
$this->db = $db;
$this->root = JPATH_SITE . '/cache/page';
$this->storage = $storage;
}function storeUrlInsDel($excludeJc, $excludeDb) {
$query = $this->db->getQuery(true);
$del = array_diff_key($excludeDb, $excludeJc);
$ins = array_diff_key($excludeJc, $excludeDb);
if (count($del) > 0) {
$delList = implode("','", array_keys($del));
$query->clear();
$query->delete()
->from($this->db->quoteName('#__jotcache_exclude'))
->where("name IN ('$delList')");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}}foreach ($ins as $option => $views) {
$query->clear();
$query->insert('#__jotcache_exclude')
->columns('name,value,type')
->values("'$option', '$views','0'");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}}}function getExcludePost($post) {
$nameList = "";
$cnt = 0;
foreach ($post as $key => $value) {
if (substr($key, 0, 3) == "ex_") {
if ($cnt > 0) {
$nameList.=",";
}$nameList.="'" . substr($key, 3) . "'";
$cnt++;
}}$query = $this->db->getQuery(true);
$query->select('id,name,value')
->from('#__jotcache_exclude')
->where('name IN (' . $nameList . ')');
try {
$rows = $this->db->setQuery($query)->loadObjectList();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}if (!empty($rows)) {
foreach ($rows as $row) {
$this->exclude[$row->name] = $row->value;
}}return $this->exclude;
}function storeUrlUpdate($upd) {
$query = $this->db->getQuery(true);
$updList = implode("','", array_keys($upd));
$query->clear();
$query->select($this->db->quoteName('fname'))
->from('#__jotcache')
->where("com IN ('$updList')");
try {
$names = $this->db->setQuery($query)->loadObjectList();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}foreach ($names as $name) {
if (file_exists($this->root . '/' . $name->fname . '.php')) {
unlink($this->root . '/' . $name->fname . '.php');
unlink($this->root . '/' . $name->fname . '.php_expire');
}}foreach ($upd as $option => $views) {
$query->clear();
$query->update($this->db->quoteName('#__jotcache_exclude'))
->set($this->db->quoteName('value') . "='$views'")
->where($this->db->quoteName('name') . " = '$option'");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}}}function storeTplInsDel($tplStored, $packed) {
$query = $this->db->getQuery(true);
if ($tplStored == 1) {
$query->update($this->db->quoteName('#__jotcache_exclude'))
->set($this->db->quoteName('value') . ' = ' . $this->db->quote($packed))
->where($this->db->quoteName('type') . ' = 4')
->where($this->db->quoteName('name') . ' = 1');
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}} else {
$query->insert('#__jotcache_exclude')
->columns('name,value,type')
->values("'1','$packed','4'");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}}}function getBrowserPost($post) {
$defs = new stdClass();
$defs->upd = array();
$defs->ins = array();
$params = JComponentHelper::getParams('com_jotcache');
$defbtime = (int) $params->get('defbtime');
foreach ($post as $key => $value) {
$part = substr($key, 0, 2);
switch ($part) {
case 'ux':
$id = (int) substr($key, 2);
$value = $this->uriFilter($value);
if ($value === false) {
break;
}$defs->upd[] = array($id, $value, (int) $post['uy' . $id]);
break;
case 'ix':
if ($value != "") {
$id = (int) substr($key, 2);
$value = $this->uriFilter($value);
if ($value === false) {
break;
}if ($post['iy' . $id] == 0) {
$post['iy' . $id] = $defbtime;
}$defs->ins[] = array($value, (int) $post['iy' . $id]);
}break;
default:
break;
}}return $defs;
}function uriFilter($value) {
if ($value == "") {
return false;
}if (substr($value, 0, 1) != "/") {
$value = "/" . $value;
}$pattern = '#^[\/]([a-zA-Z0-9-_:\.]*[\/]?)*[?]?([a-zA-Z0-9-_:\.]*[=]?[a-zA-Z0-9-_:\.]*[&]?)*#';
preg_match($pattern, (string) $value, $matches);
if (is_array($matches)) {
$filtered = @ (string) $matches[0];
if ($value == $filtered) {
return $value;
}}$this->app->enqueueMessage(JText::_('JOTCACHE_BCACHE_NEW_URI') . ' ' . $value . ' ' . JText::_('JOTCACHE_BCACHE_BLOCKED'), 'warning');
return false;
}function storeBrowserInsDel($defs) {
    $set = array();
$query = $this->db->getQuery(true);
foreach ($defs->upd as $item) {
$query->clear();
$query->update($this->db->quoteName('#__jotcache_exclude'))
->set($this->db->quoteName('name') . ' = ' . (int) $item[2])
->set($this->db->quoteName('value') . ' = ' . $this->db->quote($item[1]))
->where($this->db->quoteName('type') . ' = 2')
->where($this->db->quoteName('id') . ' = ' . (int) $item[0]);
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}$set[] = $item[1];
}foreach ($defs->ins as $item) {
if (!in_array($item[0], $set)) {
$query->clear();
$name = $item[1];
$value = $item[0];
$query->insert('#__jotcache_exclude')
->columns('name,value,type')
->values("'$name', '$value', '2'");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
return false;
}} else {
$this->app->enqueueMessage(JText::_('JOTCACHE_BCACHE_NEW_URI') . ' ' . $item[0] . ' ' . JText::_('JOTCACHE_BCACHE_DUPL'), 'warning');
}$set[] = $item[0];
}}}