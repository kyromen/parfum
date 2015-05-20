<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: https://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') OR die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'cancel' || document.formvalidator.isValid(document.id('easybook-form')))
        {
            Joomla.submitform(task, document.getElementById('easybook-form'));
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_easybookreloaded'); ?>" method="post" name="adminForm" id="easybook-form" class="form-validate form-horizontal">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#badword" data-toggle="tab"><?php echo JText::_('COM_EASYBOOKRELOADED_DETAILS'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="badword">
                        <div class="control-group">
                            <div class="control-label">
                                <label for="word" class="required">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_WORD'); ?>
                                    <span class="star">*</span>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area required" type="text" name="word" id="word" size="32" value="<?php echo $this->badword->word; ?>" aria-required="true" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <input type="hidden" name="option" value="com_easybookreloaded" />
    <input type="hidden" name="id" value="<?php echo $this->badword->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="badwords" />
    <input type="hidden" name="url_current" value="<?php echo JUri::getInstance()->getQuery(); ?>" />
    <?php echo JHTML::_('form.token'); ?>
</form>
<div style="text-align: center;">
    <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', EASYBOOK_VERSION) ?></p>
</div>
<?php echo $this->donation_code_message; ?>