<?php
/**
 * Class that offers some static functions that can be used by the module
 * @package	customfilters
 * @author 	Sakis Terz
 * @since	1.8.0
 * @copyright	Copyright (C) 2008 - 2013 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
Class cftools{
	protected static $menuparams;
	protected static $moduleparams=array();
	protected static $componentparams;
	protected static $_publishedCustomFilters=array();
	protected static $rangeVars=array();
	protected static $active_tree=array(); 
	//the vendor's accepted currencies
	protected static $vendor_cur;
	//info about the a specific currency
	protected static $currency_info=array();
	//the date format as set in the component's parems
	protected static $dateFormat_php='';
	protected static $dateFormat='';
	

	/**
	 * Function to get the menu params
	 * @since	1.8.0
	 * @author	Sakis Terz
	 */
	public static function getMenuparams(){
		if(empty(self::$menuparams)){
			$app=JFactory::getApplication();
			$menus	= $app->getMenu();
			$cfmenus=$menus->getItems('link','index.php?option=com_customfilters&view=products');
			$menuparams=new JRegistry;
			if(empty($cfmenus)){
				$app->enqueueMessage(JText::_('COM_CUSTOMFILTERS_MENU_ITEM_MISSING'),'Notice');
			}else{
				$menuparams->loadString($cfmenus[0]->params);
				$menuparams->set('cf_itemid',$cfmenus[0]->id);
			}
			self::$menuparams=$menuparams;
		}
		return self::$menuparams;
	}

	/**
	 * Function to get the module's params
	 * @since	1.9.0
	 * @author	Sakis Terz
	 */
	public static function getModuleparams($module=''){
		if(!empty($module))$key=$module->id;
		else $key=0;
		if(empty(self::$moduleparams[$key])){
			if(empty($module))$module=JModuleHelper::getModule('mod_cf_filtering');
			$moduleParams=new JRegistry;
			$moduleParams->loadString($module->params);
			self::$moduleparams[$key]=$moduleParams;
		}
		return self::$moduleparams[$key];
	}

	/**
	 * Function to get the component's params
	 * @since	1.9.0
	 * @author	Sakis Terz
	 */
	public static function getComponentparams(){
		if(empty(self::$componentparams)){
			self::$componentparams  = JComponentHelper::getParams('com_customfilters');
		}
		return self::$componentparams;
	}

	/**
	 * Return the url of a specific media
	 * @author	Sakis Terz
	 * @param 	int $media_id
	 * @since	1.7.1
	 */
	public static function getMediaFile($media_id){
		if($media_id){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select("file_url_thumb");
			$query->from("#__virtuemart_medias");
			$query->where("file_is_downloadable=0");
			$query->where("published=1");
			$query->where("virtuemart_media_id=$media_id");
			$db->setQuery($query);
			$img_url=$db->loadResult();
			if(!empty($img_url)){
				$img_path=JPATH_ROOT.DIRECTORY_SEPARATOR.$img_url;
				if(!file_exists($img_path))$img_url=JURI::base().'components/com_virtuemart/assets/images/vmgeneral/'.VmConfig::get('no_image_found');
				else $img_url=JURI::base().$img_url;
			}
		}else $img_url=JURI::base().'components/com_virtuemart/assets/images/vmgeneral/'.VmConfig::get('no_image_set');
		$img_prop=getimagesize($img_url);
		$img=new stdClass();

		$img->url=$img_url;
		if(!empty($img_prop) && is_array($img_prop)){
			$img->width=$img_prop[0];
			$img->height=$img_prop[1];
		}
		return $img;
	}


	/**
	 * used to convert the hex custom filter option to normal/dec string
	 * It controls also the string format for security reasons
	 * @author	Sakis Terz
	 * @since 	1.0
	 * @return	String
	 */
	public static function cfHex2bin($h)
	{
		if (is_string($h) && preg_match('/^[a-fA-F0-9]+$/', $h)){
			$r='';
			for ($a=0; $a<strlen($h); $a+=2) {
				$r.=chr(hexdec($h{$a}.$h{($a+1)}));
			}
			return $r;
		}return;
	}

	/**
	 * Convert the hex array to normal stings array
	 * @param	Array
	 * @return	Array
	 * @since	1.0
	 * @author	Sakis Terz
	 */
	public static function hex2binArray($array){
		$myArray=array();
		foreach($array as $h){
			$r=self::cfHex2bin($h);
			if(!empty($r))$myArray[]=$r;
		}
		return $myArray;
	}

	/**
	 * Create an assoc. array with the filter options using as key the value id
	 * Also it converts special characters of the label/name to their html equivelants
	 * @param 	Array	The object list with the values and the counter
	 * @return	Array	The values array using as key the value id. We need the key later to check the active/inactive options
	 */
	public static function arrayFromValList($valList){
		if(empty($valList))return;
		$valArray=array();

		foreach($valList as $val){
			if(!array_key_exists($val->id, $valArray)){
				if(!empty($val->name))$val->name=htmlspecialchars($val->name);
				$valArray[$val->id]=$val;
			}
		}//print_r($valArray);
		return $valArray;
	}


	/**
	 * Function to get the existing custom filters
	 *
	 * The same function exist in the module options_helper.php file
	 *
	 * @since	1.9
	 * @author	Sakis Terz
	 */
	public static function getCustomFilters($module_params=''){
		if(empty(self::$_publishedCustomFilters)){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			
			if(empty($module_params))$module_params=self::getModuleparams();
			//ordering
			$order=$module_params->get('cf_ordering','cf.ordering');
			$order_dir=$module_params->get('cf_ordering_dir','ASC');

			//table cf_customfields
			$query->select('cf.id AS id');
			$query->select('cf.type_id  AS disp_type');
			$query->select('cf.params AS params');
			$query->select('cf.data_type AS data_type');
			$query->from('#__cf_customfields AS cf');

			//table vituemart_customfields
			$query->select('vmc.custom_title AS custom_title');
			$query->select('vmc.virtuemart_custom_id AS custom_id,vmc.field_type');
			$query->select('vmc.is_list AS is_list, vmc.custom_value AS custom_value, vmc.custom_element');

			//joins
			$query->join('INNER','#__virtuemart_customs AS vmc ON cf.vm_custom_id=vmc.virtuemart_custom_id');
			$query->where('cf.published=1');
			$query->order($order.' '.$order_dir);
			$db->setQuery($query);
			$cust_filters=$db->loadObjectList();
			$cust_filters=self::setPluginparamsAsAttributes($cust_filters);
			//echo $db->getErrorMsg();
			foreach ($cust_filters as $cf){
				self::$_publishedCustomFilters[$cf->custom_id]=$cf;
			}
		}
		//echo(var_dump(self::$_publishedCustomFilters));
		return self::$_publishedCustomFilters;
	}


	/**
	 * If the customfield is plugin then get the plugin params and assign them to the custom filter as object attr.
	 *
	 * @param 	array $cust_filters
	 * @return	array $cust_filters
	 * @since	1.9.0
	 */
	public static function setPluginparamsAsAttributes($cust_filters){
		if(!is_array($cust_filters))return;
		JPluginHelper::importPlugin ('vmcustom');
		foreach ($cust_filters as &$customfilter){
			if($customfilter->field_type=='E'){
				$name=$customfilter->custom_element;
				$virtuemart_custom_id=$customfilter->custom_id;
				$dispatcher = JDispatcher::getInstance ();
				$product_customvalues_table='';
				$customvalues_table='';
				$filter_by_field='';
				$customvalue_value_field='';
				$filter_data_type='string';
				$sort_by='';
				$ret = $dispatcher->trigger ('onFilteringCustomfilters', array($name,$virtuemart_custom_id,&$product_customvalues_table,&$customvalues_table,&$filter_by_field,&$customvalue_value_field,&$filter_data_type, &$sort_by));
				//all the necessary variables should be there
				if($ret && !empty($product_customvalues_table) && !empty($customvalues_table) && !empty($filter_by_field) && !empty($customvalue_value_field) && !empty($filter_data_type) && !empty($sort_by)){
					$pluginparams=new stdClass();
					$pluginparams->product_customvalues_table=$product_customvalues_table;
					$pluginparams->customvalues_table=$customvalues_table;
					$pluginparams->filter_by_field=$filter_by_field;
					$pluginparams->filter_data_type=strtolower($filter_data_type);
					$pluginparams->customvalue_value_field=$customvalue_value_field;
					$pluginparams->sort_by=$sort_by;
					$customfilter->pluginparams=$pluginparams;
				}
			}
		}
		return $cust_filters;
	}


	/**
	 * Get the vendor's accepted currency ids
	 * @author	Sakis Terz
	 * @since	1.4.0
	 */
	public static function getVendorCurrency(){
		if(empty(self::$vendor_cur)){
			$db = JFactory::getDbo();
			$q  = 'SELECT CONCAT(`vendor_accepted_currencies`, ",",`vendor_currency`) AS all_currencies, `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`=1';
			$db->setQuery($q);
			self::$vendor_cur = $db->loadAssoc();
		}
		return self::$vendor_cur;
	}

	/**
	 * Get info of the current used currency
	 * @author	Sakis Terz
	 * @since	1.4.0
	 * @param integer $curr_id
	 */
	public static function getCurrencyInfo($curr_id){
		if(empty($curr_id))return;
		if(empty(self::$currency_info[$curr_id])){
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select('currency_symbol, currency_decimal_place, currency_decimal_symbol,currency_positive_style');
			$query->from('#__virtuemart_currencies');
			$query->where('virtuemart_currency_id='.(int)$curr_id);
			$db->setQuery($query);
			self::$currency_info[$curr_id]=$db->loadObject();
		}
		return self::$currency_info[$curr_id];
	}

	/**
	 * Get the date format as set in the component params
	 * Enter description here ...
	 */
	public static function getDateFormat(){
		if(empty(self::$dateFormat) && empty(self::$dateFormat_php)){
			$component_params=self::getComponentparams();
			self::$dateFormat_php=$component_params->get('date_format','d-m-Y');
			$format_array=explode('-', self::$dateFormat_php);
			self::$dateFormat='%'.$format_array[0].'-%'.$format_array[1].'-%'.$format_array[2];
		}		
		return array('dateFormat_php'=>self::$dateFormat_php,'dateFormat'=>self::$dateFormat);
	}
	
	/**
	 * set the range vars
	 * 
	 * @param array $rangeVars
	 * @since	1.9.0
	 */
	public static function setRangeVars($rangeVars){
		self::$rangeVars=$rangeVars;
	}
	
	/**
	 * get the range vars
	 * 
	 * @param array $rangeVars
	 * @since	1.9.0
	 */
	public static function getRangeVars(){
		return self::$rangeVars;
	}
	
/**
	 * set the range vars
	 * 
	 * @param array $rangeVars
	 * @since	1.9.0
	 */
	public static function setActiveTree($active_tree){
		self::$active_tree=$active_tree;
	}
	
	/**
	 * get the current active trees
	 * 
	 * @param array $rangeVars
	 * @since	1.9.0
	 */
	public static function getActiveTree(){
		return self::$active_tree;
	}


	public static $standard_colors  =  array(
	"Black" => "#000000",	"Navy" => "#000080",    "DarkBlue" => "#00008B",	"MediumBlue" => "#0000CD",	"Blue" => "#0000FF",	"DarkGreen" => "#006400",
	"Green" => "#008000",	"Teal" => "#008080",	"DarkCyan" => "#008B8B",	"DeepSkyBlue" => "#00BFFF",	"DarkTurquoise" => "#00CED1",	"MediumSpringGreen" => "#00FA9A",
	"Lime" => "#00FF00",	"SpringGreen" => "#00FF7F",	"Aqua" => "#00FFFF",	"Cyan" => "#00FFFF",	"MidnightBlue" => "#191970",	"DodgerBlue" => "#1E90FF",
	"LightSeaGreen" => "#20B2AA",	"ForestGreen" => "#228B22",	"SeaGreen" => "#2E8B57",	"DarkSlateGray" => "#2F4F4F",	"LimeGreen" => "#32CD32",	"MediumSeaGreen" => "#3CB371",
	"Turquoise" => "#40E0D0",	"RoyalBlue" => "#4169E1",	"SteelBlue" => "#4682B4",	"DarkSlateBlue" => "#483D8B",	"MediumTurquoise" => "#48D1CC",	"Indigo " => "#4B0082",
	"DarkOliveGreen" => "#556B2F",	"CadetBlue" => "#5F9EA0",	"CornflowerBlue" => "#6495ED",	"MediumAquaMarine" => "#66CDAA",	"DimGray" => "#696969",
	"SlateBlue" => "#6A5ACD",	"OliveDrab" => "#6B8E23",	"SlateGray" => "#708090",	"LightSlateGray" => "#778899",	"MediumSlateBlue" => "#7B68EE",
	"LawnGreen" => "#7CFC00",	"Chartreuse" => "#7FFF00",	"Aquamarine" => "#7FFFD4",	"Maroon" => "#800000",	"Purple" => "#800080",	"Olive" => "#808000",
	"Gray" => "#808080",	"SkyBlue" => "#87CEEB",	"LightSkyBlue" => "#87CEFA",	"BlueViolet" => "#8A2BE2",	"DarkRed" => "#8B0000",	"DarkMagenta" => "#8B008B",
	"SaddleBrown" => "#8B4513",	"DarkSeaGreen" => "#8FBC8F",	"LightGreen" => "#90EE90",	"MediumPurple" => "#9370DB",	"DarkViolet" => "#9400D3",	"PaleGreen" => "#98FB98",
	"DarkOrchid" => "#9932CC",	"YellowGreen" => "#9ACD32",	"Sienna" => "#A0522D",	"Brown" => "#A52A2A",	"DarkGray" => "#A9A9A9",	"LightBlue" => "#ADD8E6",
	"GreenYellow" => "#ADFF2F",	"PaleTurquoise" => "#AFEEEE",	"LightSteelBlue" => "#B0C4DE",	"PowderBlue" => "#B0E0E6",	"FireBrick" => "#B22222",	"DarkGoldenRod" => "#B8860B",
	"MediumOrchid" => "#BA55D3",	"RosyBrown" => "#BC8F8F",	"DarkKhaki" => "#BDB76B",	"Silver" => "#C0C0C0",	"MediumVioletRed" => "#C71585",	"IndianRed " => "#CD5C5C",
	"Peru" => "#CD853F",	"Chocolate" => "#D2691E",	"Tan" => "#D2B48C",	"LightGray" => "#D3D3D3",	"Thistle" => "#D8BFD8",	"Orchid" => "#DA70D6",	"GoldenRod" => "#DAA520",
	"PaleVioletRed" => "#DB7093",	"Crimson" => "#DC143C",	"Gainsboro" => "#DCDCDC",	"Plum" => "#DDA0DD",	"BurlyWood" => "#DEB887",	"LightCyan" => "#E0FFFF",
	"Lavender" => "#E6E6FA",	"DarkSalmon" => "#E9967A",	"Violet" => "#EE82EE",	"PaleGoldenRod" => "#EEE8AA",	"LightCoral" => "#F08080",	"Khaki" => "#F0E68C",
	"AliceBlue" => "#F0F8FF",	"HoneyDew" => "#F0FFF0",	"Azure" => "#F0FFFF",	"SandyBrown" => "#F4A460",	"Wheat" => "#F5DEB3",	"Beige" => "#F5F5DC",
	"WhiteSmoke" => "#F5F5F5",	"MintCream" => "#F5FFFA",	"GhostWhite" => "#F8F8FF",	"Salmon" => "#FA8072",	"AntiqueWhite" => "#FAEBD7",	"Linen" => "#FAF0E6",
	"LightGoldenRodYellow" => "#FAFAD2",	"OldLace" => "#FDF5E6",	"Red" => "#FF0000",  "FerrariRed" => "#FF2800",	"Fuchsia" => "#FF00FF",	"Magenta" => "#FF00FF",
	"DeepPink" => "#FF1493",	"OrangeRed" => "#FF4500",	"Tomato" => "#FF6347",	"HotPink" => "#FF69B4",	"Coral" => "#FF7F50",	"DarkOrange"	=> "#FF8C00",
	"LightSalmon" => "#FFA07A",	"Orange" => "#FFA500",	"LightPink" => "#FFB6C1",	"Pink" => "#FFC0CB",	"Gold" => "#FFD700",	"PeachPuff" => "#FFDAB9",	"NavajoWhite" => "#FFDEAD",
	"Moccasin" => "#FFE4B5",	"Bisque" => "#FFE4C4",	"MistyRose" => "#FFE4E1",	"BlanchedAlmond" => "#FFEBCD",	"PapayaWhip" => "#FFEFD5",	"LavenderBlush" => "#FFF0F5",
	"SeaShell" => "#FFF5EE",	"Cornsilk" => "#FFF8DC",	"LemonChiffon" => "#FFFACD",	"FloralWhite" => "#FFFAF0",	"Snow" => "#FFFAFA",	"Yellow" => "#FFFF00",
	"LightYellow" => "#FFFFE0",	"Ivory" => "#FFFFF0",	"White" => "#FFFFFF");
	
	
	/**
	 * Check if a value is color and format it to be used in css
	 *
	 * @param 	string 	$string
	 * @return 	mixed	color on suceess, false on failure
	 * @see		customfieldsforall\helpers\filter.php
	 */
	public static function checkNFormatColor($string){
		$string=(string)$string;
		if(empty($string))return false;
		//$string=strtolower($string);

		//check for hexademical
		preg_match('/^[a-f0-9]{6}$/i', $string, $matches);
		$result = @$matches[0];
		if(!empty($result))return '#'.$result;

		//check for standard color name
		$string=ucfirst($string);
		if(isset(self::$standard_colors[$string]))$result=strtolower($string);
		else $result=false;

		return $result;
	}
	
	/**
	 * Prints the profiler data to the screen
	 * @author	Sakis Terz
	 * @param	Object	The jprofiler instance
	 * @since 	1.0
	 */
	public static function printProfiler($profiler){
		$data=$profiler->getBuffer();
		$counter=count($data);
		$data_str=implode('<hr/>',$data);
		$data_str.='<hr/><b>Total filters:</b>'.$counter;
		$data_str.='&nbsp;<b>RAM usage:</b>'.$profiler->getMemory().' b';
		echo $data_str;
	}	
	
}