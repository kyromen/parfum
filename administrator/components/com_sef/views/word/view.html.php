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

jimport('joomla.application.component.view');

class SefViewWord extends SefView
{
    function display($tpl = null)
    {
        //get the data
        $word     =& $this->get('Data');
        $lists    = $this->get('Lists');
        $isNew    = ($word->id < 1);

        $text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
        JToolBarHelper::title('JoomSEF - ' .  JText::_( 'Word' ).' [ ' . $text.' ]', 'manage-words.png' );
        JToolBarHelper::save();
        if ($isNew)  {
            JToolBarHelper::cancel();
        } else {
            // for existing items the button is renamed `close`
            JToolBarHelper::cancel('cancel', 'Close');
        }

        $this->assignRef('word', $word);
        $this->assignRef('lists', $lists);
        
        JHTML::_('behavior.tooltip');
        
        // Load JS
        $document = & JFactory::getDocument();
        $document->addScript('components/com_sef/assets/js/words.js');
        $document->addStyleSheet('components/com_sef/assets/css/words.css');

        parent::display($tpl);
    }
    
}
?>