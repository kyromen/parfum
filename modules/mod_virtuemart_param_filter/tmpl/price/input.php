<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param Filter: Virtuemart 2 search module
* Version: 2.0.6 (2013.08.13)
* Author: Usov Dima
* Copyright: Copyright (C) 2012-2013 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

$selected_values = array(JRequest::getVar('pl',''),JRequest::getVar('pr',''));
$selected_values = array_diff($selected_values,array(''));
$reset = !empty($selected_values) ? '<a class="reset" href="#">[x]</a>' : '';
$html .= '<div class="price">';
if(!empty($mcf_price_heading))
	$html .= '<div class="heading">'.$mcf_price_heading.$reset.'</div>';
$html .= '<div class="values" data-id="p"><input type="text" name="pl" value="'.$price_left.'" size="4" /> - <input type="text" name="pr" value="'.$price_right.'" size="4" /></div></div>';