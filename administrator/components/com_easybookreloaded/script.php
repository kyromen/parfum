<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: http://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
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

class Com_EasybookReloadedInstallerScript
{
    function install($parent)
    {
        // Not needed at the moment
    }

    function uninstall($parent)
    {
        // Not needed at the moment
    }

    function update($parent)
    {
		$db = JFactory::getDbo();

        // Add the column gbid if the component was already installed previously - since version 3-2
        $db->setQuery("DESCRIBE ".$db->quoteName('#__easybook'));
        $ebr_db_structure = $db->loadAssocList();

        if($ebr_db_structure[1]['Field'] != 'gbid')
        {
            $db->setQuery("ALTER TABLE ".$db->quoteName('#__easybook')." ADD ".$db->quoteName('gbid')." INT NOT NULL DEFAULT '1' AFTER ".$db->quoteName('id'));
            $db->execute();
        }

        // Add new table easybook_gb - since version 3-2
        $db->setQuery("CREATE TABLE IF NOT EXISTS ".$db->quoteName('#__easybook_gb')." (".$db->quoteName('id')." INT(11) NOT NULL AUTO_INCREMENT, ".$db->quoteName('title')." VARCHAR(255) NOT NULL DEFAULT '', ".$db->quoteName('introtext')." TEXT NOT NULL, PRIMARY KEY (".$db->quoteName('id').")) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;");
        $db->execute();

        // Do we have a default guestbook? - needed for existing entries - since version 3-2
        $db->setQuery("SELECT * FROM ".$db->quoteName('#__easybook_gb'));
        $ebr_gb_entries = $db->loadAssoc();

        if(empty($ebr_gb_entries))
        {
            $db->setQuery("INSERT INTO ".$db->quoteName('#__easybook_gb')." (".$db->quoteName('id').", ".$db->quoteName('title').", ".$db->quoteName('introtext').") VALUES (1, '".JText::_('COM_EASYBOOKRELOADED_SCRIPT_DEFAULT_GUESTBOOK')."', '".JText::_('COM_EASYBOOKRELOADED_SCRIPT_DEFAULT_GUESTBOOK_INTROTEXT')."');");
            $db->execute();
        }

        // Rewrite the menu link entry if guestbook id is not set - since version 3-2
        $db->setQuery("SELECT * FROM ".$db->quoteName('#__menu')." WHERE ".$db->quoteName('link')." = 'index.php?option=com_easybookreloaded&view=easybookreloaded' AND ".$db->quoteName('client_id')." = 0");
        $menu_gbid = $db->loadAssoc();

        if(!empty($menu_gbid))
        {
            $db->setQuery("UPDATE ".$db->quoteName('#__menu')." SET ".$db->quoteName('link')." = 'index.php?option=com_easybookreloaded&view=easybookreloaded&gbid=1' WHERE ".$db->quoteName('id')." = ".$menu_gbid['id'].";");
            $db->execute();
        }
    }

    function postflight($type, $parent)
    {
		// Not needed at the moment
    }
}