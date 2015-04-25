<?php
/**
* Optimize Your Tables - System plugin for daily database table optimization
* @version 1.2
* @copyright (C) 2013 - Emmanuel Lemor - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL.
* Optimize Your Tables is free software and based on the work of:  Joomlaportal.ru 
* Beta 1 for Joomla 1.7 by Emmanuel Lemor - This has not been tested on Joomla 1.6 and will not work in Joomla 1.5.
* v1.0 for Joomla 1.7 and Joomla 2.5 by Emmanuel Lemor - This has not been tested on versions older than Joomla v1.7
* v1.1 for Joomla 1.7 and Joomla 2.5 by Emmanuel Lemor - bug fix thanks to Ebo of Ebo Eppenga of Exact Corporate Services B.V.
* v1.2 for Joomla 1.7, Joomla 2.5 and now Joomla 3.x by Emmanuel Lemor - Tested on Joomla 3.x
*/

defined('JPATH_BASE') or die;

jimport( 'joomla.plugin.plugin' );

class plgSystemOptimizeYourTables extends JPlugin
{
	function plgSystemOptimizeYourTables( &$subject, $config)
	{
		parent::__construct( $subject, $config);
	}

	function onAfterInitialise()
	{
		$mainframe = &JFactory::getApplication();
        $host=$mainframe->getCfg('host');
        $user=$mainframe->getCfg('user');
        $password=$mainframe->getCfg('password');
        $database=$mainframe->getCfg('db');

		mysql_connect($host,$user,$password);
		@mysql_select_db($database) or die("Connection error");

		$currentTime = time();
		$tomorrowDate = date('Y-m-d', time());

	        $time = $this->params->get('time', '00:00:00');
	        $nextOptimization = $this->params->get('nextOptimization', $tomorrowDate . ' ' . $time);

	        $nextOptimizationTime = strtotime($nextOptimization);

		if ($nextOptimizationTime < $currentTime) {
			$dbo = JFactory::getDbo();
		
		// Statement to select the databases
			$db_select = 'SHOW DATABASES';
 
		// Query mySQL for the results
			$db_result = MYSQL_QUERY($db_select);
 
	    // Loop through all the databases
     		WHILE ($db_row = MYSQL_FETCH_ARRAY($db_result)) {
 
          // Select currently looped database and continue only if successful
          IF (MYSQL_SELECT_DB($db_row[0])) {
 
           /*    // Show database name - you may uncomment this to see what database it is seeing.
               ECHO "<br><b>";
               ECHO $db_row[0];
               ECHO "</b><br>";
 		   */	
               // Statement to select the tables in the currently looped database
               $tbl_status = 'SHOW TABLE STATUS FROM `' . $db_row[0] . '`';

               // Query mySQL for the results
               $tbl_result = MYSQL_QUERY($tbl_status);
 
                    // Check to see if any tables exist within database
                    IF(MYSQL_NUM_ROWS($tbl_result)) {
 
                         // Loop through all the tables
                         WHILE ($tbl_row = MYSQL_FETCH_ARRAY($tbl_result)) {
 
                              // Statement to optimize table
                              $opt_table = 'OPTIMIZE TABLE `' . $tbl_row[0] . '`';
 
                              // Query mySQL to optimize currently looped table
                             $opt_result = MYSQL_QUERY($opt_table);
 
                           /*   //  Show table name - you may uncomment this to see what table(s) it is seeing.
                              ECHO "  <i>";
                              ECHO $tbl_row[0];
                              ECHO "</i><br>";
						   */ 

                         } // End table while loop 
 
                    } ELSE {
 
                         // Alert that there are no tables within database
                              ECHO "  <i>No Tables</i><br>";
 
                    } // End table exists if statement
 
          } // End database if statement
 
     } // End database while loop
/* 
// Show that operation was successful - you may uncomment this if you want a visual confirmation of the operation.
ECHO "<br><b>Above tables successfully optimized.</b>";
*/
 
			$nextOptimization = date('Y-m-d H:i:s', $nextOptimizationTime + 86400);

			$query = "UPDATE #__extensions"
				. " SET params = 'time=" . $time . "\nnextOptimization=" . $nextOptimization . "\n'"
				. " WHERE folder = 'system'"
				. "   AND element = 'optimizeyourtables'"
				;
			$dbo->setQuery($query);
			$dbo->query();

		}
	}
} 
?>