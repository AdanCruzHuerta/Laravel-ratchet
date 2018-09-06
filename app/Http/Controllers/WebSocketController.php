<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Log;

class WebSocketController extends Controller implements MessageComponentInterface
{
    //private $connections = [];
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn){
        $this->clients->attach($conn);
        Log::info("New connection! ({$conn->resourceId})");
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn){
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        Log::info("Connection {$conn->resourceId} has disconnected");
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e){
        Log::error("An error has occurred: {$e->getMessage()}");
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $conn The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg){
        $numRecv = count($this->clients) - 1;
        Log::info(sprintf('Connection %d sending message "%s" to %d other connection%s' . ""
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's'));

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }
}
