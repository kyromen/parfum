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

class SEFControllerWords extends SEFController
{
    function display()
    {
        JRequest::setVar( 'view', 'words' );
        
        parent::display();
    }
    
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
        
        $this->registerTask('add', 'edit');
    }

    function edit()
    {
        JRequest::setVar( 'view', 'word' );
        
        parent::display();
    }
    
    function save()
    {
        $model = $this->getModel('words');

        if ($model->store()) {
            $msg = '';
        } else {
            $msg = JText::_( 'Error Saving Words' ) . ': ' . $model->getError();
        }
        
        $this->setRedirect('index.php?option=com_sef&controller=words', $msg);
    }

    function remove()
    {
		$model = $this->getModel('words');
		
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Words Could not be Deleted' );
		} else {
			$msg = '';
		}

		$this->setRedirect( 'index.php?option=com_sef&controller=words', $msg );
    }
}
?>