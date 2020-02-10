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
    $sql = "select chatroom.*, members.email from chatroom, members where chatroom.username = members.username order by id limit {$limit} offset {$offset}";
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
  
  public function __destruct() {
    Database::disconnect();
  }
  
}
