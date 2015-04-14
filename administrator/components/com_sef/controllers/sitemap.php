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

class SEFControllerSiteMap extends SEFController
{
    function display()
    {
        JRequest::setVar( 'view', 'sitemap' );
        
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
        $model = $this->getModel('sitemap');

        if ($model->store()) {
            $msg = '';
        } else {
            $msg = JText::_( 'Error Saving SiteMap Data' ) . ': ' . $model->getError();
        }
        
        $task = JRequest::getCmd('task');
        $link = 'index.php?option=com_sef';
        if ($task == 'apply') {
            $link = 'index.php?option=com_sef&controller=sitemap';
        }

        $this->setRedirect($link, $msg);
    }
    
    function generateXml()
    {
        $model = $this->getModel('sitemap');

        if ($model->generateXml()) {
            $msg = JText::_( 'XML Generated' );
        } else {
            $msg = JText::_( 'Error Generating XML' ) . ': ' . $model->getError();
        }
        
        $this->setRedirect('index.php?option=com_sef&controller=sitemap', $msg);
    }
    
    function pingGoogle()
    {
        $model = $this->getModel('sitemap');
        $model->pingGoogle();
        $this->setRedirect('index.php?option=com_sef&controller=sitemap');
    }
    
    function pingYahoo()
    {
        $model = $this->getModel('sitemap');
        $model->pingYahoo();
        $this->setRedirect('index.php?option=com_sef&controller=sitemap');
    }
    
    function pingBing()
    {
        $model = $this->getModel('sitemap');
        $model->pingBing();
        $this->setRedirect('index.php?option=com_sef&controller=sitemap');
    }
    
    function pingServices()
    {
        $model = $this->getModel('sitemap');
        $model->pingServices();
        $this->setRedirect('index.php?option=com_sef&controller=sitemap');
    }
    
    function index()
    {
        $this->_setIndex(1);
    }
    
    function unindex()
    {
        $this->_setIndex(0);
    }
    
    function _getWhere()
    {
        $selection = JRequest::getVar('selection', 'selected', 'post');
        $model =& $this->getModel('sitemap');
        
        $where = '';
        if ($selection == 'selected') {
            $where = $model->_getWhereIds();
        }
        else {
            $where = $model->_getWhere();
        }
        
        return $where;
    }
    
    function setDate()
    {
        $model =& $this->getModel('sitemap');
        $where = $this->_getWhere();
        $date = JRequest::getVar('newdate', null, 'post');
        
        $msg = '';
        if( !$model->setDate($date, $where) ) {
            $msg = JText::_( 'Error Saving URLs' );
        }
        
        $this->setRedirect( 'index.php?option=com_sef&controller=sitemap', $msg );
    }
    
    function setPriority()
    {
        $model =& $this->getModel('sitemap');
        $where = $this->_getWhere();
        $date = JRequest::getVar('newpriority', null, 'post');
        
        $msg = '';
        if( !$model->setPriority($date, $where) ) {
            $msg = JText::_( 'Error Saving URLs' );
        }
        
        $this->setRedirect( 'index.php?option=com_sef&controller=sitemap', $msg );
    }
    
    function setFrequency()
    {
        $model =& $this->getModel('sitemap');
        $where = $this->_getWhere();
        $date = JRequest::getVar('newfrequency', null, 'post');
        
        $msg = '';
        if( !$model->setFrequency($date, $where) ) {
            $msg = JText::_( 'Error Saving URLs' );
        }
        
        $this->setRedirect( 'index.php?option=com_sef&controller=sitemap', $msg );
    }
    
    function indexPublished()
    {
        $model =& $this->getModel('sitemap');
        $where = $this->_getWhere();
        
        $msg = '';
        if( !$model->indexPublished($where) ) {
            $msg = JText::_( 'Error Saving URLs' );
        }
        
        $this->setRedirect( 'index.php?option=com_sef&controller=sitemap', $msg );
    }
    
    function _setIndex($state)
    {
        $model =& $this->getModel('sitemap');
        $where = $this->_getWhere();
        
        $msg = '';
        if( !$model->setIndex($state, $where) ) {
            $msg = JText::_( 'Error Saving URLs' );
        }
        
        $this->setRedirect( 'index.php?option=com_sef&controller=sitemap', $msg );
    }
}
?>