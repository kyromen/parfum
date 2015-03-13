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

$plr = JRequest::getVar('plr',null);
$reset = !empty($plr) ? '<a class="reset" href="#">[x]</a>' : '';
$prices_array = array(
	"0-1000" => " до 1000 руб.",
	"1000-3000" => "от 1000 до 3000 руб.",
	"3000" => "от 3000 руб."
);
		
$html .= '<div class="price">';
if(!empty($mcf_price_heading))
	$html .= '<div class="heading">'.$mcf_price_heading.$reset.'</div>';
$html .= '<div class="values" data-id="p"><select name="plr">';
$html .= '<option value="">'.$mcf_price_select_heading.'</option>';
foreach($prices_array as $k => $v){
	$selected = $plr==$k ? ' selected="selected"' : '';
	$html .= '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
}
$html .= '</select></div></div>';