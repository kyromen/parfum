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
<table class="adminlist table table-striped">
	<tr>
		<th width="70%"><?php echo Jtext::_('COM_SEF_URL'); ?></th>
		<th width="10%"><?php echo JText::_('COM_SEF_PAGEVIEWS'); ?></th>
		<th width="10%"><?php echo Jtext::_('COM_SEF_PERCENT_OF_WEB'); ?></th>
		<th width="10%"><?php echo JText::_('COM_SEF_AVG_TIME_ON_PAGE'); ?></th>
	</tr>
	<?php
	for($i=0;$i<count($this->top);$i++) {
		?>
		<tr class="row<?php echo $i%2; ?>">
			<td><?php echo $this->top[$i]["pagePath"]; ?></td>
			<td><?php echo $this->top[$i]["pageviews"]; ?></td>
			<td><?php echo round(($this->top[$i]["pageviews"]/$this->globals["pageviews"])*100,2); ?></td>
			<td><?php echo round($this->top[$i]["timeOnPage"]/$this->top[$i]["pageviews"],2); ?> s</td>
		</tr>
		<?php
	}
	?>
</table>