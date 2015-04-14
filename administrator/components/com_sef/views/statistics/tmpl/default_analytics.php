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

?>
<script type="text/javascript">
function update_analytics() {
	$('globals_html').set('html','<img src="components/com_sef/assets/images/ajax-loader.gif" />');
	$('most_pages_html').set('html','<img src="components/com_sef/assets/images/ajax-loader.gif" />');
	$('most_referers_html').set('html','<img src="components/com_sef/assets/images/ajax-loader.gif" />');
	$('visits_html').set('html','<img src="components/com_sef/assets/images/ajax-loader.gif" />');
	$('sources_html').set('html','<img src="components/com_sef/assets/images/ajax-loader.gif" />');
	
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=get_global&tmpl=component',
		'method':'post',
		'data':'start_date='+$('start_date').value+'&end_date='+$('end_date').value+'&account_id='+$('account_id').options[$('account_id').selectedIndex].value,
		'onSuccess':function(json,text) {
			$('globals_html').set('html',json.html);
		}
	}).send();
	
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=get_most_pages&tmpl=component',
		'method':'post',
		'data':'start_date='+$('start_date').value+'&end_date='+$('end_date').value+'&account_id='+$('account_id').options[$('account_id').selectedIndex].value,
		'onSuccess':function(json,text) {
			$('most_pages_html').set('html',json.html);
		}
	}).send();
	
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=get_most_referers&tmpl=component',
		'method':'post',
		'data':'start_date='+$('start_date').value+'&end_date='+$('end_date').value+'&account_id='+$('account_id').options[$('account_id').selectedIndex].value,
		'onSuccess':function(json,text) {
			$('most_referers_html').set('html',json.html);
		}
	}).send();
	
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=get_visits&tmpl=component',
		'method':'post',
		'data':'start_date='+$('start_date').value+'&end_date='+$('end_date').value+'&account_id='+$('account_id').options[$('account_id').selectedIndex].value,
		'onSuccess':function(json,text) {
			$('visits_html').set('html',json.html);
		}
	}).send();
	
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=get_sources&tmpl=component',
		'method':'post',
		'data':'start_date='+$('start_date').value+'&end_date='+$('end_date').value+'&account_id='+$('account_id').options[$('account_id').selectedIndex].value,
		'onSuccess':function(json,text) {
			$('sources_html').set('html',json.html);
		}
	}).send();
}
</script>
<div>
	<?php echo JText::_('COM_SEF_ANALYTICS_ACCOUNT'); ?>:&nbsp;<?php echo $this->accounts; ?>
	<?php echo JText::_('COM_SEF_START_DATE').":&nbsp;".JHTML::_('calendar',JFactory::getDate((JFactory::getDate()->toUnix()-(60*60*24*7)))->format("Y-m-d"),'start_date','start_date', '%Y-%m-%d', 'class="fltlft"'); ;?>
	<?php echo Jtext::_('COM_SEF_END_DATE').":&nbsp;".JHTML::_('calendar',JFactory::getDate()->format("Y-m-d"),'end_date','end_date'); ?>
	<?php echo JHTML::_('link','javascript:void(0);',JText::_('COM_SEF_ANALYTICS_UPDATE'),'onclick="update_analytics();"'); ?>
</div>
<fieldset class="adminform" style="width:260px;float:left;">
	<legend><?php echo Jtext::_('COM_SEF_GLOBAL_INFO'); ?></legend>
	<div id="globals_html"><?php echo $this->globals_html; ?></div>
</fieldset>
<fieldset class="adminform" style="width:500px;float:left;">
	<legend><?php echo JText::_('COM_SEF_VISITS'); ?></legend>
	<div id="visits_html"><?php echo $this->visits_html; ?></div>
</fieldset>
<fieldset class="adminform" style="width:260px;float:left;">
<legend><?php echo Jtext::_('COM_SEF_VISIT_SOURCES'); ?></legend>
	<div id="sources_html"><?php echo $this->sources_html; ?></div>
</fieldset>
<fieldset class="adminform" style="clear:both">
<legend><?php echo JText::_('COM_SEF_MOST_PAGES'); ?></legend>
	<div id="most_pages_html"><?php echo $this->top_urls_html; ?></div>
</fieldset>
<fieldset class="adminform">
<legend><?php echo Jtext::_('COM_SEF_MOST_REFERERS'); ?></legend>
	<div id="most_referers_html"><?php echo $this->top_referers_html; ?></div>
</fieldset>