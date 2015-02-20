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

$html .= '<div class="filter_category">';
if($mcf_category_heading)
	$html .= '<div class="heading">'.$mcf_category_heading.'</div>';
$html .= '<div class="values" data-id="c"><select name="cids[]"><option value="">'.$mcf_category_select_heading.'</option>'.recursiveList($categories,$cids,$parent_category_id,0,'select').'</select></div>';
$html .= '</div>';