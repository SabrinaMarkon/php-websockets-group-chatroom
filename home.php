<?php
$showcontent = new PageContent();
if (isset($_GET['page'])) {
  echo $showcontent->showPage($_GET['page']);
} else {
  echo $showcontent->showPage('Home Page');
}
?>