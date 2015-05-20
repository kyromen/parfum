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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
?>

<form action="<?php echo JRoute::_('index.php?option=com_easybookreloaded'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if(!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else : ?>
        <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_EASYBOOKRELOADED_FILTERSEARCH'); ?></label>
                    <input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_EASYBOOKRELOADED_FILTERSEARCH'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_EASYBOOKRELOADED_FILTERSEARCH'); ?>" />
                </div>
                <div class="btn-group pull-left">
                    <button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button class="btn tip hasTooltip" type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
                </div>
                <div class="btn-group pull-right">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right">
                    <select name="filter_gb_id" class="inputbox" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('COM_EASYBOOKRELOADED_SELECT_GUESTBOOKS'); ?></option>
                        <?php echo JHtml::_('select.options', $this->guestbooks, 'id', 'title', $this->state->get('filter.gb_id')); ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"> </div>
            <div id="editcell">
                <table id="articleList" class="table table-striped">
                    <thead>
                        <tr>
                            <th width="20">
                                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                            </th>
                            <th width="2%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_PUBLISHED'); ?>
                            </th>
                            <th width="6%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_AUTHOR'); ?>
                            </th>
                            <th width="12%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_TITLE'); ?>
                            </th>
                            <th>
                                <?php echo JText::_('COM_EASYBOOKRELOADED_ENTRY'); ?>
                            </th>
                            <th width="15%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_COMMENT'); ?>
                            </th>
                            <th width="16%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_DATE'); ?>
                            </th>
                            <th width="20">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_RATING'); ?>
                            </th>
                            <th width="14%">
                                <?php echo JText::_('COM_EASYBOOKRELOADED_GB'); ?>
                            </th>
                        </tr>
                    </thead>
                    <?php $k = 0; ?>
                    <?php $n = count($this->items); ?>
                    <?php for($i = 0; $i < $n; $i++) : ?>
                        <?php $row = $this->items[$i]; ?>
                        <?php $checked = JHTML::_('grid.id', $i, $row->id); ?>
                        <?php $published = JHTML::_('jgrid.published', $row->published, $i); ?>
                        <?php $link = JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=edit&cid[]='.$row->id); ?>
                        <tr class="<?php echo "row$k"; ?>">
                            <td>
                                <?php echo $checked; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php echo $published; ?>
                            </td>
                            <td>
                                <span class="hasTooltip" title="<?php echo $row->gbname ?>">
                                    <?php if(strlen($row->gbname) > 16) : ?>
                                        <?php echo substr($row->gbname, 0, 16)."..."; ?>
                                    <?php else : ?>
                                        <?php echo $row->gbname; ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="hasTooltip" title="<?php echo $row->gbtitle ?>">
                                    <?php if(strlen($row->gbtitle) > 30) : ?>
                                        <?php echo substr($row->gbtitle, 0, 30)."..."; ?>
                                    <?php else : ?>
                                        <?php echo $row->gbtitle; ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="hasTooltip" title="<?php echo $row->gbtext ?>">
                                    <a href="<?php echo $link ?>">
                                        <?php if(strlen(htmlspecialchars_decode($row->gbtext)) > 150) : ?>
                                            <?php echo htmlspecialchars(mb_substr(htmlspecialchars_decode($row->gbtext, ENT_QUOTES), 0, 150))."..."; ?>
                                        <?php else : ?>
                                            <?php echo $row->gbtext; ?>
                                        <?php endif; ?>
                                    </a>
                                </span>
                            </td>
                            <td>
                                <?php if($row->gbcomment) : ?>
                                    <span class="hasTooltip" title="<?php echo $row->gbcomment ?>">
                                    <?php if(strlen(htmlspecialchars_decode($row->gbcomment)) > 60) : ?>
                                        <?php echo htmlspecialchars(mb_substr(htmlspecialchars_decode($row->gbcomment, ENT_QUOTES), 0, 60))."..."; ?>
                                    <?php else : ?>
                                        <?php echo $row->gbcomment; ?>
                                    <?php endif; ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo JHTML::_('date', $row->gbdate, JText::_('DATE_FORMAT_LC2')); ?>
                            </td>
                            <td style="text-align: center;">
                                <?php echo $row->gbvote; ?>
                            </td>
                            <td>
                                <span class="hasTooltip" title="<?php echo $row->gbid_title ?>">
                                    <?php if(strlen(htmlspecialchars_decode($row->gbid_title)) > 165) : ?>
                                        <?php echo htmlspecialchars(mb_substr(htmlspecialchars_decode($row->gbid_title, ENT_QUOTES), 0, 60))."..."; ?>
                                    <?php else : ?>
                                        <?php echo $row->gbid_title; ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                        </tr>
                        <?php $k = 1 - $k; ?>
                    <?php endfor; ?>
                    <tfoot>
                    <tr>
                        <td colspan="9">
                            <?php echo $this->pagination->getListFooter(); ?>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <input type="hidden" name="option" value="com_easybookreloaded" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="controller" value="entry" />
            <?php echo JHTML::_('form.token'); ?>
            <div style="text-align: center;">
                <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', EASYBOOK_VERSION) ?></p>
            </div>
            <?php echo $this->donation_code_message; ?>
        </div>
</form>
