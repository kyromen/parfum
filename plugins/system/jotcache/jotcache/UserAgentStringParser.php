<?php
/*
 * Simple PHP User Agent string parser
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.x
 */
class UserAgentStringParser {
public function parse($userAgentString = null) {
if (!$userAgentString) {
$userAgentString = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
}$clean = $this->cleanUserAgentString($userAgentString);
if ($this->isBot($clean)) {
return array(
'string' => $clean,
'browser_name' => 'bot',
'browser_version' => null,
'operating_system' => null,
'engine' => null
);}if ($this->isIPhone($clean)) {
return array(
'string' => $clean,
'browser_name' => 'iPhone',
'browser_version' => null,
'operating_system' => null,
'engine' => null
);}if ($this->isIPad($clean)) {
return array(
'string' => $clean,
'browser_name' => 'iPad',
'browser_version' => null,
'operating_system' => null,
'engine' => null
);}if ($this->isMobile($clean)) {
if (preg_match('/ipad|viewpad|tablet|bolt|xoom|touchpad|playbook|kindle|gt-p|gt-i|sch-i|sch-t|mz609|mz617|mid7015|tf101|g-v|ct1002|transformer|silk|tab/i', $clean) || ( preg_match('/android/i', $clean) && !preg_match('/mobile/i', $clean))) {
return array(
'string' => $clean,
'browser_name' => 'tablet',
'browser_version' => null,
'operating_system' => null,
'engine' => null
);} else {
return array(
'string' => $clean,
'browser_name' => 'phone',
'browser_version' => null,
'operating_system' => null,
'engine' => null
);}} else {
$informations = $this->doParse($userAgentString);
foreach ($this->getFilters() as $filter) {
$this->$filter($informations);
}if ($this->isOtherDesktop($informations['browser_name'])) {
$informations['browser_name'] = 'desktop';
}return $informations;
}}function isOtherDesktop($browser_name) {
return preg_match('/applewebkit|gecko|konqueror|opera/i', $browser_name);
}function isMobile($clean) {
return preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|galaxy|hiptop|htc|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile|nexus|nitro|nokia|o2|opera mini|palm( os)?|phone|plucker|pocket|portalmmm|proxinet|pre\/|psp|samsung|smartphone|series40|series60|s60|sonyericsson|symbian|touchpad|treo|up\.(browser|link)|vodafone|wap|webos|windows\sce|(iemobile|ppc)|xiino|240x320|400X240/i', $clean);
}function isIPad($clean) {
if (empty($clean)) {
return FALSE;
} else {
$pos = stripos($clean, 'iPad');
return ($pos > 0) ? TRUE : FALSE;
}}function isIPhone($clean) {
if (empty($clean)) {
return FALSE;
} else {
$pos = stripos($clean, 'iPhone');
return ($pos > 0) ? TRUE : FALSE;
}}function isBot($clean) {
return preg_match('/bot|crawler|facebookexternalhit|linkdex|slurp|spider/i', $clean);
}protected function doParse($userAgentString) {
$userAgent = array(
'string' => $this->cleanUserAgentString($userAgentString),
'browser_name' => null,
'browser_version' => null,
'operating_system' => null,
'engine' => null
);if (empty($userAgent['string'])) {
return $userAgent;
}$pattern = '#(' . join('|', $this->getKnownBrowsers()) . ')[/ ]+([0-9]+(?:\.[0-9]+)?)#';
if (preg_match_all($pattern, $userAgent['string'], $matches)) {
$i = count($matches[1]) - 1;
if (isset($matches[1][$i])) {
$userAgent['browser_name'] = $matches[1][$i];
}if (isset($matches[2][$i])) {
if ($userAgent['browser_name'] == 'msie') {
$userAgent['browser_version'] = $matches[2][0];
} else {
$userAgent['browser_version'] = $matches[2][$i];
}}}$pattern = '#' . join('|', $this->getKnownOperatingSystems()) . '#';
if (preg_match($pattern, $userAgent['string'], $match)) {
if (isset($match[0])) {
$userAgent['operating_system'] = $match[0];
}}$pattern = '#' . join('|', $this->getKnownEngines()) . '#';
if (preg_match($pattern, $userAgent['string'], $match)) {
if (isset($match[0])) {
$userAgent['engine'] = $match[0];
}}return $userAgent;
}public function cleanUserAgentString($userAgentString) {
$userAgentString = trim(strtolower($userAgentString));
$userAgentString = strtr($userAgentString, $this->getKnownBrowserAliases());
$userAgentString = strtr($userAgentString, $this->getKnownOperatingSystemAliases());
$userAgentString = strtr($userAgentString, $this->getKnownEngineAliases());
return $userAgentString;
}public function getFilters() {
return array(
'filterGoogleChrome',
'filterSafariVersion',
'filterOperaVersion',
'filterYahoo',
'filterMsie'
);}public function addFilter($filter) {
$this->filters += $filter;
}protected function getKnownBrowsers() {
return array(
'msie',
'firefox',
'safari',
'webkit',
'opera',
'netscape',
'konqueror',
'gecko',
'chrome',
'googlebot',
'iphone',
'msnbot',
'applewebkit'
);}protected function getKnownBrowserAliases() {
return array(
'shiretoko' => 'firefox',
'namoroka' => 'firefox',
'shredder' => 'firefox',
'minefield' => 'firefox',
'granparadiso' => 'firefox'
);}protected function getKnownOperatingSystems() {
return array(
'windows',
'macintosh',
'linux',
'freebsd',
'unix',
'iphone'
);}protected function getKnownOperatingSystemAliases() {
return array();
}protected function getKnownEngines() {
return array(
'gecko',
'webkit',
'trident',
'presto'
);}protected function getKnownEngineAliases() {
return array();
}protected function filterGoogleChrome(array &$userAgent) {
if ('safari' === $userAgent['browser_name'] && strpos($userAgent['string'], 'chrome/')) {
$userAgent['browser_name'] = 'chrome';
$userAgent['browser_version'] = preg_replace('|.+chrome/([0-9]+(?:\.[0-9]+)?).+|', '$1', $userAgent['string']);
}}protected function filterSafariVersion(array &$userAgent) {
if ('safari' === $userAgent['browser_name'] && strpos($userAgent['string'], ' version/')) {
$userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+(?:\.[0-9]+)?).+|', '$1', $userAgent['string']);
}}protected function filterOperaVersion(array &$userAgent) {
if ('opera' === $userAgent['browser_name'] && strpos($userAgent['string'], ' version/')) {
$userAgent['browser_version'] = preg_replace('|.+\sversion/([0-9]+\.[0-9]+)\s*.*|', '$1', $userAgent['string']);
}}protected function filterYahoo(array &$userAgent) {
if (null === $userAgent['browser_name'] && strpos($userAgent['string'], 'yahoo! slurp')) {
$userAgent['browser_name'] = 'yahoobot';
}}protected function filterMsie(array &$userAgent) {
if ('msie' === $userAgent['browser_name'] && empty($userAgent['engine'])) {
$userAgent['engine'] = 'trident';
}if ($userAgent['operating_system'] == 'windows' && $userAgent['browser_name'] === null && $userAgent['engine'] == 'trident') {
if (strpos($userAgent['string'], 'rv:11') >= 0) {
$userAgent['browser_name'] = 'msie11';
} else {
$userAgent['browser_name'] = 'msie';
}}}}