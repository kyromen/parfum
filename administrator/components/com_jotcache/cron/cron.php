<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */

/**
 * PURPOSE
 * cron.php file is dedicated for cron job execution of Joomla/JotCache PAGE CACHE CLEANING.
 * Here are cleaned all expired cached pages from file system and database.
 */
// Initialize Joomla framework
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
/* * *************************
 * Settings for cron job run :
 * 1. Move and rename this file cron.php to the directory outside site public access.
 * 2. The constant 'JPATH_BASE' have to contain full server path to the server file directory
 *    - the root of Joomla installation
 * ************************* */
define('JPATH_BASE', '/.....');
// Use ONLY for testing - when this file is located in original directory :
//define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
// echo JPATH_BASE;

require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

try {
  $tmp = php_sapi_name();
  if (php_sapi_name() != 'cli') {
    throw new Exception('cron script is not called with CLI');
  }
  $app = JFactory::getApplication('site');
  if (!is_object($app)) {
    throw new Exception('Application object was not created');
  }
  $db = JFactory::getDBO();
  if (!is_object($db)) {
    throw new Exception('Database object was not created');
  }
  $root = JPATH_BASE . DS . 'cache' . DS . 'page';
  if (!file_exists($root)) {
    $query = $db->getQuery(true);
    $query->delete($db->quoteName('#__jotcache'));
    $cursor = $db->setQuery($query)->query();
    if ($cursor === false) {
      throw new Exception('delete items on #__jotcache table not successfull');
    } else {
      exit();
    }
  }
  $query = $db->getQuery(true);
  $query->select('fname')->from('#__jotcache');
  $db->setQuery($query);
  $rows = $db->loadObjectList();
  $deleteList = array();
  $cnt = 0;
  foreach ($rows as $row) {
    $deleteList[$row->fname] = $cnt;
    $cnt++;
  }
  $expired=0;
  if ($handle = opendir($root)) {
    while (false !== ($file = readdir($handle))) {
      if ($file != "." && $file != "..") {
        $ext = strrchr($file, ".");
        if ($ext == '.php_expire') {
          $fname = substr($file, 0, -11);
          $file1 = $root . DS . $fname . '.php_expire';
          $time = @file_get_contents($file1);
          if ($time < microtime(true) || !array_key_exists($fname, $deleteList)) {
            @chmod($file1, 0777);
            @unlink($file1);
            $file2 = $root . DS . $fname . '.php';
            @chmod($file2, 0777);
            @unlink($file2);
            $expired++;
          }else{
            if (array_key_exists($fname, $deleteList))
            unset($deleteList[$fname]);
          }
        }
      }
    }
    closedir($handle);
  }
  $deleteList = array_flip($deleteList);
  $cnt = count($deleteList);
  if ($cnt > 0) {
    $list = implode("','", $deleteList);
    $query->clear();
    $query->delete($db->quoteName('#__jotcache'))
            ->where("fname IN ('$list')");
    $cursor = $db->setQuery($query)->query();
    if ($cursor === false) {
      throw new Exception('delete expired items on #__jotcache table not successfull');
    }
  }
  exit("JotCache clean cron job finished. $expired expired items for cache storage deleted.");
} catch (Exception $e) {
  // An exception has been caught, echo the message.
  exit('[JotCache clean] cron script error : ' . $e->getMessage());
}
?>
