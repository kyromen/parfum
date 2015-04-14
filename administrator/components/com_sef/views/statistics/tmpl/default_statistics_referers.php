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
		<th width="20%">
		<?php echo Jtext::_('COM_SEF_SOURCE'); ?>
		</th>
		<th width="50%">
		<?php echo Jtext::_('COM_SEF_PATH'); ?>
		</th>
		<th width="10%">
		<?php echo Jtext::_('COM_SEF_PAGEVIEWS'); ?>
		</th>
		<th width="10%">
		<?php echo Jtext::_('COM_SEF_PERCENT'); ?>
		</th>
		<th width="10%">
		<?php echo Jtext::_('COM_SEF_AVG_TIME'); ?>
		</th>
	</tr>
	<?php
	for($i=0;$i<count($this->referers);$i++) {
		?>
		<tr class="row<?php echo $i%2; ?>">
			<td><?php echo $this->referers[$i]["source"]; ?></td>
			<td><?php echo $this->referers[$i]["referralPath"]; ?></td>
			<td><?php echo $this->referers[$i]["visits"]; ?></td>
			<td><?php echo round(($this->referers[$i]["visits"]/$this->globals["visits"])*100,2); ?> %</td>
			<td><?php echo round($this->referers[$i]["timeOnSite"]/$this->referers[$i]["visits"],2); ?></td>
		</tr>
		<?php
	}
	?>
</table>