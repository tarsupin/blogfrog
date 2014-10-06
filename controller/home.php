<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare the Content Feed
ContentFeed::prepare();

// Retrieve a list of Recent Blog Posts
$contentIDs = array();

$getList = Database::selectMultiple("SELECT id FROM content_entries WHERE status >= ? ORDER BY id DESC LIMIT 0, 20", array(Content::STATUS_GUEST));

foreach($getList as $getID)
{
	$contentIDs[] = (int) $getID['id'];
}

/****** Page Configuration ******/
$config['canonical'] = "/";
//$config['pageTitle'] = "UniFaction";		// Up to 70 characters. Use keywords.
//$config['description'] = "All of your online interests with one login.";	// Overwrites engine: <160 char
Metadata::$index = false;
Metadata::$follow = true;
// Metadata::openGraph($title, $image, $url, $desc, $type);		// Title = up to 95 chars.

// Run Global Script
require(CONF_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

// Display the Feed Header
ContentFeed::displayHeader($config['site-name']);

echo '
<h3>Recent Blogs</h3>';

// Display the Feed
ContentFeed::displayFeed($contentIDs, true, Me::$id, true);

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
