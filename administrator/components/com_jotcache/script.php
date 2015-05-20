<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
class com_jotcacheInstallerScript {
function uninstall() {
if (count(JError::getErrors()) > 0) {
echo "Error condition - Uninstallation not successfull! You have to manually remove com_jotcache from '.._extensions' table as well as to drop '.._jotcache' and '.._jotcache_exclude' tables";
} else {
echo "Uninstallation successfull!";
}}}