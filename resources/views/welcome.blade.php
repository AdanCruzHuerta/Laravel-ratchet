<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .box{
                margin-top: 10px;
                margin-bottom: 10px;
                padding: 5px;
                border-radius: 5px;
            }

        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>
                <center>
                    <div class="box">
                        <button onclick="subscribe('channel1')">Suscribe channel 1</button>
                        <button onclick="sendMessage('Mensaje enviado desde el canal 1')">Mensaje channel 1</button>
                    </div>

                    <div class="box">
                        <button onclick="subscribe('channel2')">Suscribe channel 2</button>
                        <button onclick="sendMessage('Mensaje enviado desde el canal 2')">Mensaje channel 2</button>
                    </div>

                    <div class="box">
                        <button onclick="subscribe('channel3')">Suscribe channel 3</button>
                        <button onclick="sendMessage('Mensaje enviado desde el canal 3')">Mensaje channel 3</button>
                    </div>

                </center>
            </div>
        </div>
        <script>
            var conn = new WebSocket('ws://ratchet.test:8001');

            /*conn.onopen = function(e) {
                console.log("Connection established!");
            };

            conn.onmessage = function(e) {
                console.log(e.data);
            };*/

            conn.onopen = function(e) {
                console.log("Connection established!");
            };

            conn.onmessage = function(e) {
                console.log(e.data);
            };

            function subscribe(channel) {
                conn.send(JSON.stringify({command: "subscribe", channel: channel}));
            }

            function sendMessage(msg) {
                conn.send(JSON.stringify({command: "message", message: msg}));
            }
        </script>
    </body>
</html>
