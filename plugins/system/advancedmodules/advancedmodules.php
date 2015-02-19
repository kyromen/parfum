<?php
/**
 * Main Plugin File
 *
 * @package         Advanced Module Manager
 * @version         4.19.2
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 * Plugin that shows active modules in menu item edit view
 */
class plgSystemAdvancedModules extends JPlugin
{
	private $_alias = 'advancedmodules';
	private $_title = 'ADVANCED_MODULE_MANAGER';
	private $_lang_prefix = 'AMM';

	private $_init = false;
	private $_helper = null;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		if (!$this->getHelper())
		{
			return;
		}

		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		$this->_helper->loadModuleHelper();
		$this->_helper->registerEvents();
	}

	/*
	 * Replace links to com_modules with com_advancedmodules
	 */
	public function onAfterRender()
	{
		if (!$this->getHelper())
		{
			return;
		}

		// only in html
		if (JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		$this->_helper->replaceLinks();
	}

	/*
	 * Below methods are general functions used in most of the NoNumber extensions
	 * The reason these are not placed in the NoNumber Framework files is that they also
	 * need to be used when the NoNumber Framework is not installed
	 */

	/**
	 * Create the helper object
	 *
	 * @return object The plugins helper object
	 */
	private function getHelper()
	{
		// Already initialized, so return
		if ($this->_init)
		{
			return $this->_helper;
		}

		$this->_init = true;

		if (!$this->isFrameworkEnabled())
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

		if (!nnProtect::isComponentInstalled($this->_alias))
		{
			return false;
		}

		if (nnProtect::isProtectedPage($this->_alias))
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/nnframework/helpers/helper.php';
		$this->_helper = nnFrameworkHelper::getPluginHelper($this);

		return $this->_helper;
	}

	/**
	 * Check if the NoNumber Framework is enabled
	 *
	 * @return bool
	 */
	private function isFrameworkEnabled()
	{
		// Return false if NoNumber Framework is not installed
		if (!$this->isFrameworkInstalled())
		{
			return false;
		}

		$nnframework = JPluginHelper::getPlugin('system', 'nnframework');
		if (!isset($nnframework->name))
		{
			$this->throwError($this->_lang_prefix . '_NONUMBER_FRAMEWORK_NOT_ENABLED');

			return false;
		}

		return true;
	}

	/**
	 * Check if the NoNumber Framework is installed
	 *
	 * @return bool
	 */
	private function isFrameworkInstalled()
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
		{
			$this->throwError($this->_lang_prefix . '_NONUMBER_FRAMEWORK_NOT_INSTALLED');

			return false;
		}

		return true;
	}

	/**
	 * Place an error in the message queue
	 */
	private function throwError($text)
	{
		// Return if page is not an admin page or the admin login page
		if (
			!JFactory::getApplication()->isAdmin()
			|| JFactory::getUser()->get('guest')
		)
		{
			return;
		}

		// load the admin language file
		JFactory::getLanguage()->load('plg_' . $this->_type . '_' . $this->_name, JPATH_ADMINISTRATOR);

		$text = JText::_($text) . ' ' . JText::sprintf($this->_lang_prefix . '_EXTENSION_CAN_NOT_FUNCTION', JText::_($this->_title));

		// Check if message is not already in queue
		$messagequeue = JFactory::getApplication()->getMessageQueue();
		foreach ($messagequeue as $message)
		{
			if ($message['message'] == $text)
			{
				return;
			}
		}

		JFactory::getApplication()->enqueueMessage($text, 'error');
	}
}

