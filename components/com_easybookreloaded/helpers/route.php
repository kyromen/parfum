<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3.x
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

class EasybookReloadedHelperRoute
{
    /**
     * Creates correct URL to the entry
     *
     * @param $id
     * @param $gbid
     *
     * @return string
     */
    static function getEasybookReloadedRoute($id, $gbid)
    {
        $item_id = EasybookReloadedHelperRoute::getItemId($gbid);
        $limit = EasybookReloadedHelperRoute::getLimitstart($id);

        $link = 'index.php?option=com_easybookreloaded&view=easybookreloaded';
        $link .= '&gbid='.$gbid;
        $link .= '&Itemid='.$item_id;

        if(!empty($limit))
        {
            $link .= '&limitstart='.$limit;
        }

        $link .= '#gbentry_'.$id;

        return $link;
    }

    /**
     * Gets the Item ID of the component - the Item ID is the ID from the menu entry
     *
     * @return int The Item ID of the menu entry of the component
     */
    static function getItemId($gbid = 1)
    {
        // First get the ItemID from the request variable
        $item_id_request = JFactory::getApplication()->input->getInt('Itemid');

        // Now also load the ID from the db to get sure that we have a correct ItemID
        $db = JFactory::getDBO();
        $query = "SELECT ".$db->quoteName('id')." FROM ".$db->quoteName('#__menu')." WHERE ".$db->quoteName('link')." = 'index.php?option=com_easybookreloaded&view=easybookreloaded&gbid=".$gbid."' AND ".$db->quoteName('published')." = 1";
        $db->setQuery($query);
        $item_id = (int)$db->loadResult();

        if(!empty($item_id))
        {
            return $item_id;
        }

        if((!empty($item_id_request) AND empty($item_id)) OR ($item_id_request == $item_id))
        {
            return $item_id_request;
        }

        return false;
    }

    /**
     * Gets limitstart to set the correct page with the entry
     *
     * @param int $id
     *
     * @return int
     */
    static function getLimitstart($id)
    {
        $params = JComponentHelper::getParams('com_easybookreloaded');
        $entries_per_page = (int)$params->get('entries_perpage', 5);
        $order = $params->get('entries_order', 'DESC');

        $db = JFactory::getDBO();
        $query = "SELECT * FROM ".$db->quoteName('#__easybook')." WHERE ".$db->quoteName('published')." = 1 ORDER BY ".$db->quoteName('id')." ".$order;
        $db->setQuery($query);
        $result = $db->loadRowList();

        foreach($result as $key => $value)
        {
            if($value[0] == $id)
            {
                break;
            }
        }

        $limit = $entries_per_page * intval($key / $entries_per_page);

        return (int)$limit;
    }

    /**
     * Creates correct URL with the task for the hash link in the notification mail
     *
     * @param string $task
     *
     * @return string
     */
    static function getEasybookReloadedRouteHash($task, $gbid)
    {
        $link = 'index.php?option=com_easybookreloaded&task=';

        // Add the task to the URL
        $link .= $task;

        // Add the GB ID and Item ID to the URL
        $link .= '&gbid='.$gbid;
        $link .= '&Itemid='.EasybookReloadedHelperRoute::getItemId($gbid);
        $link .= '&hash=';

        return $link;
    }
}
