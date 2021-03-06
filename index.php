<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!isset($_SESSION))
{
session_start();
}
require_once "config/Database.php";
require_once "config/Settings.php";
require_once "config/Layout.php";

function php_autoloader($class) {
	$file = "classes/" . $class . ".php";
	if (file_exists($file)) {
			require($file);
	}
}
spl_autoload_register("php_autoloader");

$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
foreach ($settings as $key => $value)
{
	$$key = $value;
}

if (isset($_SESSION['username'])) {
# User should not show as online in the chatroom unless they are properly logged into the site AND page is /chatroom.
$logoutchat = new ChatRoom();
$logoutchat->updateChatLoginStatus($_SESSION['username'], 0);
}

################################
######
if (isset($_POST['login']))
{
$_SESSION['username'] = $_REQUEST['username'];
$_SESSION['password'] = $_REQUEST['password'];
# Check if this is even a valid user.
$logincheck = new User();
$newlogin = $logincheck->userLogin($_SESSION['username'],$_SESSION['password']);
 if ($newlogin === false)
	{
	$logout = new User();
	$logout->userLogout();
	}
else
	{
	# returned member details.
	foreach ($newlogin as $key => $value)
		{
		$$key = $value;
		$_SESSION[$key] = $value;
		}
	$showgravatar = $logincheck->getGravatar($_SESSION['username'],$_SESSION['email']);
	}
}
if (isset($_POST['forgotlogin']))
{
$forgot = new User();
$showforgot = $forgot->forgotLogin($settings);
}
if (isset($_POST['resendverify']))
{
$resend = new User();
$showresend = $resend->resendVerificationEmail($settings);
}
if (isset($_POST['contactus']))
{
$contact = new Contact();
$sentcontact = $contact->sendContact($settings);
}
if (isset($_POST['register']))
{
$register = new User();
$showregistration = $register->newSignup($settings);
}
if (isset($_POST['saveprofile']))
{
$update = new User();
$showupdate = $update->saveProfile($_SESSION['username'], $settings);
}
if (isset($_GET['page']) && ($_GET['page'] == 'chatroom') && isset($_SESSION['username'])) {
	# Update chat online status to 1 if the user is in the chatroom.
	$loginchat = new ChatRoom();
	$loginchat->updateChatLoginStatus($_SESSION['username'], 1);
}
if (isset($_GET['page']) && ($_GET['page'] == "logout"))
{
	$logout = new User();
	$logout->userLogout();
	$logoutpage = new PageContent();
	$showlogout = $logoutpage->showPage('logout');
}
######################################

if ((!empty($_GET['page'])) && ((file_exists($_GET['page'] . ".php") && ($_GET['page'] != '')))) {

    $Layout = new Layout();
    $Layout->showHeader();
		$page = $_REQUEST['page'];
    include $page . ".php";
    $Layout->showFooter();

} else {

    $Layout = new Layout();
    $Layout->showHeader();
    include "home.php";
    $Layout->showFooter();
}
