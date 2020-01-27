<?php
if ((isset($_SESSION['username'])) && (isset($_SESSION['password'])))
{
    $logincheck = new Admin();
    $newlogin = $logincheck->adminLogin($_SESSION['username'],$_SESSION['password']);
    if ($newlogin === false)
    {
        $showcontent = new LoginForm();
        echo $showcontent->showLoginForm(1, 1);
        $Layout = new Layout();
        $Layout->showFooter();
        exit;
    }
}
else
{
    $showcontent = new LoginForm();
    echo $showcontent->showLoginForm(1, 1);
    $Layout = new Layout();
    $Layout->showFooter();
    exit;
}
