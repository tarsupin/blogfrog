<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure the $contentID value was passed properly
if(!isset($contentID))
{
	header("Location: /"); exit;
}

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

// Run Tip Exchanges
if($getData = Link::getData("send-tip-blogfrog") and is_array($getData) and isset($getData[0]))
{
	// Get the user from the post
	Credits::tip(Me::$id, (int) $getData[0]);
}

// Include Responsive Script
Photo::prepareResponsivePage();
Metadata::addHeader('<link rel="stylesheet" href="' . CDN . '/css/content-block.css" />');

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
<p style="margin-bottom:0px;">Published ' . date("F jS, Y", $contentData['date_posted']) . ' by <a href="' . URL::unifaction_social() . '/' . $contentData['handle'] . '">' . $contentData['display_name'] . '</a> (<a href="' . URL::fastchat_social() . '/' . $contentData['handle'] . '">@' . $contentData['handle'] . '</a>)</p>
<p><a href="' . Content::$returnURL . "?" . Link::prepareData("send-tip-blogfrog", $contentData['uni_id']) . '">Tip the Author</a> | <a href="' . Content::shareContent($contentID, "article") . '">Share this Article</a> | <a href="' . Content::chatContent($contentID, "article") . '">Chat this Article</a> | <a href="' . Content::setVote($contentID) . '">Like</a> | <a href="' . Content::flag($contentID) . '">Flag</a>';

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