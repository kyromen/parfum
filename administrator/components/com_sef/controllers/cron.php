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

class SEFControllerCron extends SEFController
{
    function display()
    {
        JRequest::setVar('view', 'cron');
        
        parent::display();
    }
    
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
        
        $this->registerTask('apply', 'save');
    }
    
    function save()
    {
        $model = $this->getModel('cron');
        
        if ($model->store())
        {
            $task = JRequest::getCmd('task');
            if( $task == 'save' ) {
                $link = 'index.php?option=com_sef';
            }
            elseif( $task == 'apply' ) {
                $link = 'index.php?option=com_sef&controller=cron';
            }
            $this->setRedirect($link, JText::_('COM_SEF_CRON_SAVE_SUCCESS'));
        }
        else
        {
            $this->setRedirect('index.php?option=com_sef&controller=cron', JText::_('COM_SEF_CRON_SAVE_FAIL'));
        }
    }
    
    function getfile()
    {
        $model = $this->getModel('cron');
        $file = $model->generateFile();
        
        if ($file === false)
        {
            $this->setRedirect('index.php?option=com_sef&controller=cron', JText::_('COM_SEF_CRON_SAVE_FAIL'));
            return;
        }
        
        // Output file
        if (!headers_sent()) {
            // Flush output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header ('Expires: 0');
            header ('Last-Modified: '.gmdate ('D, d M Y H:i:s', time()) . ' GMT');
            header ('Pragma: public');
            header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header ('Accept-Ranges: bytes');
            header ('Content-Length: ' . strlen($file));
            header ('Content-Type: text/plain');
            header ('Content-Disposition: attachment; filename=joomsef_crontab');
            header ('Connection: close');
            echo $file;
            jexit();
        }
        else {
            $this->setRedirect('index.php?option=com_sef&controller=cron', JText::_('COM_SEF_ERROR_HEADERS'));
        }
    }
}