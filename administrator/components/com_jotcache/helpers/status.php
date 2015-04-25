<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('JPATH_PLATFORM') or die;
class JToolbarButtonStatus extends JToolbarButton {
protected $_name = 'Status';
public function fetchButton($type = 'Status', $name = '', $text = '', $title = '', $link = '') {
$htm = '<a href="' . $link . '" target="_blank"><button class="btn btn-small hasTooltip" data-original-title="' . $title . '">' .
'<span>' . $text . '</span></button></a>';
return $htm;
}public function fetchId($type = 'Status', $name = '', $text = '', $task = '', $list = true, $hideMenu = false) {
return $this->_parent->getName() . '-' . $name;
}}