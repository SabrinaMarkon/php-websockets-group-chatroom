<?php
$logout = new User();
$logout->updateChatLoginStatus($_SESSION['username'], 0);
$logout->userLogout();
$showcontent = new PageContent();
echo $showcontent->showPage('logout');
?>