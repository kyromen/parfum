<?php
/**
 * @package customfilters
 * @version $Id: include/search.php  2014-8-12 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2014 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('JPATH_BASE') or die;

class CfSearchHelper extends JObject{

	/*in a later version take into account the common words in the tokenization*/
	private $commonWords=array('OR','AND');
	

	/**
	 * Method to tokenize a text string.
	 *
	 * @param   string   $input   The input to tokenize.
	 * @param   string   $lang    The language of the input.
	 * @param   boolean  $phrase  Flag to indicate whether input could be a phrase. [optional]
	 *
	 * @return  array  An array of FinderIndexerToken objects.
	 *
	 * @since   2.5
	 */
	public static function tokenize($input, $lang, $matching = 'any')
	{
		static $cache;
		$store = JString::strlen($input) < 128 ? md5($input . '::' . $lang . '::' . $matching) : null;

		// Check if the string has been tokenized already.
		if ($store && isset($cache[$store]))
		{
			return $cache[$store];
		}
		/*
		 * Remove whitespaces at start and end
		 * Remove multiple space characters and replaces with a single space.
		 * convert to lower case
		 */
		//$input = preg_replace('#[^\pL\pM\pN\p{Pi}\p{Pf}\'+-.,]+#mui', ' ', $input);
		$input = JString::trim($input);
		$input = preg_replace('#\s+#mui', ' ', $input);
		$input=strtolower($input);		

		//if($matching=='exact' || count($input)==1)return $input;
		$terms = explode(' ', $input);		
		$primary_tokens=array();
		$tokens=array();
		
		
		/*
		 * Create the single word tokens
		 */
		for ($i = 0; $i<count($terms); $i++)
		{
			$token=new stdClass();
			$token->term=$terms[$i];
			$token->phrase=false;
			$token->numerical=preg_match('/^-?[0-9]+(\.[0-9]+)?$/', $token->term);
			
			/*
			 * check if the number is followed by a unit
			 * Units are usually 1-2 characters
			 * In that case unit should not be a new token but part of this one
			 */
			if($token->numerical && isset($terms[$i+1]) && strlen($terms[$i+1])<=2){
				$tmp_term=$token->term;
				$token->term=$tmp_term.' '.$terms[$i+1];			
				
				//create 1 more without the space
				$token_tmp=new stdClass();
				$token_tmp->term=$tmp_term.$terms[$i+1];
				$token_tmp->phrase=false;
				$token_tmp->numerical=true;
				$primary_tokens[]=$token_tmp;
				$i++;
			}			
			// Add the token to the stack.
			$primary_tokens[]=$token;
		}
		
		//check of any of the tokens is category/manufacturer/customfield value
		$categories=self::getCategories($primary_tokens);
		$manufacturers=self::getManufacturers($primary_tokens);
		$custom_values=self::getCustomfieldValues($primary_tokens);
			
		/*
		 * Create the combinations
		 * The rule is that the phrase should have as much words as it initialy had
		 * If any of the words is category, manufacturer, custom value, then new combinations without that word can be generated
		 */
		$spacer=' ';
		// Create two and three word phrase tokens from the individual words.
		for ($i = 0, $n = count($primary_tokens); $i < $n; $i++)
		{			

			// Setup the phrase positions.
			$i2 = $i + 1;
			$i3 = $i + 2;
			echo $n;
			// Create the two word phrase.
			if ($n==2 && $i2 < $n && isset($primary_tokens[$i2]))
			{
				$token=new stdClass();
				$token->term=$primary_tokens[$i]->term.$spacer.$primary_tokens[$i2]->term;
				$token->phrase=true;
				if(in_array($token->term, $manufacturers))$token->manufacturer=true;
				else $token->manufacturer=false;

				if(in_array($token->term, $categories))$token->category=true;
				else $token->category=false;
				
				// Add the token to the stack.
				$tokens[]=$token;
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i2]->term.$spacer.$primary_tokens[$i]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
			}

			// Create the three word phrase.
			if ($n==3 && $i3 < $n && isset($primary_tokens[$i3]))
			{
				$token=new stdClass();
				$token->term=$primary_tokens[$i]->term.$spacer.$primary_tokens[$i2]->term.$spacer.$primary_tokens[$i3]->term;
				$token->phrase=true;
				if(in_array($token->term, $manufacturers))$token->manufacturer=true;
				else $token->manufacturer=false;

				if(in_array($token->term, $categories))$token->category=true;
				else $token->category=false;

				// Add the token to the stack.
				$tokens[]=$token;
				
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i]->term.$spacer.$primary_tokens[$i3]->term.$spacer.$primary_tokens[$i2]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i2]->term.$spacer.$primary_tokens[$i]->term.$spacer.$primary_tokens[$i3]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i2]->term.$spacer.$primary_tokens[$i3]->term.$spacer.$primary_tokens[$i]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i3]->term.$spacer.$primary_tokens[$i]->term.$spacer.$primary_tokens[$i2]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
				
				//reverse the phrase
				$reverse_token=new stdClass();
				$reverse_token->term=$primary_tokens[$i3]->term.$spacer.$primary_tokens[$i2]->term.$spacer.$primary_tokens[$i]->term;
				$reverse_token->phrase=true;
				if(in_array($reverse_token->term, $manufacturers))$reverse_token->manufacturer=true;
				else $reverse_token->manufacturer=false;

				if(in_array($reverse_token->term, $categories))$reverse_token->category=true;
				else $reverse_token->category=false;
				$tokens[]=$reverse_token;
			}
		}
		
print_r($tokens);
		//todo if its multiwords token check if a category/manufacturer has aboslute match
			
	}


	/**
	 * Checks if any of the terms is category
	 *
	 * @param 	mixed 	$input - string or array of strings
	 * @return	array	The records from the db matching
	 * @since	2.2.0
	 */
	public function getCategories($tokens){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('LOWER(cl.category_name)')
		->from('#__virtuemart_categories_'.VMLANG.' AS cl')
		->innerJoin('#__virtuemart_categories AS c ON c.virtuemart_category_id=cl.virtuemart_category_id');

		if(!is_array($tokens)){
			$query->where('cl.category_name LIKE '.$db->quote($db->escape($tokens->term, true).'%', false));
		}
		else {
			$whereOr=array();
			foreach ($tokens as $token){
				$whereOr[]='cl.category_name LIKE '.$db->quote($db->escape($token->term, true).'%', false);
				$query->where(implode(' OR ', $whereOr));
			}
		}
		$query->where('c.published=1');
		$db->setQuery($query);
		$results=$db->loadColumn();
		return $results;
	}

	/**
	 * Checks if any of the terms is manufacturer
	 *
	 * @param 	mixed 	$input - string or array of strings
	 * @return	array	The records from the db matching
	 * @since	2.2.0
	 */
	public function getManufacturers($tokens){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('LOWER(ml.mf_name)')
		->from('#__virtuemart_manufacturers_'.VMLANG.' AS ml')
		->innerJoin('#__virtuemart_manufacturers AS m ON m.virtuemart_manufacturer_id=ml.virtuemart_manufacturer_id');

		if(!is_array($tokens)){
			$query->where('ml.mf_name LIKE '.$db->quote($db->escape($tokens->term, true).'%', false));
		}
		else {
			$whereOr=array();
			foreach ($tokens as $token){
				$whereOr[]=('ml.mf_name LIKE '.$db->quote($db->escape($token->term, true).'%', false));
				$query->where(implode(' OR ', $whereOr));
			}
		}
		$query->where('m.published=1');
		$db->setQuery($query);
		$results=$db->loadColumn();
		return $results;
	}
	
	
	/**
	 * Checks if any of the terms is custom field value
	 *
	 * @param 	mixed 	$tokens - string or array of strings
	 * @return	array	The records from the db matching
	 * @since	2.2.0
	 */
	public function getCustomfieldValues($tokens){
		$vmCompatibility=VmCompatibility::getInstance();
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('LOWER('.$vmCompatibility->getColumnName('custom_value').') AS customfield_value')
		->from('#__virtuemart_product_customfields AS cfv')
		->innerJoin('#__virtuemart_customs AS cf ON cfv.virtuemart_custom_id=cf.virtuemart_custom_id');

		if(!is_array($tokens)){
			$query->where('cfv.'.$vmCompatibility->getColumnName('custom_value').' LIKE '.$db->quote($db->escape($tokens->term, true).'%', false));
		}
		else {
			$whereOr=array();
			foreach ($tokens as $token){
				$whereOr[]='cfv.'.$vmCompatibility->getColumnName('custom_value').' LIKE '.$db->quote($db->escape($token->term, true).'%', false);
				$query->where(implode(' OR ', $whereOr));
			}
		}
		$query->where('cf.published=1');
		$db->setQuery($query);
		$results=$db->loadColumn();
		$results=array_unique($results);
		return $results;
	}

}