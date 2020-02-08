<?php
session_start();
// Log the user out of the chatroom.
if ((isset($_SESSION['username'])) && (isset($_SESSION['password']))) {
  // require_once "config/Database.php";
  // $user = new ChatRoom();
  // $user->updateChatLoginStatus($_SESSION['username'], 0);
} 
?>