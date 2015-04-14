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

class SEFControllerHtaccess extends SEFController
{
    function display()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        JRequest::setVar( 'view', 'htaccess' );
        JRequest::setVar( 'layout', 'simple' );

        parent::display();
    }
    
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
        
        $this->registerTask('applySimple', 'saveSimple');
        $this->registerTask('applyAdvanced', 'saveAdvanced');
        $this->registerTask('add', 'edit');
    }

    function _checkWritable()
    {
        $model =& $this->getModel('htaccess');

        if( !$model->IsWritable() ) {
            JError::raiseWarning('100', JText::_('COM_SEF_INFO_HTACCESS_NOT_WRITABLE'));
            $this->setRedirect('index.php?option=com_sef');
            return false;
        }

        return true;
    }

    function advanced()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        JRequest::setVar( 'view', 'htaccess' );
        JRequest::setVar( 'layout', 'advanced' );

        parent::display();
    }
    
    function edit()
    {
        JRequest::setVar( 'view', 'htaccess' );
        JRequest::setVar( 'layout', 'redirect' );

        parent::display();
    }
    
    function save()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        $model =& $this->getModel('htaccess');
        
        $newid = $model->storeOptions();
        if( $newid !== false ) {
            $msg = JText::_('COM_SEF_HTACCESS_SAVED');
        }
        else {
            $msg = JText::_('COM_SEF_ERROR_SAVING_HTACCESS');
        }
        
        $this->setRedirect('index.php?option=com_sef&controller=htaccess', $msg);
    }
    
    function cancel()
    {
        $this->setRedirect('index.php?option=com_sef&controller=htaccess');
    }
    
    function remove()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        $model =& $this->getModel('htaccess');
        
        if( $model->remove() ) {
            $msg = JText::_('COM_SEF_HTACCESS_SAVED');
        }
        else {
            $msg = JText::_('COM_SEF_ERROR_SAVING_HTACCESS');
        }
        
        $this->setRedirect('index.php?option=com_sef&controller=htaccess', $msg);
    }
    
    function saveAdvanced()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        $task = JRequest::getCmd('task');
        $model =& $this->getModel('htaccess');
        
        if( $model->storeAdvanced() ) {
            $msg = JText::_('COM_SEF_HTACCESS_SAVED');
        }
        else {
            $msg = JText::_('COM_SEF_ERROR_SAVING_HTACCESS');
        }
        
        if( $task == 'saveAdvanced' ) {
            $this->setRedirect('index.php?option=com_sef&controller=htaccess', $msg);
        }
        else {
            $this->setRedirect('index.php?option=com_sef&controller=htaccess&task=advanced', $msg);
        }
    }
    
    function saveSimple()
    {
        if( !$this->_checkWritable() ) {
            return;
        }
        
        $task = JRequest::getCmd('task');
        $model =& $this->getModel('htaccess');
        
        $newid = $model->storeSimple();
        if( $newid !== false ) {
            $msg = JText::_('COM_SEF_HTACCESS_SAVED');
        }
        else {
            $msg = JText::_('COM_SEF_ERROR_SAVING_HTACCESS');
        }
        
        if( $task == 'saveSimple' ) {
            $this->setRedirect('index.php?option=com_sef&controller=htaccess', $msg);
        }
        else {
            $this->setRedirect('index.php?option=com_sef&controller=htaccess&task=edit&cid[]='.$newid, $msg);
        }
    }
}
?>