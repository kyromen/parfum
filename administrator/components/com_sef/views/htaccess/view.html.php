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

class SefViewHtaccess extends SefView
{
    function display($tpl = null)
    {
        switch($this->_layout) {
            case 'advanced':
                $this->_displayAdvanced();
                break;
                
            case 'redirect':
                $this->_displayRedirect();
                break;
                
            default:
                $this->_displaySimple();
                break;
        }
    }
    
    function _displaySimple()
    {
        JToolBarHelper::title('JoomSEF - ' . JText::_('COM_SEF_HTACCESS_EDITOR'), 'edit.png');
        
        JToolBarHelper::addNew();
        JToolBarHelper::editList();
        JToolBarHelper::deleteList('COM_SEF_CONFIRM_DEL_REDIRECTS');
        JToolBarHelper::divider();
        JToolBarHelper::save('save', 'COM_SEF_SAVE_OPTIONS');
        JToolBarHelper::divider();
        JToolBarHelper::custom('advanced', 'move', 'move', 'COM_SEF_ADVANCED_EDIT', false);
        JToolBarHelper::divider();
        JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
        
        $this->assignRef('items', $this->get('Redirects'));
        $this->assignRef('lists', $this->get('Lists'));
        
        parent::display();
    }
    
    function _displayAdvanced()
    {
        JError::raiseNotice('100', JText::_('COM_SEF_WARNING_HTACCESS_EDIT'));
        
        JToolBarHelper::title('JoomSEF - '. JText::_('COM_SEF_HTACCESS_EDITOR').' - '.JText::_('COM_SEF_ADVANCED_EDIT'), 'edit.png');

        JToolBarHelper::save('saveAdvanced');
        JToolBarHelper::apply('applyAdvanced');
        JToolBarHelper::cancel();
        
        $this->assignRef('file', $this->get('File'));
        
        parent::display();
    }
    
    function _displayRedirect()
    {
        $redirect = $this->get('Redirect');
        $isNew = ($redirect->id < 1);
        
        $text = $isNew ?  'COM_SEF_NEW_REDIRECT'  : 'COM_SEF_EDIT_REDIRECT';
        JToolBarHelper::title(JText::_('COM_SEF_JOOMSEF_HTACCESS_EDITOR').' - '.JText::_($text), 'edit.png');
        
        JToolBarHelper::save('saveSimple');
        JToolBarHelper::apply('applySimple');
        if( $isNew ) {
            JToolBarHelper::cancel();
        } else {
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancel', 'Close');
        }
        
        $this->assignRef('redirect', $redirect);
        
        JHTML::_('behavior.tooltip');
        
        parent::display();
    }
}
?>