<?php
/**
 * Retrieves more chat messages from the database when the user scrolls up to the top of the chatbox.
 * PHP 5+
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, SabrinaMarkon.com
 * @license LICENSE.md
 **/
function php_autoloader($class) {
	$file = "classes/" . $class . ".php";
	if (file_exists($file)) {
			require($file);
	}
}
spl_autoload_register("php_autoloader");

if (isset($_GET['offset']) && isset($_GET['limit'])) {
  require_once "config/Database.php";
  $allmembers = new Member();
  $chatroom = new ChatRoom();
  $morechatmessages = $chatroom->loadChatRoom($_GET['offset'], $_GET['limit']);
  $appendtochat = '';
  foreach($morechatmessages as $chatmessage) {
    // show the time if the message was sent today, otherwise show the date and time.
    $messagedatestr = strtotime($chatmessage['created_on']);
    $onedayagostr = strtotime('-1 day');
    if ($messagedatestr < $onedayagostr) {
      $messagedate = date("M d, Y g:i A", strtotime($chatmessage['created_on']));
    } else {
      $messagedate = date("g:i A", strtotime($chatmessage['created_on']));
    }
    $appendtochat .= "<div class=\"ja-chat-onemessage\">";
    $appendtochat .= "<div>" . $allmembers->getGravatar($chatmessage['username'], $chatmessage['email']) . "</div>";
    $appendtochat .= "<div>" . $chatmessage['username'] . "<br />" . $chatmessage['msg'] . "</div>";
    $appendtochat .= "<div>" . $messagedate . "</div>";
    $appendtochat .= "</div>";
  }
  echo $appendtochat;
}
