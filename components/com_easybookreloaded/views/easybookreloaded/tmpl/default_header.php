<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel <admin@kubik-rubik.de>
 * Projectsite: http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
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
defined('_JEXEC') or die('Restricted access');
?>

<?php if(EASYBOOK_CANADD AND !$this->params->get('offline')) : ?>
    <div class="sign">
        <strong>
            <a class="sign" href="<?php echo JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add'); ?>" title="<?php echo JText::_('COM_EASYBOOKRELOADED_SIGN_GUESTBOOK'); ?>">
                <button class="btn btn-success">
                    <span class="icon-new icon-white"></span><?php echo JText::_('COM_EASYBOOKRELOADED_SIGN_GUESTBOOK'); ?>
                </button>
            </a>
        </strong>
    </div>
<?php endif; ?>
<?php if($this->params->get('show_introtext')) : ?>
	<div class="easy_intro">
		<?php if($this->params->get('show_introtext') == 1) : ?>
			<?php echo nl2br($this->params->get('introtext')); ?>
		<?php elseif($this->params->get('show_introtext') == 2) : ?>
			<?php echo JTEXT::_('COM_EASYBOOKRELOADED_INTROTEXT'); ?>
		<?php elseif($this->params->get('show_introtext') == 3) : ?>
			<?php echo htmlspecialchars_decode($this->gb_data->introtext); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>