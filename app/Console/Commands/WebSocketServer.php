<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\{WebSocketController, WebSocketSuscriptionController};

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     * Nombre del comando 'websocket:init'
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     * Descripción del comando
     *
     * @var string
     */
    protected $description = 'Initializing Websocket server to receive and manage connections';

    /**
     * Create a new command instance.
     * Crear nueva instancia de comando
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Código que se ejecuta cuando se inicia el comando
     *
     * @return mixed
     */
    public function handle()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    //new WebSocketController()
                    new WebSocketSuscriptionController()
                )
            ),
            8001
        );
        $server->run();
    }
}
