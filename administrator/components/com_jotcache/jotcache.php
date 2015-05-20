<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die('Restricted access');
$controller = JControllerLegacy::getInstance('Main');
$task = JFactory::getApplication()->input->get('task');
$controller->execute($task);
$controller->redirect();
?>