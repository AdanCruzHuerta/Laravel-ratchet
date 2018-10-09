<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.css">
    </head>
    <body>
        <div id="app">
            <section class="section">
                <div class="container">
                    <div v-show="typeConnection == null">
                        <h1 class="title">¿Tipo de conexión?</h1>
                        <div class="columns">
                            <div class="column">
                                <a class="button is-outlined" @click="setTypeConnection(1)">Broadcast</a>
                                <a class="button is-primary is-outlined" @click="setTypeConnection(2)">Dirigida</a>
                            </div>
                        </div>
                    </div>
                    <div v-show="typeConnection == 1">
                        <h1 class="title">Conexión broadcast</h1>
                        <h2 class="subtitle">Cliente: @{{ idConnection }}</h2>
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <div class="control">
                                        <input class="input" type="text" placeholder="Enviar.." @keyup.enter="sendMessage" v-model="message">
                                    </div>
                                </div>
                                <hr>
                                <a class="button is-outlined" @click="typeConnection = null">Regresar</a>
                            </div>
                            <div class="column">
                                <ul>
                                    <li v-for="item in messages">
                                        @{{item}}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div v-show="typeConnection == 2">
                        <h1 class="title">Conexión dirigida</h1>
                        <div class="columns">
                            <div class="column">
                                <a class="button is-outlined" @click="typeConnection = null">Regresar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--<div class="content">
                <center>
                    <div class="box">
                        <button onclick="subscribe('channel1')">Suscribe channel 1</button>
                        <button onclick="sendMessage('Mensaje enviado desde el canal 1')">Mensaje channel 1</button>
                    </div>

                    <div class="box">
                        <button onclick="subscribe('channel2')">Suscribe channel 2</button>
                        <button onclick="sendMessage('Mensaje enviado desde el canal 2')">Mensaje channel 2</button>
                    </div>
                </center>
            </div>-->
        </div>
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script>
            var vm = new Vue({
                el: '#app',
                data: {
                    typeConnection: null,
                    message: null,
                    messages: [],
                    conn: null,
                    idConnection: null
                },
                mounted() {
                    this.conn = new WebSocket('ws://ratchet.test:8001');

                    this.conn.onopen = function(e) {
                        console.log("Connection established!");
                    };

                    this.conn.onmessage = function(e) {
                        var resp = e.data.split("|");
                        var event = resp[0];
                        if(event == 'connect'){
                            vm.idConnection = resp[1];
                            return false;
                        } else if(event == 'broadcast') {
                            var message = "(" + resp[1] + ")" + ": " + resp[2];
                            vm.messages.push(message);
                            return false;
                        }
                    };
                    /*function subscribe(channel) {
                        conn.send(JSON.stringify({
                            command: "subscribe",
                            channel: channel
                        }));
                    }

                    function sendMessage(msg) {
                        conn.send(JSON.stringify({
                            command: "message",
                            message: msg
                        }));
                    }*/
                },
                methods: {
                    setTypeConnection(val) {
                        this.typeConnection = val;
                        this.conn.send(JSON.stringify({
                            command: "connect"
                        }));
                    },
                    suscribe() {

                    },
                    sendMessage() {
                        if(this.message != null){
                            this.conn.send(JSON.stringify({
                                command: "broadcast",
                                message: this.message
                            }));
                            this.message = null;
                        }
                    }
                }
            });
        </script>
    </body>
</html>
