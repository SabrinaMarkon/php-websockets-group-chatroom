<?php
$showcontent = new PageContent();
echo $showcontent->showPage('login');
$showloginform = new LoginForm();
echo $showloginform->showLoginForm(0, 0);
?>
