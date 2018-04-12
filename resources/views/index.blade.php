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

            .boxgroup {
                text-align: left;
                position: relative;
                margin: 2em 0;
                padding: 1em 2em;
                border: solid 3px #95ccff;
                border-radius: 8px;
            }
            .boxgroup .box-title {
                margin-right: auto;
                position: absolute;
                display: inline-block;
                top: -13px;
                left: 10px;
                padding: 0 9px;
                line-height: 1;
                font-size: 19px;
                background: #FFF;
                color: #95ccff;
                font-weight: bold;
            }
            .boxgroup p {
                margin: 0em;
                padding: 3px;
            }

            .box7{
                padding: 0.5em 1em;
                margin: 2em 0;
                color: #474747;
                background: whitesmoke;/*背景色*/
                border-left: double 7px #4ec4d3;/*左線*/
                border-right: double 7px #4ec4d3;/*右線*/
            }
            .box7 p {
                margin: 0;
                padding: 0;
            }

            .box {
                padding: 0.5em 1em;
                margin: 2em 0;
                background: -webkit-repeating-linear-gradient(-45deg, #f0f8ff, #f0f8ff 3px,#e9f4ff 3px, #e9f4ff 7px);
                background: repeating-linear-gradient(-45deg, #f0f8ff, #f0f8ff 3px,#e9f4ff 3px, #e9f4ff 7px);

            }
            .box p {
                margin: 0;
                padding: 0;
            }

            html, body {
                background-color: #fff;
                color: #636b6f;

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
                font-size: 64px;
                font-family: 'Raleway', sans-serif;
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
                margin-bottom: 0px;
            }

            .checkA {
                display: inline-block;
                position: relative;
                margin-right: 5px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-appearance: button;
                appearance: button;
                width: 20px;
                height: 20px;
                border: 1px solid #999;
                vertical-align: middle;
            }

            .checkA:checked::after {
                position: absolute;
                content: "";
                top: -3px;
                left: 3px;
                width: 8px;
                height: 14px;
                border-right: 4px solid #668ad8;
                border-bottom: 4px solid #668ad8;
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
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
                <div class="title m-b-md">karaoke-random</div>

                {{-- Form::open(['url' => 'start']) --}}
                {{ Form::open(['url' => '']) }}
                <div class="boxgroup">
                    <span class="box-title">抽選対象曲</span>
                    <p>
                        {{ Form::checkbox('selectcheck[]', 'sl', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l1', 'ST@RTING LINE') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', 'op', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l2', 'ORIGIN@L PIECES') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', '2nd', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l3', '2nd ANNIVERSARY DISC') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', '3rd', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l4', '3rd ANNIVERSARY DISC') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', 'anime', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l5', 'ANIMATION PROJECT') }}
                    </p>
                    <p>
                        {{ Form::checkbox('selectcheck[]', 'everyone', true, ['class' => 'checkA']) }}
                        {{ Form::label('selectcheck_l6', '全体曲') }}
                    </p>

                    <hr>
                    <p>
                        {{ Form::checkbox('all', 1, true, ['class' => 'checkA']) }}
                        {{ Form::label('all_l', 'すべてにチェック') }}
                    </p>
                </div>


                <div class="boxgroup">
                    <span class="box-title">曲情報の表示・非表示</span>
                    <p>
                        {{ Form::radio('selectradio[]', 1, true) }}
                        {{ Form::label('selectradio_l1', '表示する') }}
                    </p>
                    <p>
                        {{ Form::radio('selectradio[]', 2, false) }}
                        {{ Form::label('selectradio_l2', '隠す（機種番号のみ表示されます）') }}
                    </p>
                </div>
                <p><div id="message" class="box7"></div></p>

                {{ Form::submit('開始する', ['class' => 'square_btn']) }}
                {{ Form::close() }}
            </div>
        </div>


    </body>
</html>
