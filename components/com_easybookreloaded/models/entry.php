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

class EasybookReloadedModelEntry extends JModelLegacy
{
    protected $data;
    protected $id;
    protected $badwords;
    protected $input;
    protected $params;
    protected $user;
    protected $session;
    protected $app;

    function __construct()
    {
        parent::__construct();

        $this->app = JFactory::getApplication();
        $this->params = JComponentHelper::getParams('com_easybookreloaded');
        $this->user = JFactory::getUser();
        $this->session = JFactory::getSession();
        $this->input = $this->app->input;

        $this->id = $this->input->getInt('cid', 0);

        // Requests from the notification mail do send a valid hash value
        if($hashrequest = $this->input->getString('hash'))
        {
            $hash_data = explode('-', $hashrequest);

            if(count($hash_data) != 2)
            {
                return false;
            }

            if(!empty($hash_data[0]) AND is_numeric($hash_data[0]))
            {
                $this->id = $hash_data[0];
            }
        }
    }

    /**
     * Stores the guestbook entries into the database
     *
     * @return bool|JTable
     * @throws Exception
     */
    function store()
    {
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        // Load all request variables - becaus JInput doesn't allow to load the whole data at once, a workaround
        //is used. This was easily possible with the deprecated JRequest (e.g. JRequest::get('post');)
        $data = $_REQUEST;
        array_walk($data, create_function('&$data', '$data = htmlspecialchars(strip_tags(trim($data)));'));

        // Get unfiltered request variable is only with a trick with JInput possible, so direct access is used instead
        // Possible solution: list($gbtext) = ($this->_input->get('gbtext', array(0), 'array') - use the filter array
        // With JRequest one could use - JRequest::getVar('gbtext', NULL, 'post', 'none', JREQUEST_ALLOWRAW)
        $data['gbtext'] = htmlspecialchars($_REQUEST['gbtext'], ENT_QUOTES);

        $date = JFactory::getDate();

        if($this->user->guest == 0 AND !EASYBOOK_CANEDIT)
        {
            $data['gbname'] = $this->user->get('username');

            if($this->params->get('registered_username'))
            {
                $data['gbname'] = $this->user->get('name');
            }

            $data['gbmail'] = $this->user->get('email');
        }

        if(!isset($data['id']))
        {
            $data['gbdate'] = $date->toSql();
            $data['published'] = $this->params->get('default_published', 1);
            $data['gbip'] = '0.0.0.0';

            if($this->params->get('enable_log', true))
            {
                $data['gbip'] = getenv('REMOTE_ADDR');
            }

            $data['gbcomment'] = null;
        }

        // Validate the entered data
        if(!$this->validate($data))
        {
            return false;
        }

        if(!$row->save($data))
        {
            throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
        }

        $this->clearSession();

        return $row;
    }

    /**
     * Validates all entered data that was submitted by the user
     *
     * @param $data
     *
     * @return bool
     * @throws exception
     */
    function validate(&$data)
    {
        // Load mail helper class - needed for the email address checks
        jimport('joomla.mail.helper');

        $errors = array();
        $error = false;

        // gbid session variable has to be set and the ID in the request has to be the same
        $gbid_session = (int)$this->session->get('gbid', false, 'easybookreloaded');

        // Clear gbid session variable
        $this->session->clear('gbid', 'easybookreloaded');

        if(!empty($gbid_session))
        {
            // Ooops, wrong ID submitted - prevent saving of such requests
            if($data['gbid'] != $gbid_session)
            {
                unset($data['gbid']);
                $error = true;
                $errors['gbid'] = true;
            }
        }
        else
        {
            // No chance, my little friend :-)
            unset($data['gbid']);
            $error = true;
            $errors['gbid'] = true;
        }

        if($this->params->get('enable_spam_reg') OR $this->user->guest)
        {
            $time = $this->session->get('time', null, 'easybookreloaded');

            if($time == '')
            {
                $error = true;
                $errors['sessionvariable'] = true;
            }
            else
            {
                if((time() - $this->params->get('type_time_sec')) <= $time)
                {
                    $error = true;
                    $errors['easycalccheck_time'] = true;
                }
            }

            if($this->params->get('enable_spam', true))
            {
                $spamcheck1 = $this->session->get('spamcheck1', null, 'easybookreloaded');
                $spamcheck2 = $this->session->get('spamcheck2', null, 'easybookreloaded');
                $spamcheckresult = $this->session->get('spamcheckresult', null, 'easybookreloaded');
                $spamcheck_field_name = $this->session->get('spamcheck_field_name', null, 'easybookreloaded');

                if(($spamcheck1 == '') OR ($spamcheck2 == '') OR ($spamcheckresult == '') OR ($spamcheck_field_name == ''))
                {
                    $error = true;
                    $errors['sessionvariable'] = true;
                }
                else
                {
                    if($data[$spamcheck_field_name] != $spamcheckresult)
                    {
                        $error = true;
                        $errors['easycalccheck'] = true;
                    }
                }
            }

            if($this->params->get('spamcheck_question') AND ($this->params->get('spamcheck_question_question') AND $this->params->get('spamcheck_question_answer')))
            {
                $spamcheck_question_field_name = $this->session->get('spamcheck_question_field_name', null, 'easybookreloaded');

                if($spamcheck_question_field_name == '')
                {
                    $error = true;
                    $errors['sessionvariable'] = true;
                }
                else
                {
                    if(strtolower($data[$spamcheck_question_field_name]) != strtolower($this->params->get('spamcheck_question_answer')))
                    {
                        $error = true;
                        $errors['easycalccheck_question'] = true;
                    }
                }
            }

            // Akismet - Further information: http://akismet.com/
            if($this->params->get('akismet'))
            {
                $akismet_key = $this->params->get('akismet_key');

                if($akismet_key)
                {
                    require_once(JPATH_COMPONENT.'/helpers/akismet.php');
                    $akismet_url = JURI::getInstance()->toString();

                    $name = $data['gbname'];
                    $email = $data['gbmail'];
                    $comment = $data['gbtext'];

                    // Add title if provided
                    if(!empty($data['gbtitle']))
                    {
                        $comment = $data['gbtitle'].' '.$comment;
                    }

                    // Check homepage if provided
                    if(!empty($data['gbpage']))
                    {
                        $url = $data['gbpage'];
                    }
                    else
                    {
                        $url = '';
                    }

                    $akismet = new Akismet($akismet_url, $akismet_key);
                    $akismet->setCommentAuthor($name);
                    $akismet->setCommentAuthorEmail($email);
                    $akismet->setCommentAuthorURL($url);
                    $akismet->setCommentContent($comment);

                    if($akismet->isCommentSpam())
                    {
                        $error = true;
                        $errors['akismet'] = true;
                    }
                }
            }
        }

        if($this->params->get('block_ip'))
        {
            $gbip = getenv('REMOTE_ADDR');
            $ips = array_map('trim', explode(',', $this->params->get('block_ip')));

            foreach($ips as $ip)
            {
                $ip_regexp = str_replace('x', '..?.?', preg_quote($ip));

                if(preg_match('@'.$ip_regexp.'@', $gbip))
                {
                    $error = true;
                    $errors['easycalccheck'] = true;
                }
            }
        }

        if($this->params->get('timelock_ip') AND $this->params->get('enable_log'))
        {
            $gbip = getenv('REMOTE_ADDR');
            $date_last_entry = $this->lastEntryDate($gbip);

            if(!empty($date_last_entry))
            {
                date_default_timezone_set('UTC');
                $date_back = strftime("%Y-%m-%d %H:%M:%S", time() - $this->params->get('timelock_ip'));

                if($date_last_entry > $date_back)
                {
                    $error = true;
                    $errors['iptimelock'] = true;
                }
            }
        }

        if(empty($data['gbname']))
        {
            $error = true;
            $errors['name'] = true;
        }

        if(empty($data['gbtext']))
        {
            $error = true;
            $errors['text'] = true;
        }
        else
        {
            if(preg_match_all('@\[img\].+\[/img\]@isU', $data['gbtext'], $matches))
            {
                $text = $data['gbtext'];

                foreach($matches[0] as $value)
                {
                    $img = str_replace(array('\'', "\""), '', $value);

                    if(strpos($img, ' ') == true)
                    {
                        $img_new = substr($img, 0, strpos($img, ' ')).'[/img]';
                        $text = str_replace($value, $img_new, $text);
                    }
                }

                $data['gbtext'] = $text;
            }

            if(preg_match_all('@https?://(www\.)?([a-zA-Z0-9-]+\.)?([a-zA-Z0-9-]{3,65})(\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))@is', $data['gbtext'], $matches))
            {
                if(count($matches[0]) > $this->params->get('maxnumberlinks'))
                {
                    $error = true;
                    $errors['toomanylinks'] = true;
                }
            }

            if(preg_match('@\[link.*\].*\[/link\]@isU', $data['gbtext']))
            {
                $error = true;
                $errors['easycalccheck'] = true;
            }
        }

        if(!empty($data['gbaim']))
        {
            $allowed = '@^[A-Za-z0-9_\.]+$@';

            if(!preg_match($allowed, $data['gbaim']))
            {
                $error = true;
                $errors['aim'] = true;
            }
        }

        if(!empty($data['gbicq']))
        {
            $allowed = '@^[0-9]+$@';

            if(!preg_match($allowed, $data['gbicq']))
            {
                $error = true;
                $errors['icq'] = true;
            }
        }

        if(!empty($data['gbyah']))
        {
            $allowed = '@^[A-Za-z0-9_\.]+$@';

            if(!preg_match($allowed, $data['gbyah']))
            {
                $error = true;
                $errors['yah'] = true;
            }
        }

        if(!empty($data['gbskype']))
        {
            $allowed = '@^[A-Za-z0-9_\.-]+$@';

            if(!preg_match($allowed, $data['gbskype']))
            {
                $error = true;
                $errors['skype'] = true;
            }
        }

        if(!empty($data['gbpage']))
        {
            $data['gbpage'] = str_replace(array('\'', "\""), '', $data['gbpage']);

            if(strpos($data['gbpage'], ' ') == true)
            {
                $data['gbpage'] = substr($data['gbpage'], 0, strpos($data['gbpage'], ' '));
            }

            // Add scheme if not provided
            if(!preg_match('@^https?://@i', $data['gbpage']))
            {
                $data['gbpage'] = 'http://'.$data['gbpage'];
            }

            $data['gbpage'] = htmlspecialchars($data['gbpage'], ENT_QUOTES);
        }

        if((!empty($data['gbmail']) OR $this->params->get('require_mail', true)) AND !JMailHelper::isEmailAddress($data['gbmail']))
        {
            $error = true;
            $errors['mail'] = true;
        }

        if(($this->params->get('show_title', true)) AND (empty($data['gbtitle']) AND $this->params->get('require_title', true)))
        {
            $error = true;
            $errors['title'] = true;
        }
        elseif(!empty($data['gbtitle']))
        {
            $data['gbtitle'] = htmlspecialchars($data['gbtitle'], ENT_QUOTES);
        }

        if(!empty($data['gbmsn']) AND !JMailHelper::isEmailAddress($data['gbmsn']))
        {
            $error = true;
            $errors['msn'] = true;
        }

        if($this->params->get('badwordfilter', true))
        {
            $badwords = $this->getBadwordList();
            $badwordfilter_regexp = $this->params->get('badwordfilter_regexp', false);

            if(!empty($badwordfilter_regexp))
            {
                foreach($badwords as $badword)
                {
                    $data['gbtext'] = preg_replace('@'.$badword.'@iU', '***', $data['gbtext']);

                    if(!empty($data['gbtitle']))
                    {
                        $data['gbtitle'] = preg_replace('@'.$badword.'@iU', '***', $data['gbtitle']);
                    }
                }
            }
            else
            {
                $data['gbtext'] = str_replace($badwords, '***', $data['gbtext']);

                if(!empty($data['gbtitle']))
                {
                    $data['gbtitle'] = str_replace($badwords, '***', $data['gbtitle']);
                }
            }
        }

        if($error == true)
        {
            $this->session->set('errors', $errors, 'easybookreloaded');
            $this->app->setUserState('eb_validation_errors', $errors);
            $this->app->setUserState('eb_validation_data', $data);

            return false;
        }

        return true;
    }

    /**
     * Checks the latest date of an entry from a specific IP address
     *
     * @param $ip
     *
     * @return mixed
     */
    private function lastEntryDate($ip)
    {
        $query = "SELECT ".$this->_db->quoteName('gbdate')." FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('gbip')." = ".$this->_db->quote($ip)." ORDER BY gbdate DESC";
        $this->_db->setQuery($query);
        $date_last_entry = $this->_db->loadResult();

        return $date_last_entry;
    }

    /**
     * Loads all language bad words from the database for the validation check
     *
     * @return mixed
     */
    private function getBadwordList()
    {
        if(empty($this->badwords))
        {
            $query = "SELECT ".$this->_db->quoteName('word')." FROM ".$this->_db->quoteName('#__easybook_badwords')." ORDER BY length(word) DESC";
            $this->_db->setQuery($query);
            $this->badwords = $this->_db->loadColumn();
        }

        return $this->badwords;
    }

    /**
     * Clears saved session data if entry was stored successfully in the database
     */
    private function clearSession()
    {
        $this->session->clear('spamcheck1', 'easybookreloaded');
        $this->session->clear('spamcheck2', 'easybookreloaded');
        $this->session->clear('spamcheckresult', 'easybookreloaded');
        $this->session->clear('spamcheck_field_name', 'easybookreloaded');
        $this->session->clear('operator', 'easybookreloaded');
        $this->session->clear('time', 'easybookreloaded');
        $this->session->clear('spamcheck_question_field_name', 'easybookreloaded');
    }

    /**
     * Deletes an entry using JTable
     *
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        if(!$row->delete($this->id))
        {
            throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
        }

        return true;
    }

    /**
     * Changes the status of an entry - online / offline
     *
     * @return int
     */
    public function publish()
    {
        $data = $this->getData();
        $status = $data->published;

        $query = "UPDATE ".$this->_db->quoteName('#__easybook')." SET ".$this->_db->quoteName('published')." = ".$this->_db->quote(!$status)." WHERE ".$this->_db->quoteName('id')." = ".$this->_db->quote($this->id)." LIMIT 1;";
        $this->_db->setQuery($query);

        if(!$this->_db->execute())
        {
            return -1;
        }

        return (int)!$status;
    }

    /**
     * Loads the entry data when a form is loaded
     *
     * @return JTable|mixed
     * @throws Exception
     */
    public function getData()
    {
        // Error occured - load the form with entered data again
        if($this->input->get('retry') == 'true')
        {
            $this->data = $this->getTable('entry', 'EasybookReloadedTable');
            $this->data->bind($this->app->getUserState('eb_validation_data'));
        }

        // Modification procoess of an existing entry
        if(empty($this->data) AND !empty($this->id))
        {
            $query = "SELECT * FROM ".$this->_db->quoteName('#__easybook')." WHERE ".$this->_db->quoteName('id')." = ".$this->_db->quote($this->id);
            $this->_db->setQuery($query);
            $this->data = $this->_db->loadObject();
        }

        // First loading of the form - new entry
        if(empty($this->data))
        {
            $this->data = $this->getTable('entry', 'EasybookReloadedTable');
            $this->data->id = 0;

            if($this->user->get('id'))
            {
                if($this->params->get('registered_username'))
                {
                    $this->data->gbname = $this->user->get('name');
                }
                else
                {
                    $this->data->gbname = $this->user->get('username');
                }

                $this->data->gbmail = $this->user->get('email');
            }

            // Okay, if we get here, then the gbid must be set. If not, then we have a direct call - bad idea!
            $gbid_session = $this->session->get('gbid', false, 'easybookreloaded');

            if(empty($gbid_session))
            {
                // Do not interrupt, let the fools play their game ;-)
                $this->session->set('gbid', false, 'easybookreloaded');
            }
        }
        else
        {
            $this->session->set('gbid', $this->data->gbid, 'easybookreloaded');
        }

        return $this->data;
    }

    /**
     * Creates math exercise and saves values to the session for the validation process
     */
    public function getCalcCheck()
    {
        if($this->params->get('enable_spam_reg') OR $this->user->guest)
        {
            $this->session->set('time', time(), 'easybookreloaded');

            if($this->params->get('enable_spam', true))
            {
                $spamcheck1 = mt_rand(1, $this->params->get('max_value', 20));
                $spamcheck2 = mt_rand(1, $this->params->get('max_value', 20));

                if($this->params->get('operator') == 0)
                {
                    $operator = 0;
                }
                elseif($this->params->get('operator') == 1)
                {
                    $operator = 1;
                }
                elseif($this->params->get('operator') == 2)
                {
                    $operator = mt_rand(0, 1);
                }

                if($operator == 0)
                {
                    $spamcheckresult = $spamcheck1 + $spamcheck2;
                    $operator = '+';
                }
                elseif($operator == 1)
                {
                    $spamcheckresult = $spamcheck1 - $spamcheck2;
                    $operator = '-';
                }

                $spamcheck_field_name = $this->getRandomValue();

                $this->session->set('spamcheck1', $spamcheck1, 'easybookreloaded');
                $this->session->set('spamcheck2', $spamcheck2, 'easybookreloaded');
                $this->session->set('spamcheckresult', $spamcheckresult, 'easybookreloaded');
                $this->session->set('spamcheck_field_name', $spamcheck_field_name, 'easybookreloaded');
                $this->session->set('operator', $operator, 'easybookreloaded');
            }

            if($this->params->get('spamcheck_question') AND ($this->params->get('spamcheck_question_question') AND $this->params->get('spamcheck_question_answer')))
            {
                $spamcheck_question_field_name = $this->getRandomValue();
                $this->session->set('spamcheck_question_field_name', $spamcheck_question_field_name, 'easybookreloaded');
            }
        }
    }

    /**
     * Creates a random string for the calc check field ID
     *
     * @return string
     */
    private function getRandomValue()
    {
        $pw = '';

        // first character has to be a letter
        $characters = range('a', 'z');
        $pw .= $characters[mt_rand(0, 25)];

        // other characters arbitrarily
        $numbers = range(0, 9);
        $characters = array_merge($characters, $numbers);

        $pw_length = mt_rand(4, 12);

        for($i = 0; $i < $pw_length; $i++)
        {
            $pw .= $characters[mt_rand(0, 35)];
        }

        return $pw;
    }

    /**
     * Saves the comment from authorized users with admin rights for the component
     *
     * @return bool
     * @throws Exception
     */
    public function savecomment()
    {
        $row = $this->getTable('entry', 'EasybookReloadedTable');

        // Load all request variables - becaus JInput doesn't allow to load the whole data at once, a workaround
        //is used. This was easily possible with the deprecated JRequest (e.g. JRequest::get('post');)
        $data = $_REQUEST;
        array_walk($data, create_function('&$data', '$data = htmlspecialchars(strip_tags(trim($data)));'));

        // Get unfiltered request variable is only with a trick with JInput possible, so direct access is used instead
        // Possible solution: list($gbtext) = ($this->_input->get('gbtext', array(0), 'array') - use the filter array
        // With JRequest one could use - JRequest::getVar('gbtext', NULL, 'post', 'none', JREQUEST_ALLOWRAW)
        $data['gbcomment'] = htmlspecialchars($_REQUEST['gbcomment'], ENT_QUOTES);

        // gbid sessian variable has to be set and the ID in the request has to be the same
        $gbid_session = (int)$this->session->get('gbid', false, 'easybookreloaded');

        // Clear gbid session variable
        $this->session->clear('gbid', 'easybookreloaded');

        if(empty($gbid_session))
        {
            return false;
        }

        // Ooops, wrong ID - prevent saving of such requests
        if($data['gbid'] != $gbid_session)
        {
            return false;
        }

        if(!$row->save($data))
        {
            throw new Exception(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 404);
        }

        return $data;
    }

    /**
     * Loads entry data from the database using JTable
     *
     * @param $id
     *
     * @return JTable
     * @throws Exception
     */
    public function getRow($id)
    {
        $id = (int)$id;
        $table = $this->getTable('entry', 'EasybookReloadedTable');
        $table->load($id);

        return $table;
    }
}
