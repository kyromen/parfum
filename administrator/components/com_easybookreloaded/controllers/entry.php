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

class EasybookReloadedControllerEntry extends JControllerLegacy
{
    protected $input;

    function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->input = JFactory::getApplication()->input;

        if($this->input->getCmd('task', '') == '')
        {
            $view = $this->getView('easybookreloaded', 'html');

            if($model_gb = $this->getModel('gb'))
            {
                $view->setModel($model_gb, false);
            }

            require_once JPATH_COMPONENT.'/helpers/easybookreloaded.php';
            EasybookReloadedHelper::addSubmenu($this->input->getCmd('view', 'easybookreloaded'));
        }
    }

    function edit()
    {
        $this->input->set('view', 'entry');
        $this->input->set('layout', 'form');
        $this->input->set('hidemainmenu', 1);

        $view = $this->getView('entry', 'html');
        $view->setLayout('form');

        if($model_gb = $this->getModel('gb'))
        {
            $view->setModel($model_gb, false);
        }

        parent::display();
    }

    function save()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        $model = $this->getModel('entry');

        if($model->store())
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_SAVED');
            $type = 'message';
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_SAVING_ENTRY');
            $type = 'error';
        }

        if($this->task == 'apply')
        {
            $this->setRedirect('index.php?'.$this->input->getString('url_current'), $msg, $type);
        }
        else
        {
            $this->setRedirect('index.php?option=com_easybookreloaded', $msg, $type);
        }
    }

    function remove()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        $model = $this->getModel('entry');

        if(!$model->delete())
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_ENTRY_COULD_NOT_BE_DELETED');
            $type = 'error';
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_DELETED');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false), $msg, $type);
    }

    function cancel()
    {
        $msg = JText::_('COM_EASYBOOKRELOADED_OPERATION_CANCELLED');
        $this->setRedirect('index.php?option=com_easybookreloaded', $msg, 'notice');
    }

    function publish()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        $model = $this->getModel('entry');

        if($model->publish(1))
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_PUBLISHED');
            $type = 'message';
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS')." - ".$model->getError();
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false), $msg, $type);
    }

    function unpublish()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        $model = $this->getModel('entry');

        if($model->publish(0))
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_UNPUBLISHED');
            $type = 'message';
        }
        else
        {
            $msg = JText::_('COM_EASYBOOKRELOADED_ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS')." - ".$model->getError();
            $type = 'error';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false), $msg, $type);
    }
}
