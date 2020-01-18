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
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  }

  public function saveChatRoom() {
		$sql = "insert into chatrooms (username, msg, createdOn) values (?, ?, NOW())";
		$q = $pdo->prepare($sql);
		$q->execute(array($this->username, $this->msg));
  }

  public function loadChatRoom() {
    // limit the number so if there are 1000s we don't crash.
    // (function later to click and view older messages or search them).
    $sql = "select * from chatrooms order by id desc limit 100";
    $q = $pdo->prepare($sql);
    $q->execute();
    $q->setFetchMode(PDO::FETCH_ASSOC);
    $messages = $q->fetchAll();
    return $messages;
  }

  public function __destruct() {
    Database::disconnect();
  }


  
}
