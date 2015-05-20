<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
class JotcacheBundle {
protected $params;
protected $cache;
protected $db;
protected $insertList;
protected $updateList;
protected $com;
protected $view;
function __construct($params, $cache) {
$this->params = $params;
$this->cache = $cache;
$this->db = JFactory::getDBO();
$this->com = JRequest::getCmd('option', '');
$this->view = JRequest::getCmd('view', '');
}function checkExclude() {
$query = $this->db->getQuery(true);
    $query->select($this->db->quoteName('value'))
->from('#__jotcache_exclude')
->where('name=' . $this->db->quote($this->com))
->where('type=0');
try {
$value = $this->db->setQuery($query)->loadResult();
} catch (RuntimeException $ex) {
      echo $ex->getMessage();
return;
}    $expart = $this->exclude($this->view, $value);
$exclude = ($value == '1' or $expart) ? true : false;
$urlSelection = $this->params->get('urlselection', '0');
if ($urlSelection) {
$exclude = !$exclude;
}return $exclude;
}protected function exclude($view, $value) {
$value = str_replace(' ', '', $value);
$divs = explode(',', $value);
if ($view != '' && array_search($view, $divs) !== false) {
return true;
}$queryArray = JRequest::get();
foreach ($divs as $div) {
if (@strpos($div, '&') !== false) {
        $chunks = explode('&', $div);
$cnt = 0;
foreach ($chunks as $chunk) {
if (@strpos($chunk, '=') !== false) {
$parts = explode('=', $chunk);
if (array_key_exists($parts[0], $queryArray) && $queryArray[$parts[0]] == $parts[1]) {
$cnt++;
}} else {
if ($chunk == $view) {
$cnt++;
}}}if ($cnt == count($chunks)) {
return true;
}      } else {
if (@strpos($div, '=') !== false) {
$parts = explode('=', $div);
if (array_key_exists($parts[0], $queryArray) && $queryArray[$parts[0]] == $parts[1]) {
return true;
}}}}return false;
}function storeBundle($app, $uri, $mark, $agent) {
$id = JRequest::getInt('id', 0);
if ($id == 0) {
$id = JRequest::getInt('cid', 0);
}$fname = $this->cache->fname;
    if (version_compare(JVERSION, '3.2') >= 0) {
$qs = serialize($app->input->getArray());
} else {
$qs = serialize($_REQUEST);
}$browser = $this->cache->options['browser'];
$language = $this->cache->options['language'];
$document = JFactory::getDocument();
$title = (get_magic_quotes_gpc()) ? addslashes($document->title) : $document->title;
$query = $this->db->getQuery(true);
$query->select('COUNT(*)')
->from('#__jotcache')
->where('fname=' . $this->db->quote($fname));
try {
$found = $this->db->setQuery($query)->loadResult();
} catch (RuntimeException $ex) {
$app->enqueueMessage($ex->getMessage(), 'error');
return;
}$query->clear();
$ftime = date($this->db->getDateFormat());
$cookies = $this->cache->options['cookies'];
$agent = $agent ? '1' : '0';
$domain = '';
if ($this->params->get('domain', '0')) {
$uri2 = JUri::getInstance();
$domain = $uri2->toString(array('scheme', 'host', 'port'));
}if (!$found) {
$com = $this->com;
$view = $this->view;
$cols = array();
$vals = array();
$insertList = array('domain', 'com', 'view', 'id', 'fname', 'title', 'uri', 'browser', 'language', 'agent', 'ftime', 'mark', 'qs', 'cookies');
foreach ($insertList as $item) {
$cols[] = $this->db->quoteName($item);
$vals[] = $this->db->quote($$item);
}$columns = implode(",", $cols);
$values = implode(",", $vals);
$query->insert('#__jotcache')
->columns($columns)
->values($values);
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$app->enqueueMessage($ex->getMessage(), 'error');
return;
}} else {
$updateList = array('title', 'ftime', 'mark', 'agent', 'qs', 'cookies');
$query->update($this->db->quoteName('#__jotcache'));
foreach ($updateList as $item) {
$query->set($this->db->quoteName($item) . ' = ' . $this->db->quote($$item));
}$query->where($this->db->quoteName('fname') . ' = ' . $this->db->quote($fname));
try {
$this->db->setQuery($query)->query();
} catch (RuntimeException $ex) {
$app->enqueueMessage($ex->getMessage(), 'error');
return;
}}}}