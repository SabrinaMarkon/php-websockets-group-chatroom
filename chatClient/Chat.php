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
        // $msg is the data = {'user': username, 'text': msg} sent from the jQuery in chatroom.php. 
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d with username %u sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg['user'], $msg['text'], $numRecv, $numRecv == 1 ? '' : 's');
            // $numRecv == 1 ? '' : 's' just makes 'connections' word plural or not depending on $numRecv.

        // Add the date/time to the $data object:
        $data['dt'] = date("M-d-Y h:i:s a");
        foreach ($this->clients as $client) {
            // if ($from !== $client) {
                // Uncomment if we don't want users to see their own messages.
                $client->send($json_encode($data));
            // }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}