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
<script language="javascript">
<!--
function isValidURL(url)
{ 
    var RegExp = /^(http|https|ftp):\/\/(([\d\w]|%[a-fA-F\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;
    if( RegExp.test(url) ) {
        return true;
    } else {
        return false;
    }
}

Joomla.submitbutton = function(pressbutton)
{
    var form = document.adminForm;
    if (pressbutton == 'cancel') {
        Joomla.submitform(pressbutton);
        return;
    }
    
    // do field validation
    if( form.from.value == '' || form.to.value == '' ) {
        alert('<?php echo JText::_('COM_SEF_ERROR_EMPTY_REDIRECT_FIELDS'); ?>');
        return;
    }
    if( form.from.value[0] != '/' ) {
        alert('<?php echo JText::_('COM_SEF_ERROR_REDIRECT_FROM'); ?>');
        return;
    }
    if( (form.to.value[0] != '/') && !isValidURL(form.to.value) ) {
        alert('<?php echo JText::_('COM_SEF_ERROR_REDIRECT_TO'); ?>');
        return;
    }
    
    Joomla.submitform(pressbutton);
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<table class="adminform table table-striped">
    <tr><th colspan="2"><?php echo JText::_('COM_SEF_REDIRECT'); ?></th></tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_REDIRECT_FROM'); ?></td>
		<td><input class="inputbox" type="text" size="100" name="from" value="<?php echo $this->redirect->from; ?>">
		<?php echo JHTML::_('tooltip', JText::_('COM_SEF_TT_HTACCESS_FROM')); ?>
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_REDIRECT_TO');?></td>
		<td align="left"><input class="inputbox" type="text" size="100" name="to" value="<?php echo $this->redirect->to; ?>">
		<?php echo JHTML::_('tooltip', JText::_('COM_SEF_TT_HTACCESS_TO'));?>
		</td>
	</tr>
</table>

<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="htaccess" />
<input type="hidden" name="id" value="<?php echo $this->redirect->id; ?>" />
</form>