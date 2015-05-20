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
jimport('joomla.application.component.view');

class EasybookReloadedViewEntry extends JViewLegacy
{
    function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $this->params = JComponentHelper::getParams('com_easybookreloaded');
        $task = JFactory::getApplication()->input->getWord('task');
        $this->session = JFactory::getSession();
        $this->user = JFactory::getUser();

        // Set CSS File
        $template = $this->params->get('template', 0);

        if($template == 0)
        {
            $document->addStyleSheet('components/com_easybookreloaded/css/easybookreloaded.css');
        }
        elseif($template == 1)
        {
            $document->addStyleSheet('components/com_easybookreloaded/css/easybookreloadeddark.css');
        }
        elseif($template == 2)
        {
            $document->addStyleSheet('components/com_easybookreloaded/css/easybookreloadedtransparent.css');
        }

        $document->addCustomTag('
		<!--[if IE 6]>
    		<style type="text/css">
                    .easy_align_middle { behavior: url('.JURI::base().'components/com_easybookreloaded/scripts/pngbehavior.htc); }
                    .png { behavior: url('.JURI::base().'components/com_easybookreloaded/scripts/pngbehavior.htc); }
    		</style>
  		<![endif]-->');

        $document->addScriptDeclaration($this->addBbcodeJs($this->params, $task), 'text/javascript');


        if($this->params->get('show_rating', 1))
        {
            JHtml::_('behavior.framework');
            $document->addScript('components/com_easybookreloaded/scripts/moostarrating.js', 'text/javascript');

            $show_rating_type = $this->params->get('show_rating_type', 1);

            if($show_rating_type == 0)
            {
                $document->addCustomTag('
                <script type="text/javascript">
                        //<![CDATA[
                        window.addEvent("load", function() {
                                MooStarRatingImages.defaultImageFolder = "'.JURI::base().'components/com_easybookreloaded/images";
                                var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "sun_empty.png", imageFull:  "sun_full.png", imageHover: "sun_hover.png", tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html"  });
                        });
                        //]]>
                </script>');
            }
            elseif($show_rating_type == 1)
            {
                $document->addCustomTag('
                <script type="text/javascript">
                        //<![CDATA[
                        window.addEvent("load", function() {
                                MooStarRatingImages.defaultImageFolder = "'.JURI::base().'components/com_easybookreloaded/images";
                                var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "star_empty.png", imageFull:  "star_full.png", imageHover: "star_hover.png", tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html"  });
                        });
                        //]]>
                </script>');
            }
            elseif($show_rating_type == 2)
            {
                $document->addCustomTag('
                <script type="text/javascript">
                        //<![CDATA[
                        window.addEvent("load", function() {
                                MooStarRatingImages.defaultImageFolder = "'.JURI::base().'components/com_easybookreloaded/images";
                                var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "star_boxed_empty.png", imageFull:  "star_boxed_full.png", imageHover: "star_boxed_hover.png", width: 17, tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html" });
                        });
                        //]]>
                </script>');
            }
        }

        $this->entry = $this->get('Data');
        $this->entry->ip = getenv('REMOTE_ADDR');

        // Get the correct guestbook ID 
        $this->_gbid = (int)JFactory::getSession()->get('gbid', false, 'easybookreloaded');

        // Load antispam checks
        $this->get('CalcCheck');

        switch($task)
        {
            case 'add':
                $this->heading = $document->getTitle()." - ".JTEXT::_('COM_EASYBOOKRELOADED_SIGN_GUESTBOOK');
                break;
            case 'edit' OR 'edit_mail':
                $this->heading = $document->getTitle()." - ".JTEXT::_('COM_EASYBOOKRELOADED_EDIT_ENTRY');
                break;
            case 'comment' OR 'comment_mail':
                $this->heading = $document->getTitle()." - ".JTEXT::_('COM_EASYBOOKRELOADED_EDIT_COMMENT');
                break;
        }

        parent::display($tpl);
    }

    private function addBbcodeJs($params, $task = 'add')
    {
        if($task == 'add' OR $task == 'edit' OR $task == 'edit_mail')
        {
            $textarea_name = 'gbtext';
        }
        elseif($task == 'comment' OR $task == 'comment_mail')
        {
            $textarea_name = 'gbcomment';
        }

        $js = 'function x()
            {
                return;
            }

            function insertprompt(insert, input, start, end, revisedMessage, currentMessage)
            {
                // Internet Explorer
                if (typeof document.selection != \'undefined\')
                {
                    var range = document.selection.createRange();
                    range.text = insert;
                    var range = document.selection.createRange();
                    range.move(\'character\', 0);
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != \'undefined\')
                {
                    revisedMessage = currentMessage.substr(0, start) + insert + currentMessage.substr(end);
                    document.gbookForm.'.$textarea_name.'.value=revisedMessage;
                    document.gbookForm.'.$textarea_name.'.focus();
                    var pos;
                    pos = start + insert.length;
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
            }

            function insert(aTag, eTag)
            {
                var input = document.forms[\'gbookForm\'].elements[\''.$textarea_name.'\'];
                input.focus();
                // Internet Explorer
                if(typeof document.selection != \'undefined\')
                {
                    var range = document.selection.createRange();
                    var insText = range.text;
                    range.text = aTag + insText + eTag;
                    range = document.selection.createRange();
                    if (insText.length == 0)
                    {
                        range.move(\'character\', -eTag.length);
                    }
                    else
                    {
                        range.moveStart(\'character\', aTag.length + insText.length + eTag.length);
                    }
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != \'undefined\')
                {
                    var start = input.selectionStart;
                    var end = input.selectionEnd;
                    var insText = input.value.substring(start, end);
                    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
                    var pos;
                    if (insText.length == 0)
                    {
                        pos = start + aTag.length;
                    }
                    else
                    {
                        pos = start + aTag.length + insText.length + eTag.length;
                    }
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
                else
                {
                    var pos;
                    var re = new RegExp(\'^[0-9]{0,3}$\');
                    while (!re.test(pos))
                    {
                        pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
                    }
                    if (pos > input.value.length)
                    {
                        pos = input.value.length;
                    }
                    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
                    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
                }
            }

            function insertsmilie(thesmile)
            {
                var input = document.forms[\'gbookForm\'].elements[\''.$textarea_name.'\'];
                input.focus();
                // Internet Explorer
                if(typeof document.selection != \'undefined\')
                {
                    var range = document.selection.createRange();
                    var insText = range.text;
                    range.text = " "+thesmile+" ";
                    range = document.selection.createRange();
                    range.move(\'character\', 0);
                    range.select();
                }
                // Gecko Software
                else if (typeof input.selectionStart != \'undefined\')
                {
                    var start = input.selectionStart;
                    var end = input.selectionEnd;
                    var insText = input.value.substring(start, end);
                    input.value = input.value.substr(0, start) + " "+thesmile+" " + input.value.substr(end);
                    var pos;
                    pos = start + (thesmile.length + 2);
                    input.selectionStart = pos;
                    input.selectionEnd = pos;
                }
                else
                {
                    var pos;
                    var re = new RegExp(\'^[0-9]{0,3}$\');
                    while (!re.test(pos))
                    {
                        pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
                    }
                    if (pos > input.value.length)
                    {
                        pos = input.value.length;
                    }
                    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
                    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
                }
            }';

        if($params->get('support_bbcode', true))
        {
            $js .= 'function DoPrompt(action)
            {
                var input = document.forms[\'gbookForm\'].elements[\''.$textarea_name.'\'];
                input.focus();

                var start = input.selectionStart;
                var end = input.selectionEnd;
                var revisedMessage;
                var currentMessage = document.gbookForm.'.$textarea_name.'.value;';

            if($params->get('support_link', true))
            {
                $js .= 'if (action == "url")
                    {
                        var thisURL = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_URL_HERE").'", "http://");
                        var thisTitle = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_WEB_PAGE_TITLE").'", "'.JTEXT::_("COM_EASYBOOKRELOADED_WEB_PAGE_TITLE").'");
                        if (thisURL != undefined && thisTitle != undefined)
                        {
                            if  (thisURL != "" && thisTitle != "")
                            {
                                var urlBBCode = "[URL="+thisURL+"]"+thisTitle+"[/URL]";
                                insertprompt(urlBBCode, input, start, end, revisedMessage, currentMessage);
                            }
                        }
                        return;
                    }';
            }

            if($params->get('support_mail', true))
            {
                $js .= 'if (action == "email")
                {
                    var thisEmail = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_EMAIL_ADDRESS").'", "");
                    if (thisEmail != undefined)
                    {
                        if  (thisEmail != "")
                        {
                            var emailBBCode = "[EMAIL]"+thisEmail+"[/EMAIL]";
                            insertprompt(emailBBCode, input, start, end, revisedMessage, currentMessage);
                        }
                    }
                    return;
                }';
            }

            $js .= 'if (action == "code")
                {
                    var thisLanguage = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_WHICH_LANGUAGE").'", "");
                    if (thisLanguage != undefined)
                    {
                        if  (thisLanguage != "")
                        {
                            var codeBBCode = "[CODE="+thisLanguage+"]\n\n[/CODE]";
                            insertprompt(codeBBCode, input, start, end, revisedMessage, currentMessage);
                        }
                    }
                    return;
                }
                if (action == "youtube")
                {
                    var thisYoutube = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_YOUTUBE_VIDEO_ID").'", "");
                    if (thisYoutube != undefined)
                    {
                        if  (thisYoutube != "")
                        {
                            var codeBBCode = "[YOUTUBE]"+thisYoutube+"[/YOUTUBE]";
                            insertprompt(codeBBCode, input, start, end, revisedMessage, currentMessage);
                        }
                    }
                    return;
                }';

            if($params->get('support_pic', true))
            {

                $js .= 'if (action == "image")
                {
                    var thisImage = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_URL_OF_THE_PICTURE_YOU_WANT_TO_SHOW").'", "http://");
                    if (thisImage != undefined)
                    {
                        if  (thisImage != "")
                        {
                            var imageBBCode = "[IMG]"+thisImage+"[/IMG]";
                            insertprompt(imageBBCode, input, start, end, revisedMessage, currentMessage);
                        }
                    }
                    return;
                }
                if (action == "image_link")
                {
                    var thisImage = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_URL_OF_THE_PICTURE_YOU_WANT_TO_SHOW").'", "http://");
                    var thisURL = prompt("'.JTEXT::_("COM_EASYBOOKRELOADED_ENTER_THE_URL_HERE").'", "http://");
                    if (thisImage != undefined && thisURL != undefined)
                    {
                        if  (thisImage != "" && thisURL != "")
                        {
                            var imageBBCode = "[IMGLINK="+thisURL+"]"+thisImage+"[/IMGLINK]";
                            insertprompt(imageBBCode, input, start, end, revisedMessage, currentMessage);
                        }
                    }
                    return;
                }';
            }

            $js .= '}';
        }

        return $js;
    }

}
