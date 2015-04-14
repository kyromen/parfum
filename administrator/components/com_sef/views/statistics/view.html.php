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

jimport('joomla.application.component.view');

class SefViewStatistics extends SefView {
	function display($tpl=null) {
        $this->setLayout(JRequest::getCmd('layout','default'));
        
		$icon = 'statistics.png';
        if(JRequest::getCmd('layout','default')=='speed') {
			$this->speed=$this->get('pageSpeed');
			
			JFactory::getDocument()->addScript(JFactory::getURI()->base(false)."/components/com_sef/assets/charts/FusionCharts116.js");
			
		} else {
			$this->items=$this->get('Items');
			$this->pagination=$this->get('Pagination');
			$this->state=$this->get('State');
			$this->config=$this->get('config');
            $this->ordering = $this->get('Ordering');
            $this->lists = $this->get('Lists');
			
			$this->accounts=$this->get('accounts');
			if($this->accounts!==false) {
				$this->globals_html=$this->renderGlobals();
				$this->top_urls_html=$this->renderTopUrls();
				$this->top_referers_html=$this->renderTopReferers();
				$this->sources_html=$this->renderSources();
				$this->visits_html=$this->renderVisits();
			}
			
			JHTML::_('behavior.tooltip');
			JHTML::_('behavior.modal');
			JFactory::getDocument()->addScript(JFactory::getURI()->base(false)."/components/com_sef/assets/charts/FusionCharts116.js");
			
			JToolbarHelper::title(JText::_('COM_SEF_STATISTICS'), $icon);
			JToolbarHelper::custom("update_statistics","refresh.png","refresh_f2.png",JText::_('COM_SEF_UPDATE_STATS'));
			JToolbarHelper::custom("updatevalidity","refresh.png","refresh_f2.png",JText::_('COM_SEF_UPDATE_VALIDITY'));
            JToolBarHelper::spacer();
            JToolBarHelper::back('COM_SEF_BACK', 'index.php?option=com_sef');
        
            // Check CURL and OpenSSL presence
            if (!function_exists('curl_init') && !function_exists('openssl_open')) {
                $app = JFactory::getApplication();
                $app->enqueueMessage(JText::_('COM_SEF_ANALYTICS_NO_SSL'));
            }
		}
		
		parent::display($tpl);

	}
	
	function __construct() {
		parent::__construct();
		
		$this->cache=JFactory::getCache('com_sef','output');
		$this->cache->setLifeTime(3600*24);
		$this->cache->setCaching(1);
	}
	
	function showUpdateValidity() {
		JToolbarHelper::title(JText::_('COM_SEF_UPDATE_VALIDITY'));
		$this->total=$this->get('totalUrls');
		
		$this->setLayout('updatevalidity');
		JHTML::_('behavior.framework');
		parent::display();
	}
	
	function showUpdateStatistics() {
		JToolbarHelper::title(JText::_('COM_SEF_UPDATE_STATS'));
		$this->total=$this->get('totalUrls');
		
        // Check API key presence
        $config = SEFConfig::getConfig();
        if (strlen($config->google_apikey) == 0) {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('COM_SEF_PAGESPEED_UPDATE_NO_KEY'));
        }
        // Check SSL support
        else if (!function_exists('curl_init') && !function_exists('openssl_open')) {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('COM_SEF_PAGESPEED_UPDATE_NO_SUPPORT'));
        }
        
		$this->setLayout('updatestatistics');
		JHTML::_('behavior.framework');
		parent::display();
	}
	
	function renderGlobals($force=false) {
		$id=md5(JRequest::getInt('account_id',$this->get('defaultAccountId')).JRequest::getString('start_date',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d")).JRequest::getString('end_date',JFactory::getDate()->format("Y-m-d"))."globals");
		
		if($force) {
			$this->cache->remove($id);
		}
		$data=$this->cache->get($id);
		if($data==false) {
			$this->globals=$this->get('globals');
			$data=$this->loadTemplate('statistics_global');
			$this->cache->store($data,$id);
		}
		return $data;
	}
	
	function renderTopUrls($force=false) {
		$id=md5(JRequest::getInt('account_id',$this->get('defaultAccountId')).JRequest::getString('start_date',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d")).JRequest::getString('end_date',JFactory::getDate()->format("Y-m-d"))."topUrls");
		
		if($force) {
			$this->cache->remove($id);
		}
		
		$data=$this->cache->get($id);
		if($data==false) {
			if(!isset($this->globals)) {
				$this->globals=$this->get('globals');
			}
			$this->top=$this->get('topUrls');
			$data=$this->loadTemplate('statistics_top');
			$this->cache->store($data,$id);
		}
		return $data; 
	}
	
	function renderTopReferers($force=false) {
		$id=md5(JRequest::getInt('account_id',$this->get('defaultAccountId')).JRequest::getString('start_date',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d")).JRequest::getString('end_date',JFactory::getDate()->format("Y-m-d"))."topReferers");
		
		if($force) {
			$this->cache->remove($id);
		}
		
		$data=$this->cache->get($id);
		if($data==false) {
			if(!isset($this->globals)) {
				$this->globals=$this->get('globals');
			}
			$this->referers=$this->get('topReferers');
			$data=$this->loadTemplate('statistics_referers');
			$this->cache->store($data,$id);
		}
		
		return $data; 
	}
	
	function renderSources($force=false) {
		$id=md5(JRequest::getInt('account_id',$this->get('defaultAccountId')).JRequest::getString('start_date',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d")).JRequest::getString('end_date',JFactory::getDate()->format("Y-m-d"))."sources");
		
		if($force) {
			$this->cache->remove($id);
		}
		
		$data=$this->cache->get($id);
		if($data==false) {
			$this->sources=$this->get('sources');
			$data=$this->loadTemplate('statistics_sources');	
		}
		return $data;
		
	}
	
	function renderVisits($force=false) {
		$id=md5(JRequest::getInt('account_id',$this->get('defaultAccountId')).JRequest::getString('start_date',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d")).JRequest::getString('end_date',JFactory::getDate()->format("Y-m-d"))."sources");
		
		if($force) {
			$this->cache->remove($id);
		}
		
		$data=$this->cache->get($id);
		
		if($data==false) {
			$this->visits=$this->get('visits');
			$data=$this->loadTemplate('statistics_visits');
		}
		return $data;
	}
}
?>