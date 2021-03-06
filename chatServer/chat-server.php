<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\OriginCheck;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use chatClient\Chat;

    require dirname(__DIR__) . '/vendor/autoload.php';

    # Make sure the client is connected from one of the allowed domains only.
    $checkedApp = new OriginCheck(new WsServer(new Chat()), array('localhost'));

    // CHANGE THIS LINE TO YOUR SITE'S DOMAIN!
    $checkedApp->allowedOrigins[] = 'YOURDOMAIN.com'; 

    $server = IoServer::factory(
        new HttpServer($checkedApp),
        8081
    );

    $server->run();