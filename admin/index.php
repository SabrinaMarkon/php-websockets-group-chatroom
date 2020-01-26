<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!isset($_SESSION))
{
    session_start();
}
require_once "../config/Database.php";
require_once "../config/Settings.php";
require_once "../config/Layout.php";
require_once "../classes/Email.php";

function php_autoloader($class) {
    require '../classes/' . $class . ".php";
}
spl_autoload_register("php_autoloader");

$sitesettings = new Settings();
$settings = $sitesettings->getSettings();
foreach ($settings as $key => $value)
{
    $$key = $value;
}

if (isset($_REQUEST['id']))
{
    $id = $_REQUEST['id'];
}
else
{
    $id = "";
}

######################################
if (isset($_POST['login']))
{
    $_SESSION['username'] = $_REQUEST['username'];
    $_SESSION['password'] = $_REQUEST['password'];
    $logincheck = new Admin();
    $newlogin = $logincheck->adminLogin($_SESSION['username'],$_SESSION['password']);
    if ($newlogin === false)
    {
        $logout = new Admin();
        $logout->adminLogout();
    }
}
if (isset($_POST['forgotlogin']))
{
    $forgot = new Admin();
    $showforgot = $forgot->forgotLogin($sitename,$domain,$adminemail);
}
if (isset($_POST['saveadminnotes']))
{
    $update = new AdminNote();
    $showupdate = $update->setAdminNote($_POST['htmlcode']);
}
if (isset($_POST['savesettings']))
{
    $update = new Setting();
    $showupdate = $update->saveSettings();
}

if (isset($_POST['editmail']))
{
    $editmail = new Mail();
    $showeditmail = $editmail->editMail($id);
}
if (isset($_POST['addmail']))
{
    $update = new Mail();
    $showupdate = $update->addMail();
}
if (isset($_POST['savemail']))
{
    $update = new Mail();
    $showupdate = $update->saveMail($id);
}
if (isset($_POST['deletemail']))
{
    $delete = new Mail();
    $showupdate = $delete->deleteMail($id);
}
if (isset($_POST['sendmail']))
{
    $send = new Mail();
    $showupdate = $send->sendMail($id);
}

if (isset($_POST['editpage']))
{
    $editpage = new Page();
    $showeditpage = $editpage->editPage($id);
}
if (isset($_POST['addpage']))
{
    $update = new Page();
    $showupdate = $update->addPage($domain);
}
if (isset($_POST['savepage']))
{
    $update = new Page();
    $showupdate = $update->savePage($id);
}
if (isset($_POST['deletepage']))
{
    $delete = new Page();
    $showupdate = $delete->deletePage($id);
}

if (isset($_POST['addmember']))
{
    $update = new Member();
    $showupdate = $update->addMember($settings);
}
if (isset($_POST['savemember']))
{
    $update = new Member();
    $showupdate = $update->saveMember($id);
}
if (isset($_POST['deletemember']))
{
    $delete = new Member();
    $showupdate = $delete->deleteMember($id);
}
if (isset($_POST['resendverify']))
{
    $resend = new Member();
    $showupdate = $resend->resendMember($id, $settings);
}
// REFACTOR LATER to make better routes etc. like laravel
//if (isset($_POST['_method'])) {
//
//    $_method = $_POST['_method'];
//    if($_method === 'DELETE') {
//
//        $delete = new Thing();
//        $showdelete = $delete->deleteThing($id);
//
//    }
//    elseif($_method === 'PATCH')
//    {
//
//        $update = new Thing();
//        $showupdate = $update->saveThing($id);
//    }
//}

if (isset($_GET['page']) && ($_GET['page'] == "logout"))
{
    $logout = new Admin();
    $logout->adminLogout();
}
######################################

//echo $_GET['page'] . "<br>";
if ((!empty($_GET['page'])) and ((file_exists($_GET['page'] . ".php") and ($_GET['page'] != "index")))) {
    $Layout = new Layout();
    $Layout->showHeader();
    $page = $_REQUEST['page'];
    include $page . ".php";
    $Layout->showFooter();

} else {

    $Layout = new Layout();
    $Layout->showHeader();
    include "login.php";
    $Layout->showFooter();

}
