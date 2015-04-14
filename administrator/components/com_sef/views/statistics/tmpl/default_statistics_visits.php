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

$xml=new DomDocument("1.0");
$chart=$xml->createELement('graph');

$decimalPrecision=$xml->createAttribute('decimalPrecision');
$decimalPrecision->appendChild($xml->createTextNode(0));
$chart->appendChild($decimalPrecision);

$formatNumberScale=$xml->createAttribute('formatNumberScale');
$formatNumberScale->appendChild($xml->createTextNode(0));
$chart->appendChild($formatNumberScale);

$xml->appendChild($chart);

foreach($this->visits as $visit) {
	$set=$xml->createElement('set');
	
	$label=$xml->createAttribute('name');
	$label->appendChild($xml->createTextNode(JHTML::_('date',$visit["year"]."-".$visit["month"]."-".$visit["day"],"d.m.Y")));
	$set->appendChild($label);
	
	$value=$xml->createAttribute('value');
	$value->appendChild($xml->createTextNode($visit["visits"]));
	$set->appendChild($value);
	
	$show_name=$xml->createAttribute('show_name');
	$show_name->appendChild($xml->createTextNode(1));
	$set->appendChild($show_name);
	
	$chart->appendChild($set);
	
}
?>
<div id="visits_chart">
<embed id="visits" width="500" height="260" flashvars="chartWidth=500&chartHeight=260&debugMode=0&DOMId=visits&registerWithJS=0&scaleMode=noScale&lang=EN&dataXML=<?php echo str_replace('<?xml version="1.0"?>',"",str_replace("\n","",str_replace('"',"'",$xml->saveXML()))); ?>" allowscriptaccess="always" quality="high" name="visits" src="<?php echo JFactory::getUri()->base(false); ?>/components/com_sef/assets/charts/line2d/chart.swf" type="application/x-shockwave-flash">
</div>