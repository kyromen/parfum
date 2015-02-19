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

function recursiveListmultiselect-chosen($categories,$active_categories=array(),$parent_category_id,$depth,$tmpl){
	$html = '';
	foreach($categories as $v){
		if($v->category_parent_id == $parent_category_id){
			$v->depth = $depth + 1;
			$selected = (isset($active_categories) && in_array($v->virtuemart_category_id,$active_categories)) ? ' selected="selected"' : '';
			$html .= '<option value="'.$v->virtuemart_category_id.'"'.$selected.'>';
			if($v->depth > 1){
				$html .= str_repeat('-',$v->depth - 1).'&nbsp;';
			}
			$html .= $v->category_name.'</option>';
			$child = recursiveListmultiselect-chosen($categories,$active_categories,$v->category_child_id,$v->depth,$tmpl);
			if(!empty($child)){
				$html .= $child;
			}
		}
	}
	return $html;
}