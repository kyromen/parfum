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

JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');
?>
<style type="text/css">
div.current input, div.current textarea, div.current select {
	float:none !important;
}

#globals_html table tr td:last-child {
	text-align:right;
}
</style>

<script type="text/javascript">
<!--
function handleKeyDown(e)
{
    var code;
    code = e.keyCode;
    
    if (code == 13) {
        // Enter pressed
        document.adminForm.submit();
        return false;
    }
    
    return true;
}

function resetFilters()
{
    document.adminForm.filterSEF.value = '';
    document.adminForm.filterReal.value = '';
    document.adminForm.comFilter.value = '';
    if (document.adminForm.filterLang) {
        document.adminForm.filterLang.value = '';
    }
    
    document.adminForm.submit();
}
-->
</script>
<?php
echo JHTML::_('tabs.start','stats-panel',array('useCookie'=>1));

echo JHTML::_('tabs.panel',JText::_('COM_SEF_STATISTICS'),'stats-statistics');
echo $this->loadTemplate('statistics');

echo JHTML::_('tabs.panel',JText::_('COM_SEF_GOOGLE_ANALYTICS'),'stats-analytics');
if($this->accounts==false) {
	$link1 = '<a href="index.php?option=com_sef&controller=config&task=edit&tab=analytics">';
    $link2 = '</a>';
    echo JText::sprintf('COM_SEF_FAILED_TO_OBTAIN_DATA', $link1, $link2);
} else {
	echo $this->loadTemplate('analytics');
}

echo JHTML::_('tabs.end');
?>