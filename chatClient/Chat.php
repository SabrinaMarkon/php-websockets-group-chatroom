<?php
/**
 * Chat client handling for group chatroom.
 * PHP 5+
 * @author Sabrina Markon
 * @copyright 2020 Sabrina Markon, SabrinaMarkon.com
 * @license LICENSE.md
 **/
namespace chatClient;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once(__DIR__ . '/../config/Database.php');
require_once(__DIR__ . '/../classes/ChatRoom.php');
require_once(__DIR__ . '/../classes/Member.php');

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "Chat server started!\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // $msg is the data = {'username': username, 'email': email, 'text': text, 'chatstatus': chatstatus} sent from the jQuery in chatroom.php. 
        $numRecv = count($this->clients) - 1;
        $userobj = json_decode($msg);
        echo sprintf('Connection %d with username %s sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $userobj->username, $userobj->text, $numRecv, $numRecv == 1 ? '' : 's');
            // $numRecv == 1 ? '' : 's' just makes 'connections' word plural or not depending on $numRecv.

        // Add the date/time to the $data object:
        $userobj->dt = date("M-d-Y h:i:s A");
        // Create the user gravatar image and add to the $data object.
        $allmembers = new \Member();
        $gravatar = $allmembers->getGravatar($userobj->username, $userobj->email);
        $userobj->gravatar = $gravatar;

        $chathistory = new \ChatRoom();
        if ($userobj->chatstatus == 'left') {
            $chathistory->removeWebSocketsResourceId($conn->resourceId);
        } else if ($userobj->chatstatus == 'joined') {
            // Assign the resourceId to the username in the members table.
            $chathistory->addWebSocketsResourceId($userobj->username, $from->resourceId);
        }

        foreach ($this->clients as $client) {
            // if ($from !== $client) {
                // Uncomment if we don't want users to see their own messages.
                $client->send(json_encode($userobj));
            // }
        }
        // Add the message to the database chatroom history.
        $chathistory->saveChatRoom($userobj->username, $userobj->text);
    }

    public function onClose(ConnectionInterface $conn) {
        // foreach ($this->clients as $client) {
        //         $client->send(json_encode($userobj));
        // }
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        // Remove the websockets resourceId from the members table.
        $chat = new \ChatRoom();
        $chat->removeWebSocketsResourceId($conn->resourceId);
        echo "Connection {$conn->resourceId} has disconnected\n";

        // RIGHT NOW IF A USER CLOSES THE BROWSER, THEY STILL SHOW AS ONLINE IN THE CHAT!!! This should say "user left the chat" or
        // "user joined the chat".
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}