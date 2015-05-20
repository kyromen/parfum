<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license	GNU General Public License version 2
 */
defined('JPATH_BASE') or die;
class JotcacheStorage{
public $fname;
public $options;
public $params;
protected $_id;
protected $_application;
protected $_hash;
protected $_language;
protected $_root;
protected $_now;
protected $_lifetime;
protected $_group;
protected $_locking;
public function __construct($options = array(),$params) {
    $this->options = $options;
$this->params = $params;
$config = JFactory::getConfig();
    $this->_root = $config->get('cache_path', JPATH_ROOT.'/cache');
    $this->_language = $config->get('language', 'en-GB');
$this->options['language']= $this->_language;
    $this->_hash = $config->get('secret');
$this->_group = $options['defaultgroup'];
$this->_locking = (isset($options['locking'])) ? $options['locking'] : true;
$this->_lifetime = (isset($options['lifetime'])) ? $options['lifetime'] : null;
$this->_now = (isset($options['now'])) ? $options['now'] : time();
if (empty($this->_lifetime)) {
$this->_lifetime = 60;
}$this->_id = md5($options['uri'].'-'.$options['browser'].$options['cookies']);
    $this->fname = md5($this->_application.'-'.$this->_id.'-'.$this->_hash.'-'.$this->_language);
}}