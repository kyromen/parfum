<?php
/**
 * VirtueMart SEF extension for ARTIO JoomSEF
 * 
 * @package   JoomSEF
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2014 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

jimport('joomla.installer.installer');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.helper');

class ext_joomsef4_virtuemartInstallerScript
{
	
	function __construct($parent) {
	}
	
	function preflight($action, $installer) {
		return true;
	}

	public function postflight($action, $installer)
	{
		
		//set initial params for category urls based on previous ones. its upgrade to 3.0.19. 
		//how to get previous version? can we trust database?
		if (!empty($installer->manifest->version) AND version_compare((string)$installer->manifest->version, '3.0.19')>=0){
			
			$db = JFactory::getDBO();
			$db->setQuery('SELECT params FROM #__extensions WHERE type = '.$db->Quote('sef_ext').' AND element = '.$db->Quote('ext_joomsef4_virtuemart'));
			
			if (($vmExtParams = $db->loadResult()) AND ($vmExtParams = json_decode($vmExtParams))){
			
				$vmExtParamsChange = false;
				if (!isset($vmExtParams->manufacturer_category) && isset($vmExtParams->manufacturer)){
					if ($vmExtParams->manufacturer=="0") //manufacturer was not added to URL
						$vmExtParams->manufacturer_category = "1"; //defaultly yes anyway..
					elseif ($vmExtParams->manufacturer=="1") //if manufacturer was added, it was added before category (weird, but we must keep consistency)
						$vmExtParams->manufacturer_category = "2";
					$vmExtParamsChange = true;
				}
			
				if (!isset($vmExtParams->manufacturerid_category) && isset($vmExtParams->manufacturerid)){
					$vmExtParams->manufacturerid_category = $vmExtParams->manufacturerid; //copy "add manufacturer id" setting from product url setting
					$vmExtParamsChange = true;
				}
			
				if ($vmExtParamsChange){
					$db->setQuery('UPDATE #__extensions SET params = '.$db->Quote(json_encode($vmExtParams)).' WHERE type = '.$db->Quote('sef_ext').' AND element = '.$db->Quote('ext_joomsef4_virtuemart'));
					if (!$db->query())
						JError::raiseWarning(0, 'Cannot perform DB params update: '.$db->getErrorMsg());
				}
			}
		}
		
		return true;
    }

    function update($installer) {
    	return true;
    }

    public function uninstall($installer)
    {
        return true;
    }
}
?>