<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;
class MainControllerRecache extends JControllerLegacy {
protected $model;
public function __construct($config = array()) {
parent::__construct($config);
$this->model = $this->getModel('recache');
}function display($cachable = false, $urlparams = false) {
$view = $this->getView('recache', 'html');
$view->setModel($this->model, true);
$cids = $this->input->get('cid', array(0), 'array');
if (count($cids) > 0) {
$this->model->flagRecache($cids);
}$view->display();
}function close() {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=display');
}function start() {
JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
$this->model->runRecache();
$this->model->controlRecache(0);
$view = $this->getView('recache', 'html');
$view->stopRecache();
}function stop() {
$this->model->controlRecache(0);
}}?>