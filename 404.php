<?php
require_once('classes/PageContent.php');
$showcontent = new PageContent();
echo $showcontent->showPage('404');
?>