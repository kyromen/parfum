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
function stripslashes (str) {
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +      fixed by: Mick@el
    // +   improved by: marrtins
    // +   bugfixed by: Onno Marsman
    // +   improved by: rezna
    // +   input by: Rick Waldron
    // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +   input by: Brant Messenger (http://www.brantmessenger.com/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: stripslashes('Kevin\'s code');
    // *     returns 1: "Kevin's code"
    // *     example 2: stripslashes('Kevin\\\'s code');
    // *     returns 2: "Kevin\'s code"
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
        case '\\':
            return '\\';
        case '0':
            return '\u0000';
        case '':
            return '';
        default:
            return n1;
        }
    });
}

var left=<?php echo $this->total; ?>;
var updated=0;
window.addEvent('domready',function() {
	alert('<?php echo JText::_('COM_SEF_NO_INTERRUPT_VALIDATION'); ?>');
	processUpdate();
});

function processUpdate() {
	new Request.JSON({
		'url':'index.php?option=com_sef&controller=statistics&task=process_validation',
		'onSuccess':function(json,text) {
			if(json.type=="step") {
				left-=json.cnt;
				updated+=json.cnt;
				$('validated_cnt').set('text',updated);
				$('validated_left').set('text',left);
				if(left>0) {
					processUpdate();
				} else {
					alert('<?php echo JText::_('COM_SEF_VALIDATION_COMPLETE'); ?>');
					$('validation_finished').disabled=false;
				}
			} else if(json.type=="error") {
				alert(stripslashes(json.message));
			}
		}
	}).send();
}

function redirect() {
	window.location.href="index.php?option=com_sef&view=statistics";
}
</script>
<table class="adminform table">
	<tr>
		<td width="100"><?php echo JText::_('COM_SEF_URLS_VALIDATED'); ?></td>
		<td id="validated_cnt">0</td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_URLS_LEFT'); ?></td>
		<td id="validated_left"><?php echo $this->total; ?></td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_URLS_TOTAL'); ?></td>
		<td><?php echo $this->total; ?></td>
	</tr>
	<tr>
		<td colspan="2">
		<input class="button btn btn-primary" type="button" onclick="redirect();" disabled="disabled" value="<?php echo JText::_('COM_SEF_VALIDATION_FINISHED'); ?>" id="validation_finished" />
		</td>
	</tr>
</table>