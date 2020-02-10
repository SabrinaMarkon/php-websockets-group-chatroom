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
  $morechatmessages_array = $chatroom->loadChatRoom($_GET['offset'], $_GET['limit']);
  $morechatmessages = array_reverse($morechatmessages_array);
  $prependtochat = '';
  foreach($morechatmessages as $chatmessage) {
    // show the time if the message was sent today, otherwise show the date and time.
    $messagedatestr = strtotime($chatmessage['created_on']);
    $onedayagostr = strtotime('-1 day');
    if ($messagedatestr < $onedayagostr) {
      $messagedate = date("M d, Y g:i A", strtotime($chatmessage['created_on']));
    } else {
      $messagedate = date("g:i A", strtotime($chatmessage['created_on']));
    }
    $prependtochat .= "<div class=\"ja-chat-onemessage\">";
    $prependtochat .= "<div>{$chatmessage['id']}" . $allmembers->getGravatar($chatmessage['username'], $chatmessage['email']) . "</div>";
    $prependtochat .= "<div>" . $chatmessage['username'] . "<br />" . $chatmessage['msg'] . "</div>";
    $prependtochat .= "<div>" . $messagedate . "</div>";
    $prependtochat .= "</div>";
  }
  echo $prependtochat;
}
