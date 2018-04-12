<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @include('layouts/head_TMP')

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
        </style>
    </head>

    <script type="text/javascript" src="{{ asset('js/index.js') }}"></script>
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
                <div class="title m-b-md">Index</div>
                {{ $aaa }}
                {{ Form::open(['url' => 'start']) }}
                <p>
                    {{ Form::label('username_l', 'ユーザー名：') }}
                    {{ Form::text('username', Input::old('username', '')) }}
                </p>
                <p>
                    <p>
                        {{ Form::checkbox('all', 1, true) }}
                        {{ Form::label('all_l', 'すべてにチェック') }}
                    </p>

                    <p>
                        {{ Form::checkbox('selectcheck[]', 'sl', true) }}
                        {{ Form::label('selectcheck_l1', 'SL') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', 'op', true) }}
                        {{ Form::label('selectcheck_l2', 'OP') }}
                    </p>
                <p>
                    {{ Form::checkbox('selectcheck[]', '2nd', true) }}
                    {{ Form::label('selectcheck_l3', '2nd') }}
                </p>

                </p>
                <p>
                    {{ Form::label('selectradio_l', 'ラジオボタン：') }}
                    {{ Form::radio('selectradio[]', 1, true) }}
                    {{ Form::label('selectradio_l1', '表示する') }}
                    {{ Form::radio('selectradio[]', 2, false) }}
                    {{ Form::label('selectradio_l2', '隠す') }}
                </p>
                <p><div id="message"></div></p>

                {{ Form::submit('OK') }}
                {{ Form::close() }}
            </div>
        </div>


    </body>
</html>
