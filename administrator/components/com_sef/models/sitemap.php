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

jimport('joomla.language.helper');
jimport('joomla.application.component.model');

class SEFModelSiteMap extends SEFModel
{
    function __construct()
    {
        parent::__construct();
        $this->_getVars();
    }

    function _getVars()
    {
        $mainframe =& JFactory::getApplication();

        $this->filterComponent = $mainframe->getUserStateFromRequest("sef.sitemap.comFilter", 'comFilter', '');
        $this->filterSEF = $mainframe->getUserStateFromRequest("sef.sitemap.filterSEF", 'filterSEF', '');
        $this->filterReal = $mainframe->getUserStateFromRequest("sef.sitemap.filterReal", 'filterReal', '');
        $this->filterLang = $mainframe->getUserStateFromRequest('sef.sitemap.filterLang', 'filterLang', '');
        $this->filterIndexed = $mainframe->getUserStateFromRequest("sef.sitemap.filterIndexed", 'filterIndexed', '');
        $this->filterFrequency = $mainframe->getUserStateFromRequest("sef.sitemap.filterFrequency", 'filterFrequency', '');
        $this->filterPriority = $mainframe->getUserStateFromRequest("sef.sitemap.filterPriority", 'filterPriority', '');
        $this->filterOrder = $mainframe->getUserStateFromRequest('sef.sitemap.filter_order', 'filter_order', 'sefurl');
        $this->filterOrderDir = $mainframe->getUserStateFromRequest('sef.sitemap.filter_order_Dir', 'filter_order_Dir', 'asc');

        $this->limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $this->limitstart	= $mainframe->getUserStateFromRequest('sef.sitemap.limitstart', 'limitstart', 0, 'int');

        // In case limit has been changed, adjust limitstart accordingly
        $this->limitstart = ( $this->limit != 0 ? (floor($this->limitstart / $this->limit) * $this->limit) : 0 );
    }

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery()
    {
        $limit = '';
        if( ($this->limit != 0) || ($this->limitstart != 0) ) {
            $limit = " LIMIT {$this->limitstart},{$this->limit}";
        }

        $query = "SELECT * FROM `#__sefurls` ".$this->_getWhere()." ORDER BY ".$this->_getSort().$limit;

        return $query;
    }

    function _getSort()
    {
        if( !isset($this->_sort) ) {
            $this->_sort = '`' . $this->filterOrder . '` ' . $this->filterOrderDir;
        }

        return $this->_sort;
    }
    
    function _getWhereIds()
    {
        $ids = JRequest::getVar('cid', array(), 'post', 'array');

        $where = '';
        if( count($ids) > 0 ) {
            $where = 'WHERE `id` IN (' . implode(',', $ids) . ')';
        }

        return $where;
    }

    function _getWhere()
    {
        if( empty($this->_where) ) {
            $where = "`origurl` != '' ";
            $db =& JFactory::getDBO();

            // filter URLs
            if ($this->filterComponent != '') {
                $where .= "AND (`origurl` LIKE '%option={$this->filterComponent}&%' OR `origurl` LIKE '%option={$this->filterComponent}') ";
            }
            if ($this->filterLang != '' ) {
                $where .= "AND (`origurl` LIKE '%lang={$this->filterLang}%') ";
            }
            if ($this->filterSEF != '') {
                if( substr($this->filterSEF, 0, 4) == 'reg:' ) {
                    $val = substr($this->filterSEF, 4);
                    if( $val != '' ) {
                        // Regular expression search
                        $val = $db->Quote($val);
                        $where .= "AND `sefurl` REGEXP $val ";
                    }
                }
                else {
                    $val = $db->Quote('%'.$this->filterSEF.'%');
                    $where .= "AND `sefurl` LIKE $val ";
                }
            }
            if ($this->filterReal != '') {
                if( substr($this->filterReal, 0, 4) == 'reg:' ) {
                    $val = substr($this->filterReal, 4);
                    if( $val != '' ) {
                        // Regular expression search
                        $val = $db->Quote($val);
                        $where .= "AND `origurl` REGEXP $val ";
                    }
                }
                else {
                    $val = $db->Quote('%'.$this->filterReal.'%');
                    $where .= "AND `origurl` LIKE $val ";
                }
            }

            // filter sitemap data
            if ($this->filterIndexed != 0) {
                if ($this->filterIndexed == 1) {
                    $where .= "AND `sm_indexed` = '1' ";
                }
                elseif ($this->filterIndexed == 2) {
                    $where .= "AND `sm_indexed` = '0' ";
                }
            }
            if ($this->filterFrequency != '') {
                $where .= "AND `sm_frequency` = '{$this->filterFrequency}' ";
            }
            if ($this->filterPriority != '') {
                $where .= "AND `sm_priority` = '{$this->filterPriority}' ";
            }

            if( !empty($where) ) {
                $where = "WHERE " . $where;
            }

            $this->_where = $where;
        }

        return $this->_where;
    }

    function getTotal()
    {
        if( !isset($this->_total) )
        {
            $this->_db->setQuery("SELECT COUNT(*) FROM `#__sefurls` ".$this->_getWhere());
            $this->_total = $this->_db->loadResult();
        }

        return $this->_total;
    }

    /**
     * Retrieves the data
     */
    function getData()
    {
        // Lets load the data if it doesn't already exist
        if (empty( $this->_data ))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList( $query );
        }

        return $this->_data;
    }

    function getLists()
    {
        // make the select list for the component filter
        $comList[] = JHTML::_('select.option', '', JText::_('COM_SEF_ALL'));
        $rows = SEFTools::getInstalledComponents();
        foreach(array_keys($rows) as $i) {
            $row = &$rows[$i];
            $comList[] = JHTML::_('select.option', $row->option, $row->name );
        }
        $lists['comList'] = JHTML::_( 'select.genericlist', $comList, 'comFilter', "class=\"inputbox\" onchange=\"document.adminForm.submit();\" size=\"1\"", 'value', 'text', $this->filterComponent);
        
        // make the filter text boxes
        $lists['filterSEF']  = "<input class=\"hasTip\" type=\"text\" name=\"filterSEF\" value=\"{$this->filterSEF}\" size=\"40\" maxlength=\"255\" onkeydown=\"return handleKeyDown(event);\" title=\"".JText::_('COM_SEF_TT_FILTER_SEF')."\" />";
        $lists['filterReal'] = "<input class=\"hasTip\" type=\"text\" name=\"filterReal\" value=\"{$this->filterReal}\" size=\"40\" maxlength=\"255\" onkeydown=\"return handleKeyDown(event);\" title=\"".JText::_('COM_SEF_TT_FILTER_REAL')."\" />";
        
        $lists['filterSEFRE'] = JText::_('COM_SEF_USE_RE').'&nbsp;<input type="checkbox" style="float:none" ' . ((substr($this->filterSEF, 0, 4) == 'reg:') ? 'checked="checked"' : '') . ' onclick="useRE(this, document.adminForm.filterSEF);" />';
        $lists['filterRealRE'] = JText::_('COM_SEF_USE_RE').'&nbsp;<input type="checkbox" style="float:none" ' . ((substr($this->filterReal, 0, 4) == 'reg:') ? 'checked="checked"' : '') . ' onclick="useRE(this, document.adminForm.filterReal);" />';
        
        // Filter Indexed state
        $indexes[] = JHTML::_('select.option', 0, JText::_('COM_SEF_ALL'));
        $indexes[] = JHTML::_('select.option', 1, JText::_('COM_SEF_INDEXED'));
        $indexes[] = JHTML::_('select.option', 2, JText::_('COM_SEF_NOT_INDEXED'));
        $lists['filterIndexed'] = JHTML::_('select.genericlist', $indexes, 'filterIndexed', 'class="inputbox" onchange="document.adminForm.submit();" style="width: 120px;" size="1"', 'value', 'text', $this->filterIndexed);

        // Filter Frequency state
        $freqs[] = JHTML::_('select.option', '', JText::_('COM_SEF_ALL'));
        $freqs[] = JHTML::_('select.option', 'always', 'always');
        $freqs[] = JHTML::_('select.option', 'hourly', 'hourly');
        $freqs[] = JHTML::_('select.option', 'daily', 'daily');
        $freqs[] = JHTML::_('select.option', 'weekly', 'weekly');
        $freqs[] = JHTML::_('select.option', 'monthly', 'monthly');
        $freqs[] = JHTML::_('select.option', 'yearly', 'yearly');
        $freqs[] = JHTML::_('select.option', 'never', 'never');
        $lists['filterFrequency'] = JHTML::_('select.genericlist', $freqs, 'filterFrequency', 'class="inputbox" onchange="document.adminForm.submit();" style="width: 120px;" size="1"', 'value', 'text', $this->filterFrequency);

        // Filter Priority state
        $priorities[] = JHTML::_('select.option', '', JText::_('COM_SEF_ALL'));
        $priorities[] = JHTML::_('select.option', '0.0', '0.0');
        $priorities[] = JHTML::_('select.option', '0.1', '0.1');
        $priorities[] = JHTML::_('select.option', '0.2', '0.2');
        $priorities[] = JHTML::_('select.option', '0.3', '0.3');
        $priorities[] = JHTML::_('select.option', '0.4', '0.4');
        $priorities[] = JHTML::_('select.option', '0.5', '0.5');
        $priorities[] = JHTML::_('select.option', '0.6', '0.6');
        $priorities[] = JHTML::_('select.option', '0.7', '0.7');
        $priorities[] = JHTML::_('select.option', '0.8', '0.8');
        $priorities[] = JHTML::_('select.option', '0.9', '0.9');
        $priorities[] = JHTML::_('select.option', '1.0', '1.0');
        $lists['filterPriority'] = JHTML::_('select.genericlist', $priorities, 'filterPriority', 'class="inputbox" onchange="document.adminForm.submit();" style="width: 120px;" size="1"', 'value', 'text', $this->filterPriority);

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
        
        $lists['filterReset'] = '<input type="button" class="btn" value="'.JText::_('COM_SEF_RESET').'" onclick="resetFilters();" />';
        
        // Ordering
        $lists['filter_order'] = $this->filterOrder;
        $lists['filter_order_Dir'] = $this->filterOrderDir;

        // Selection
        $sel[] = JHTML::_('select.option', 'selected', JText::_('COM_SEF_ONLY_SELECTED'));
        $sel[] = JHTML::_('select.option', 'filtered', JText::_('COM_SEF_ALL_FILTERED'));
        $lists['selection'] = JHTML::_('select.genericlist', $sel, 'sef_selection', 'class="inputbox" size="1"');
        
        // Actions
        $acts[] = JHTML::_('select.option', 'index', JText::_('COM_SEF_INDEX'));
        $acts[] = JHTML::_('select.option', 'unindex', JText::_('COM_SEF_UNINDEX'));
        $acts[] = JHTML::_('select.option', 'indexpublished', JText::_('COM_SEF_INDEXED_FROM_PUBLISHED'));
        $acts[] = JHTML::_('select.option', 'setdate', JText::_('COM_SEF_SET_DATE'));
        $acts[] = JHTML::_('select.option', 'setfrequency', JText::_('COM_SEF_SET_FREQUENCY'));
        $acts[] = JHTML::_('select.option', 'setpriority', JText::_('COM_SEF_SET_PRIORITY'));
        $lists['actions'] = JHTML::_('select.genericlist', $acts, 'sef_actions', 'class="inputbox" size="1" onchange="showInput();"');
        
        $sefConfig =& SEFConfig::getConfig();
        $lists['newdate'] = '<div id="divdate" style="display: none">'.JHTML::calendar(date('Y-m-d'), 'tb_newdate', 'tb_newdate', '%Y-%m-%d', array('style' => 'width: 70px')).'</div>';
        $lists['newpriority'] = '<div id="divpriority" style="display: none">'.JHTML::_('select.genericlist', $priorities, 'tb_newpriority', 'class="inputbox" size="1"', 'value', 'text', $sefConfig->sitemap_priority).'</div>';
        $lists['newfrequency'] = '<div id="divfrequency" style="display: none">'.JHTML::_('select.genericlist', $freqs, 'tb_newfrequency', 'class="inputbox" size="1"', 'value', 'text', $sefConfig->sitemap_frequency).'</div>';

        return $lists;
    }

    function getPagination()
    {
        jimport('joomla.html.pagination');
        $pagination = new JPagination($this->getTotal(), $this->limitstart, $this->limit);

        return $pagination;
    }

    function store()
    {
        $ids = JRequest::getVar('id');
        $smindexed = JRequest::getVar('sm_indexed');
        $smdate = JRequest::getVar('sm_date');
        $smfrequency = JRequest::getVar('sm_frequency');
        $smpriority = JRequest::getVar('sm_priority');

        if (is_array($ids)) {
            foreach ($ids as $id) {
                if (!is_numeric($id)) {
                    continue;
                }

                $indexed = isset($smindexed[$id]) ? '1' : '0';
                $date = isset($smdate[$id]) ? $smdate[$id] : '0000-00-00';
                $frequency = isset($smfrequency[$id]) ? $smfrequency[$id] : 'never';
                $priority = isset($smpriority[$id]) ? $smpriority[$id] : '0.0';

                $query = "UPDATE `#__sefurls` SET `sm_indexed` = ".$this->_db->Quote($indexed).", `sm_date` = ".$this->_db->Quote($date).", `sm_frequency` = ".$this->_db->Quote($frequency).", `sm_priority` = ".$this->_db->Quote($priority)." WHERE `id` = '{$id}' LIMIT 1";
                $this->_db->setQuery($query);

                if (!$this->_db->query()) {
                    $this->setError($this->_db->getErrorMsg());
                    return false;
                }
            }
        }

        // Set the sitemap changed flag
        $sefConfig =& SEFConfig::getConfig();
        if (!$sefConfig->sitemap_changed) {
            $sefConfig->sitemap_changed = true;
            $sefConfig->saveConfig();
        }

        return true;
    }
    
    function _setState($state, $value, $where)
    {
        if (empty($where)) {
            return true;
        }
        
        $query = "UPDATE `#__sefurls` SET `{$state}` = '{$value}' $where";
        $this->_db->setQuery($query);
        if (!$this->_db->query()) {
            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }
        
        // Set the sitemap changed flag
        $sefConfig =& SEFConfig::getConfig();
        if (!$sefConfig->sitemap_changed) {
            $sefConfig->sitemap_changed = true;
            $sefConfig->saveConfig();
        }

        return true;
    }
    
    function setIndex($state, $where)
    {
        return $this->_setState('sm_indexed', $state, $where);
    }

    function setDate($state, $where)
    {
        return $this->_setState('sm_date', $state, $where);
    }

    function setFrequency($state, $where)
    {
        return $this->_setState('sm_frequency', $state, $where);
    }

    function setPriority($state, $where)
    {
        return $this->_setState('sm_priority', $state, $where);
    }

    function indexPublished($where = '')
    {
        $where2 = "`origurl` REGEXP 'option=com_content&.*view=article'";
        $where = trim($where);
        if ($where == '') {
            $where = 'WHERE '.$where2;
        }
        else {
            $where .= ' AND ('.$where2.')';
        }
        
        $db =& JFactory::getDBO();
        
        $query = "SELECT `id`, `origurl`, `sm_indexed` FROM `#__sefurls` ".$where;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        if (is_null($rows)) {
            return true;
        }
        
        $matches = array();
        $trueUpdates = array();
        $falseUpdates = array();
        $states = array();
        $now =& JFactory::getDate();
        $nullDate = $db->getNullDate();
        foreach ($rows as &$row) {
            // Get article ID
            preg_match('/&id=([^&]+)/', $row->origurl, $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $id = $matches[1];
            
            // Get article published state
            if (!isset($states[$id])) {
                $db->setQuery("SELECT `state`, `publish_down` FROM `#__content` WHERE `id` = '{$id}' LIMIT 1");
                $publish = $db->loadObject();
                
                if (is_null($publish)) {
                    $states[$id] = false;
                }
                else {
                    if (intval($publish->state) <= 0) {
                        $states[$id] = false;
                    }
                    else {
                        $to =& JFactory::getDate($publish->publish_down);
                                                
                        if ($now->toUnix() <= $to->toUnix() || $publish->publish_down == $nullDate) {
                            $states[$id] = true;
                        }
                        else {
                            $states[$id] = false;
                        }
                    }
                }
            }
            
            // Check the state change
            if (((bool)$row->sm_indexed) != $states[$id]) {
                // State changed
                if ($states[$id]) {
                    $trueUpdates[] = $row->id;
                }
                else {
                    $falseUpdates[] = $row->id;
                }
            }
        }
        
        $ret = true;
        $changed = false;
        
        // Execute the true updates
        if (count($trueUpdates) > 0) {
            $db->setQuery("UPDATE `#__sefurls` SET `sm_indexed` = '1' WHERE `id` IN (".implode(',', $trueUpdates).')');
            if (!$db->query()) {
                $ret = false;
            }
            $changed = true;
        }
        
        // Execute the false updates
        if (count($falseUpdates) > 0) {
            $db->setQuery("UPDATE `#__sefurls` SET `sm_indexed` = '0' WHERE `id` IN (".implode(',', $falseUpdates).')');
            if (!$db->query()) {
                $ret = false;
            }
            $changed = true;
        }
        
        // Set the sitemap changed flag
        if ($changed) {
            $sefConfig =& SEFConfig::getConfig();
            if (!$sefConfig->sitemap_changed) {
                $sefConfig->sitemap_changed = true;
                $sefConfig->saveConfig();
            }
        }
        
        return $ret;
    }
    
    private function prepareDomain($domain, $wwwHandle)
    {
        $sefConfig = SEFConfig::getConfig();
        
        // Remove scheme
        $pos = strpos($domain, '://');
        if ($pos !== false) {
            $domain = substr($domain, $pos + 3);
        }
        
        // Remove everything after host (also port if present)
        $pos = strpos($domain, '/');
        $pos2 = strpos($domain, ':');
        if ($pos === false || ($pos2 !== false && $pos2 < $pos)) {
            $pos = $pos2;
        }
        if ($pos !== false) {
            $domain = substr($domain, 0, $pos);
        }
        
        // Handle scheme
        if ($sefConfig->sitemap_ssl) {
            $domain = 'https://'.$domain;
        }
        else {
            $domain = 'http://'.$domain;
        }
        
        // Adjust domain according to www handling
        if ($wwwHandle) {
            if ($sefConfig->wwwHandling != _COM_SEF_WWW_NONE) {
                if ($sefConfig->wwwHandling == _COM_SEF_WWW_USE_WWW) {
                    if (strpos($domain, '://www.') === false) {
                        $domain = str_replace('://', '://www.', $domain);
                    }
                }
                else if ($sefConfig->wwwHandling == _COM_SEF_WWW_USE_NONWWW) {
                    $domain = str_replace('://www.', '://', $domain);
                }
            }
        }
        
        // Add base
        $domain .= JURI::root(true);
        
        // Add slash
        if(substr($domain, -1) != '/') {
            $domain .= '/';
        }
        
        return $domain;
    }
    
    protected function _createXmlFile($file)
    {
        // Check that the file is writable
        if (!file_exists($file)) {
            // Try to create the file
            $f = @fopen($file, 'w');
            if ($f === false) {
                $this->setError(JText::_('COM_SEF_ERROR_CREATE_XML'));
                return false;
            }
            fclose($f);

            // Chmod the file, so it is writable
            JPath::setPermissions($file, '0666');
        }
        if (!is_writable($file)) {
            $this->setError(JText::_('COM_SEF_ERROR_XML_NOT_WRITABLE'));
            return false;
        }
        
        return true;
    }
    
    function generateXml()
    {
        $sefConfig =& SEFConfig::getConfig();
        
        // Whether multiple sitemaps for different domains are used
        $multiple = ($sefConfig->langEnable && $sefConfig->langPlacementJoomla == _COM_SEF_LANG_DOMAIN && $sefConfig->multipleSitemaps);
        
        // Prepare file(s) and headers
        if (!$multiple) {
            $file = JPATH_ROOT.'/'.$sefConfig->sitemap_filename.'.xml';
            if (!$this->_createXmlFile($file)) {
                return false;
            }
            $text =
                '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
        }
        else {
            $langs = JLanguageHelper::getLanguages();
            $texts = array();
            $files = array();
            foreach ($langs as $lang) {
                if (isset($sefConfig->multipleSitemapsFilenames[$lang->sef])) {
                    $file = JPATH_ROOT.'/'.$sefConfig->multipleSitemapsFilenames[$lang->sef].'.xml';
                }
                else {
                    $file = JPATH_ROOT.'/sitemap_'.$lang->sef.'.xml';
                }
                if (!$this->_createXmlFile($file)) {
                    return false;
                }
                $files[$lang->sef] = $file;
                $texts[$lang->sef] = 
                    '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
            }
        }


        // Prepare default domain
        $domain = $this->prepareDomain(JURI::root(), true);
        
        // Prepare domains for languages
        $langDomains = array();
        if ($sefConfig->langEnable && $sefConfig->langPlacementJoomla == _COM_SEF_LANG_DOMAIN) {
            foreach ($sefConfig->subDomainsJoomla as $lang => $host) {
                $langDomains[$lang] = $this->prepareDomain($host, false);
            }
        }
        
        // Get number of URLs
        $query = "SELECT `sefurl`, `origurl`, `sm_date`, `sm_frequency`, `sm_priority` FROM `#__sefurls` WHERE `sm_indexed` = '1' AND `origurl` != '' ORDER BY `sefurl`";
        $total = $this->_getListCount($query);
        
        $batchSize = 100;
        $start = 0;

        // Process URLs in batches
        if ($total > 0) {
            while ($start < $total) {
                $urls = $this->_getList($query, $start, $batchSize);
                foreach ($urls as $url) {
                    $url->sefurl = str_replace('&', '&amp;', $url->sefurl);
                    
                    // Handle multilanguage domains
                    $urlDomain = $domain;
                    $urlLang = null;
                    if ($sefConfig->langEnable && $sefConfig->langPlacementJoomla == _COM_SEF_LANG_DOMAIN) {
                        $matches = array();
                        if (preg_match('/[?&]lang=([^&]+)/', $url->origurl, $matches)) {
                            $urlLang = $matches[1];
                            if (isset($langDomains[$urlLang])) {
                                $urlDomain = $langDomains[$urlLang];
                            }
                        }
                    }
                    
                    $urlText = "    <url>\n";
                    $urlText .= "        <loc>{$urlDomain}{$url->sefurl}</loc>\n";
                    if ($sefConfig->sitemap_show_date) {
                        if ($url->sm_date == '0000-00-00' || $url->sm_date == ''){
                            $url->sm_date = date('Y-m-d');
                        }
                        $urlText .= "        <lastmod>{$url->sm_date}</lastmod>\n";
                    }
                    if ($sefConfig->sitemap_show_frequency) {
                        if ($url->sm_frequency == '') {
                            $url->sm_frequency = $sefConfig->sitemap_frequency;
                        }
                        $urlText .= "        <changefreq>{$url->sm_frequency}</changefreq>\n";
                    }
                    if ($sefConfig->sitemap_show_priority) {
                        if ($url->sm_priority == '') {
                            $url->sm_priority = $sefConfig->sitemap_priority;
                        }
                        $urlText .= "        <priority>{$url->sm_priority}</priority>\n";
                    }
                    $urlText .= "    </url>\n";
                    
                    // Add to correct file
                    if ($multiple) {
                        if (!is_null($urlLang) && isset($texts[$urlLang])) {
                            $texts[$urlLang] .= $urlText;
                        }
                    }
                    else {
                        $text .= $urlText;
                    }
                }
            
                $start += $batchSize;
            }
        }

        // Write the file(s)
        if ($multiple) {
            foreach ($langs as $lang) {
                $texts[$lang->sef] .= '</urlset>';
                if (!JFile::write($files[$lang->sef], $texts[$lang->sef])) {
                    $this->setError(JText::_('COM_SEF_ERROR_SAVE_XML'));
                    return false;
                }
            }
        }
        else {
            $text .= '</urlset>';
            if (!JFile::write($file, $text)) {
                $this->setError(JText::_('COM_SEF_ERROR_SAVE_XML'));
                return false;
            }
        }

        // Unset the sitemap changed flag
        if ($sefConfig->sitemap_changed) {
            $sefConfig->sitemap_changed = false;
            $sefConfig->saveConfig();
        }

        // Ping search engines if set to
        if ($sefConfig->sitemap_pingauto) {
            $this->pingGoogle();
            //$this->pingYahoo();
            $this->pingBing();
        }
        
        return true;
    }
    
    protected function _pingUrl($urlPattern)
    {
        $sefConfig =& SEFConfig::getConfig();
        
        if ($sefConfig->langEnable && $sefConfig->langPlacementJoomla == _COM_SEF_LANG_DOMAIN && $sefConfig->multipleSitemaps) {
            // Multiple sitemaps
            $langs = JLanguageHelper::getLanguages();
            foreach ($langs as $lang) {
                $domain = $this->prepareDomain($sefConfig->subDomainsJoomla[$lang->sef], false);
                $file = $domain.$sefConfig->multipleSitemapsFilenames[$lang->sef].'.xml';
                $url = str_replace('%s', urlencode($file), $urlPattern);
                $response = SEFTools::PostRequest($url, null, null, 'get');
                
                if ($response->code != 200) {
                    return false;
                }
            }
        }
        else {
            $domain = $this->prepareDomain(JURI::root(), true);
            $file = $domain.$sefConfig->sitemap_filename.'.xml';
            $url = str_replace('%s', urlencode($file), $urlPattern);
            $response = SEFTools::PostRequest($url, null, null, 'get');
            
            if ($response->code != 200) {
                return false;
            }
        }
        
        return true;
    }

    function pingGoogle()
    {
        if (!$this->_pingUrl('http://www.google.com/webmasters/tools/ping?sitemap=%s')) {
            JError::raiseWarning(100, JText::_('COM_SEF_COULD_NOT_PING').' '.JText::_('COM_SEF_GOOGLE'));
            return false;
        }
        else {
            JError::raiseNotice(100, JText::_('COM_SEF_GOOGLE').' '.JText::_('COM_SEF_PINGED'));
            return true;
        }
    }

    function pingYahoo()
    {
        $sefConfig = SEFConfig::getConfig();
        $appid = trim($sefConfig->sitemap_yahooId);
        if ($appid == '') {
            JError::raiseWarning(100, JText::_('COM_SEF_YAHOO_ID_NOT_SET'));
            return false;
        }
        
        if (!$this->_pingUrl('http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid='.$appid.'&url=%s')) {
            JError::raiseWarning(100, JText::_('COM_SEF_COULD_NOT_PING').' '.JText::_('COM_SEF_YAHOO'));
            return false;
        }
        else {
            JError::raiseNotice(100, JText::_('COM_SEF_YAHOO').' '.JText::_('COM_SEF_PINGED'));
            return true;
        }
    }

    function pingBing()
    {
        if (!$this->_pingUrl('http://www.bing.com/webmaster/ping.aspx?siteMap=%s')) {
            JError::raiseWarning(100, JText::_('COM_SEF_COULD_NOT_PING').' '.JText::_('COM_SEF_BING'));
            return false;
        }
        else {
            JError::raiseNotice(100, JText::_('COM_SEF_BING').' '.JText::_('COM_SEF_PINGED'));
            return true;
        }
    }

    function pingServices()
    {
        $sefConfig =& SEFConfig::getConfig();

        if (!is_array($sefConfig->sitemap_services) || count($sefConfig->sitemap_services) == 0) {
            return;
        }
        
        // Get domain
        $domain = JURI::root();

        // Add slash after domain
        if (substr($domain, -1) != '/') {
            $domain .= '/';
        }

        $file = $domain.$sefConfig->sitemap_filename.'.xml';

        // Site name
        $config = &JFactory::getConfig();
        $sitename = $config->get('sitename');

		$data = "<?xml version=\"1.0\"?>\r\n".
				"  <methodCall>\r\n".
				"    <methodName>weblogUpdates.ping</methodName>\r\n".
				"    <params>\r\n".
				"      <param>\r\n".
				"        <value>$sitename</value>\r\n".
				"      </param>\r\n".
				"      <param>\r\n".
				"        <value>$file</value>\r\n".
				"      </param>\r\n".
				"    </params>\r\n".
				"  </methodCall>";
				
		// loop through services and try to ping them
		foreach ($sefConfig->sitemap_services as $service) {
		    $response = SEFTools::PostRequest($service, null, $data, 'post', 'Joomla! Ping/1.0');
		    
		    if ($response->code != 200) {
		        JError::raiseWarning(100, JText::_('COM_SEF_COULD_NOT_PING').' '.$service);
		        continue;
		    }
		    
		    // Parse the response
		    $xml = @simplexml_load_string($response->content);
		    
		    if ($xml === false) {
		        JError::raiseWarning(100, $service.' | '.JText::_('COM_SEF_COULD_NOT_PARSE_RESPONSE'));
		        continue;
		    }
		    
		    $m1 = $xml->params->param->value->struct->member[0];
		    $m2 = $xml->params->param->value->struct->member[1];
		    if (empty($m1) || empty($m2)) {
		        JError::raiseWarning(100, $service.' | '.JText::_('COM_SEF_COULD_NOT_PARSE_RESPONSE'));
		        continue;
		    }
		    
		    if (empty($m1->value) || empty($m2->value)) {
		        JError::raiseWarning(100, $service.' | '.JText::_('COM_SEF_COULD_NOT_PARSE_RESPONSE'));
		        continue;
		    }
		    
		    if (((string)($m1->name)) == 'flerror') {
		        $err = (int)($m1->value->boolean);
		        if (!empty($m2->value->string)) {
		            $msg = (string)($m2->value->string);
		        } else {
		          $msg = (string)($m2->value);
		        }
		    }
		    else {
		        $err = (int)($m2->value->boolean);
		        if (!empty($m2->value->string)) {
		            $msg = (string)($m1->value->string);
		        } else {
		          $msg = (string)($m1->value);
		        }
		    }
		    
		    JError::raiseNotice(100, $service.' | '.$err.' - '.$msg);
		}
    }
}
?>