<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
class plgSystemJotmarker extends JPlugin {
protected static $rules;
public function onAfterDispatch() {
$app = JFactory::getApplication();
if ($app->isAdmin() || JDEBUG || $_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}JLoader::register('JDocumentRendererModules', dirname(__FILE__) . '/modules.php', true);
}public static function onAfterRenderModules(&$buffer, &$params) {
$app = JFactory::getApplication();
if ($app->isAdmin() || JDEBUG || $_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}$user = JFactory::getUser();
if (!$user->get('guest', false)) {
return;
}    if (empty(self::$rules)) {
$database = JFactory::getDBO();
$query = $database->getQuery(true);
$tpl_id = 1;
$query->select('value')
->from('#__jotcache_exclude')
->where($database->quoteName('type') . ' = 4')
->where($database->quoteName('name') . ' = ' . (int) $tpl_id);
$value = $database->setQuery($query)->loadResult();
self::$rules = unserialize($value);
}if (is_array(self::$rules) && is_array($params) && key_exists("name", $params) && key_exists($params["name"], self::$rules) && strlen($buffer) > 0) {
$prefix = '<jot ' . $params["name"] . ' s';
if (key_exists('style', $params)) {
$prefix .=' style="' . $params["style"] . '"';
}if (count($params) > 2) {
foreach ($params as $key => $value) {
if ($key == 'name' || $key == 'style') {
continue;
} else {
$prefix .=' ' . $key . '="' . $value . '"';
}}}$buffer = $prefix . '></jot>' . $buffer . '<jot ' . $params["name"] . ' e></jot>';
}}}?>