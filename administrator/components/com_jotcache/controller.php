<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;
class MainController extends JControllerLegacy {
protected $model;
public function __construct($config = array()) {
parent::__construct($config);
$this->registerTask('apply', 'save');
$this->registerTask('tplapply', 'tplsave');
$this->registerTask('bcapply', 'bcsave');
$this->model = $this->getModel();
}function display($cachable = false, $urlparams = false) {
parent::display();
}function refresh() {
$this->model->refresh();
parent::display();
}function mark() {
$markid = $this->input->getInt('markid');
$line = "option=com_jotcache&view=main";
$this->model->resetMark();
$uri = Juri::getInstance();
    $domain = $uri->toString(array('host'));
$parts = explode('.', $domain);
$last = count($parts) - 1;
    if ($last >= 1 && is_numeric($parts[$last]) === false) {
$domain = $parts[$last - 1] . '.' . $parts[$last];
}    switch ($markid) {
case 0:
setcookie('jotcachemark', '0', '0', '/', $domain);
$this->setRedirect('index.php?' . $line . "&filter_mark=", JText::_('JOTCACHE_RS_MSG_RESET'));
break;
case 1:
setcookie('jotcachemark', '1', '0', '/', $domain);
$this->setRedirect('index.php?' . $line, JText::_('JOTCACHE_RS_MSG_SET'));
break;
case 2:
setcookie('jotcachemark', '2', '0', '/', $domain);
$this->setRedirect('index.php?' . $line, JText::_('JOTCACHE_RS_MSG_RENEW'));
break;
default:
break;
}}function renew() {
$token = $this->input->getCmd('token', '');
if (strlen($token) == 32) {
$this->model->renew($token);
$url = $_SERVER['HTTP_REFERER'];
$this->setRedirect($url);
}}function delete() {
$this->model->delete();
$this->setRedirect('index.php?option=com_jotcache&view=main', JText::_('JOTCACHE_RS_DEL'));
}function deleteall() {
$this->model->deleteall();
$this->setRedirect('index.php?option=com_jotcache&view=main', JText::_('JOTCACHE_RS_DEL'));
}function exclude() {
$view = $this->getView('Main', 'html');
$view->setModel($this->model);
$view->exclude();
}function tplex() {
$view = $this->getView('Main', 'html');
$view->setModel($this->model);
$view->tplex();
}function bcache() {
$view = $this->getView('Main', 'html');
$view->setModel($this->model);
$view->bcache();
}function debug() {
$view = $this->getView('Main', 'html');
$view->setModel($this->model);
$view->debug();
}function save() {
        if (version_compare(JVERSION, '3.2') >= 0) {
$post = $this->input->post->getArray();
} else {
$post = $_POST;
}$cid = $this->input->post->get('cid', array(0), 'array');
JArrayHelper::toInteger($cid, array(0));
if ($this->model->store($post, $cid)) {
$msg = JText::_('JOTCACHE_EXCLUDE_SAVE');
}if ($this->getTask() == 'save') {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=refresh', $msg);
} else {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=exclude', $msg);
}}function tplsave() {
        if (version_compare(JVERSION, '3.2') >= 0) {
$post = $this->input->post->getArray();
} else {
$post = $_POST;
}$cids = $this->input->post->get('cid', array(0), 'array');
    $cids = array_map(function($src) {
return JFilterInput::getInstance(null, null, 1, 1)->clean($src, 'CMD');
}, $cids);
$tpl_id = $this->model->tplstore($post, $cids);
if ($tpl_id > 0) {
$msg = JText::_('JOTCACHE_TPLEX_SAVE');
}if ($this->getTask() == 'tplsave') {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=display&sel_id=' . $tpl_id, $msg);
} else {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=tplex&sel_id=' . $tpl_id, $msg);
}}function bcsave() {
$post = $this->input->post->getArray();
if ($this->model->bcstore($post)) {
$msg = JText::_('JOTCACHE_EXCLUDE_SAVE');
}if ($this->getTask() == 'bcsave') {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=display', $msg);
} else {
$this->setRedirect('index.php?option=com_jotcache&view=main&task=bcache', $msg);
}}function bcdelete() {
if ($this->model->bcdelete()) {
$msg = JText::_('JOTCACHE_RS_DEL');
}$this->setRedirect('index.php?option=com_jotcache&view=main&task=bcache', $msg);
}}?>