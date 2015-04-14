<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.2
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2015 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class SefViewSitemap extends SefView
{
	function display($tpl = null)
	{
	    $icon = 'manage-sitemap.png';
		JToolBarHelper::title(JText::_('COM_SEF_JOOMSEF_SITEMAP_MANAGER'), $icon);
		
        $this->assign($this->getModel());
        $lists =& $this->get('Lists');
        
        $bar =& JToolBar::getInstance();
        
		// Actions
		$bar->appendButton('Custom', $lists['selection']);
		$bar->appendButton('Custom', $lists['actions']);
		$bar->appendButton('Custom', $lists['newdate'] . $lists['newpriority'] . $lists['newfrequency']);
		$bar->appendButton('Custom', '<input type="button" value="'.JText::_('COM_SEF_PROCEED').'" onclick="doAction();" />');
		JToolBarHelper::divider();
		
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::custom('generatexml', 'xml', '', 'COM_SEF_GENERATE_XML', false);
        JToolBarHelper::spacer();
        JToolBarHelper::custom('pinggoogle', 'google', '', 'COM_SEF_PING_GOOGLE', false);
        //JToolBarHelper::custom('pingyahoo', 'yahoo', '', 'COM_SEF_PING_YAHOO', false);
        JToolBarHelper::custom('pingbing', 'bing', '', 'COM_SEF_PING_BING', false);
        JToolBarHelper::custom('pingservices', 'services', '', 'COM_SEF_PING_SERVICES', false);
        JToolBarHelper::spacer();
        JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
        
		// Get data from the model
        $this->assignRef('items', $this->get('Data'));
        $this->assignRef('total', $this->get('Total'));
        $this->assignRef('lists', $lists);
        $this->assignRef('pagination', $this->get('Pagination'));
        
        // Check the sitemap changed flag
        $sefConfig =& SEFConfig::getConfig();
        //$file = JPATH_ROOT.'/'.$sefConfig->sitemap_filename.'.xml';
        if ($sefConfig->sitemap_changed) {
            JError::raiseNotice(100, JText::_('COM_SEF_SITEMAP_DEPRECATED'));
        }
        
        JHTML::_('behavior.tooltip');
        
		parent::display($tpl);
	}

}
