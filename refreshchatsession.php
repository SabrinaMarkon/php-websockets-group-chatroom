<?php
session_start();
// Recreates session variables so user is not logged out of the site if they are idle for awhile in the chat.
if ((isset($_SESSION['username'])) && (isset($_SESSION['password']))) {
  $_SESSION['username'] = $_SESSION['username'];
  $_SESSION['password'] = $_SESSION['password'];
} 
