<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.4
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license GNU General Public License version 2 or later
 */
defined('JPATH_PLATFORM') or die;
class JToolbarButtonSelector extends JToolbarButton {
protected $_name = 'Selector';
public function fetchButton($type = 'Selector', $name = '', $value = '', $link = '') {
$selected = array('', '', '');
if ($value > 2 || $value < 0) {
$value = 0;
}$selected[$value] = 'selected';
$htm = '<div style="position:absolute;top:-8px;"><form action="' . $link . '" method="post" name="frontForm" id="frontForm"><select name="' . $name . '" id="' . $name . '" onchange="this.form.submit()"><option value="0" ' . $selected[0] . '>Normal</option><option value="1" ' . $selected[1] . '>Mark</option><option value="2" ' . $selected[2] . '>Renew</option></select></form></div>';
return $htm;
}public function fetchId($type = 'Selector', $name = '', $text = '', $task = '', $list = true, $hideMenu = false) {
return $this->_parent->getName() . '-' . $name;
}}