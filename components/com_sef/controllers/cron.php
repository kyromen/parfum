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

// no direct access
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/classes/config.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/classes/seftools.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/controller.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/controllers/crawler.php');

class JoomSEFControllerCron extends SEFController
{
    function display()
    {
        $this->setRedirect(JURI::root());
    }
    
    function run()
    {
        $sefConfig =& SEFConfig::getConfig();
        
        // Check enabled
        if (!$sefConfig->cronEnabled) {
            $this->setRedirect(JURI::root());
            return;
        }
        
        // Check remote server
        if ($sefConfig->cronOnlyLocal) {
            if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
                $this->setRedirect(JURI::root());
                return;
            }
        }
        
        // Check key
        $key = JRequest::getVar('key');
        if ($sefConfig->cronKey != $key) {
            $this->setRedirect(JURI::root());
            return;
        }
        
        // Check tasks
        $tasks = JRequest::getVar('tasks', 0, 'default', 'int');
        if ($tasks == 0) {
            $this->setRedirect(JURI::root());
            return;
        }
        
        // Remove time limit
        set_time_limit(0);
        
        // Run tasks
        if (($tasks & COM_SEF_CRON_UPDATE_URLS) > 0) {
            // Update URLs
            $this->_updateUrls();
            echo "<br/>\n";
        }
        else {
            if (($tasks & COM_SEF_CRON_UPDATE_META) > 0) {
                // Update meta tags
                $this->_updateMetas();
                echo "<br/>\n";
            }
            if (($tasks & COM_SEF_CRON_UPDATE_SITEMAP) > 0) {
                // Update sitemap
                $this->_updateSitemap();
                echo "<br/>\n";
            }
        }
        if (($tasks & COM_SEF_CRON_UPDATE_SITEMAP_XML) > 0) {
            // Update sitemap XML
            $this->_updateSitemapXml();
            echo "<br/>\n";
        }
        if (($tasks & COM_SEF_CRON_CRAWL_WEB) > 0) {
            // Crawl website
            $this->_crawlWeb();
            echo "<br/>\n";
        }
    }
    
    function _prepareUpdate()
    {
        $db = JFactory::getDBO();
        $db->setQuery("UPDATE `#__sefurls` SET `flag` = '1' WHERE `dateadd` = '0000-00-00' AND `locked` = '0'");
        if (!$db->query()) {
            return false;
        }
        return true;
    }
    
    function _updateUrls()
    {
        echo "Running task: Update URLs<br/>\n";
        flush();
        
        if (!$this->_prepareUpdate()) {
            echo "ERROR: Could not prepare update<br/>\n";
            flush();
            return;
        }
        
        // Start update
        $count = 0;
        do
        {
            $response = SEFTools::PostRequest(JURI::root().'index.php?option=com_sef&task=updateNext&format=json', null, null, 'get');
            if (($response === false) || ($response->code != 200)) {
                // Error
                echo "ERROR: Problem encountered when updating URLs<br/>\n";
                flush();
                return;
            }
            
            $ret = json_decode($response->content);
            $type = $ret->type;
            if ($type != 'error') {
                $count += $ret->updated;
            }
        } while ($type == 'updatestep');
        
        if ($type == 'error') {
            echo "ERROR: Problem encountered when updating URLs<br/>\n";
            flush();
            return;
        }
        
        // Success
        echo "SUCCESS: {$count} URLs updated successfully<br/>\n";
        flush();
    }
    
    function _updateMetas()
    {
        echo "Running task: Update Meta tags<br/>\n";
        flush();
        
        if (!$this->_prepareUpdate()) {
            echo "ERROR: Could not prepare update<br/>\n";
            flush();
            return;
        }
        
        // Start update
        $count = 0;
        do
        {
            $response = SEFTools::PostRequest(JURI::root().'index.php?option=com_sef&task=updateMetaNext&format=json', null, null, 'get');
            if (($response === false) || ($response->code != 200)) {
                // Error
                echo "ERROR: Problem encountered when updating meta tags<br/>\n";
                flush();
                return;
            }
            
            $ret = json_decode($response->content);
            $type = $ret->type;
            if ($type != 'error') {
                $count += $ret->updated;
            }
        } while ($type == 'updatestep');
        
        if ($type == 'error') {
            echo "ERROR: Problem encountered when updating meta tags<br/>\n";
            flush();
            return;
        }
        
        // Success
        echo "SUCCESS: Meta tags for {$count} URLs updated successfully<br/>\n";
        flush();
    }
    
    function _updateSitemap()
    {
        echo "Running task: Update Sitemap<br/>\n";
        flush();
        
        if (!$this->_prepareUpdate()) {
            echo "ERROR: Could not prepare update<br/>\n";
            flush();
            return;
        }
        
        // Start update
        $count = 0;
        do
        {
            $response = SEFTools::PostRequest(JURI::root().'index.php?option=com_sef&task=updateSitemapNext&format=json', null, null, 'get');
            if (($response === false) || ($response->code != 200)) {
                // Error
                echo "ERROR: Problem encountered when updating sitemap<br/>\n";
                flush();
                return;
            }
            
            $ret = json_decode($response->content);
            $type = $ret->type;
            if ($type != 'error') {
                $count += $ret->updated;
            }
        } while ($type == 'updatestep');
        
        if ($type == 'error') {
            echo "ERROR: Problem encountered when updating sitemap<br/>\n";
            flush();
            return;
        }
        
        // Success
        echo "SUCCESS: Sitemap setting for {$count} URLs updated successfully<br/>\n";
        flush();
    }
    
    function _updateSitemapXml()
    {
        $this->addModelPath(JPATH_ADMINISTRATOR.'/components/com_sef/models');
        $model = $this->getModel('SiteMap', 'SEFModel');
        
        if (!$model->generateXml()) {
            echo "ERROR: Could not update Sitemap XML file: ".$model->getError()."<br/>\n";
        }
        else {
            echo "SUCCESS: Sitemap XML file updated<br/>\n";
        }
        flush();
    }
    
    function _crawlWeb()
    {
        echo "Running task: Crawl Website<br/>\n";
        flush();
        
        // Initialize
        $maxLevel = JRequest::getVar('maxlevel', 5, 'default', 'int');
        $level = 0;
        $currentUrls = array(JURI::root());
        $crawledUrls = array();
        $crawler = new SEFControllerCrawler();
        
        // Crawl levels
        while ($level <= $maxLevel)
        {
            echo "Crawling level {$level}<br/>\n";
            flush();
            
            // Crawl current level URLs
            $nextUrls = array();
            foreach ($currentUrls as $url)
            {
                // Skip already crawled URLs
                if (!in_array($url, $crawledUrls)) {
                    $crawler->_parseLinks($url, $nextUrls);
                    $crawledUrls[] = $url;
                }
            }
            
            // Set next URLs as current
            $currentUrls = $nextUrls;
            
            // Increase level
            $level++;
        }
        
        $count = count($crawledUrls);
        echo "SUCCESS: {$count} URLs crawled successfully<br/>\n";
        flush();
    }
}