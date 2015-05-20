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

class JFormFieldEbrGb extends JFormField
{
    protected $type = 'EbrGb';

    protected function getInput()
    {
        $db = JFactory::getDBO();

        $query = "SELECT ".$db->quoteName('a.title')." AS ".$db->quoteName('title').", ".$db->quoteName('a.id')." AS ".$db->quoteName('gbid')." FROM ".$db->quoteName('#__easybook_gb')." AS ".$db->quoteName('a')." ORDER BY ".$db->quoteName('a.id')." ASC";

        $db->setQuery($query);
        $guestbooks = $db->loadObjectList();

        array_unshift($guestbooks, JHTML::_('select.option', '', '- '.JText::_('COM_EASYBOOKRELOADED_SELECT_GUESTBOOK').' -', 'gbid', 'title'));

        return JHTML::_('select.genericlist', $guestbooks, $this->name, 'class="inputbox required"', 'gbid', 'title', $this->value, $this->id);
    }
}
