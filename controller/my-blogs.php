<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// If $userData is not already loaded, the page cannot be interpreted
if(!Me::$loggedIn)
{
	Me::redirectLogin("/my-blogs");
}

header("Location: /" . Me::$vals['handle']);