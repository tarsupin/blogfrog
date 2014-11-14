<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// UniFaction Dropdown Menu
$extraColumns = (Me::$loggedIn ? '<li class="menu-slot' . ($url[0] == Me::$vals['handle'] ? " menu-active" : "") . '"><a href="/' . Me::$vals['handle'] . '">My Blog</a></li>' : '<li class="menu-slot"><a href="/login">My Blog</a></li>') . 
	'<li class="menu-slot' . ($url[0] == "" ? " menu-active" : "") . '"><a href="/">Feed</a>';

// Main Navigation
$html = '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot' . (in_array($url[0], array("", "home")) ? " nav-active" : "") . '"><a href="/">Home<span class="icon-circle-right nav-arrow"></span></a></li>';
		
		if(Me::$id)
		{
			$html .= '
			<li class="nav-slot' . ($url[0] == Me::$vals['handle'] ? " nav-active" : "") . '"><a href="/' . Me::$vals['handle'] . '">My Blogs<span class="icon-circle-right nav-arrow"></span></a></li>';
		}
		else
		{
			$html .= '
			<li class="nav-slot' . ($url[0] == "my-blogs" ? " nav-active" : "") . '"><a href="/my-blogs">My Blogs<span class="icon-circle-right nav-arrow"></span></a></li>';
		}
		
		$html .= '
	</ul>
</div>';

WidgetLoader::add("SidePanel", 10, $html);

// Load the Social Menu
require(SYS_PATH . "/controller/includes/social-menu.php");