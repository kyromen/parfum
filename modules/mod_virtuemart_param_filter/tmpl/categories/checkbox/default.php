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
if(!empty($mcf_category_heading))
	$html .= '<div class="heading">'.$mcf_category_heading.'</div>';
$html .= '<ul class="values" data-id="c">'.recursiveList($categories,$cids,$parent_category_id,0,'checkbox').'</ul>';
$html .= '</div>';