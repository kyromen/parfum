<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;
class MainControllerAjax extends JControllerLegacy {
public function __construct($config = array()) {
parent::__construct($config);
}function status() {
$model = $this->getModel('recache');
$flag = $this->input->getWord('flag', '');
    if ($flag == 'stop') {
$model->controlRecache(0);
} else {
$plugin = strtolower($this->input->getWord('plugin'));
include JPATH_PLUGINS . '/jotcacheplugins/' . $plugin . '/' . $plugin . '_status.php';
}}}?>
