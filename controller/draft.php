<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure the $contentID value was passed properly
if(!isset($_GET['id']))
{
	header("Location: /"); exit;
}

// Prepare Values
$contentID = (int) $_GET['id'];

// Retrieve important content data
$contentData = Content::load($contentID);

// Prepare Values
Content::$returnURL = "/" . $contentData['url_slug'];
Content::$openPost = true;

// Validate Clearance
Content::validateClearance($contentData['status'], $contentData['uni_id']);

ModuleRelated::widget($contentID);
ModuleAuthor::widget(Me::$id);

// Run Comment Form, if applicable
if($contentData['comments'])
{
	ContentComments::interpreter($contentID, $url_relative, $contentData['comments']);
}

// Retrieve a list of hashtags
$hashtags = ModuleHashtags::get($contentID);

// Include Responsive Script
Photo::prepareResponsivePage();
Metadata::addHeader('<link rel="stylesheet" href="' . CDN . '/css/content-system.css" />');

/****** Page Configurations ******/
$config['canonical'] = "/" . $contentData['url_slug'];
$config['pageTitle'] = $contentData['title'];
Metadata::$index = true;
Metadata::$follow = true;

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

// Display the Page
echo '
<h1>' . $contentData['title'] . '</h1>
<p style="margin-bottom:0px;">Draft by <a href="' . URL::unifaction_social() . '/' . $contentData['handle'] . '">' . $contentData['display_name'] . '</a> (<a href="' . URL::fastchat_social() . '/' . $contentData['handle'] . '">@' . $contentData['handle'] . '</a>)</p>
<p>';

// Display the hashtag list
if($hashtags)
{
	$hashtagURL = URL::hashtag_unifaction_com();
	
	echo '<br />';
	
	foreach($hashtags as $htag)
	{
		echo '<a class="c-hashtag" href="' . $hashtagURL . '/' . $htag . '">#' . $htag . '</a> ';
	}
}

// Display the Body Text
if($contentData['body'])
{
	echo $contentData['body'];
}
else
{
	Content::output($contentID);
}

// Show Comments, if applicable
if($contentData['comments'])
{
	ContentComments::draw($contentID, $url_relative, $contentData['comments']);
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
