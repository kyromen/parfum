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

?>
<table width="100%" class="table table-striped">
	<tr>
		<td><?php echo JText::_('COM_SEF_VISITORS'); ?></td>
		<td><?php echo @$this->globals["visitors"]; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_UNIQUE_VISITORS'); ?></td>
		<td><?php echo @$this->globals["newVisits"]; ?></td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_TOTAL_VISITS'); ?></td>
		<td><?php echo @$this->globals["visits"]; ?></td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_NEW_VISITS_RATE'); ?></td>
		<td><?php echo @round($this->globals["percentNewVisits"],2); ?> %</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_BOUNCES_RATE'); ?></td>
		<td><?php echo @round($this->globals["visitBounceRate"],2); ?> %</td>
	<tr>
		<td><?php echo JText::_('COM_SEF_PAGEVIEWS'); ?></td>
		<td><?php echo @$this->globals["pageviews"]; ?></td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_UNIQUE_PAGEVIEWS'); ?></td>
		<td><?php echo @$this->globals["uniquePageviews"]; ?></td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_PAGES_PER_VISIT'); ?></td>
		<td><?php echo @round($this->globals["pageviewsPerVisit"],2); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_AVG_TIME_ON_SITE'); ?></td>
		<td><?php echo @round($this->globals["avgTimeOnSite"],2); ?> s</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_AVG_TIME_ON_PAGE'); ?></td>
		<td><?php echo @round($this->globals["avgTimeOnPage"],2); ?> s</td>
	</tr>
	<tr>
		<td><?php echo Jtext::_('COM_SEF_ENTRANCES'); ?></td>
		<td><?php echo @$this->globals["entrances"]; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_ENTRANCE_RATE'); ?></td>
		<td><?php echo @$this->globals["bounces"]; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_ENTRANCE_BOUNCE_RATE'); ?></td>
		<td><?php echo @round($this->globals["entranceBounceRate"],2); ?> %</td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_EXISTS'); ?></td>
		<td><?php echo @$this->globals["exits"]; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_SEF_EXIT_RATE'); ?></td>
		<td><?php echo @round($this->globals["exitRate"],2); ?> %</td>
	</tr>
</table>