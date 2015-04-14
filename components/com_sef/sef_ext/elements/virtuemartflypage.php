<?php 
/**
 * VirtueMart SEF extension for ARTIO JoomSEF
 * 
 * @package   JoomSEF
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2014 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementVirtuemartFlypage extends JElement
{
	var $_name = 'VirtuemartFlypage';
	
	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mosConfig_absolute_path;
		if (empty($mosConfig_absolute_path)) {
			$mosConfig_absolute_path = JPATH_ROOT;
		}
        
        $fieldName	= $control_name.'['.$name.']';
        $default = '<select class="inputbox" name="'.$fieldName.'" size="1"><option value="flypage.tpl">flypage.tpl</option></select>';
		
		if (!class_exists('ps_html')) {
            $file = JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/ps_html.php';
            if (!is_file($file)) {
                return $default;
            }
            include($file);
		}
		if (!function_exists('vmReadDirectory')) {
            $file = JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/ps_main.php';
            if (!is_file($file)) {
                return $default;
            }
			include($file);
		}
		if (!function_exists('shopMakeHtmlSafe')) {
			$file = JPATH_ADMINISTRATOR.'/components/com_virtuemart/classes/htmlTools.class.php';
            if (!is_file($file)) {
                return $default;
            }
            include($file);
		}
		
		return ps_html::list_template_files($fieldName, 'product_details', $value);
	}
}
?>