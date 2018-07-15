<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        @include('layouts/head_TMP')

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            .square_btn{
                display: inline-block;
                padding: 0.5em 1em;
                text-decoration: none;
                background: #668ad8;
                color: #FFF;
                border-radius: 4px;
                box-shadow: 0px 0px 0px 5px #668ad8;
                border: dashed 1px #FFF;
            }

            .square_btn:hover{
                border: dotted 1px #FFF;
            }

            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
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

    <script type="text/javascript" src="{{ asset('js/lottery.js') }}"></script>
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
                <div class="title m-b-md">Finish</div>
                <p>終了しました。</p>
                <table>
                    @foreach($songs as $index => $song)
                        <tr>
                            <td>{{ $index + 1 }}曲目</td>
                            <td>{{ $song->getTitle()}}</td>
                        </tr>
                    @endforeach
                </table>
                <br>
                {{ $time }} かかりました。
                <br><br><br>
                {{ Form::open(['url' => '/']) }}
                {{ Form::submit('Topへ戻る', ['class' => 'square_btn']) }}
                {{ Form::close() }}

            </div>
        </div>
    </body>
</html>
