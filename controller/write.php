<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Force the user to log in
if(!Me::$loggedIn)
{
	Me::redirectLogin("/");
}

// Make sure the ID is used
if(!isset($_GET['id']))
{
	header("Location: /"); exit;
}

// Prepare the Class and Values
$contentForm = new ContentForm((int) $_GET['id']);
$contentForm->contentType = "blog";
$contentForm->baseURL = "/write";
$contentForm->redirectOnError = "/";
$contentForm->urlPrefix = Me::$vals['handle'] . '/';
$contentForm->openPost = true;

// Set the modules allowed in this content entry
$contentForm->settings = array(
	"Hashtags"		=> true
,	"Related"		=> true
);

// Make sure the user has access
$contentForm->verifyAccess();

// Run the Interpreter
$contentForm->interpret();

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display the Page
echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

$contentForm->draw();

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
