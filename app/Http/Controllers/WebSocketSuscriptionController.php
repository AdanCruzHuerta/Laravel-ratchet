<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Log;

class WebSocketSuscriptionController extends Controller implements MessageComponentInterface
{
    protected $clients;
    private $subscriptions;
    private $users;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        Log::info("Clientes: " . json_encode($this->clients));
        Log::info("Usuarios: " . json_encode($this->users));
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::info("An error has occurred: {$e->getMessage()}\n");
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg);
        if($data->command == 'connect') {
            $this->connect($from);
        }
        if($data->command == 'broadcast'){
            $this->broadcast($from, $data);
        }
        /*switch ($data->command) {
            case "broadcast":
                foreach ($this->users as $client) {
                    Log::info(json_encode($client));
                    //Log::info("from: {$from->resourceId}, client: {$client}");
                    if ($from->resourceId === $client) {
                        $client->send($msg);
                    }
                }
                break;
            case "subscribe":
                $this->subscriptions[$from->resourceId] = $data->channel;
                Log::info("Usuario  {$from->resourceId} suscrito a canal {$data->channel}");
                break;
            case "message":
                Log::info("Suscripciones: " . json_encode($this->subscriptions));
                Log::info("Sucripcion:: " . json_encode($from->resourceId));
                if (isset($this->subscriptions[$from->resourceId])) {
                    $target = $this->subscriptions[$from->resourceId];
                    foreach ($this->subscriptions as $id => $channel){
                        if ($channel == $target && $id == $from->resourceId) {
                            Log::info("Mensaje enviado al canal: " . $channel . "");
                            $this->users[$id]->send($data->message);
                            break;
                        }
                    }
                } else {
                    Log::info("No existe el recursoId");
                    break;
                }
                break;
        }*/
    }

    public function connect($from)
    {
        $idConnection = $from->resourceId;
        $resp = implode('|', ['connect', $idConnection]);
        $this->users[$idConnection]->send($resp);
    }

    public function broadcast($from, $msg)
    {
        //Emite el mensaje para todos los clientes conectados
        foreach ($this->users as $client) {
            $resp = implode('|', ['broadcast', $from->resourceId, $msg->message]);
            $client->send($resp);
        }
    }
}
