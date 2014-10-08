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
			header("Location: /write?id=" . $contentID); exit;
		}
	}
}

// Get a list of the user's Content Entries
$contentIDs = ContentFeed::getUserEntryIDs($userData['uni_id']);

// Prepare the Content Feed
ContentFeed::prepare();

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
<div id="panel-right"></div>
<div id="content">' . Alert::display();

// Display the Feed Header
ContentFeed::displayHeader($userData['display_name'] . "'s Blogs", "Home", "/");

// If the user owns this list, show the option to create a new blog
if($myPage)
{
	echo '
	<form class="uniform" action="/' . Me::$vals['handle'] . '" method="post">' . Form::prepare("uniblog-gen") . '
		<p><input type="submit" name="submit" value="Create New Blog" tabindex="30" /></p>
	</form>';
}

// Display the Feed
ContentFeed::displayFeed($contentIDs, true, Me::$id);

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");
