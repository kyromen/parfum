<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('JPATH_PLATFORM') or die;
final class BrowserAgents {
private static $defaultBrowser = 'Opera/9.80 (Windows NT 6.1; U; es-ES) Presto/2.9.181 Version/12.00';
private static $browsers;
private static function loadAgents() {
$lang = JFactory::getLanguage();
$lang->load('com_jotcache', JPATH_ADMINISTRATOR, null, false, true);
self::$browsers = array(
"chrome" => array('Chrome', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1284.0 Safari/537.13'),
"firefox" => array('Firefox', 'Mozilla/6.0 (Windows NT 6.2; WOW64; rv:16.0.1) Gecko/20121011 Firefox/16.0.1'),
        "msie6" => array('IE 6.0', 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1)'),
"msie7" => array('IE 7.0', 'Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)'),
"msie8" => array('IE 8.0', 'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; SLCC1; .NET CLR 1.1.4322)'),
"msie9" => array('IE 9.0', 'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)'),
"msie10" => array('IE 10.0', 'Mozilla/5.0 (compatible; MSIE 10.6; Windows NT 6.1; Trident/5.0; InfoPath.2; SLCC1; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET CLR 2.0.50727) 3gpp-gba UNTRUSTED/1.0'),
"msie11" => array('IE 11.0', 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko'),
"safari" => array('Safari', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2'),
"desktop" => array(JText::_('JOTCACHE_CLIENT_DESKTOP'), 'Mozilla/5.0 (compatible; Konqueror/4.0; Linux) KHTML/4.0.5 (like Gecko)'),
"iPad" => array('iPad', 'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25'),
"iPhone" => array('iPhone', 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/531.22.7'));
self::$browsers['tablet'] = array(JText::_('JOTCACHE_CLIENT_TABLET'), 'Mozilla/5.0 (Linux; U; Android 4.0.3; en-us; KFTT Build/IML74K) AppleWebKit/535.19 (KHTML, like Gecko) Silk/2.1 Mobile Safari/535.19 Silk-Accelerated=true');
self::$browsers['phone'] = array(JText::_('JOTCACHE_CLIENT_PHONE'), 'Mozilla/5.0 (Linux; U; Android 4.0.3; de-de; Galaxy S II Build/GRJ22) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
}public static function getDefaultAgent() {
return self::$defaultBrowser;
}public static function getBrowserAgents() {
if (!isset(self::$browsers)) {
self::loadAgents();
}return self::$browsers;
}public static function getActiveBrowserAgents() {
if (!isset(self::$browsers)) {
self::loadAgents();
}$clients = array();
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('params')
->from('#__extensions')
->where('type =' . $db->Quote('plugin'))
->where('folder =' . $db->Quote('system'))
->where('element =' . $db->Quote('jotcache'));
$cacheplg = $db->setQuery($query)->loadResult();
if ($cacheplg && stripos($cacheplg, 'cacheclient') !== false) {
$pars = json_decode($cacheplg);
$clients = get_object_vars($pars->cacheclient);
}$activeBrowsers = array();
$catchFirstNonsplitted = true;
    foreach (self::$browsers as $key => $value) {
if ($clients[$key] == 2) {
$activeBrowsers[$key] = self::$browsers[$key];
} else {
if ($catchFirstNonsplitted && $clients[$key] == 1) {
          $activeBrowsers[$key] = self::$browsers[$key];
$catchFirstNonsplitted = false;
}}}return $activeBrowsers;
}}