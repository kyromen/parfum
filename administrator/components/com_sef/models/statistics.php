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

jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/libs/google_analytics/google_analytics.php';

class SEFModelStatistics extends JModelList {
	private $_cid=null;
	private $_error=false;
	private $_message="";
    private $filterOrder;
    private $filterOrderDir;
    private $filterSEF;
    private $filterReal;
    private $filterComponent;
    private $filterLang;
    
	function __construct($config) {
		parent::__construct($config);
		$this->_cid=JRequest::getVar('cid',array(),'post','array');
        
        // Sorting
        $app = JFactory::getApplication();
        $this->filterOrder = $app->getUserStateFromRequest('sef.statistics.filter_order', 'filter_order', 'u.sefurl');
        $this->filterOrderDir = $app->getUserStateFromRequest('sef.statistics.filter_order_Dir', 'filter_order_Dir', 'asc');
        
        // Filters
        $this->filterComponent = $app->getUserStateFromRequest("sef.statistics.comFilter", 'comFilter', '');
        $this->filterSEF = $app->getUserStateFromRequest("sef.statistics.filterSEF", 'filterSEF', '');
        $this->filterReal = $app->getUserStateFromRequest("sef.statistics.filterReal", 'filterReal', '');
        $this->filterLang = $app->getUserStateFromRequest("sef.statistics.filterLang", 'filterLang', '');
	}
	
    protected function _getWhere() {
		$where = array();
        $where[] = "`origurl` != ''";
		if (!empty($this->filterSEF)) {
            $where[] = "`sefurl` LIKE ".$this->_db->Quote('%'.$this->filterSEF.'%');
		}
		if (!empty($this->filterReal)) {
            $where []= "`origurl` LIKE ".$this->_db->Quote('%'.$this->filterReal.'%');
		}
		if (!empty($this->filterLang)) {
			$where []= "(`origurl` LIKE '%lang=".$this->filterLang."%')";
		}
		if (!empty($this->filterComponent)) {
			$where []= "(`origurl` LIKE '%option=".$this->filterComponent."&%' OR `origurl` LIKE '%option=".$this->filterComponent."')";
		}
        
		return ' WHERE '.implode(' AND ', $where);
    }
    
	protected function getListQuery() {	
		$query = "SELECT u.id AS id, u.sefurl AS url, s.* FROM #__sefurls AS u LEFT JOIN #__sef_statistics AS s ON u.id = s.url_id";
        $query .= $this->_getWhere();
		$query .= " ORDER BY ".$this->filterOrder." ".$this->filterOrderDir;
        
		return $query;
	}
	
	function getConfig() {
		return SEFConfig::getConfig();
	}
    
    function getOrdering() {
        $ordering = array();
        $ordering['filterOrder'] = $this->filterOrder;
        $ordering['filterOrderDir'] = $this->filterOrderDir;
        
        return $ordering;
    }
	
	function getTotalUrls() {
		$query=$this->_db->getQuery(true);
		$query->select('COUNT(*)')->from("#__sefurls")->where("flag=1");
		$this->_db->setQuery($query);
		return $this->_db->loadResult();		
	}
	
	private function _getUrls() {
		$query=$this->_db->getQuery(true);
		$query->select("id AS id, sefurl")->from("#__sefurls")->where("flag=1");
		$this->_db->setQuery($query, 0, 20);
		return $this->_db->loadObjectList();
	}
	
	function prepareUpdate() {
		$query=$this->_db->getQuery(true);
		
		$query->update("#__sefurls")->set("flag=1");
		if(!empty($this->_cid)) {
			$query->where("id IN(".implode(",",$this->_cid).")");
		}
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			$this->setError($this->_db->stderr(true));
			return false;
		}
		return true;
	}
	
	function processValidation() {
		$urls=$this->_getUrls();
		$data=array();
		$i=0;
		foreach($urls as $url) {
			$i++;
			$validity=$this->_db->quote($this->_processValidity($url->sefurl));
			if($this->_error) {
				$query=$this->_db->getQuery(true);
				$query->update("#__sefurls")->set("flag=0")->where("flag=1");
				$this->_db->setQuery($query);
				if(!$this->_db->query()) {
					return array("type"=>"error","message"=>$this->_db->stderr(true));
				}
				return array("type"=>"error","message"=>str_replace("\\n","\n",$validity));
			}
			
			$query=$this->_db->getQuery(true);
			$query->select('COUNT(*)')->from("#__sef_statistics")->where("url_id=".$url->id);
			$this->_db->setQuery($query);
			$cnt=$this->_db->loadResult();
			
			$query=$this->_db->getQuery(true);
			if($cnt) {
				$query->update("#__sef_statistics")->set("validation_score=".$validity)->where("url_id=".$url->id);
			} else {
				$query->insert("#__sef_statistics")->set("validation_score=".$validity)->set("url_id=".$url->id);
			}
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				return array("type"=>"error","message"=>$this->_db->stderr(true));
			}
			
			$query=$this->_db->getQuery(true);
			$query->update("#__sefurls")->set("flag=0")->where("id=".$url->id);
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				return array("type"=>"error","message"=>$this->_db->stderr(true));
			}
			sleep(1);
		}
		return array("type"=>"step","cnt"=>$i);
	}
	
	private function _processValidity($url) {
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/classes/seftools.php';
		jimport('joomla.utilities.simplexml');
		
		$uri=str_replace("/administrator","",JFactory::getURI()->base(false)).$url;
		$validator="http://validator.w3.org/check?uri=".urlencode($uri)."&output=soap12";
		$data=SEFTools::PostRequest($validator,null,null,'get');
        if (!$data || $data->code != 200) {
            return 'Error';
        }
        
		$data=$data->content;
		
		$xml=new DomDocument("1.0");
		$xml->loadXML($data);
        if (!$xml) {
            return 'Could not load response';
        }
		if($xml->getElementsByTagName('Fault')->length>0) {
			$this->_error=true;
			return $xml->getElementsByTagName('errordetail')->item(0)->nodeValue;
		}
		$validity=$xml->getElementsByTagName('validity')->item(0)->nodeValue;
		$errors=$xml->getElementsByTagName('errorcount')->item(0)->nodeValue;
		$warnings=$xml->getElementsByTagName('warningcount')->item(0)->nodeValue;
		
		return(implode("/",array($validity,$errors,$warnings)));
	}
	
	function processStatistics() {
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/libs/stats/google.php';
		$urls=$this->_getUrls();
		$i=0;
		foreach($urls as $url) {
			$i++;
			$data=$this->_processStatistic($url->sefurl);
			if($this->_error) {
				$query=$this->_db->getQuery(true);
				$query->update("#__sefurls")->set("flag=0")->where("flag=1");
				$this->_db->setQuery($query);
				if(!$this->_db->query()) {
					return array("type"=>"error","message"=>$this->_db->stderr(true));
				}
				return array("type"=>"error","message"=>$this->message);
			}
			
			$query=$this->_db->getQuery(true);
			$query->select('COUNT(*)')->from("#__sef_statistics")->where("url_id=".$url->id);
			$this->_db->setQuery($query);
			$cnt=$this->_db->loadResult();
			
			$query=$this->_db->getQuery(true);
			if($cnt) {
				$query->update("#__sef_statistics");
			} else {
				$query->insert("#__sef_statistics");
			}
			$values=array();
			foreach($data as $col=>$val) {
				$query->set($col."=".$this->_db->quote(str_replace(",",".",$val)));
			}
			if($cnt) {
				$query->where("url_id=".$url->id);
			} else {
				$query->set("url_id=".$url->id);
			}
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				return array("type"=>"error","message"=>$this->_db->stderr(true));
			}
			
			$query=$this->_db->getQuery(true);
			$query->update("#__sefurls")->set("flag=0")->where("id=".$url->id);
			$this->_db->setQuery($query);
			if(!$this->_db->query()) {
				return array("type"=>"error","message"=>$this->_db->stderr(true));
			}
		}
		return array("type"=>"step","cnt"=>$i);
	}
	
	private function _processStatistic($url) {
		
		$data=array();
		$google=new StatsGoogle();
		$data["page_rank"]=$google->getPageRank($url);
		$data["total_indexed"]=$google->getTotalIndexed($url);
		$data["popularity"]=$google->getPopularity($url);
		$data["facebook_indexed"]=$google->getFacebookIndexed($url);
		$data["twitter_indexed"]=$google->getTwitterIndexed($url);
        $data["page_speed_score"]='';
        
        // Check API key presence
        $config = SEFConfig::getConfig();
        if (strlen($config->google_apikey) > 0) {
    		$page_speed=$google->getPageSpeed($url);
    		if(is_object($page_speed)) {
    			// Don't stop the update
                //$this->_error=true;
    			//$this->message=$page_speed->message;
    		} else if ($page_speed !== false) {
    			$data["page_speed_score"]=$page_speed;
    		}
        }
		
		return $data;
		
	}
	
	function getAccounts() {
		$analytics=Google_Analytics::getInstance();
		if($analytics==false) {
			return false;
		}
        
		$accounts_arr=$analytics->getAccounts();
        if (!is_array($accounts_arr) || count($accounts_arr) == 0) {
            return false;
        }
		
		$accounts=JHTML::_('select.genericlist',$accounts_arr,'account_id','class="inputbox" style="float:none"','id','title',JRequest::getInt('account_id',$analytics->getDefaultId()));
		
		return $accounts;
		
	}
	
	function getDefaultAccountId() {
		$analytics=Google_Analytics::getInstance();
		return $analytics->getDefaultId();
	}
	
	function getGlobals() {
		$analytics=Google_Analytics::getInstance();
		
		$data=array();
		$metrics="ga:visitors,ga:newVisits,ga:percentNewVisits,ga:visits,ga:avgTimeOnSite,ga:pageviews,ga:pageviewsPerVisit,ga:visitBounceRate,ga:avgTimeOnPage,ga:entrances";
		$datas=$analytics->getData($metrics);
		if(isset($datas[0])) {
			$data=$datas[0];
		}
		
		$metrics1="ga:entranceRate,ga:bounces,ga:entranceBounceRate,ga:visitBounceRate,ga:uniquePageviews,ga:exits,ga:exitRate";
		$datas=$analytics->getData($metrics1);
		if(isset($datas[0])) {
			$data=array_merge($data,$datas[0]);
		}
				
		return $data;
	}
	
	function getTopUrls() {
		$analytics=Google_Analytics::getInstance();
		
		$metrics="ga:pageviews,ga:timeOnPage";
		$dimensions="ga:pagePath";
		$sort="-ga:pageviews";
		
		$data=$analytics->getData($metrics,$dimensions,$sort,25);
		
		return $data;
	}
	
	function getTopReferers() {
		$analytics=Google_Analytics::getInstance();
		
		$metrics="ga:timeOnSite,ga:bounces,ga:newVisits,ga:visits";
		$dimensions="ga:source,ga:referralPath";
		$sort="-ga:visits";
		
		return $analytics->getData($metrics,$dimensions,$sort,25);
	}
	
	function getSources() {
		$analytics=Google_Analytics::getInstance();
		
		$metrics="ga:visits";
		$dimensions="ga:medium";
		
		return $analytics->getData($metrics,$dimensions);
	}
	
	function getVisits() {
		$analytics=Google_Analytics::getInstance();
		
		$metrics="ga:visits";
		$dimensions="ga:year,ga:month,ga:day";
		
		return $analytics->getData($metrics, $dimensions, $dimensions);
		
	}
	
	function getPageSpeed() {
		$query=$this->_db->getQuery(true);
		$query->select('page_speed_score');
		$query->from('#__sef_statistics');
		$query->where('url_id='.JRequest::getInt('id'));
		$this->_db->setQuery($query);
		$data=$this->_db->loadResult();
		
		$reg=new JRegistry();
		$reg->loadString($data, 'INI');
		return $reg->toObject();
	}
    
    public function getLists() {
        $lists = array();
        
        // Make the select list for the component filter
        $comList[] = JHTML::_('select.option', '', JText::_('COM_SEF_ALL'));
        $rows = SEFTools::getInstalledComponents();
        foreach (array_keys($rows) as $i) {
            $row = &$rows[$i];
            $comList[] = JHTML::_('select.option', $row->option, $row->name );
        }
        $lists['comList'] = JHTML::_('select.genericlist', $comList, 'comFilter', "class=\"inputbox\" onchange=\"document.adminForm.submit();\" size=\"1\"", 'value', 'text', $this->filterComponent);

        // Make the filter text boxes
        $lists['filterSEF']  = "<input class=\"hasTip\" type=\"text\" name=\"filterSEF\" value=\"{$this->filterSEF}\" size=\"40\" maxlength=\"255\" onkeydown=\"return handleKeyDown(event);\" title=\"".JText::_('COM_SEF_TT_FILTER_SEF')."\" />";
        $lists['filterReal'] = "<input class=\"hasTip\" type=\"text\" name=\"filterReal\" value=\"{$this->filterReal}\" size=\"40\" maxlength=\"255\" onkeydown=\"return handleKeyDown(event);\" title=\"".JText::_('COM_SEF_TT_FILTER_REAL')."\" />";
        
        // Reset filters button
        $lists['filterReset'] = '<input type="button" class="btn" value="'.JText::_('COM_SEF_RESET').'" onclick="resetFilters();" />';
        
        // Language filter
        $sefConfig = SEFConfig::getConfig();
        if ($sefConfig->langEnable) {
            $langs = JLanguageHelper::getLanguages();
            
            $langList = array();
            $langList[] = JHTML::_('select.option', '', JText::_('COM_SEF_ALL'));
            foreach ($langs as $lng) {
                $langList[] = JHTML::_('select.option', $lng->sef, $lng->title);
            }
            $lists['filterLang'] = JHTML::_('select.genericlist', $langList, 'filterLang', 'class="inputbox" onchange="document.adminForm.submit();" size="1"', 'value', 'text', $this->filterLang);
        }
        
        return $lists;
    }
}
?>