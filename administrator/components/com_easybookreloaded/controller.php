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
jimport('joomla.application.component.controller');

class EasybookReloadedController extends JControllerLegacy
{
    function display($cachable = false, $urlparams = false)
    {
        $input = JFactory::getApplication()->input;

        $task = $input->getCmd('task');

        if($task == 'about')
        {
            $input->set('view', 'about');
            $input->set('layout', 'default');
        }
        elseif($task == 'gb')
        {
            $input->set('view', 'gb');
            $input->set('layout', 'default');
        }

        // We need the gb model also in the default view for the selection list
        if($model_gb = $this->getModel('gb'))
        {
            $view = $this->getView('easybookreloaded', 'html');
            $view->setModel($model_gb, false);
        }

        // Add slide menu
        require_once JPATH_COMPONENT.'/helpers/easybookreloaded.php';
        EasybookReloadedHelper::addSubmenu($input->getCmd('view', 'easybookreloaded'));

        parent::display();
    }
}
