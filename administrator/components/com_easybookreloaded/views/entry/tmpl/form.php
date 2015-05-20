<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3
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
JHtml::_('formbehavior.chosen', 'select');
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
                    <li class="active"><a href="#entry" data-toggle="tab"><?php echo JText::_('COM_EASYBOOKRELOADED_DETAILS'); ?></a></li>
                    <li><a href="#details" data-toggle="tab"><?php echo JText:: _('COM_EASYBOOKRELOADED_CONTACT_DETAILS'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="entry">
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbid">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_GB'); ?>
                                    <span class="small">*</span>
                                </label>
                            </div>
                            <div class="controls">
                                <select name="gbid" class="inputbox">
                                    <?php echo JHtml::_('select.options', $this->guestbooks, 'id', 'title', $this->entry->gbid); ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbname" class="required">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_AUTHOR'); ?>
                                    <span class="star">*</span>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area required" type="text" name="gbname" id="gbname" size="32" value="<?php echo $this->entry->gbname; ?>" aria-required="true" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbmail" class="required">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_EMAIL'); ?>
                                    <span class="star">*</span>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area required" type="text" name="gbmail" id="gbmail" size="32" value="<?php echo $this->entry->gbmail; ?>" aria-required="true" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbmailshow">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_SHOW_EMAIL'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <fieldset id="gbmailshow" class="radio btn-group">
                                    <input type="radio" name="gbmailshow" id="gbmailshow_yes" value="1"
                                    <?php if($this->entry->gbmailshow) : ?>
                                        <?php echo "checked='checked'"; ?>
                                    <?php endif; ?>
                                    />
                                    <label class="btn" for="gbmailshow_yes"><?php echo JText::_('JYES'); ?></label>
                                    <input type="radio" name="gbmailshow" id="gbmailshow_no" value="0"
                                    <?php if(!$this->entry->gbmailshow) : ?>
                                       <?php echo "checked='checked'"; ?>
                                    <?php endif; ?>
                                    />
                                    <label class="btn" for="gbmailshow_no"><?php echo JText::_('JNO'); ?></label>
                                </fieldset>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbdate">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_DATE'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <?php echo JHTML::_('calendar', $this->entry->gbdate, 'gbdate', 'gbdate', '%Y-%m-%d %H:%M:%S', array('class' => 'text_area', 'size' => '32', 'maxlength' => '19')); ?>
                            </div>
                        </div>
                        <?php if($this->params->get('show_rating', true)) : ?>
                            <div class="control-group">
                                <label class="control-label" for="gbvote"><?php echo JTEXT::_('COM_EASYBOOKRELOADED_WEBSITE_RATING'); ?></label>
                                <div class="controls">
                                    <input type="hidden" name="gbvote" value="0" />
                                    <?php for($i = 1; $i <= $this->params->get('rating_max', 5); $i++) : ?>
                                        <?php if((isset($this->entry->gbvote)) AND ($i == $this->entry->gbvote)) : ?>
                                            <input type="radio" name="gbvote" value="<?php echo $i; ?>" checked="checked">
                                        <?php else : ?>
                                            <input type="radio" name="gbvote" value="<?php echo $i; ?>">
                                        <?php endif; ?>
                                        <?php echo $i; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <input type="hidden" name="gbvote" value="0" />
                        <?php endif; ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbtitle">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_TITLE'); ?>
                                    <?php if($this->params->get('require_title', true)) :?>
                                        <span class="small">*</span>
                                    <?php endif; ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbtitle" id="gbtitle" size="32" value="<?php echo $this->entry->gbtitle; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbtext" class="required">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_ENTRY'); ?>
                                    <span class="star">*</span>
                                </label>
                            </div>
                            <div class="controls">
                                <textarea class="text_area required" rows="10" cols="60" id="gbtext" name="gbtext" aria-required="true"><?php echo $this->entry->gbtext; ?></textarea>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbcomment">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_COMMENT'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <textarea class="text_area" rows="6" cols="60" id="gbcomment" name="gbcomment"><?php echo $this->entry->gbcomment; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="details">
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbpage">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_HOMEPAGE'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbpage" id="gbpage" size="32" value="<?php echo $this->entry->gbpage; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbloca">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_LOCATION'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbloca" id="gbloca" size="32" value="<?php echo $this->entry->gbloca; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbicq">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_ICQ'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbicq" id="gbicq" size="32" value="<?php echo $this->entry->gbicq; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbaim">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_AIM'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbaim" id="gbaim" size="32" value="<?php echo $this->entry->gbaim; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbmsn">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_MSN'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbmsn" id="gbmsn" size="32" value="<?php echo $this->entry->gbmsn; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbyah">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_YAHOO'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbyah" id="gbyah" size="32" value="<?php echo $this->entry->gbyah; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbskype">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_SKYPE'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbskype" id="gbskype" size="32" value="<?php echo $this->entry->gbskype; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label for="gbip">
                                    <?php echo JText::_('COM_EASYBOOKRELOADED_VISITOR_IP'); ?>
                                </label>
                            </div>
                            <div class="controls">
                                <input class="text_area" type="text" name="gbip" id="gbip" size="32" value="<?php echo $this->entry->gbip; ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <input type="hidden" name="option" value="com_easybookreloaded" />
    <input type="hidden" name="id" value="<?php echo $this->entry->id; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="entry" />
    <input type="hidden" name="url_current" value="<?php echo JUri::getInstance()->getQuery(); ?>" />
    <?php echo JHTML::_('form.token'); ?>
</form>
<div class="clr"></div>
<div style="text-align: center;">
    <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', EASYBOOK_VERSION) ?></p>
</div>
<?php echo $this->donation_code_message; ?>