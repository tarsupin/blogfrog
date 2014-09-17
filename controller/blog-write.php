<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Force the user to log in
if(!Me::$loggedIn)
{
	Me::redirectLogin("/");
}

// Prepare Form
Content::$openPost = true;
$contentForm = new ContentForm('/blog-write', (isset($_GET['content']) ? (int) $_GET['content'] : 0));

// Set the modules allowed in this content entry
/*
$contentForm->modules = array(
	"Hashtags"		=> ArticleForm::MODULE_TYPE_META
,	"Text"			=> ContentForm::MODULE_TYPE_SEGMENT
,	"Image"			=> ContentForm::MODULE_TYPE_SEGMENT
,	"Video"			=> ContentForm::MODULE_TYPE_SEGMENT
,	"Related"		=> ContentForm::MODULE_TYPE_META
);
*/

// Make sure you have permissions to edit this form
$contentForm->verifyAccess("/my-blogs");

// Prepare Settings
ContentForm::$contentType = 'blog';
$contentForm->urlPrefix = Me::$vals['handle'] . '/';
$contentForm->urlClearance = ContentForm::URL_ALLOW;
$contentForm->useHashtags = true;

// Run Form Behaviors and Interpretations
$contentForm->runBehavior();
$contentForm->runInterpreter();

// Include Responsive Script
Photo::prepareResponsivePage();
Metadata::addHeader('<link rel="stylesheet" href="' . CDN . '/css/content-block.css" />');

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

echo '
<h1>' . $contentForm->contentData['title'] . '</h1>';

if($contentForm->contentData['status'] != 0)
{
	echo '<p>Blog is LIVE. Posted ' . Time::fuzzy((int) $contentForm->contentData['date_posted']) . '.</p>';
}

$contentForm->drawEditingBox();
$contentForm->drawContent();

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
