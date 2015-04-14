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
defined('_JEXEC') or die('Restricted access.');

define( '_COM_SEF_PRIORITY_VIRTUEMART_PRODUCT_ITEMID',  15 );
define( '_COM_SEF_PRIORITY_VIRTUEMART_PRODUCT',         20 );
define( '_COM_SEF_PRIORITY_VIRTUEMART_CATEGORY_ITEMID', 25 );
define( '_COM_SEF_PRIORITY_VIRTUEMART_CATEGORY',        30 );

jimport('joomla.application.module.helper');

// Backwards compatibility
if (!class_exists('JoomSefLogger')) {
    class JoomSefLogger {
        function Log($msg, $sefExt, $component = null, $page = null) {
            // Do nothing
        }
    }
}

class SefExt_com_virtuemart extends SefExt
{
    var $pagetitle;
    var $category_desc;
    var $product_desc;
    var $should_number;
    var $catTitles = array();
    var $productTitle = null;
    
    private static $isChp = null;
    private static $isChpCF = null;
    private static $chpFilters = null;
    private static $listLimit = null;
    private static $productsPerRow = null;
    private static $pageSeq = null;
    private static $dbTables = null;

    function SefExt_com_virtuemart() {
        // Get default list limit
        if (is_null(self::$listLimit)) {
            if (!class_exists('VmConfig')) {
                if (file_exists(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php')) {
                    include_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
                    self::$listLimit = VmConfig::get('list_limit', 20);
                    self::$productsPerRow = VmConfig::get('products_per_row', 3);
                    self::$pageSeq = VmConfig::get('pagseq');
                }
                else {
                    self::$listLimit = 20;
                    self::$productsPerRow = 3;
                    self::$pageSeq = '';
                }
            }
            else {
                self::$listLimit = VmConfig::get('list_limit', 20);
                self::$productsPerRow = VmConfig::get('products_per_row', 3);
                self::$pageSeq = VmConfig::get('pagseq');
            }
        }
        
        // Check if Cherry Picker modules are installed
        if (is_null(self::$isChp) || is_null(self::$isChpCF)) {
            $mod = JModuleHelper::getModule('mod_vm_cherry_picker');
            self::$isChp = (is_object($mod) && ($mod->id > 0));
            
            $mod = JModuleHelper::getModule('mod_vm_cherry_picker_cf');
            self::$isChpCF = (is_object($mod) && ($mod->id > 0));
        }
        
        // call parent constructor
        parent::__construct();
    }

    function getNonSefVars(&$uri)
    {
        $this->_createNonSefVars($uri);
        
        return array($this->nonSefVars, $this->ignoreVars);
    }
    
    function _createNonSefVars(&$uri)
    {
        if (isset($this->nonSefVars) && isset($this->ignoreVars))
            return;
            
        $this->params = SEFTools::getExtParams('com_virtuemart');
        
        $this->nonSefVars = array();
        $this->ignoreVars = array();
        
        // non-sef vars
        if ($this->params->get('pagehandle', '0') == '0') {
            if (!is_null($uri->getVar('limit')))
                $this->nonSefVars['limit'] = $uri->getVar('limit');
            if (!is_null($uri->getVar('limitstart')))
                $this->nonSefVars['limitstart'] = $uri->getVar('limitstart');
        }
        if (!is_null($uri->getVar('orderby')))
            $this->nonSefVars['orderby'] = $uri->getVar('orderby');
        if (!is_null($uri->getVar('order')))
            $this->nonSefVars['order'] = $uri->getVar('order');
        if (!is_null($uri->getVar('dir')))
            $this->nonSefVars['dir'] = $uri->getVar('dir');
        if (!is_null($uri->getVar('DescOrderBy')))
            $this->nonSefVars['DescOrderBy'] = $uri->getVar('DescOrderBy');
        if (!is_null($uri->getVar('redirected')))
            $this->nonSefVars['redirected'] = $uri->getVar('redirected');
        if (!is_null($uri->getVar('format')))
            $this->nonSefVars['format'] = $uri->getVar('format');
        if (!is_null($uri->getVar('tmpl')))
            $this->nonSefVars['tmpl'] = $uri->getVar('tmpl');
        if (!is_null($uri->getVar('filter_product')))
            $this->nonSefVars['filter_product'] = $uri->getVar('filter_product');
        
        /*
        if (($uri->getVar('page') == 'shop.downloads') && !is_null($uri->getVar('download_id')))
            $this->nonSefVars['download_id'] = $uri->getVar('download_id');
            
        if (($uri->getVar('page') == 'shop.parameter_search_form') && !is_null($uri->getVar('product_type')))
            $this->nonSefVars['product_type'] = $uri->getVar('product_type');
        */
        
        if ($this->params->get('flypagehandle', 'addnever') == 'nonsef') {
            if (!is_null($uri->getVar('flypage')))
                $this->nonSefVars['flypage'] = $uri->getVar('flypage');
        }
        
        // ignored vars
        if (!is_null($uri->getVar('order_id')))
            $this->ignoreVars['order_id'] = $uri->getVar('order_id');
        if (!is_null($uri->getVar('virtuemart_order_id')))
            $this->ignoreVars['virtuemart_order_id'] = $uri->getVar('virtuemart_order_id');
        if (!is_null($uri->getVar('order_number')))
            $this->ignoreVars['order_number'] = $uri->getVar('order_number');
        if (!is_null($uri->getVar('order_pass')))
            $this->ignoreVars['order_pass'] = $uri->getVar('order_pass');
        if (!is_null($uri->getVar('ship_to_info_id')))
            $this->ignoreVars['ship_to_info_id'] = $uri->getVar('ship_to_info_id');
        if (!is_null($uri->getVar('shipping_rate_id')))
            $this->ignoreVars['shipping_rate_id'] = $uri->getVar('shipping_rate_id');
        if (!is_null($uri->getVar('payment_method_id')))
            $this->ignoreVars['payment_method_id'] = $uri->getVar('payment_method_id');
        if (!is_null($uri->getVar('checkout_this_step')))
            $this->ignoreVars['checkout_this_step'] = $uri->getVar('checkout_this_step');
        if (!is_null($uri->getVar('checkout_next_step')))
            $this->ignoreVars['checkout_next_step'] = $uri->getVar('checkout_next_step');
        if (!is_null($uri->getVar('checkout_last_step')))
            $this->ignoreVars['checkout_last_step'] = $uri->getVar('checkout_last_step');
        if (!is_null($uri->getVar('checkout_stage')))
            $this->ignoreVars['checkout_stage'] = $uri->getVar('checkout_stage');
        if (!is_null($uri->getVar('keyword')))
            $this->ignoreVars['keyword'] = $uri->getVar('keyword');
        if (!is_null($uri->getVar('keyword1')))
            $this->ignoreVars['keyword1'] = $uri->getVar('keyword1');
        if (!is_null($uri->getVar('keyword2')))
            $this->ignoreVars['keyword2'] = $uri->getVar('keyword2');
        if (!is_null($uri->getVar('search')))
            $this->ignoreVars['search'] = $uri->getVar('search');
        if (!is_null($uri->getVar('Search')))
            $this->ignoreVars['Search'] = $uri->getVar('Search');
        if (!is_null($uri->getVar('vmcchk')))
            $this->ignoreVars['vmcchk'] = $uri->getVar('vmcchk');
        if (!is_null($uri->getVar('pop')))
            $this->ignoreVars['pop'] = $uri->getVar('pop');
        if (!is_null($uri->getVar('virtuemart_userinfo_id')))
            $this->ignoreVars['virtuemart_userinfo_id'] = $uri->getVar('virtuemart_userinfo_id');
        if (!is_null($uri->getVar('cart_virtuemart_product_id')))
            $this->ignoreVars['cart_virtuemart_product_id'] = $uri->getVar('cart_virtuemart_product_id');
        
        // Cherry picker vars
        if (!is_null($uri->getVar('low-price')))
            $this->ignoreVars['low-price'] = $uri->getVar('low-price');
        if (!is_null($uri->getVar('high-price')))
            $this->ignoreVars['high-price'] = $uri->getVar('high-price');
    }
    
    function _getCurLangTable($table)
    {
        $lang = JFactory::getLanguage();
        $tag = $lang->getTag();
        return $table.strtolower(str_replace('-', '_', $tag));
    }
    
    /**
     * Returns limit for given category ID.
     * If $fromState is true, tries to load current limit from session first.
     */
    function GetCategoryLimit($catId, $fromState = false) {
        if ($fromState) {
            $app = JFactory::getApplication();
            $limitString = 'com_virtuemart.categoryc' . $catId . '.limit';
            $limit = $app->getUserState($limitString, null);
            if (!is_null($limit)) {
                return $limit;
            }
        }
        
        $db = JFactory::getDbo();
        $catId = intval($catId);
        $sql = "SELECT `products_per_row`, `limit_list_initial`, `limit_list_step` FROM `#__virtuemart_categories` WHERE `virtuemart_category_id` = '{$catId}'";
        $db->setQuery($sql);
        
        $limits = $db->loadObject();
        $sugLimit = 0;
        if (!empty($limits->limit_list_initial)) {
            $sugLimit = $limits->limit_list_initial;
        }
        else if (!empty($limits->limit_list_step)) {
            $sugLimit = $limits->limit_list_step;
        }
        else {
            $sugLimit = self::$listLimit;
        }
        
        if (empty($limits->products_per_row)) {
            $limits->products_per_row = self::$productsPerRow;
        }
        
        $limit = $sugLimit - ($sugLimit % $limits->products_per_row);
        
        if (!empty(self::$pageSeq)) {
            $prod_per_page = explode(',', self::$pageSeq);
            if (count($prod_per_page) == 1) {
                $l = intval($prod_per_page[0]);
                if ($l > 0 && $limit != $l) {
                    $limit = $l;
                }
            }
            else {
                $n = count($prod_per_page);
                for ($i = 1; $i < $n; $i++) {
                    $l = intval($prod_per_page[$i]);
                    $prevL = intval($prod_per_page[$i - 1]);
                    if ($l > $limit) {
                        if ($prevL < $limit) {
                            $limit = $prevL;
                        }
                        break;
                    }
                }
            }
        }
        
        return $limit;
    }
    
    /**
     * Creates an array of nested categories from given category id
     */
    function GetCategories($catId)
    {
        $database = JFactory::getDBO();
        
        $categories = array();
        
        // Get table name for current language
        $table = $this->_getCurLangTable('#__virtuemart_categories_');

        // Check if lang table exists
        if ($table != '#__virtuemart_categories_en_gb') {
            if (!$this->TableExists($table)) {
                $table = '#__virtuemart_categories_en_gb';
            }
        }
        
        while ($catId > 0) {
            $database->setQuery("SELECT `category_name`, `slug`, `category_description` FROM `{$table}` WHERE `virtuemart_category_id` = '{$catId}'");
            $row = $database->loadObject();
            if( !$row ) {
                // Try to load from EN table if exists
                if ($table != '#__virtuemart_categories_en_gb' && $this->TableExists('#__virtuemart_categories_en_gb')) {
                    $database->setQuery("SELECT `category_name`, `slug`, `category_description` FROM `#__virtuemart_categories_en_gb` WHERE `virtuemart_category_id` = '{$catId}'");
                    $row = $database->loadObject();
                }
                if (!$row) {
                    JoomSefLogger::Log("Category with ID {$catId} not found.", $this, 'com_virtuemart');
                    break;
                }
            }
            
            // Build name
            $name = array();
            $this->AddCategoryNamePart($name, $catId, $row, $this->params->get('catname1', ''));
            $this->AddCategoryNamePart($name, $catId, $row, $this->params->get('catname2', 'title'));
            $this->AddCategoryNamePart($name, $catId, $row, $this->params->get('catname3', ''));
            $name = implode('-', $name);
            
            array_unshift($categories, $name);
            
            // Set category description if not already set
            if( empty($this->category_desc) ) {
                $this->category_desc = stripslashes($row->category_description);
            }
            
            // Store category titles
            $this->catTitles[] = $row->category_name;
            
            $database->setQuery("SELECT `category_parent_id` FROM `#__virtuemart_category_categories` WHERE `category_child_id` = '{$catId}'");
            $catId = $database->loadResult();
        }
        
        if (empty($categories)) {
            return null;
        }
        
        return $categories;
    }

    function AddCategoryNamePart(&$name, $id, $obj, $part)
    {
        switch ($part)
        {
            case 'id':
                $name[] = $id;
                return;
            case 'title':
                $name[] = $obj->category_name;
                return;
            case 'alias':
                $name[] = $obj->slug;
                return;
        }
    }
    
    /**
     * Returns category_id for a given product id
     */
    function GetProductCategoryId($productId)
    {
        $database = JFactory::getDBO();
        
        $database->setQuery("SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = '{$productId}'");
        $catId = $database->loadResult();
        if (is_null($catId)) {
            JoomSefLogger::Log("Category for product ID {$productId} not found.", $this, 'com_virtuemart');
        }
        
        return $catId;
    }

    /**
     * Returns manufacturer id for a given product id
     */
    function GetProductManufacturerId($productId)
    {
        $database = JFactory::getDBO();
        
        $database->setQuery("SELECT `virtuemart_manufacturer_id` FROM `#__virtuemart_product_manufacturers` WHERE `virtuemart_product_id` = '{$productId}'");
        $mId = $database->loadResult();
        if (is_null($mId)) {
            JoomSefLogger::Log("Manufacturer for product ID {$productId} not found.", $this, 'com_virtuemart');
        }
        
        return $mId;
    }
    
    /**
     * Returns cached list of DB tables
     */
    function GetDbTables()
    {
        if (is_null(self::$dbTables)) {
            $db = JFactory::getDbo();
            self::$dbTables = $db->getTableList();
        }
        
        return self::$dbTables;
    }
    
    /**
     * Checks if given table exists in the database
     */
    function TableExists($table)
    {
        $db = JFactory::getDbo();
        $prefix = $db->getPrefix();
        $table = str_replace('#__', $prefix, $table);
        $tables = $this->GetDbTables();
        
        if (array_search($table, $tables) === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns product name for a given product id
     */
    function GetProductName($productId)
    {
        $database = JFactory::getDBO();
        
        $descrow = ($this->params->get('product_desc', '2') == '1') ? 'product_s_desc' : 'product_desc';
        $table = $this->_getCurLangTable('#__virtuemart_products_');
        
        // Build list of tables to use in the DB query
        $tables = array();
        if ($this->TableExists($table)) {
            // Add current language table if it exists
            $tables[] = $table;
        }
        if ($table != '#__virtuemart_products_en_gb' && $this->TableExists('#__virtuemart_products_en_gb')) {
            // Add also en_gb table if it exists for fallback
            $tables[] = '#__virtuemart_products_en_gb';
        }
        $tablesCount = count($tables);
        
        // List of translatable fields
        $fields = array('product_name', 'slug', $descrow);
        
        // Build query
        $query = "SELECT `p`.`product_sku`";
        foreach ($fields as $field) {
            if ($tablesCount > 1) {
                // Use COALESCE
                $query .= ", COALESCE(";
                for ($i = 1; $i <= $tablesCount; $i++) {
                    if ($i > 1)
                        $query .= ", ";
                    
                    $query .= "`l{$i}`.`{$field}`";
                }
                $query .= ") AS `{$field}`";
            }
            else {
                // Simple field select
                $query .= ", `l1`.`{$field}`";
            }
        }
        
        // Add tables to query
        $query .= " FROM `#__virtuemart_products` AS `p`";
        for ($i = 1; $i <= $tablesCount; $i++) {
            $table = $tables[$i-1];
            $query .= " LEFT JOIN `{$table}` AS `l{$i}` ON `p`.`virtuemart_product_id` = `l{$i}`.`virtuemart_product_id`";
        }
        
        // Add condition
        $query .= " WHERE `p`.`virtuemart_product_id` = '{$productId}'";
        
        // If we're in this function, it means that the URL with
        // product name is being created, so let's number duplicates
        // if set to
        if ($this->params->get('numberproducts', '1') == '1') {
            $this->should_number = true;
        }
        
        $database->setQuery($query);
        $row = $database->loadObject();
        if (!$row) {
            JoomSefLogger::Log("Product with ID {$productId} not found.", $this, 'com_virtuemart');
            return null;
        }
        
        if (empty($this->product_desc)) {
            $this->product_desc = $row->$descrow;
        }
        
        $name = array();
        $this->AddProductNamePart($name, $productId, $row, $this->params->get('productname1', ''));
        $this->AddProductNamePart($name, $productId, $row, $this->params->get('productname2', 'title'));
        $this->AddProductNamePart($name, $productId, $row, $this->params->get('productname3', ''));
        $name = implode('-', $name);
        
        // Store product title
        $this->productTitle = $row->product_name;
        
        return $name;
    }
    
    function AddProductNamePart(&$name, $id, $product, $part)
    {
        switch ($part)
        {
            case 'id':
                $name[] = $id;
                return;
            case 'title':
                $name[] = $product->product_name;
                return;
            case 'alias':
                $name[] = $product->slug;
                return;
            case 'sku':
                $name[] = $product->product_sku;
                return;
        }
    }
    
    /**
     * Returns product type name for given product type id
     */
    function GetProductType($id)
    {
        $db = JFactory::getDBO();
        $sefConfig = SEFConfig::getConfig();
        
        $id = intval($id);
        $db->setQuery("SELECT `product_type_name` FROM `#__vm_product_type` WHERE `product_type_id` = $id");
        $row = $db->loadObject();
        if (!$row) {
            return null;
        }
        
        $type = ($this->params->get('producttypeid', '0') != '0' ? $id . '-' : '') . $row->product_type_name;
        return $type;
    }
    
    /**
     * Returns parent product ID
     */
    function GetProductParent($id)
    {
        $db = JFactory::getDbo();
        
        $db->setQuery("SELECT `product_parent_id` FROM `#__virtuemart_products` WHERE `virtuemart_product_id` = ".intval($id));
        $parent = $db->loadResult();
        
        return $parent;
    }
    
    /**
     * Returns all filters defined in Cherry Picker, no matter whether the Product Type or Custom Fields version is used
     */
    function GetCherryPickerFilters() {
        if (is_null(self::$chpFilters)) {
            $db = JFactory::getDbo();
            self::$chpFilters = array();
            
            // Load Cherry picker filters for Product Types if used
            if (self::$isChp) {
                $db->setQuery("SELECT `parameter_name` FROM `#__vm_product_type_parameter`");
                $names = $db->loadColumn();
                if (is_array($names)) {
                    self::$chpFilters = array_merge(self::$chpFilters, $names);
                }
            }
            
            // Load Cherry picker filters for Custom Fields if used
            if (self::$isChpCF) {
                $q = "SELECT `custom_title` FROM `#__virtuemart_customs` WHERE `custom_parent_id` ".
                    "IN (SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` ".
                    "WHERE `custom_parent_id`=0 AND `field_type`='P')";
                $db->setQuery($q);
                $names = $db->loadColumn();
                if (is_array($names)) {
                    self::$chpFilters = array_merge(self::$chpFilters, $names);
                }
            }
            
            self::$chpFilters = array_unique(self::$chpFilters);
        }
        
        return self::$chpFilters;
    }
    
    /**
     * Returns manufacturer name for given manufacturer id
     */
    function GetManufacturer($id, $view)
    {
        $database = JFactory::getDBO();
        $table = $this->_getCurLangTable('#__virtuemart_manufacturers_');
        
        $query = "SELECT `mf_name` FROM `{$table}` WHERE `virtuemart_manufacturer_id` = '{$id}'";
        $database->setQuery($query);
        $row = $database->loadObject();
        if (!$row) {
            JoomSefLogger::Log("Manufacturer with ID {$id} not found.", $this, 'com_virtuemart');
            return null;
        }
        
        $paramName = $view=='category' ? 'manufacturerid_category' : 'manufacturerid';
        
        $name = ($this->params->get($paramName, '0') != '0' ? $id . '-' : '') . $row->mf_name;
        return $name;
    }
    
    /**
     * Returns vendor name for given vendor id
     */
    function GetVendor($id)
    {
        $db = JFactory::getDbo();
        
        $id = intval($id);
        $query = "SELECT `vendor_name` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id` = '{$id}'";
        $db->setQuery($query);
        $row = $db->loadObject();
        if (!$row) {
            JoomSefLogger::Log("Vendor with ID {$id} not found.", $this, 'com_virtuemart');
            return null;
        }
        
        $name = ($this->params->get('vendorid', '0') != '0' ? $id . '-' : '') . $row->vendor_name;
        return $name;
    }

    /**
     * Returns file title for given file id
     */
    /*function GetFileTitle($id)
    {
        $database = & JFactory::getDBO();
        $sefConfig = & SEFConfig::getConfig();
        
        // Check if we want our URLs translated
        if ($sefConfig->translateNames) {
            $jfTranslate = ', `file_id`';
        } else {
            $jfTranslate = '';
        }
        
        $query = "SELECT `file_title`$jfTranslate FROM `#__vm_product_files` WHERE `file_id` = '$id'";
        $database->setQuery($query);
        $row = $database->loadObject();
        if (!$row) {
            return null;
        }
        
        $name = ($this->params->get('fileid', '0') != '0' ? $id . '-' : '') . $row->file_title;
        return $name;
    }*/

    /**
     * Fixes some artifacts in URL
     */
    function beforeCreate(&$uri)
    {
        // remove empty category variable
        // 16.5.2014 dajo: we can't remove the virtuemart_category_id variable,
        //                 because VM remembers last open category, so we need the value 0 present
        //if ($uri->getVar('view') == 'category') {
        //    $cat = $uri->getVar('virtuemart_category_id');
        //    if ($cat == '' || $cat == '0') {
        //        $uri->delVar('virtuemart_category_id');
        //    }
        //}

        // remove redundant view
        if ($uri->getVar('view') == 'virtuemart') {
            $uri->delVar('view');
        }
        
        // try to read information from menu parameters
        if (($uri->getVar('Itemid', '')) != '') {
            $menuItem = JoomSEF::_getMenuItemInfo($uri->getVar('option'), $uri->getVar('task'), $uri->getVar('Itemid'));
            if ($menuItem && isset($menuItem->params) && is_object($menuItem->params)) {
                // get variables from params if not defined and available
                if (!$uri->getVar('page') && ($page = $menuItem->params->get('page'))) $uri->setVar('page', $page);
                if (!$uri->getVar('flypage') && ($flypage = $menuItem->params->get('flypage'))) $uri->setVar('flypage', $flypage);
                if (!$uri->getVar('category_id') && ($category_id = $menuItem->params->get('category_id'))) $uri->setVar('category_id', $category_id);
                if (!$uri->getVar('product_id') && ($product_id = $menuItem->params->get('product_id'))) $uri->setVar('product_id', $product_id);
            }
        }
            
        // set default page if not set
        $view = $uri->getVar('view');
        if (!$view) {
            $product_id = $uri->getVar('virtuemart_product_id');
            $category_id = $uri->getVar('virtuemart_category_id');
            if (isset($product_id) && $product_id) $view = 'productdetails';
            elseif (isset($category_id) && $category_id) $view = 'category';
            if ($view) $uri->setVar('view', $view);
        }
        
        if ($uri->getVar('view') == 'productdetails') {
            // Change the product id to its parent if set to
            if ($this->params->get('childtoparent', '0') == '1') {
                $prodId = $uri->getVar('virtuemart_product_id');
                if (!empty($prodId)) {
                    // Try to get the parent product
                    $parentId = $this->GetProductParent($prodId);
                    if (!empty($parentId)) {
                        $uri->setVar('virtuemart_product_id', $parentId);
                    }
                }
            }
            
            // find category id if not present
            if ($uri->getVar('virtuemart_category_id', '') == '' && $uri->getVar('virtuemart_product_id')) {
                $category_id = $this->GetProductCategoryId($uri->getVar('virtuemart_product_id'));
                $uri->setVar('virtuemart_category_id', $category_id);
            }
            
            // Handle manufacturer_id according to extension parameters
            $storeManufacturerId = ($this->params->get('store_manufacturerid', '1') == '1');
            if ($storeManufacturerId && ($uri->getVar('virtuemart_manufacturer_id', '') == '') && $uri->getVar('virtuemart_product_id')) {
                $manufacturer_id = $this->GetProductManufacturerId($uri->getVar('virtuemart_product_id'));
                if (!empty($manufacturer_id)) {
                    $uri->setVar('virtuemart_manufacturer_id', $manufacturer_id);
                }
            }
            else if (!$storeManufacturerId && ($uri->getVar('virtuemart_manufacturer_id', '') != '')) {
                $uri->delVar('virtuemart_manufacturer_id');
            }
        }
        
        // Remove the search variables (the buttons names)
        $uri->delVar('search');
        $uri->delVar('Search');
        
        // Remove the flypage variable if set to
        if ($this->params->get('flypagehandle', 'addnever') == 'remove') {
            $uri->delVar('flypage');
        }
        
        // If improved pagination caching is enabled, make sure that the URL always contains current limit and limitstart
        if ($this->params->get('improvecaching', '0') == '1') {
            if ($uri->getVar('view') == 'category' && !is_null($uri->getVar('virtuemart_category_id'))) {
                if (is_null($uri->getVar('limit'))) {
                    $limit = $this->GetCategoryLimit($uri->getVar('virtuemart_category_id'), true);
                    $uri->setVar('limit', $limit);
                }
                if (is_null($uri->getVar('limitstart'))) {
                    $app = JFactory::getApplication();
                    $str = 'com_virtuemart.categoryc'.$uri->getVar('virtuemart_category_id').'m'.$uri->getVar('virtuemart_manufacturer_id', 0).'.limitstart';
                    $limitstart = $app->getUserState($str, 0);
                    $uri->setVar('limitstart', $limitstart);
                }
            }
        }
        
        // Add limitstart to category link if set to
        if (($this->params->get('addlimitstart', '0') == '1') && ($uri->getVar('view') == 'category') && is_null($uri->getVar('limitstart'))) {
            $uri->setVar('limitstart', '0');
        }
        
        // Handle pagination?
        if ($this->params->get('pagehandle', '0') == '1') {
            $limit = $uri->getVar('limit');
            if (!is_null($limit)) {
                $limit = intval($limit);
            }
            if (!is_null($uri->getVar('limitstart')) && empty($limit) && $uri->getVar('view') == 'category'
                && !is_null($uri->getVar('virtuemart_category_id')))
            {
                $app = JFactory::getApplication();
        		$limitString = 'com_virtuemart.categoryc'.$uri->getVar('virtuemart_category_id').'.limit';
        		$limit = (int)$app->getUserStateFromRequest ($limitString, 'limit');
                
                if (empty($limit)) {
                    $limit = intval($this->params->get('pagelimit', 0));
                }
                if (empty($limit)) {
                    $limit = $this->GetCategoryLimit($uri->getVar('virtuemart_category_id'));
                }
                $uri->setVar('limit', $limit);
            }
            
            if (!is_null($uri->getVar('limit')) && (is_null($uri->getVar('limitstart')) || ($uri->getVar('limitstart') < $uri->getVar('limit')))) {
                $uri->setVar('limitstart', 0);
            }
        }
        
        // Fix limitstart if needed
        $l = $uri->getVar('limit');
        $ls = $uri->getVar('limitstart');
        if (!is_null($l) && !is_null($ls)) {
            $l = intval($l);
            $ls = intval($ls);
            if (($l > 0) && ($ls > 0) && (($ls % $l) > 0)) {
                $ls -= ($ls % $l);
                $uri->setVar('limitstart', $ls);
            }
        }
    }

    /**
     *  Tries to find the URL in database
     * Overloaded to implement ignoring multiple categories
     */
    function getSefUrlFromDatabase(&$uri)
    {
        $database = JFactory::getDBO();
        $sefConfig = SEFConfig::getConfig();
        
        $result = parent::getSefUrlFromDatabase($uri);
        
        if (($result === false)) {
            $this->params = SEFTools::getExtParams('com_virtuemart');
            
            // Ignore multiple categories?
            if ($this->params->get('ignorecats', '0') != '0') {
                
                // Extract variables
                $url = new JURI($uri->toString());
                $vars = $url->getQuery(true);
                
                // Return if page is not product_details or there is no category id
                if (! isset($vars['view']) || ($vars['view'] != 'productdetails')) {
                    return false;
                }
                
                // Remove variables
                $url->delVar('Itemid');
                if ($this->params->get('nonsefflypage', '0')) {
                    $url->delVar('flypage');
                }
                
                // Find category ID if not present
                if (empty($vars['virtuemart_category_id'])) {
                    $category_id = $this->GetProductCategoryId($vars['virtuemart_product_id']);
                    $url->setVar('virtuemart_category_id', $category_id);
                }
                
                // Fix manufacturer_id
                //if (empty($vars['manufacturer_id'])) {
                    // Get the manufacturer id and add it to non-SEF url (manufacturer id isn't present in shopping cart urls)
                //    $manufacturer_id = $this->GetProductManufacturerId($vars['product_id']);
                //    $url->setVar('manufacturer_id', $manufacturer_id);
                //}
                
                // Sort variables
                $string = JoomSEF::_uriToUrl($url);
                
                // Create regular expression
                $regexp = str_replace(array('?', '*', '+', '^', '[', ']', '.', '|', '(', ')', '{', '}', '^', '$'), array('\\?', '\\*', '\\+', '\\^', '\\[', '\\]', '\\.', '\\|', '\\(', '\\)', '\\{', '\\}', '\\^', '\\$'), $string);
                $regexp = preg_replace('/virtuemart_category_id=[^&]*/', 'virtuemart_category_id=[^&]*', $regexp);
                
                // Get ignore source parameter
                $where = '';
                $extIgnore = $this->params->get('ignoreSource', 2);
                $ignoreSource = ($extIgnore == 2 ? $sefConfig->ignoreSource : $extIgnore);
                if (! $ignoreSource && isset($vars['Itemid'])) {
                    $where = " AND `Itemid` = '" . $vars['Itemid'] . "'";
                }
                
                // Support URLs Trash?
                /*
                if (method_exists('SEFTools', 'getSEFVersion') && (version_compare('3.7.3', SEFTools::getSEFVersion()) <= 0)) {
                    $where .= " AND `trashed` = '0'";
                }
                */
                
                // Try to find the URL
                $query = "SELECT `sefurl` FROM `#__sefurls` WHERE `origurl` REGEXP '^" . addslashes($regexp) . "$'" . $where;
                $database->setQuery($query);
                $result = $database->loadresult();
                
                // Add flypage if it was removed
                if (! empty($result)) {
                    if (($this->params->get('nonsefflypage', '0')) && ! empty($vars['flypage'])) {
                        $result .= '?flypage=' . $vars['flypage'];
                    }
                }
            }
        }
        
        return $result;
    }

    function create(&$uri)
    {
        $this->should_number = false;
        
        // VirtueMart language translations.
        $sefConfig = SEFConfig::getConfig();
        
        // do not SEF admin URLs
        if (substr($uri->getPath(), 0, 10) == 'index2.php') {
            return $uri;
        }
        
        // extract variables
        $vars = $uri->getQuery(true);
        extract($vars);
        
        // reset variables
        $title = array();
        
        $this->params =& SEFTools::getExtParams('com_virtuemart');
        
        // Load the texts to use in URL
        $texts = SEFTools::getExtTexts('com_virtuemart');
        $texts = $this->checkTexts($texts);
        
        // ignore if admin mode
        if (isset($pshop_mode) && ($pshop_mode == 'admin')) {
            return $uri;
        }
        
        // don't SEF checkout URLs
        if (isset($task) && (substr($task, 0, 8) == 'checkout') && ($this->params->get('sefcheckout', '1') != '1')) {
            return $uri;
        }
        
        // don't SEF payment response URLs
        if (isset($task) && ($task == 'pluginresponsereceived')) {
            return $uri;
        }
        
        $title[] = JoomSEF::_getMenuTitle(@$option, null, @$Itemid);

        switch (@$view) {
            case 'category':
            	
                // manufacturer

            	//hm, ale ti co meli manuf zapnuty tak to maji pred kategorii. musime dat pozor pri upddatu!!!
            	//a take ti co to mali v ignoreVars...
            	
            	
            	
            	$manuFacturerTitle = false;
            	
                if (isset($virtuemart_manufacturer_id)) {
                	
                	if ($this->params->get('manufacturer_category', '1')=='0') //if not add manufacturer, must be in URL for proper filter
                		$this->ignoreVars['virtuemart_manufacturer_id'] = $uri->getVar('virtuemart_manufacturer_id');
                	                	
                    if (isset($virtuemart_category_id) && ($virtuemart_category_id != 0)) {
                        // Category with some manufacturer filter
                        if ($this->params->get('manufacturer_category', '1') != '0') {
                            $manufacturerName = $this->GetManufacturer($virtuemart_manufacturer_id, $view);
                            if (is_null($manufacturerName)) {
                                return $uri;
                            }
                            $manuFacturerTitle = $manufacturerName;
                        }
                    }
                    else {
                        // Only manufacturer - all products list
                        $manufacturerName = $this->GetManufacturer($virtuemart_manufacturer_id, $view);
                        if (is_null($manufacturerName)) {
                            return $uri;
                        }
                        $manuFacturerTitle = $manufacturerName;
                    }
                }
                

                
                if ($manuFacturerTitle AND $this->params->get('manufacturer_category', '1') == '2') //manufacturer before category
               		$title[] = $manuFacturerTitle;
                
                // listing categories
                if (isset($virtuemart_category_id) && ($virtuemart_category_id != 0)) {
                    $catNames = $this->GetCategories($virtuemart_category_id);
                    if (is_null($catNames)) {
                        // Not found, don't SEF
                        return $uri;
                    }
                    
                    if ($this->params->get('categories', '2') == '2') {
                        // All categories
                        foreach ($catNames as $cat) {
                            $title[] = $cat;
                        }
                    } else {
                        // One category
                        $title[] = @$catNames[count($catNames) - 1];
                    }
                    
                    // Meta description
                    if ($this->params->get('category_desc', '1') == '1') {
                        $this->metadesc = $this->category_desc;
                    }
                    
                    // Meta keywords
                    if ($this->params->get('meta_keys', '1') == '1') {
                        $catKeys = $this->params->get('cat_keys_src', '0');
                        if ($catKeys == '0') {
                            // Description only
                            $this->metakeySource = $this->category_desc;
                        }
                        elseif ($catKeys == '1') {
                            // Title only
                            $this->metakeySource = $this->catTitles[0];
                        }
                        elseif ($catKeys == '2') {
                            // Title and description
                            $this->metakeySource = $this->catTitles[0] . ' ' . $this->category_desc;
                        }
                    }
                    
                    // Page title
                    if (($c = $this->params->get('category_title', '0')) != '0') {
                        if ($c == '1') {
                            // One category
                            $this->pagetitle = $this->catTitles[0];
                        } else {
                            // All categories
                            $sep = ' ' . trim($this->params->get('title_sep', '/')) . ' ';
                            $this->pagetitle = implode($sep, $this->catTitles);
                        }
                    }
                }
                // listing all
                else {
                    if (isset($categorylayout) && ($categorylayout == 'categories')) {
                        $title[] = $texts['_PHPSHOP_LIST_CATEGORIES'];
                    }
                    else {
                        $title[] = $texts['_PHPSHOP_LIST_ALL_PRODUCTS'];
                    }
                }
                
                if ($manuFacturerTitle AND $this->params->get('manufacturer_category', '1') == '1') //manufacturer after category
                	$title[] = $manuFacturerTitle;
                
                // Cherry Picker support
                if (self::$isChp || self::$isChpCF) {
                    // Handle product type
                    if (isset($ptid) && !empty($ptid)) {
                        if( $this->params->get('producttype', '1') ) {
                            $type = $this->GetProductType($ptid);
                            if (is_null($type)) {
                                // Not found, don't SEF
                                return $uri;
                            }
                            $title[] = $type;
                        }
                    }
                    
                    // Loop through available Cherry Picker filters and check if they're used in URI
                    $filters = $this->GetCherryPickerFilters();
                    foreach ($filters as $filter) {
                        $val = $uri->getVar($filter);
                        if (!is_null($val)) {
                            // Parameter found
                            if ($this->params->get('producttypeparameter', '0')) {
                                // Add parameter name
                                $title[] = $filter;
                            }
                            
                            // Add parameter value
                            if (is_array($val)) {
                                $title = array_merge($title, $val);
                            }
                            else {
                                $title[] = $val;
                            }
                        }
                    }
                }
                break;
                
            case 'productdetails':
                {
                    $showCategories = (bool) $this->params->get('productcategories', '1');
                    
                    if (empty($virtuemart_category_id)) {
                        $virtuemart_category_id = $this->GetProductCategoryId($virtuemart_product_id);
                        if( !empty($virtuemart_category_id) ) {
                            $uri->setVar('virtuemart_category_id', $virtuemart_category_id);
                        }
                    }
                    
                    if (empty($virtuemart_manufacturer_id)) {
                        // Get the manufacturer id and add it to non-SEF url (manufacturer id isn't present in shopping cart urls)
                        //$manufacturer_id = $this->GetProductManufacturerId($product_id);
                        //if (is_null($manufacturer_id) && $this->params->get('stopOnNoManufacturer', 0)) {
                            // Not found, don't SEF
                        //    return $uri;
                        //}
                        //else if (is_null($manufacturer_id)) {
                        $uri->delVar('virtuemart_manufacturer_id');
                        //}
                        //else {
                        //    $uri->setVar('manufacturer_id', $manufacturer_id);
                        //}
                    }
                    
                    //$title[] = 'shop';
                    if (!empty($virtuemart_category_id)) {
                        $catNames = $this->GetCategories($virtuemart_category_id);
                        if (is_null($catNames)) {
                            // Not found, don't SEF
                            return $uri;
                        }
                    }
                    
                    if ($showCategories && !empty($catNames)) {
                        if (($c = $this->params->get('categories', '2')) != '0') {
                            if ($c == '2') {
                                // All categories
                                foreach ($catNames as $cat) {
                                    $title[] = $cat;
                                }
                            } else {
                                // One category
                                $title[] = @$catNames[count($catNames) - 1];
                            }
                        }
                    }
                    
                    // Add manufacturer before product if set to
                    if ($this->params->get('manufacturer', '0') && isset($virtuemart_manufacturer_id) && $virtuemart_manufacturer_id) {
                        $manufacturer = $this->GetManufacturer($virtuemart_manufacturer_id, $view);
                        if ($manufacturer) $title[] = $manufacturer;
                        //if (is_null($manufacturer)) {
                            // Not found, don't SEF
                            //return $uri;
                            // Not found, throw manufacturer away
							//unset($manufacturer_id);
                        //}
                        //else $title[] = $manufacturer;
                    }
                    
                    $product_name = $this->GetProductName($virtuemart_product_id);
                    if (is_null($product_name)) {
                        // Product not found, don't SEF
                        return $uri;
                    }
                    $title[] = $product_name;
                    
                    /*
                    if ($view == 'shop.getfile') {
                        if (isset($file_id)) {
                            $fileTitle = $this->GetFileTitle($file_id);
                            if( is_null($fileTitle) ) {
                                // Not found, don't SEF
                                return $uri;
                            }
                            
                            $title[] = $fileTitle;
                        }
                    }
                    
                    if ($view == 'shop.ask') {
                        $title[] = $texts['_PHPSHOP_ASK'];
                        if (isset($subject)) {
                            $title[] = html_entity_decode($subject);
                        }
                    }
                    */
                    
                    if ($view == 'productdetails') {
                        // Meta description
                        if ($this->params->get('product_desc', '2') != '0') {
                            $metadescParts = array();
                            if (!empty($this->catTitles) && ($this->params->get('product_desc_cat', '0') != '0')) {
                                // Prepend category
                                if ($this->params->get('product_desc_cat', '0') == '1') {
                                    // Only last category
                                    $metadescParts[] = $this->catTitles[0];
                                }
                                else {
                                    // All categories
                                    $metadescParts = array_reverse($this->catTitles);
                                }
                            }
                            $metadescParts[] = $this->product_desc;
                            $this->metadesc = implode(' - ', $metadescParts);
                        }
                        
                        // Meta keywords
                        if ($this->params->get('meta_keys', '1') == '1') {
                            $catKeys = $this->params->get('prod_keys_src', '0');
                            if ($catKeys == '0') {
                                // Description only
                                $this->metakeySource = $this->product_desc;
                            }
                            elseif ($catKeys == '1') {
                                // Title only
                                $this->metakeySource = $this->productTitle;
                            }
                            elseif ($catKeys == '2') {
                                // Title and description
                                $this->metakeySource = $this->productTitle . ' ' . $this->product_desc;
                            }
                        }
                        
                        // Page title
                        if (($p = $this->params->get('product_title', '0')) != '0') {
                            $page_titles = array();
                            $page_titles[] = $this->productTitle;
                            
                            if (!empty($this->catTitles)) {
                                if ($p == '2') {
                                    // Add last category
                                    $page_titles[] = $this->catTitles[0];
                                } elseif ($p == '3') {
                                    // Add all categories
                                    $page_titles = array_merge($page_titles, $this->catTitles);
                                }
                            }
                            
                            $sep = ' ' . trim($this->params->get('title_sep', '/')) . ' ';
                            $this->pagetitle = implode($sep, $page_titles);
                        }
                    }
                    
                    /*
                    if ($view == 'shop.waiting_list') {
                        $title[] = $texts['_PHPSHOP_WAITLIST'];
                    }
                    */
                    
                    if (isset($task)) {
                        switch($task) {
                            case 'askquestion':
                                $title[] = $texts['_PHPSHOP_ASK'];
                                break;
                            case 'recommend':
                                $title[] = $texts['_PHPSHOP_RECOMMEND'];
                                break;
                        }
                    }
                    
                    if (!empty($layout)) {
                        $title[] = $layout;
                    }
                    
                    break;
                }
                
            case 'manufacturer': {
                if (!isset($virtuemart_manufacturer_id)) {
                    // List of manufacturers
                    $title[] = $texts['_PHPSHOP_MANUFACTURERS'];
                }
                else {
                    // Manufacturer details
                    $name = $this->GetManufacturer($virtuemart_manufacturer_id, $view);
                    if (is_null($name)) {
                        return $uri;
                    }
                    $title[] = $name;
                }
                break;
            }
            
            case 'shoppergroup': {
                $title[] = $texts['_PHPSHOP_GROUP'];
                
                if (isset($task)) {
                    $title[] = $task;
                }
                
                if (isset($cid)) {
                    if (is_array($cid)) {
                        $title[] = $cid[0];
                    }
                    else {
                        $title[] = $cid;
                    }
                }
                
                break;
            }
            
            case 'cart':
                {
                    $sefcart = $this->params->get('sefcart', 0);
                    if (($sefcart == 2) || (($sefcart == 1) && isset($task))) {
                        // Do not SEF cart URLs
                        return $uri;
                    }
                    
                    $title[] = $texts['_PHPSHOP_CART_TITLE'];
                    
                    // 14.3.2013 dajo: cart_virtuemart_product_id is handled as non-SEF, otherwise it causes
                    //                 problems with Custom Fields of type Cart Variant
                    //if (isset($cart_virtuemart_product_id)) {
                    //    $product_name = $this->GetProductName($cart_virtuemart_product_id);
                    //    if( is_null($product_name) ) {
                    //        // Not found, don't SEF
                    //        return $uri;
                    //    }
                    //    
                    //    $title[] = $product_name;
                    //}
                    
                    if (isset($task)) {
                        switch($task) {
                            case 'edit_shipping':
                                $title[] = $texts['_PHPSHOP_EDIT_SHIPPING'];
                                break;
                            case 'editpayment':
                                $title[] = $texts['_PHPSHOP_EDIT_PAYMENT'];
                                break;
                            default:
                                $title[] = $task;
                        }
                    }
                    
                    break;
                }
            
            case 'user': {
                $title[] = $texts['_PHPSHOP_ACCOUNT'];
                
                if (isset($task)) {
                    $title[] = $task;
                }
                
                if (isset($addrtype)) {
                    $title[] = $addrtype;
                }
                
                if (isset($cid)) {
                    if (is_array($cid)) {
                        $title[] = $cid[0];
                    }
                    else {
                        $title[] = $cid;
                    }
                }
                
                if (!empty($new)) {
                    $title[] = 'New';
                }
                
                break;
            }
            
            case 'orders': {
                if ((isset($task) && ($task == 'details')) || (isset($layout) && ($layout == 'details'))) {
                    $title[] = 'Order';
                }
                else {
                    $title[] = 'Orders';
                }
                
                break;
            }
            
            case 'vendor': {
                $title[] = 'Vendors';
                
                if (isset($virtuemart_vendor_id)) {
                    $vendor = $this->GetVendor($virtuemart_vendor_id);
                    if (is_null($vendor)) {
                        return $uri;
                    }
                    $title[] = $vendor;
                    
                    // Handle layout
                    if (!empty($layout) && ($layout != 'details')) {
                        $title[] = $layout;
                    }
                }
                
                break;
            }
            
            default:
                break;
        }
        
        // Handle flypage
        if (isset($flypage)) {
        	switch($this->params->get('flypagehandle', 'addnever')) {
        		case 'addalways':
        			$title[] = $this->GetFlypageTitle($flypage);
        			break;
        		case 'addnondefault':
        			if ($flypage != $this->params->get('defaultflypage', '')) {
        				$title[] = $this->GetFlypageTitle($flypage);
        			}
        			break;
        	}
        }
        
        // Handle pagination
        if ((!empty($limitstart) || !empty($limit)) && ($this->params->get('pagehandle', '0') != '0')) {
            if (!isset($limitstart)) {
                $limitstart = 0;
            }
            if (empty($limit)) {
                // Get default limit
                $limit = self::$listLimit;
            }
            if ($limit == 0) {
                $limit = 20;
            }
            
            // Only add page if limit is different than default
            $defLimit = 20;
            if (!is_null($uri->getVar('virtuemart_category_id'))) {
                $defLimit = $this->GetCategoryLimit($uri->getVar('virtuemart_category_id'));
            }
            $page = intval($limitstart / $limit) + 1;
            if ($page > 1 || $limit != $defLimit) {
                if ($this->params->get('pagehandle', '0') == '1') {
                    $title[] = $page;
                    $title[] = $limit;
                }
                else {
                    $title[count($title) - 1] .= '-'.$page.'-'.$limit;
                }
            }
        }
        
        // Handle format
        if (isset($format)) {
            $title[] = $format;
        }
        
        $newUri = $uri;
        if (count($title) > 0) {           
            // non-sef vars
            $this->_createNonSefVars($uri);
                
            // Meta tags
            $meta = $this->getMetaTags();
            if (! empty($this->pagetitle)) {
                $meta['metatitle'] = str_replace('"', '&quot;', $this->pagetitle);
            }
            
            $priority = $this->getPriority($uri);
            $sitemap = $this->getSitemapParams($uri);
            
            // Should we number URLs?
            if ($this->should_number) {
                $oldNumber = $this->params->get('numberDuplicates', '2');
                $this->params->set('numberDuplicates', '1');
            }
            
            // Store the new URL
            $newUri = JoomSEF::_sefGetLocation($uri, $title, null, @$limit,  @$limitstart, @$lang, $this->nonSefVars, $this->ignoreVars, $meta, $priority, true, null, $sitemap);
            
            // Set duplicates numbering back to original
            if (isset($oldNumber)) {
                $this->params->set('numberDuplicates', $oldNumber);
            }
        }
        
        return $newUri;
    }

    function GetFlypageTitle($flypage)
    {
    	$title = str_replace('.tpl', '', $flypage);
    	$title = str_replace('flypage', '', $title);
    	$title = trim($title, '_.-');
    	
    	return $title;
    }
    
    function getSitemapParams(&$uri)
    {
        if ($uri->getVar('format', 'html') != 'html') {
            // Handle only html links
            return array();
        }
        
        $view = $uri->getVar('view');
        
        $sm = array();
        switch ($view)
        {
            case 'category':
            case 'manufacturer':
            case 'productdetails':
                if ($view == 'productdetails') $view = 'product';
                
                $indexed = $this->params->get('sm_'.$view.'_indexed', '1');
                $freq = $this->params->get('sm_'.$view.'_freq', '');
                $priority = $this->params->get('sm_'.$view.'_priority', '');
                
                if (!empty($indexed)) $sm['indexed'] = $indexed;
                if (!empty($freq)) $sm['frequency'] = $freq;
                if (!empty($priority)) $sm['priority'] = $priority;
                
                break;
        }
        
        return $sm;
    }

    function getPriority(&$uri)
    {
        $itemid = $uri->getVar('Itemid');
        $page = $uri->getVar('view');
        
        switch($page)
        {
            case 'productdetails':
                if( is_null($itemid) ) {
                    return _COM_SEF_PRIORITY_VIRTUEMART_PRODUCT;
                } else {
                    return _COM_SEF_PRIORITY_VIRTUEMART_PRODUCT_ITEMID;
                }
                break;
                
            case 'category':
            case 'manufacturer':
                if( is_null($itemid) ) {
                    return _COM_SEF_PRIORITY_VIRTUEMART_CATEGORY;
                } else {
                    return _COM_SEF_PRIORITY_VIRTUEMART_CATEGORY_ITEMID;
                }
                break;
                
            default:
                return null;
        }
    }
    
    function checkTexts($texts)
    {
    	if (!isset($texts['_PHPSHOP_LIST_ALL_PRODUCTS']))	$texts['_PHPSHOP_LIST_ALL_PRODUCTS'] 	= 'List All Products'; 
    	if (!isset($texts['_PHPSHOP_LIST_CATEGORIES']))	    $texts['_PHPSHOP_LIST_CATEGORIES'] 	    = 'List Categories';
    	if (!isset($texts['_PHPSHOP_DOWNLOADS_TITLE']))		$texts['_PHPSHOP_DOWNLOADS_TITLE'] 		= 'Download Area';
    	if (!isset($texts['_PHPSHOP_ADVANCED_SEARCH']))		$texts['_PHPSHOP_ADVANCED_SEARCH'] 		= 'Advanced Search';
    	if (!isset($texts['_PHPSHOP_CART_TITLE']))			$texts['_PHPSHOP_CART_TITLE'] 			= 'Cart';
    	if (!isset($texts['_PHPSHOP_REGISTER_TITLE']))		$texts['_PHPSHOP_REGISTER_TITLE'] 		= 'Register';
    	if (!isset($texts['_PHPSHOP_FEED']))				$texts['_PHPSHOP_FEED'] 				= 'Feed';
    	if (!isset($texts['_PHPSHOP_PARAMETER_SEARCH']))	$texts['_PHPSHOP_PARAMETER_SEARCH'] 	= 'Parameter Search';
    	if (!isset($texts['_PHPSHOP_ASK']))					$texts['_PHPSHOP_ASK'] 					= 'Ask';
        if (!isset($texts['_PHPSHOP_RECOMMEND']))			$texts['_PHPSHOP_RECOMMEND'] 			= 'Recommend';
    	if (!isset($texts['_PHPSHOP_REGISTER_TOS']))		$texts['_PHPSHOP_REGISTER_TOS'] 		= 'Terms of Service';
    	if (!isset($texts['_PHPSHOP_SAVED_CART']))			$texts['_PHPSHOP_SAVED_CART'] 			= 'Saved Cart';
    	if (!isset($texts['_PHPSHOP_CHECKOUT']))			$texts['_PHPSHOP_CHECKOUT'] 			= 'Checkout';
    	if (!isset($texts['_PHPSHOP_RESULT']))				$texts['_PHPSHOP_RESULT'] 				= 'Result';
    	if (!isset($texts['_PHPSHOP_ACCOUNT']))				$texts['_PHPSHOP_ACCOUNT'] 				= 'Account';
    	if (!isset($texts['_PHPSHOP_SHIPPING']))			$texts['_PHPSHOP_SHIPPING'] 			= 'Shipping';
        if (!isset($texts['_PHPSHOP_EDIT_SHIPPING']))		$texts['_PHPSHOP_EDIT_SHIPPING'] 		= 'Edit Shipping';
        if (!isset($texts['_PHPSHOP_EDIT_PAYMENT']))		$texts['_PHPSHOP_EDIT_PAYMENT'] 		= 'Edit Payment';
    	if (!isset($texts['_PHPSHOP_PRODUCT']))				$texts['_PHPSHOP_PRODUCT'] 				= 'Product';
    	if (!isset($texts['_PHPSHOP_ORDER']))				$texts['_PHPSHOP_ORDER'] 				= 'Order';
    	if (!isset($texts['_PHPSHOP_FORM']))				$texts['_PHPSHOP_FORM'] 				= 'Form';
    	if (!isset($texts['_PHPSHOP_WAITLIST']))            $texts['_PHPSHOP_WAITLIST']             = 'Waiting List';
        if (!isset($texts['_PHPSHOP_GROUP']))               $texts['_PHPSHOP_GROUP']                = 'Group';
        if (!isset($texts['_PHPSHOP_MANUFACTURERS']))       $texts['_PHPSHOP_MANUFACTURERS']        = 'Manufacturers';
    	
    	return $texts;
    }
    
}
?>