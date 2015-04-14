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

class SEFModelCron extends SEFModel
{
    function __construct()
    {
        parent::__construct();
    }

    function getLists()
    {
        $sefConfig = SEFConfig::getConfig();
        
        $std_opt = 'class="inputbox" size="2"';
        $lists['cronEnabled']   = $this->booleanRadio('cronEnabled',    $std_opt, $sefConfig->cronEnabled);
        $lists['cronOnlyLocal'] = $this->booleanRadio('cronOnlyLocal',  $std_opt, $sefConfig->cronOnlyLocal);
        
        $cronKey = $sefConfig->cronKey;
        if (empty($cronKey)) {
            // Generate default random cron key
            $src = time() . rand();
            $cronKey = sha1($src);
        }
        $lists['cronKey']       = '<input type="text" name="cronKey" size="50" class="inputbox" value="'.$cronKey.'" />';
        
        $std_opt .= ' onclick="jsCronUpdateFields();"';
        $lists['cronUpdateUrls'] = $this->booleanRadio('cronUpdateUrls',  $std_opt, false);
        $lists['cronUpdateMeta'] = $this->booleanRadio('cronUpdateMeta',  $std_opt, false);
        $lists['cronUpdateSitemap'] = $this->booleanRadio('cronUpdateSitemap',  $std_opt, false);
        $lists['cronUpdateSitemapXml'] = $this->booleanRadio('cronUpdateSitemapXml',  $std_opt, false);
        $lists['cronCrawlWeb'] = $this->booleanRadio('cronCrawlWeb',  $std_opt, false);
        
        // Max crawl level list
        $opts = array();
        for ($i = 1; $i <= 10; $i++) {
            $opts[] = JHTML::_('select.option', $i, $i);
        }
        $lists['cronCrawlMaxLevel'] = JHTML::_('select.genericlist', $opts, 'cronCrawlMaxLevel', 'class="classic" size="1" disabled="disabled"', 'value', 'text', 5);
        
        // Period list
        $periods = array();
        $periods[] = JHTML::_('select.option', COM_SEF_CRON_PERIOD_HOURLY, JText::_('COM_SEF_CRON_PERIOD_HOURLY'));
        $periods[] = JHTML::_('select.option', COM_SEF_CRON_PERIOD_DAILY, JText::_('COM_SEF_CRON_PERIOD_DAILY'));
        $periods[] = JHTML::_('select.option', COM_SEF_CRON_PERIOD_WEEKLY, JText::_('COM_SEF_CRON_PERIOD_WEEKLY'));
        $periods[] = JHTML::_('select.option', COM_SEF_CRON_PERIOD_MONTHLY, JText::_('COM_SEF_CRON_PERIOD_MONTHLY'));
        $periods[] = JHTML::_('select.option', COM_SEF_CRON_PERIOD_YEARLY, JText::_('COM_SEF_CRON_PERIOD_YEARLY'));
        $lists['cronPeriod'] = JHTML::_('select.genericlist', $periods, 'cronPeriod', 'class="classic" size="1"', 'value', 'text', COM_SEF_CRON_PERIOD_MONTHLY);

        return $lists;
    }
    
    function store()
    {
        $sefConfig =& SEFConfig::getConfig();
        
        $sefConfig->cronEnabled = JRequest::getVar('cronEnabled', false, 'post');
        $sefConfig->cronOnlyLocal = JRequest::getVar('cronOnlyLocal', true, 'post');
        $sefConfig->cronKey = JRequest::getVar('cronKey', '', 'post');
        
        return $sefConfig->saveConfig();
    }
    
    function generateFile()
    {
        // First save config
        if (!$this->store()) {
            return false;
        }
        
        $sefConfig =& SEFConfig::getConfig();
        
        // Build cron job line and comments
        $cronjob = '';
        $comment = "# ARTIO JoomSEF cron tasks file\n";
        $comment .= "# This script will be run: ";
                
        // Select correct period configuration
        $period = JRequest::getVar('cronPeriod', COM_SEF_CRON_PERIOD_MONTHLY, 'post');
        switch ($period) {
            case COM_SEF_CRON_PERIOD_HOURLY:
                $comment .= JText::_('COM_SEF_CRON_PERIOD_HOURLY_TEXT');
                $cronjob .= '0 * * * *';
                break;
            case COM_SEF_CRON_PERIOD_DAILY:
                $comment .= JText::_('COM_SEF_CRON_PERIOD_DAILY_TEXT');
                $cronjob .= '0 0 * * *';
                break;
            case COM_SEF_CRON_PERIOD_WEEKLY:
                $comment .= JText::_('COM_SEF_CRON_PERIOD_WEEKLY_TEXT');
                $cronjob .= '0 0 * * 0';
                break;
            case COM_SEF_CRON_PERIOD_MONTHLY:
                $comment .= JText::_('COM_SEF_CRON_PERIOD_MONTHLY_TEXT');
                $cronjob .= '0 0 1 * *';
                break;
            case COM_SEF_CRON_PERIOD_YEARLY:
                $comment .= JText::_('COM_SEF_CRON_PERIOD_YEARLY_TEXT');
                $cronjob .= '0 0 1 1 *';
                break;
        }
        
        // Select tasks to run
        $crontasks = 0;
        $comment .= "\n# Tasks that will be run:\n";
        
        $urls = JRequest::getVar('cronUpdateUrls', false, 'post', 'bool');
        $meta = JRequest::getVar('cronUpdateMeta', false, 'post', 'bool');
        $sitemap = JRequest::getVar('cronUpdateSitemap', false, 'post', 'bool');
        $sitemapXml = JRequest::getVar('cronUpdateSitemapXml', false, 'post', 'bool');
        $crawl = JRequest::getVar('cronCrawlWeb', false, 'post', 'bool');
        $crawlLevel = JRequest::getVar('cronCrawlMaxLevel', 5, 'post', 'int');
        
        if ($urls) {
            $crontasks |= COM_SEF_CRON_UPDATE_URLS;
            $comment .= "#   Update URLs\n";
        }
        else {
            if ($meta) {
                $crontasks |= COM_SEF_CRON_UPDATE_META;
                $comment .= "#   Update Meta Tags\n";
            }
            if ($sitemap) {
                $crontasks |= COM_SEF_CRON_UPDATE_SITEMAP;
                $comment .= "#   Update Sitemap\n";
            }
        }
        if ($crawl) {
            $crontasks |= COM_SEF_CRON_CRAWL_WEB;
            $comment .= "#   Crawl Website (max level: " . $crawlLevel . ")\n";
        }
        if ($sitemapXml) {
            $crontasks |= COM_SEF_CRON_UPDATE_SITEMAP_XML;
            $comment .= "#   Update Sitemap XML File\n";
        }
        
        if ($crontasks == 0) {
            return false;
        }
        
        // Build options
        $opts = 'tasks='.$crontasks.'&key='.urlencode($sefConfig->cronKey);
        if ($crawl) {
            $opts .= '&maxlevel='.$crawlLevel;
        }
        
        // Build job
        $cronjob .= ' wget -q "'.JURI::root().'index.php?option=com_sef&controller=cron&task=run&format=raw&'.$opts.'" -O /dev/null';
        $file = $comment . $cronjob;
        
        return $file;
    }
}