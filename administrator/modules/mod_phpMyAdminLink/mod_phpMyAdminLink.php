<?php
/*------------------------------------------------------------------------
# phpMyAdminLink
# ------------------------------------------------------------------------
# author &nbsp; &nbsp;Emil I Bohach
# @copyright Copyright (C) 2012 Emil I Bohach,  All rights reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.eibconsult.com
# Technical Support: &nbsp;Forum - http://www.eibconsult.com/forum/index.html
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Initialize some variables
$user = JFactory::getUser();
$uid = $user->id;
if ( $uid ) {
	$db = JFactory::getDBO();
	$db->setQuery("SELECT group_id FROM `#__user_usergroup_map`  WHERE user_id = '$uid' ");
	$gid = $db->loadResult();
}

$allowed_viewers = $params->get('allowed_viewers');
if ( $gid >= $allowed_viewers ) { 
	$app 		=& JFactory::getApplication();
	$form =  '<form  method="post" action="'.$params->get('target').'" name="login_form" target="_blank" id="phpMyAdminLink">
		<input type="hidden" name="pma_username" value="'.$app->getCfg('user').'"  />
		<input type="hidden" name="pma_password" value="'.$app->getCfg('password').'" />
		<input type="hidden" name="server" value="1" />    
		<input type="hidden" name="OK" value="OK" />
		<input type="hidden" name="lang" value="de-utf-8" />
		<input type="hidden" name="convcharset" value="iso-8859-1" />
	</form>';
	?>
	<span>
		<?php echo $form;?>
		<a href="#" onclick="document.getElementById('phpMyAdminLink').submit();" title="phpMyAdminLink"><img src="<?php echo JURI::Base(); ?>/modules/mod_phpMyAdminLink/phpMyAdmin_logo.png" /></a>
	</span>
<?php } ?>