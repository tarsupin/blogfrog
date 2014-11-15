<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure the $contentID value was passed properly
if(!isset($contentID))
{
	header("Location: /"); exit;
}

// Prepare Values
Content::$openPost = true;

// Prepare Content Entry
Content::prepare($contentID);

/****** Page Configurations ******/
$config['canonical'] = "/" . Content::$contentData['url_slug'];
$config['pageTitle'] = Content::$contentData['title'];
Metadata::$index = true;
Metadata::$follow = true;

// Prepare the Page's Active Hashtag
$config['active-hashtag'] = Content::$contentData['primary_hashtag'];

// Prepare Values
You::$id = (int) Content::$contentData['uni_id'];
You::$handle = Content::$contentData['handle'];
You::$name = Content::$contentData['display_name'];

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

// Display the Content
Content::display();

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
