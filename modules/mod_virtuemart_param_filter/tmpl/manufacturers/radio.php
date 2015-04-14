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

$html .= '<div class="filter_manufacturers">';
if(!empty($mcf_manufacturers_heading)){
	$reset = !empty($mids) ? '<a class="reset" href="#">[x]</a>' : '';
	$html .= '<div class="heading">'.$mcf_manufacturers_heading.$reset.'</div>';
}
if(count($manufacturers) > 0){
	$html .= '<ul class="values" data-id="m">';
	foreach($manufacturers as $v){
		$checked = isset($mids) && in_array($v->virtuemart_manufacturer_id,$mids)? ' checked="checked"' : '';
		
		/* ----- + Count calculate ----- */
		$v->count = isset($manufacturers_count[$v->virtuemart_manufacturer_id]->count) ? $manufacturers_count[$v->virtuemart_manufacturer_id]->count : 0;
		$count_txt = $count_show ? '</span><span class="count"> ['.$v->count.']' : '';
		$disabled = $v->count == 0 ? $count_zero_show_txt : '';
		$disable_css = $v->count == 0 ? ' '.$count_zero_show : '';
		/* ----- - Count calculate ----- */
		if($count_zero_show != 'hidden' || $v->count > 0){
			$html .= '<li><label class="filter"><input type="radio" name="mids[]" value="'.$v->virtuemart_manufacturer_id.'"'.$checked.' /><span>'.$v->mf_name.$count_txt.'</span></label></li>';
		}
	}
	$html .= '</ul>';
}
$html .= '</div>';