<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// If $userData is not already loaded, the page cannot be interpreted
if(!isset($userData))
{
	header("Location: /");
}

// Prepare Values
$myPage = (Me::$id == $userData['uni_id']);

// If the user owns this blog list
if($myPage)
{
	// Run the Blog Creation Form
	if(Form::submitted("uniblog-gen"))
	{
		// Make sure you don't have too many drafts
		$draftCount = (int) Database::selectValue("SELECT COUNT(*) as totalNum FROM content_by_user u INNER JOIN content_entries c ON u.content_id=c.id WHERE u.uni_id=? AND c.status=? LIMIT 10", array(Me::$id, 0));
		
		if($draftCount >= 10)
		{
			Alert::error("Too Many Drafts", "You have ten unfinished blogs being drafted. Please finish some before creating new blogs.");
		}
		
		if(FormValidate::pass())
		{
			// Create the New Blog
			$contentID = ContentForm::createEntry(Me::$id, "Untitled Blog", Content::STATUS_DRAFT, 0);
			
			// Begin editing the blog
			header("Location: /blog-write?content=" . $contentID); exit;
		}
	}
}

// Get a list of blog entries
$blogs = Content::getByUser($userData['uni_id'], 0, 20, ($myPage ? Content::STATUS_DRAFT : Content::STATUS_PUBLIC), "id, status");

// Include Responsive Script
Photo::prepareResponsivePage();

/****** Page Configurations ******/
$config['canonical'] = "/" . $userData['handle'];
$config['pageTitle'] = $userData['display_name'] . ' - ' . $config['site-name'];
Metadata::$index = false;
Metadata::$follow = true;

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

echo '
<style>
.content-list { overflow:auto; margin-bottom:22px; }
.content-list>.block-title { font-size:1.3em; font-weight:bold; }
.content-list:nth-child(odd) .list-img-wrap { float:left; min-width:110px; max-width:35%; margin-right:15px; }
.content-list:nth-child(even) .list-img-wrap { float:left; min-width:110px; max-width:35%; margin-right:15px; }
.list-img { max-width:100%; max-height:120px; text-align:center; }
.list-details { margin-top:8px; font-size:0.9em; }
</style>

<div id="panel-right"></div>
<div id="content">' . Alert::display();

echo '
<h1>' . $userData['display_name'] . ' - Blogs</h1>';

// If the user owns this list, show the option to create a new blog
if($myPage)
{
	echo '
	<form class="uniform" action="/' . Me::$vals['handle'] . '" method="post">' . Form::prepare("uniblog-gen") . '
		<p><input type="submit" name="submit" value="Create New Blog" tabindex="30" /></p>
	</form>';
}

// Display the Page
foreach($blogs as $blog)
{
	$blog['id'] = (int) $blog['id'];
	
	// Retrieve core data about this article (main title, body, image, etc)
	$coreData = Content::scanForCoreData($blog['id']);
	
	// Display the Content
	echo '
	<div class="content-list">';
	
	// If we have a small version of the image, use that one
	if($coreData['mobile_url'])
	{
		echo '
		<div class="list-img-wrap"><a href="/' . $coreData['url_slug'] . '">' . Photo::responsive($coreData['mobile_url'], "", 950, "", 950, "list-img") . '</a></div>';
	}
	
	// If there is an image, show it
	else if($coreData['image_url'])
	{
		echo '
		<div class="list-img-wrap"><a href="/' . $coreData['url_slug'] . '">' . Photo::responsive($coreData['image_url'], $coreData['mobile_url'], 950, "", 950, "list-img") . '</a></div>';
	}
	
	// If this is a draft, there are special links required
	if($blog['status'] == 0)
	{
		echo '
		<div class="block-title">[DRAFT] <a href="/draft?id=' . $blog['id'] . '">' . $coreData['title'] . '</a></div>';
	}
	else
	{
		echo '
		<div class="block-title">' . ($blog['status'] == 0 ? '[DRAFT] ' : '') . '<a href="/' . $coreData['url_slug'] . '">' . $coreData['title'] . '</a></div>';
	}
	
	echo '
		<div class="block-body">' . $coreData['body'] . '</div>';
	
	// If this is the user's page, allow them to have editing links available
	if($myPage)
	{
		echo '
		<div class="list-details">
			<a href="/blog-write?content=' . $blog['id'] . '">Edit This Blog</a>
		</div>';
	}
	else
	{
		echo '
		<div class="list-details">Written by <a href="' . URL::unifaction_social() . '/' . $coreData['handle'] . '">' . $coreData['display_name'] . '</a> (<a href="' . URL::fastchat_social() . '/' . $coreData['handle'] . '">@' . $coreData['handle'] . '</a>) - ' . date("F jS, Y", $coreData['date_posted']) . '</div>';
	}
	
	echo '
	</div>';
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
