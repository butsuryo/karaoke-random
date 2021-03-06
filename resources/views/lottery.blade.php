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
                <div class="title m-b-md">Lottery</div>

                <p>曲タイトル:<b> {{ $is_mask ? '●●●●●●' : $song->getTitle() }}</b></p>
                <p>選曲番号(DAM):<b> {{ $song->getDamNumber() }}</b></p>
                <p>選曲番号(JOY):<b> {{ $song->getJoysoundNumber() }}</b></p>
                <p>全：{{ $all_cnt }} 曲</p>
                <p>今: {{ $cnt }} 曲目</p>
                <p>残り： {{ $remain_cnt }} 曲</p>
                @if ($remain_cnt > 0)
                    <p>残り時間： {{ $remain_label }} （終了予定時間：{{ $finish_time }}）</p>
                @endif

                {{ Form::open(['url' => "lottery/$cnt"]) }}


                {{ Form::hidden('all_count', $all_cnt) }}
                {{ Form::hidden('file_name', $file_name) }}
                {{--{{ Form::hidden('start_time', $start_time) }}--}}

                @if ($remain_cnt > 0)
                    {{ Form::submit('次の曲へ', ['name' => 'next']) }}
                    {{ Form::submit('今はスキップ', ['name' => 'skip']) }}
                @else
                    {{ Form::submit('終了', ['name' => 'finish']) }}
                @endif

                {{ Form::close() }}
            </div>
        </div>


    </body>
</html>
