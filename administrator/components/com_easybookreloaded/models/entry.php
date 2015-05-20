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

class EasybookReloadedModelEntry extends JModelLegacy
{
    protected $data = null;
    protected $id = null;
    protected $input;

    function __construct()
    {
        parent::__construct();

        $this->input = JFactory::getApplication()->input;

        $array = $this->input->get('cid', 0, 'ARRAY');
        $this->setId((int)$array[0]);
    }

    function setId($id)
    {
        $this->id = $id;
        $this->data = null;
    }

    function getData()
    {
        if(empty($this->data))
        {
            $query = "SELECT * FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('id')." = ".$this->_db->quote($this->id);
            $this->_db->setQuery($query);
            $this->data = $this->_db->loadObject();
        }

        if(!$this->data)
        {
            $this->data = $this->getTable('entry', 'EasybookReloadedTable');
            $this->data->id = 0;
        }

        // Set correct date with the selected timezone from the configuration
        $this->data->gbdate = JHTML::_('date', $this->data->gbdate, 'Y-m-d H:i:s');

        return $this->data;
    }

    function store()
    {
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        // Load all request variable - becaus JInput doesn't allow to load the whole data at once, a workaround
        // is used. This was easily possible with the deprecated JRequest (e.g. JRequest::get('post');)
        // Another possible call with the API would be: unserialize($this->input->serialize())
        $data = $_REQUEST;
        array_walk($data, create_function('&$data', '$data = htmlspecialchars(strip_tags(trim($data)));'));

        // Get unfiltered request variable of specific input fields (with formatting)
        $data['gbtext'] = htmlspecialchars($this->input->get('gbtext', '', 'RAW'));
        $data['gbcomment'] = htmlspecialchars($this->input->get('gbcomment', '', 'RAW'));

        $date = JFactory::getDate($data['gbdate'], JFactory::getApplication()->get('offset'));
        $data['gbdate'] = $date->toSql();

        if(!$row->save($data))
        {
            throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
        }

        return true;
    }

    function delete()
    {
        $cids = $this->input->get('cid', 0, 'ARRAY');
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        foreach($cids as $cid)
        {
            if(!$row->delete($cid))
            {
                throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
            }
        }

        return true;
    }

    function publish($state)
    {
        $cids = $this->input->get('cid', 0, 'ARRAY');
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        if(!$row->publish($cids, $state))
        {
            throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
        }

        return true;
    }
}
