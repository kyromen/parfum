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

echo JHtml::_('tabs.panel', JText::_('COM_SEF_SITEMAP'), 'sitemap');

          $x = 0;
		  ?>
		  <div class="fltlft" style="width: 50%">
		  <fieldset class="adminform">
		      <legend><?php echo JText::_('COM_SEF_SITEMAP_CONFIGURATION'); ?></legend>
		      <table class="adminform table table-striped">
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_FILENAME'), JText::_('COM_SEF_XML_FILE_NAME'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_XML_FILE_NAME');?>:</td>
    	            <td><?php echo $this->lists['sitemap_filename'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_SSL'), JText::_('COM_SEF_SITEMAP_SSL'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_SITEMAP_SSL');?>:</td>
    	            <td><?php echo $this->lists['sitemap_ssl'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_INDEXED'), JText::_('COM_SEF_DEFAULT_INDEXED'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_DEFAULT_INDEXED');?>:</td>
    	            <td><?php echo $this->lists['sitemap_indexed'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_FREQUENCY'), JText::_('COM_SEF_DEFAULT_CHANGE_FREQUENCY'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_DEFAULT_CHANGE_FREQUENCY');?>:</td>
    	            <td><?php echo $this->lists['sitemap_frequency'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_PRIORITY'), JText::_('COM_SEF_DEFAULT_PRIORITY'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_DEFAULT_PRIORITY');?>:</td>
    	            <td><?php echo $this->lists['sitemap_priority'];?></td>
    	        </tr>
                <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                    <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_SHOW_DATE'), JText::_('COM_SEF_SITEMAP_DATE'));?></td>
                    <td width="200"><?php echo JText::_('COM_SEF_SITEMAP_DATE');?>:</td>
                    <td><?php echo $this->lists['sitemap_show_date'];?></td>
                </tr>
                <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                    <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_SHOW_FREQUENCY'), JText::_('COM_SEF_SITEMAP_CHANGE_FREQUENCY'));?></td>
                    <td width="200"><?php echo JText::_('COM_SEF_SITEMAP_CHANGE_FREQUENCY');?>:</td>
                    <td><?php echo $this->lists['sitemap_show_frequency'];?></td>
                </tr>
                <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                    <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_SHOW_PRIORITY'), JText::_('COM_SEF_SITEMAP_PRIORITY'));?></td>
                    <td width="200"><?php echo JText::_('COM_SEF_SITEMAP_PRIORITY');?>:</td>
                    <td><?php echo $this->lists['sitemap_show_priority'];?></td>
                </tr>
		      </table>
		  </fieldset>
		  </div>
		  <div class="fltrt" style="width: 50%">
          <?php $x = 0; ?>
		  <fieldset class="adminform">
		      <legend><?php echo JText::_('COM_SEF_PING_CONFIGURATION'); ?></legend>
		      <table class="adminform table table-striped">
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_PINGAUTO'), JText::_('COM_SEF_PING_AFTER_XML_GENERATION'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_PING_AFTER_XML_GENERATION');?>:</td>
    	            <td><?php echo $this->lists['sitemap_pingauto'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20" valign="top"><?php echo $this->tooltip(JText::_('COM_SEF_TT_SITEMAP_SERVICES'), JText::_('COM_SEF_PING_SERVICES'));?></td>
    	            <td width="200" valign="top"><?php echo JText::_('COM_SEF_PING_SERVICES');?>:</td>
    	            <td><?php echo $this->lists['sitemap_services'];?></td>
    	        </tr>
		      </table>
		  </fieldset>
		  <fieldset class="adminform">
		      <legend><?php echo JText::_('COM_SEF_MULTIPLE_SITEMAPS_LEGEND'); ?></legend>
		      <table class="adminform table table-striped">
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <td width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_MULTIPLE_SITEMAPS'), JText::_('COM_SEF_MULTIPLE_SITEMAPS'));?></td>
    	            <td width="200"><?php echo JText::_('COM_SEF_MULTIPLE_SITEMAPS');?>:</td>
    	            <td><?php echo $this->lists['multipleSitemaps'];?></td>
    	        </tr>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
    	            <th width="20"><?php echo $this->tooltip(JText::_('COM_SEF_TT_MULTIPLE_SITEMAPS_FILES'), JText::_('COM_SEF_MULTIPLE_SITEMAPS_FILES'));?></td>
    	            <th width="200"><?php echo JText::_('COM_SEF_MULTIPLE_SITEMAPS_FILES');?></td>
    	            <th>&nbsp;</td>
    	        </tr>
                <?php
                for ($i = 0, $n = count($this->lists['multipleSitemapsFilenames']); $i < $n; $i++) {
                    $s = $this->lists['multipleSitemapsFilenames'][$i];
                    ?>
    	        <tr<?php $x++; echo (($x % 2) ? '':' class="row1"' );?>>
                    <td>&nbsp;</td>
    	            <td><?php echo $s->title; ?></td>
    	            <td><input type="text" name="multipleSitemapsFilenames[<?php echo $s->sef; ?>]" class="inputbox" size="20" value="<?php echo $s->value; ?>" /> .xml</td>
    	        </tr>
                    <?php
                }
                ?>
		      </table>
		  </fieldset>
		  </div>
		  <div style="clear: both;"></div>