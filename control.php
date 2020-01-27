<?php
if ((isset($_SESSION['username'])) && (isset($_SESSION['password'])))
{
$logincheck = new User();
$newlogin = $logincheck->userLogin($_SESSION['username'],$_SESSION['password']);
 if ($newlogin === false)
	{
	$showcontent = new LoginForm();
	echo $showcontent->showLoginForm(1, 0);
	$Layout = new Layout();
	$Layout->showFooter();
	exit;
	}
else
	{
	# returned user details.
	foreach ($newlogin as $key => $value)
		{
		$$key = $value;
		$_SESSION[$key] = $value;
		}
		$showgravatar = $logincheck->getGravatar($username,$email);
	}
}
else
{
$showcontent = new LoginForm();
echo $showcontent->showLoginForm(1, 0);
$Layout = new Layout();
$Layout->showFooter();
exit;
}
