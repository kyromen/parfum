<?php
/*------------------------------------------------------------------------
# plg_iphoneicon - iPhone Icon system plugin
# ------------------------------------------------------------------------
# author    Jeremy Magne
# copyright Copyright (C) 2010 Daycounts.com. All Rights Reserved.
# Websites: http://www.daycounts.com
# Technical Support: http://www.daycounts.com/en/contact/
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.environment.browser');
jimport('joomla.filesystem.file');

class plgSystemJsAsyncDefer extends JPlugin
{

	function plgSystemJsAsyncDefer(& $subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	function onBeforeCompileHead()
	{
		$app = JFactory::getApplication('site');
		if ( $app->isAdmin()) return; //Exit if in administration
		$doc = JFactory::getDocument();
		
		$scripts_to_handle = trim( (string) $this->params->get('scripts_to_handle', ''));
		
		if ($scripts_to_handle) {
			$paths = array_map('trim', (array) explode("\n", $scripts_to_handle));
			foreach ($paths as $path) {
				if (strpos($path,'http')===0) {
					continue;
				}
				$withoutroot = str_replace(JURI::root(true),'',$path);
				if ($withoutroot != $path) {
					$paths[] = $withoutroot;
				}
				$withroot = JURI::root(true).$path;
				if ($withroot != $path) {
					$paths[] = $withroot;
				}
				$withdomain = JURI::root(false).$path;
				if ($withdomain != $path) {
					$paths[] = $withdomain;
				}
			}
			
			foreach ($doc->_scripts as $url => $scriptparams) {
				if (in_array($url,$paths)) {
					if ($this->params->get('defer')) {
						$doc->_scripts[$url]['defer'] = true;
					}
					if ($this->params->get('async')) {
						$doc->_scripts[$url]['async'] = true;
					}
				}
			}
		}
		
		return true;
	}
	
}