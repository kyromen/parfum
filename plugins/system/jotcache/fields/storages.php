<?php
/*
 * @version 5.1.0
 * @package JotCache
 * @category Joomla 3.x
 * @copyright (C) 2010-2015 Vladimir Kanich
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;
class JFormFieldStorages extends JFormField {
protected $type = 'Storages';
protected $display = null;
public function __construct() {
parent::__construct();
}protected function getInput() {
ob_start();
$storageTypes = array('file', 'memcache', 'memcached');
if (!is_array($this->value)) {
$this->value = array();
$this->value['type'] = 'file';
}?>
    <select name="jform[params][storage][type]" id="jform_params_storage" class="chzn-color-state" size="1">
      <?php
      foreach ($storageTypes as $type) {
$exists = true;
if ($type == 'memcache' && (extension_loaded('memcache') && class_exists('Memcache')) != true) {
$exists = false;
}if ($type == 'memcached' && (extension_loaded('memcached') && class_exists('Memcached')) != true) {
$exists = false;
}if ($exists) {
$selected = ($type == $this->value['type']) ? ' selected="selected"' : '';
echo '<option' . $selected . ' value="' . $type . '">' . ucfirst($type) . '</option>';
}}$show = false;
if ($this->value['type'] == 'memcache' || $this->value['type'] == 'memcached') {
$show = true;
}$this->display = $show ? '' : ' style="display:none;"';
?>
    </select>
    </div>
    </div>
    <div class="control-group"<?php echo $this->display; ?>><div class="control-label"><span class="spacer"><span class="before"></span><span><label class="" id="jform_params_spacer-lbl"><b><?php echo JText::_('JOTCACHE_MEMCACHED_SPACER'); ?></b></label></span><span class="after"></span></span></div><div class="controls">          <?php
        echo $this->showOptionNoYes('persistent', 1);
echo $this->showOptionNoYes('mcompress', 0);
echo $this->showTextInput('host', 'localhost');
echo $this->showTextInput('port', 11211);
$ret = ob_get_contents();
ob_end_clean();
return $ret;
}protected function showOptionNoYes($optionName, $defaultValue) {
if (!isset($this->value[$optionName])) {
$this->value[$optionName] = $defaultValue;
}$option = '</div></div><div class="control-group"' . $this->display . '>';
$prefix = 'JOTCACHE_' . strtoupper($optionName);
$option.='<div class="control-label"><label title="" class="hasTooltip" for="jform_params_' . $optionName . '" id="jform_params_' . $optionName . '-lbl" data-original-title="&lt;strong&gt;' . JText::_($prefix . '_LBL') . '&lt;/strong&gt;&lt;br /&gt;' . JText::_($prefix . '_DESC') . '">' . JText::_($prefix . '_LBL') . '</label></div>';
$option.='<div class = "controls"><fieldset class = "radio btn-group btn-group-yesno" id = "jform_params_' . $optionName . '">';
$labels = array(JText::_('No'), JText::_('Yes'));
for ($i = 0; $i < 2; $i++) {
$checked = ($i === (int) $this->value[$optionName]) ? ' checked = "checked"' : '';
$option.='<input type = "radio"' . $checked . ' value = "' . $i . '" name = "jform[params][storage][' . $optionName . ']" id = "jform_params_' . $optionName . $i . '"><label for = "jform_params_' . $optionName . $i . '" class = "btn">' . $labels[$i] . '</label>';
}$option.='</fieldset>';
return $option;
}protected function showTextInput($inputName, $defaultValue) {
if (!isset($this->value[$inputName]) || $this->value[$inputName] == '') {
$this->value[$inputName] = $defaultValue;
}$script = '</div></div><div class="control-group"' . $this->display . '>';
$prefix = 'JOTCACHE_' . strtoupper($inputName);
$script.='<div class="control-label"><label title="" class="hasTooltip" for="jform_params_' . $inputName . '" id="jform_params_' . $inputName . '-lbl" data-original-title="&lt;strong&gt;' . JText::_($prefix . '_LBL') . '&lt;/strong&gt;&lt;br /&gt;' . JText::_($prefix . '_DESC') . '">' . JText::_($prefix . '_LBL') . '</label></div><div class="controls"><input type="text" value="' . $this->value[$inputName] . '" id="jform_params_' . $inputName . '" name="jform[params][storage][' . $inputName . ']">';
return $script;
}}