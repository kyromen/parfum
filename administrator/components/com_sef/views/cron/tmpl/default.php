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
defined('_JEXEC') or die('Restricted access');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="sef-width-100">
<?php $this->showInfoText('COM_SEF_INFOTEXT_CRON', true); ?>

<fieldset class="adminform">
    <legend><?php echo JText::_('COM_SEF_CRON_CONFIG'); ?></legend>
    
    <table class="admintable">
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_ENABLE_TT'), JText::_('COM_SEF_CRON_ENABLE'), '', JText::_('COM_SEF_CRON_ENABLE')); ?>:</td>
            <td><?php echo $this->lists['cronEnabled']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_LOCALONLY_TT'), JText::_('COM_SEF_CRON_LOCALONLY'), '', JText::_('COM_SEF_CRON_LOCALONLY')); ?>:</td>
            <td><?php echo $this->lists['cronOnlyLocal']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_KEY_TT'), JText::_('COM_SEF_CRON_KEY'), '', JText::_('COM_SEF_CRON_KEY')); ?>:</td>
            <td><?php echo $this->lists['cronKey']; ?></td>
        </tr>
    </table>
</fieldset>

<fieldset class="adminform">
    <legend><?php echo JText::_('COM_SEF_CRON_GENERATE_FILE'); ?></legend>
    
    <table class="admintable">
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_UPDATE_URLS_TT'), JText::_('COM_SEF_UPDATE_URLS'), '', JText::_('COM_SEF_UPDATE_URLS')); ?>:</td>
            <td><?php echo $this->lists['cronUpdateUrls']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_UPDATE_META_TAGS_TT'), JText::_('COM_SEF_UPDATE_META_TAGS'), '', JText::_('COM_SEF_UPDATE_META_TAGS')); ?>:</td>
            <td><?php echo $this->lists['cronUpdateMeta']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_UPDATE_SITEMAP_TT'), JText::_('COM_SEF_UPDATE_SITEMAP'), '', JText::_('COM_SEF_UPDATE_SITEMAP')); ?>:</td>
            <td><?php echo $this->lists['cronUpdateSitemap']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_UPDATE_XML_TT'), JText::_('COM_SEF_CRON_UPDATE_XML'), '', JText::_('COM_SEF_CRON_UPDATE_XML')); ?>:</td>
            <td><?php echo $this->lists['cronUpdateSitemapXml']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_CRAWL_WEB_TT'), JText::_('COM_SEF_CRAWL_WEB'), '', JText::_('COM_SEF_CRAWL_WEB')); ?>:</td>
            <td><?php echo $this->lists['cronCrawlWeb']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRAWLER_MAXLEVEL_TT'), JText::_('COM_SEF_CRAWLER_MAXLEVEL'), '', JText::_('COM_SEF_CRAWLER_MAXLEVEL')); ?>:</td>
            <td><?php echo $this->lists['cronCrawlMaxLevel']; ?></td>
        </tr>
        <tr>
            <td class="key"><?php echo JHTML::_('tooltip', JText::_('COM_SEF_CRON_PERIOD_TT'), JText::_('COM_SEF_CRON_PERIOD'), '', JText::_('COM_SEF_CRON_PERIOD')); ?>:</td>
            <td><?php echo $this->lists['cronPeriod']; ?></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><input type="button" id="cronGenerateButton" name="cronGenerateButton" class="classic btn btn-primary" onclick="jsCronButtonClicked();" disabled="disabled" value="<?php echo JText::_('COM_SEF_CRON_GENERATE'); ?>" /></td>
        </tr>
    </table>
</fieldset>
</div>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="controller" value="cron" />
<input type="hidden" name="task" value="" />
</form>