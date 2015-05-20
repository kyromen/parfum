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

class EasybookReloadedModelEasybookReloaded extends JModelLegacy
{
    protected $data;
    protected $total;
    protected $input;
    protected $params;
    protected $pagination;

    function __construct()
    {
        parent::__construct();

        $this->input = JFactory::getApplication()->input;
        $this->params = JComponentHelper::getParams('com_easybookreloaded');

        // Get guestbook ID
        $this->_gbid = $this->input->getInt('gbid', false);
        JFactory::getSession()->set('gbid', $this->_gbid, 'easybookreloaded');
    }

    public function getData()
    {
        if(empty($this->data))
        {
            $query = $this->buildQuery();
            $this->data = $this->_getList($query);
        }

        return $this->data;
    }

    /**
     * Builds correct query to retrieve all needed entries
     *
     * @return string
     */
    private function buildQuery()
    {
        // If type is feed, then the order has to be DESC to get the latest entries in the feed reader
        $document = JFactory::getDocument();

        if($document->getType() == 'feed')
        {
            $order = 'DESC';
        }
        else
        {
            $order = $this->params->get('entries_order', 'DESC');
        }

        // Check whether limit is already set - e.g. from feed function
        $limit = $this->input->getInt('limit', 0);

        if(empty($limit))
        {
            $limit = (int)$this->params->get('entries_perpage', 5);
        }

        $query = "SELECT * FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('gbid')." = ".$this->_gbid;

        if(!EASYBOOK_CANEDIT)
        {
            $query .= " AND ".$this->_db->quoteName('published')." = 1";
        }

        $query .=  " ORDER BY ".$this->_db->quoteName('gbdate')." ".$order." LIMIT ".$this->input->getInt('limitstart', 0).", ".$limit;

        return $query;
    }

    public function getGBData()
    {
        $gb_data = $this->_getList("SELECT * FROM ".$this->_db->quoteName('#__easybook_gb')." WHERE ".$this->_db->quoteName('id')." = ".$this->_gbid);

        if(!empty($gb_data))
        {
            $gb_data = $gb_data[0];
        }

        return $gb_data;
    }

    public function getPagination()
    {
        if(empty($this->pagination))
        {
            // Check whether limit is already set - e.g. from feed function
            $limit = $this->input->getInt('limit', 0);

            if(empty($limit))
            {
                $limit = (int)$this->params->get('entries_perpage', 5);
            }

            jimport('joomla.html.pagination');
            $total = $this->getTotal();
            $this->pagination = new JPagination($total, $this->input->getInt('limitstart', 0), $limit);
        }

        return $this->pagination;
    }

    public function getTotal()
    {
        if(empty($this->total))
        {
            $query = $this->buildCountQuery();
            $this->total = $this->_getListCount($query);
        }

        return $this->total;
    }

    private function buildCountQuery()
    {
        if(EASYBOOK_CANEDIT)
        {
            $query = "SELECT * FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('gbid')." = ".$this->_gbid;
        }
        else
        {
            $query = "SELECT * FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('gbid')." = ".$this->_gbid." AND ".$this->_db->quoteName('published')." = 1";
        }

        return $query;
    }

    public function getGbid()
    {
        if(!empty($this->_gbid) AND is_numeric($this->_gbid))
        {
            return $this->_gbid;
        }
    }
}
