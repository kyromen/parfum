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

$colors=array("AFD8F8","F6BD0F","8BBA00","FF8E46","008E8E");

$i=0;
foreach($this->sources as $source) {
	$set=$xml->createElement('set');
	
	$name=$xml->createAttribute('name');
	$name->appendChild($xml->createTextNode(Jtext::_('COM_SEF_'.strtoupper(str_replace(array('(',')','none'),array('','','DIRECT_VISITS'),$source["medium"])))));
	$set->appendChild($name);
	
	$value=$xml->createAttribute('value');
	$value->appendChild($xml->createTextNode($source["visits"]));
	$set->appendChild($value);
	
	$show_name=$xml->createAttribute('show_name');
	$show_name->appendChild($xml->createTextNode(1));
	$set->appendChild($show_name);
	
	$color=$xml->createAttribute('color');
	$color->appendChild($xml->createTextNode($colors[$i]));
	$set->appendChild($color);
	
	$chart->appendChild($set);
	
	$i++;
}
?>
<div id="sources_chart">
<embed id="sources" width="260" height="260" flashvars="chartWidth=260&chartHeight=260&debugMode=0&DOMId=sources&registerWithJS=0&scaleMode=noScale&lang=EN&dataXML=<?php echo str_replace('<?xml version="1.0"?>',"",str_replace("\n","",str_replace('"',"'",$xml->saveXML()))); ?>" allowscriptaccess="always" quality="high" name="sources" src="<?php echo JFactory::getUri()->base(false); ?>/components/com_sef/assets/charts/pie2d/chart.swf" type="application/x-shockwave-flash">
</div>