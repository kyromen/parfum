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
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>
        <div class="clearfix"></div>
        <div id="editcell">
            <table id="articleList" class="table table-striped">
                <thead>
                    <tr>
                        <th width="20">
                            <input type="checkbox" name="checkall-toggle" value=""
                                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                                   onclick="Joomla.checkAll(this)"/>
                        </th>
                        <th width="10%">
                            <?php echo JText::_('COM_EASYBOOKRELOADED_GB_ID'); ?>
                        </th>
                        <th width="20%">
                            <?php echo JText::_('COM_EASYBOOKRELOADED_GB_TITLE'); ?>
                        </th>
                        <th>
                            <?php echo JText::_('COM_EASYBOOKRELOADED_GB_INTROTEXT'); ?>
                        </th>
                    </tr>
                </thead>
                <?php $k = 0; ?>
                <?php $n = count($this->items); ?>
                <?php for($i = 0; $i < $n; $i++) : ?>
                    <?php $row = $this->items[$i]; ?>
                    <?php $checked = JHTML::_('grid.id', $i, $row->id); ?>
                    <?php $link = JRoute::_('index.php?option=com_easybookreloaded&controller=entrygb&task=edit&cid[]='.$row->id); ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td>
                            <?php echo $checked; ?>
                        </td>
                        <td>
                            <?php echo $row->id; ?>
                        </td>
                        <td>
                            <span class="hasTooltip" title="<?php echo $row->title ?>">
                                <a href="<?php echo $link ?>">
                                    <?php if(strlen($row->title) > 45) : ?>
                                        <?php echo mb_substr($row->title, 0, 45)."..."; ?>
                                    <?php else : ?>
                                        <?php echo $row->title; ?>
                                    <?php endif; ?>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class="hasTooltip" title="<?php echo $row->introtext ?>">
                                <?php $intro_text = strip_tags(htmlspecialchars_decode($row->introtext)); ?>
                                <?php if(strlen($intro_text) > 165) : ?>
                                    <?php echo mb_substr($intro_text, 0, 165)."..."; ?>
                                <?php else : ?>
                                    <?php echo $intro_text; ?>
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                    <?php $k = 1 - $k; ?>
                <?php endfor; ?>
                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <input type="hidden" name="option" value="com_easybookreloaded"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="controller" value="entrygb"/>
        <?php echo JHTML::_('form.token'); ?>
</form>
<div style="text-align: center;">
    <p><?php echo JText::sprintf('COM_EASYBOOKRELOADED_VERSION', EASYBOOK_VERSION) ?></p>
</div>
<?php echo $this->donation_code_message; ?>