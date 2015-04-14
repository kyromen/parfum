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

$sefConfig = SEFConfig::getConfig();
?>
<form action="<?php echo JRoute::_('index.php?option=com_sef&view=statistics'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_SEF_FILTERS'); ?></legend>
        <table>
            <tr>
                <td width="100%" valign="bottom"></td>
                <td nowrap="nowrap"><?php echo JText::_('COM_SEF_FILTER_SEF_URLS'); ?>:</td>
                <td nowrap="nowrap"><?php echo JText::_('COM_SEF_FILTER_REAL_URLS'); ?>:</td>
                <td nowrap="nowrap"><?php echo JText::_('COM_SEF_COMPONENT'); ?>:</td>
                <?php if ($sefConfig->langEnable) { ?>
                <td nowrap="nowrap"><?php echo JText::_('COM_SEF_LANGUAGE'); ?>:</td>
                <?php } ?>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="vertical-align: top;">
                    <?php echo $this->lists['filterSEF']; ?>
                </td>
                <td style="vertical-align: top;">
                    <?php echo $this->lists['filterReal']; ?>
                </td>
                <td style="vertical-align: top;">
                    <?php echo $this->lists['comList']; ?>
                </td>
                <?php if ($sefConfig->langEnable) { ?>
                <td style="vertical-align: top;">
                    <?php echo $this->lists['filterLang']; ?>
                </td>
                <?php } ?>
                <td style="vertical-align: top;">
                    <?php echo $this->lists['filterReset']; ?>
                </td>
            </tr>
        </table>
    </fieldset>
    
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort','COM_SEF_URL',"u.sefurl",$this->ordering['filterOrder'] == 'u.sefurl' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
                <!--
				<th>
					<?php echo JHTML::_('grid.sort','COM_SEF_GOOGLE_PAGERANK',"s.page_rank",$this->ordering['filterOrder'] == 's.page_rank' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
                -->
				<?php
				if(strlen($this->config->google_apikey)) {
					?>
    				<th>
    					<?php echo JHTML::_('grid.sort','COM_SEF_GOOGLE_PAGESPEED',"s.page_speed_score",$this->ordering['filterOrder'] == 's.page_speed_score' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
    				</th>
					<?php
				}
				?>
				<th>
					<?php echo JHTML::_('grid.sort','COM_SEF_GOOGLE_TOTAL_INDEXED',"s.total_indexed",$this->ordering['filterOrder'] == 's.total_indexed' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort','COM_SEF_GOOGLE_POPULARITY',"s.popularity",$this->ordering['filterOrder'] == 's.popularity' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort','COM_SEF_FACEBOOK_INDEXED',"s.facebook_indexed",$this->ordering['filterOrder'] == 's.facebook_indexed' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
				<th>
					<?php echo JHTML::_('grid.sort','COM_SEF_TWITTER_INDEXED',"s.twitter_indexed",$this->ordering['filterOrder'] == 's.twitter_indexed' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
				<th width="5px" >
					<?php echo JHTML::_('grid.sort','COM_SEF_VALIDITY',"s.validation_score",$this->ordering['filterOrder'] == 's.validation_score' ? $this->ordering['filterOrderDir'] : 'desc', $this->ordering['filterOrder']); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			for($i=0;$i<count($this->items);$i++) {
				$item=$this->items[$i];
				$validation_score=explode("/",$item->validation_score);
				$score="";
				$uri=urlencode(str_replace("/administrator","",JFactory::getURI()->base(false)).$item->url);
				$url=str_replace("http://","",urldecode($uri));
				$url=str_replace("www.","",$url);
				if($validation_score[0]=="true") {
					$score=JHTML::_('image.administrator',"admin".'/'."tick.png");
				} else if($validation_score[0]=="false") {
					$score=JHTML::_('image.administrator',"admin".'/'."publish_x.png");
					$score.=JHTML::_('link',"http://validator.w3.org/check?uri=".$uri,JText::_('COM_SEF_VALIDATE_ERRORS'),array('target'=>'_blank'));
				}
				if(strlen($item->page_speed_score)) {
					$speed=new JRegistry();
					$speed->loadString($item->page_speed_score, 'INI');
					$speed=$speed->toObject();
				}
				
				
				?>
				<tr class="row<?php echo $i%2; ?>">
					<td class="center">
						<?php echo JHTML::_('grid.id',$i,$item->id); ?>
					</td>
					<td><?php echo strlen($item->url)?$item->url:JText::_('COM_SEF_HOMEPAGE'); ?></td>
					<!-- <td><?php echo $item->page_rank; ?></td> -->
					<?php
					if(strlen($this->config->google_apikey)) {
						?>
						<td>
							<?php echo strlen($item->page_speed_score)?JHTML::_('link',"https://developers.google.com/pagespeed/#url=".urlencode($url)."&mobile=false",$speed->score."/100",array('target'=>'_blank')):""; ?>
							&nbsp;
							<?php echo strlen($item->page_speed_score)?JHTML::_('link','index.php?option=com_sef&tmpl=component&view=statistics&layout=speed&id='.$item->id,JText::_('COM_SEF_SHOW_SPEED'),array('class'=>'modal','rel'=>"{handler: 'iframe', size: {x:450, y:400}}")):""; ?>
						</td>
						<?php
					}
					?>
					<td><?php echo JHTML::_('link',"http://www.google.com/search?q=site:".$url,$item->total_indexed,array('target'=>'_blank')); ?></td>
					<td><?php echo JHTML::_('statistic.link','http://www.google.com/search?q="'.$url.'"+-site:'.$url,$item->popularity,array('target'=>'_blank')); ?></td>
					<td><?php echo JHTML::_('statistic.link','http://www.google.com/search?q="'.$url.'"+site:'.'facebook.com',$item->facebook_indexed,array('target'=>'_blank')); ?></td>
					<td><?php echo JHTML::_('statistic.link','http://www.google.com/search?q="'.$url.'"+site:'.'twitter.com',$item->twitter_indexed,array('target'=>'_blank')); ?></td>
					<td align="center" <?php echo (($validation_score[0]=="false")?('class="hasTip" title="'.JText::_('COM_SEF_RESULTS').'::'.JText::_('COM_SEF_ERRORS').': '.$validation_score[1].'<br />'.JText::_('COM_SEF_WARNINGS').': '.$validation_score[2].'"'):""); ?>>
					<?php echo $score; ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="99">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
<input type="hidden" name="option" value="com_sef" />
<input type="hidden" name="controller" value="statistics" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->ordering['filterOrder']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->ordering['filterOrderDir']; ?>" />
<?php echo JHTML::_('form.token'); ?>
</form>