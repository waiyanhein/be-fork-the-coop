<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>22222 COM</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                background: #F44336;
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
                color: white;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .description {
                color: white;
                padding-left: 40px;
                padding-right: 40px;
            }

            .slogan {
                color: #FFC107;
            }

            .google-play-download-button img {
                width: 230px;
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

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    22222 COM
                </div>

                <h2 class="slogan">No abusive system is allowed to exist. We support human rights.</h2>

                <p class="description">
                    As we all know, the Myanmar military government has conducted a coup against the legally elected Government disrespecting the will of the citizens of Myanmar.
                    The military is also performing the violent actions against the peaceful protesters. Some of our rights are also taken away that includes internet freedom and access to the public information.
                    To ensure that our brothers and sisters in Myanmar can still connect each other and have access to the information when the internet is shutdown nation-wise, we have built this mobile application
                    that will enable us to keep each other informed.
                </p>

                <h3 class="slogan">Together we can win anything.</h3>

                <div>
                    <a class="google-play-download-button">
                        <img src="{{ url('images/google_play.png') }}">
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
