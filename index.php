<?php

/****** Preparation ******/
define("CONF_PATH",		dirname(__FILE__));
define("SYS_PATH", 		dirname(CONF_PATH) . "/system");

// Load phpTesla
require(SYS_PATH . "/phpTesla.php");

// Initialize Active User
Me::initialize();

// Determine which page you should point to, then load it
require(SYS_PATH . "/routes.php");

/****** Dynamic URLs ******/
// If a page hasn't loaded yet, check if there is a dynamic load
if($url[0] != '')
{
	// Check if we're loading a specific blog
	if(isset($url[1]))
	{
		$prep = $url[0] . '/' . $url[1];
		
		if($contentID = (int) Database::selectValue("SELECT content_id FROM content_by_url WHERE url_slug=? LIMIT 1", array($prep)))
		{
			require(APP_PATH . '/controller/read.php'); exit;
		}
	}
	
	// Load a specific user
	if($userData = User::getDataByHandle($url[0], "uni_id, handle, display_name"))
	{
		// Prepare Data
		$userData['uni_id'] = (int) $userData['uni_id'];
		
		You::$id = (int) $userData['uni_id'];
		You::$handle = $userData['handle'];
		You::$name = $userData['display_name'];
		
		require(APP_PATH . '/controller/user.php'); exit;
	}
}
//*/

/****** 404 Page ******/
// If the routes.php file or dynamic URLs didn't load a page (and thus exit the scripts), run a 404 page.
require(SYS_PATH . "/controller/404.php");