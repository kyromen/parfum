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
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SEFControllerStatistics extends SEFController {
    function display()
    {
        JRequest::setVar('view', 'statistics');
        
        parent::display();
    }
    
	function updatevalidity() {
		$model=$this->getModel('statistics');
		if(!$model->prepareUpdate()) {
			$this->setRedirect(JRoute::_('index.php?option=com_sef&view=statistics'),JText::_($model->getError()));
			return false;
		}
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$view->showUpdateValidity();
	}
	
	function process_validation() {
		$model=$this->getModel('statistics');
		echo json_encode($model->processValidation());
		JFactory::getApplication()->close();
	}
	
	function update_statistics() {
		$model=$this->getModel('statistics');
		if(!$model->prepareUpdate()) {
			$this->setRedirect(JRoute::_('index.php?option=com_sef&view=statistics'),JText::_($model->getError()));
			return false;
		}
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$view->showUpdateStatistics();
	}
	
	function process_statistics() {
		$model=$this->getModel('statistics');
		echo json_encode($model->processStatistics());
		JFactory::getApplication()->close();
	}
	
	function get_global() {
		$model=$this->getModel('statistics');
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$html=$view->renderGlobals(true);
		
		echo json_encode(array('html'=>$html));
		JFactory::getApplication()->close();
	}
	
	function get_most_pages() {
		$model=$this->getModel('statistics');
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$html=$view->renderTopUrls(true);
		
		echo json_encode(array('html'=>$html));
		JFactory::getApplication()->close();
	}
	
	function get_most_referers() {
		$model=$this->getModel('statistics');
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$html=$view->renderTopReferers(true);
		
		echo json_encode(array('html'=>$html));
		JFactory::getApplication()->close();
	}
	
	function get_visits() {
		$model=$this->getModel('statistics');
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$html=$view->renderVisits(true);
		
		echo json_encode(array('html'=>$html));
		JFactory::getApplication()->close();
	}
	
	function get_sources() {
		$model=$this->getModel('statistics');
		$view=$this->getView('statistics','html');
		$view->setModel($model,true);
		$html=$view->renderSources(true);
		
		echo json_encode(array('html'=>$html));
		JFactory::getApplication()->close();
	}
}
?>