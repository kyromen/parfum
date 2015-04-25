<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
class JotcacheRefresh {
private $app;
private $db;
private $deleted;
private $existed;
private $root;
private $storage;
function __construct($db, $storage) {
$this->app = JFactory::getApplication();
$this->db = $db;
$this->root = JPATH_SITE . '/cache/page';
$this->storage = $storage;
}function refreshMemcache() {
$query = $this->db->getQuery(true);
$query->select('fname')->from('#__jotcache');
try {
$rows = $this->db->setQuery($query)->loadObjectList();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}$existed = array();
$deleted = array();
foreach ($rows as $row) {
$hit = $this->storage->get($row->fname);
if ($hit === FALSE) {
$deleted[] = $row->fname;
} else {
$existed[$row->fname] = 1;
}}if (count($deleted) > 0) {
$list = implode("','", $deleted);
$query->clear();
$query->delete()
->from('#__jotcache')
->where("fname IN ('$list')");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}}if (is_a($this->storage, 'JotcacheMemcached')) {
$keys = $this->storage->getAllKeys();
foreach ($keys as $key) {
if (!array_key_exists($key, $existed)) {
$this->storage->remove($key);
}}} else {
foreach ($deleted as $key) {
$this->storage->remove($key);
}}return;
}function refreshFileCache() {
$root = JPATH_SITE . '/cache/page';
$query = $this->db->getQuery(true);
if (!file_exists($root)) {
$query->delete()->from('#__jotcache');
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}return;
}$query->clear();
$query->select('fname')->from('#__jotcache');
try {
$rows = $this->db->setQuery($query)->loadObjectList();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}$this->deleted = array();
$this->existed = array();
    $this->checkFileCache($rows);
    $this->removeFromDb();
    $this->removeFromFs();
}function checkFileCache($rows) {
foreach ($rows as $row) {
$filename = $this->root . '/' . $row->fname . '.php_expire';
if (file_exists($filename)) {
$exp = file_get_contents($filename);
if (time() - $exp > 0) {
$this->deleted[] = $row->fname;
} else {
$this->existed[$row->fname] = 1;
}} else {
$this->deleted[] = $row->fname;
}}}function removeFromDb() {
if (count($this->deleted) > 0) {
$list = implode("','", $this->deleted);
$query = $this->db->getQuery(true);
$query->delete()
->from('#__jotcache')
->where("fname IN ('$list')");
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$this->app->enqueueMessage($ex->getMessage(), 'error');
}}}function removeFromFs() {
if ($handle = opendir($this->root)) {
while (false !== ($file = readdir($handle))) {
if ($file != "." && $file != "..") {
$ext = strrchr($file, ".");
$fname = substr($file, 0, -strlen($ext));
if (!array_key_exists($fname, $this->existed) && ($ext == ".php" || $ext == ".php_expire")) {
unlink($this->root . '/' . $file);
}}}closedir($handle);
}}}