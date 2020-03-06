<?php
/**
 * Chatroom database access to store and retrieve chat history.
 * PHP 5+
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, SabrinaMarkon.com
 * @license LICENSE.md
 **/
class ChatRoom {
  private $pdo;
  
  public function __construct() {
		$this->pdo = Database::connect();
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  }

  public function saveChatRoom($username, $msg) {
		$sql = "insert into chatroom (username, msg, created_on) values (?, ?, NOW())";
		$q = $this->pdo->prepare($sql);
		$q->execute(array($username, $msg));
  }

  public function loadChatRoom($offset = 0, $limit = 50) {
    $sql = "select chatroom.*, members.email from chatroom, members where chatroom.username = members.username order by created_on desc limit {$limit} offset {$offset}";
    $q = $this->pdo->prepare($sql);
    $q->execute();
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $messages = $q->fetchAll();
    return $messages;
  }

	public function updateChatLoginStatus($username, $loginstatus) {
		$sql = "update members set login_status=? where username=?";
		$q = $this->pdo->prepare($sql);
		$q->execute(array($loginstatus,$username));	
  }

  public function addWebSocketsResourceId($username, $resourceId) {
    $sql = "update members set resourceId=?, login_status=1 where username=?";
    $q = $this->pdo->prepare($sql);
    $q->execute(array($resourceId, $username));
  }
  
  public function removeWebSocketsResourceId($resourceId) {
    $sql = "update members set resourceId=NULL, login_status=-0 where resourceId=?";
    $q = $this->pdo->prepare($sql);
    $q->execute(array($resourceId));
  }

  public function updateImageUrlsInChatMessage($originalFilename, $newFilename) {
    $likeOriginalFilename = "%$originalFilename%";
    $sql = "select * from chatroom where msg like ?";
    $q = $this->pdo->prepare($sql);
    $q->execute(array($likeOriginalFilename));
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $messages = $q->fetchAll();
    foreach ($messages as $message) {
      $msg = $message['msg'];
      $replaceLoaderGif = '/images/loader.gif';
      $newFilePath = "/uploads/" . $newFilename;
      $replaceLoaderClass = ' class="loader_small"';
      $newLoaderClass = '';
      $replaceDivClass = ' class="oneMessageDiv_small"';
      $newDivClass = ' class="oneMessageDiv_normal"';
      $updatedMsg = str_replace($replaceLoaderGif, $newFilePath, $msg);
      $updatedMsg = str_replace($replaceLoaderClass, $newLoaderClass, $updatedMsg);
      $updatedMsg = str_replace($replaceDivClass, $newDivClass, $updatedMsg);
      $sql = "update chatroom set msg=? where msg like ?";
      $q = $this->pdo->prepare($sql);
      $q->execute(array($updatedMsg, $likeOriginalFilename));
    }
  }

  public function __destruct() {
    Database::disconnect();
  }
  
}
