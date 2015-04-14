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

$sefConfig =& SEFConfig::getConfig();
?>

	<script language="javascript">
	<!--
	Joomla.submitbutton = function(pressbutton)
	{
	    var form = document.adminForm;
	    if (pressbutton == 'cancel') {
	        Joomla.submitform( pressbutton );
	        return;
	    }
	    // do field validation
	    if (form.word.value == "") {
	        alert( "<?php echo JText::_('COM_SEF_ERROR_EMPTY_WORD'); ?>" );
	    } else {
	        // Build the URLs array
	        var list = form.urls;
	        var urlsArray = '';
	        for (var i = 0, n = list.length; i < n; i++) {
	            if (i > 0) {
	                urlsArray += "\n";
	            }
	            urlsArray += list.options[i].value;
	        }
	        form.urlsArray.value = urlsArray;

	        Joomla.submitform( pressbutton );
	    }
	}

	function addUrl()
	{
	    var url = document.adminForm.txtUrl.value;
	    var id = document.adminForm.urlid.value;
	    var list = document.adminForm.urls;

	    // Check word length
	    if (url.length == 0) {
	        return;
	    }
	    
	    // Check ID presence
	    if (id == '') {
	        return;
	    }

	    // Try to find the URL in list (do not allow duplicities)
	    for (var i = 0, n = list.length; i < n; i++) {
	        if (list.options[i].text == url) {
	            // Found it
	            return;
	        }
	    }

	    // Add the URL
	    var newOpt;
	    newOpt = new Option(url, id);

	    try {
	        list.add(newOpt); // IE, Opera
	    }
	    catch(e) {
	        list.add(newOpt, null); // FF
	    }
	}

	function removeUrls()
	{
	    var list = document.adminForm.urls;

	    for (var i = list.length - 1; i >= 0; i--) {
	        if (list.options[i].selected) {
	            list.remove(i);
	        }
	    }
	}
	//-->
	</script>
	<ul id="autocomplete" style="display: none;"><li>dummy</li></ul>
	
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	   <legend><?php echo JText::_('COM_SEF_WORD'); ?></legend>
	   <table class="adminlist table table-striped">
	       <tr>
	           <th width="150"><?php echo JText::_('COM_SEF_WORD'); ?></td>
	           <td colspan="2"><?php echo $this->lists['word']; ?></td>
	       </tr>
	       <tr>
	           <th valign="top"><?php echo JText::_('COM_SEF_LINKED_URLS'); ?></td>
	           <td colspan="2">
    	           <?php echo $this->lists['urls']; ?>
    	           <div class="clr"></div>
    	           <input type="button" class="btn" value="<?php echo JText::_('COM_SEF_REMOVE_SELECTED'); ?>" onclick="removeUrls();" />
	           </td>
	       </tr>
	       <tr>
	           <th><?php echo JText::_('COM_SEF_ADD_URL'); ?></td>
	           <td>
    	           <input type="text" autocomplete="off" name="txtUrl" id="txtUrl" size="60" style="width: 500px;" onblur="hideAutoComplete();" onkeydown="handleKey(event, addUrl);" onkeyup="showAutoComplete(this, event, 'urlid', 'ajax', 'findUrls');" />
    	           <input type="button" class="btn" value="<?php echo JText::_('COM_SEF_ADD_URL'); ?>" onclick="addUrl();" />
	           </td>
	       </tr>
	   </table>
	   <input type="hidden" name="urlid" id="urlid" value="" />
	</fieldset>
	
<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="words" />
<input type="hidden" name="id" value="<?php echo $this->word->id; ?>" />
<input type="hidden" name="urlsArray" value="" />

</form>