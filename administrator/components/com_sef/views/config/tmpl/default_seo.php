<?php
/**
 * SEF component for Joomla!
 * 
 * @package   JoomSEF
 * @version   4.6.2
 * @author    ARTIO s.r.o., http://www.artio.net
 * @copyright Copyright (C) 2015 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */
 
defined('_JEXEC') or die('Restricted access');

echo JHtml::_('tabs.panel', JText::_('COM_SEF_SEO'), 'seo');
$x = 0;
?>
<fieldset class="adminform">
    <legend><?php echo JText::_('COM_SEF_SEO_CONFIGURATION'); ?></legend>
            <table class="adminform table table-striped">
              <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                  <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_EXTERNAL_NOFOLLOW'), JText::_('COM_SEF_NOFOLLOW_EXTERNAL'));?></td>
                  <td width="200"><?php echo JText::_('COM_SEF_NOFOLLOW_EXTERNAL');?>:</td>
                  <td><?php echo $this->lists['external_nofollow'];?></td>
              <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                  <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_INTERNAL_ENABLE'), JText::_('COM_SEF_ENABLE_INTERNAL'));?></td>
                  <td width="200"><?php echo JText::_('COM_SEF_ENABLE_INTERNAL');?>:</td>
                  <td><?php echo $this->lists['internal_enable'];?></td>
              </tr>
              <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                  <td><?php echo $this->tooltip(JText::_('COM_SEF_TT_INTERNAL_NOFOLLOW'), JText::_('COM_SEF_NOFOLLOW_INTERNAL'));?></td>
                  <td><?php echo JText::_('COM_SEF_NOFOLLOW_INTERNAL');?>:</td>
                  <td><?php echo $this->lists['internal_nofollow'];?></td>
              </tr>
              <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                  <td><?php echo $this->tooltip(JText::_('COM_SEF_TT_INTERNAL_NEWWINDOW'), JText::_('COM_SEF_OPEN_LINKS_IN_NEW_WINDOW'));?></td>
                  <td><?php echo JText::_('COM_SEF_OPEN_LINKS_IN_NEW_WINDOW');?>:</td>
                  <td><?php echo $this->lists['internal_newwindow'];?></td>
              </tr>
              <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                  <td><?php echo $this->tooltip(JText::_('COM_SEF_TT_INTERNAL_MAXLINKS'), JText::_('COM_SEF_MAXIMUM_LINKS_FOR_EACH_WORD'));?></td>
                  <td><?php echo JText::_('COM_SEF_MAXIMUM_LINKS_FOR_EACH_WORD');?>:</td>
                  <td><?php echo $this->lists['internal_maxlinks'];?></td>
              </tr>
            </table>
</fieldset>
<fieldset class="adminform">
    <legend><?php echo JText::_('COM_SEF_CANONICAL_CONFIGURATION'); ?></legend>
    <table class="adminform table table-striped">
        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_CANONICALS_REMOVE'), JText::_('COM_SEF_CANONICALS_REMOVE'));?></td>
            <td width="200"><?php echo JText::_('COM_SEF_CANONICALS_REMOVE');?>:</td>
            <td><?php echo $this->lists['canonicalsRemove'];?></td>
        </tr>
        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_CANONICALS_FIX'), JText::_('COM_SEF_CANONICALS_FIX'));?></td>
            <td width="200"><?php echo JText::_('COM_SEF_CANONICALS_FIX');?>:</td>
            <td><?php echo $this->lists['canonicalsFix'];?></td>
        </tr>
    </table>
</fieldset>