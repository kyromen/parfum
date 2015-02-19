<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param Filter: Virtuemart 2 search module
* Version: 3.0.3 (2015.01.28)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

if($show_filter){
	echo '<div id="paramfilter-'.$module_id.'" class="paramfilter ver160">';
	echo   '<script type="text/javascript">
				var mcf_uri = "'.$doc->baseurl.'"'. PHP_EOL . 'loader_img["paramfilter-'.$module_id.'"] = "'.$loader_img.'";'.PHP_EOL;
	echo '</script>';
	echo !empty($mcf_prepend_text) ? '<div class="mcf_prepend_text">'.$mcf_prepend_text.'</div>' : '';
	echo '<form class="mcf_form'.$mcf_mod_ajax_css.'" action="'.str_replace('modules/mod_virtuemart_param_filter/','',JRoute::_('index.php?option=com_virtuemart&view=category&search=true&virtuemart_category_id=0')).'" method="'.$method.'">
			<input type="hidden" name="option" value="com_virtuemart" />
			<input type="hidden" name="search" value="true" />
			<input type="hidden" name="view" value="category" />
			<input type="hidden" name="mcf_id" value="'.$module_id.'" />
			<div class="ajax_url" style="display:none;">'.str_replace('modules/mod_virtuemart_param_filter/','',JUri::base()).'modules/mod_virtuemart_param_filter/ajax.php?search=true&amp;virtuemart_category_id=0'.'</div>';
	if($stock){
		echo '<input type="hidden" name="s" value="1" />'; // instock trigger
	}
	if(!empty($mcf_mod_ajax_div)){
		echo '<div style="display:none;" id="ajax_div">'.$mcf_mod_ajax_div.'</div>'; // instock trigger
	}
	if(isset($_SESSION['__vm']['vmlastvisitedcategoryid'])){
		if(!in_array(JRequest::getVar('option'),array('com_virtuemart')) && !in_array(JRequest::getVar('view'),array('category'))){ //'productdetails'
			$_SESSION['__vm']['vmlastvisitedcategoryid'] = 0;
		}else{
			echo '<div style="display:none;" id="mcf_vmlastvisitedcategoryid">'.$_SESSION['__vm']['vmlastvisitedcategoryid'].'</div>';
		}
	}
	/* keyword, sku
	?>
	<div class="custom_params custom_kw">
		<div class="heading">Name or Sku<?php echo !empty($_REQUEST['kw'])?'<a class="reset" href="#">[x]</a>':''?></div>
		<div class="values values-kw">
			<input class="mcf_kw" type="text" name="kw" value="<?php echo !empty($_REQUEST['kw'])?htmlspecialchars($_REQUEST['kw']):''?>" placeholder="keyword" /> <a href="javascript:void(0)" class="kw_btn">[GO]</a>
		</div>
	</div>
	<?php
	*/
	/* custom value text
	?>
	<div class="custom_params custom_cvt">
		<div class="heading">Custom field text<?php echo !empty($_REQUEST['cvt'])?'<a class="reset" href="#">[x]</a>':''?></div>
		<div class="values values-cvt">
			<input class="mcf_kw" type="text" name="cvt" value="<?php echo !empty($_REQUEST['cvt'])?htmlspecialchars($_REQUEST['cvt']):''?>" placeholder="Any text value of custom fields" /> <a href="javascript:void(0)" class="kw_btn">[GO]</a>
		</div>
	</div>
	<?php
	*/
	echo $categories_html;
    echo "<div class=\"separator_first_type\"></div>";
    echo "<div class=\"clear\"></div>";
    echo $price_html;
    echo "<div class=\"separator_first_type\"></div>";

	foreach(array_reverse($types) as $type){
		$tmp_params = $type->custom_params;
		$tmp_params = explode('|',$tmp_params);
		$custom_params = array();
		foreach($tmp_params as $v){
			preg_match("/^([^=]*)=(.*)|/i",$v, $res);
			$custom_params[@$res[1]] = json_decode(@$res[2]);
		}
		if(isset($custom_params['af']) && (int)$custom_params['af']){
			$assign_field = JRequest::getVar('cv'.(int)$custom_params['af'],array());
			$assign_field = array_diff($assign_field,array('',null));
			if(isset($assign_field) && count($assign_field) > 0){
				if(isset($custom_params['av']) && !empty($custom_params['av'])){
					$custom_params['av'] = explode(';',$custom_params['av']);
					$hide = true;
					foreach($custom_params['av'] as &$v){
						if(in_array($v,$assign_field)){
							$hide = false;
							continue;
						}
					}unset($v);
					if($hide){
						continue;
					}
				}
			}else{
				continue;
			}
		}
		$customfield_value = isset($custom_values[$type->virtuemart_custom_id]) ? $custom_values[$type->virtuemart_custom_id] : array();
		if(isset($custom_params['ft']) && $custom_params['ft'] == 'int'){
			$customfield_value = isset($pre_int_values[$type->virtuemart_custom_id]) ? $pre_int_values[$type->virtuemart_custom_id] : array();
			sort($customfield_value);
			$customfield_value_vis = isset($pre_int_values_visible[$type->virtuemart_custom_id]) ? $pre_int_values_visible[$type->virtuemart_custom_id] : array();
			sort($customfield_value_vis);
		}else{
		}
		echo '<input type="hidden" name="cpi[]" value="'.$type->virtuemart_custom_id.'" />';
		$html = '';
		if(isset($custom_params['z']) && $custom_params['z'] != 'default' && $count_zero_show['z'] != $count_zero_show){
			$count_zero_show = $custom_params['z'];
			$count_zero_show_txt = $count_zero_show == 'disable' ? ' disabled="disabled"' : '';
		}
		$customfields_layout_tmp = $customfields_layout == 'auto' ? $custom_params['t'] : $customfields_layout;
		$mcf_customfields_select_heading = isset($custom_params['ld']) && !empty($custom_params['ld']) ? $custom_params['ld'] : $mcf_customfields_select_heading_global;
		$count_sum = 0;
		require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','customfields'.DS.$customfields_layout_tmp)); // Generate customfields html
		if($count_zero_show != 'hidden' || $count_sum > 0){
			echo $html;
		}
		$count_zero_show = $count_zero_show_tmp;
		$count_zero_show_txt = $count_zero_show_txt_tmp;
//		Тип товара
        if ($type->virtuemart_custom_id == 25) {
            echo "<div class=\"clear\"></div>";
            echo "<div class=\"separator_first_type\"></div>";
            echo $manufacturers_html;
        }
//		Год создания
		else if ($type->virtuemart_custom_id == 24) {
            echo "<div class=\"separator_first_type\"></div>";
        }
    }
    echo "<div class=\"clear\"></div>";
    echo "<div class=\"separator_second_type\"></div>";
	if(isset($type_req->virtuemart_custom_id)){
		echo '<input type="hidden" name="custom_parent_id" value="'.$type_req->virtuemart_custom_id.'" />';
	}else{
		echo '<span style="color:#f00;font-size:80%;">Please create at least one customfield of required type according to documentation</span>';
	}
	echo '<input type="hidden" name="limitstart" value="0" />';
	echo '<input type="hidden" name="limit" value="'.$limit.'" />';
	$all_count = $show_all_count && $param_search_ids ? ' ('.count($param_search_ids).')' : '';
    echo '<input class="mcf_button" type="submit" value="'."применить фильтр".$all_count.'" />';
//	echo '<a class="fullreset" href="#">'.JText::_('MOD_VMCUSTOM_PARAM_FILTER_RESET').'</a>';
	echo '</form>';
	echo !empty($mcf_append_text) ? '<div class="mcf_append_text">'.$mcf_append_text.'</div>' : '';
	echo '</div>';
}