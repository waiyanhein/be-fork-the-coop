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

        <h2 class="slogan">Data Privacy & Policy</h2>

        <p class="description">
        <p>We value your privacy & policy. No individual or organisation apart from your will have access to your data without your consent.</p>
        <ul style="text-align: left">
            <li><strong>Location:</strong> the app use your location to match your with the more relevant devices based on your location.</li>
            <li><strong>Phone state:</strong> the app use phone state permission because it will get the phone number from your SIM when you register so that you as a user does not need to fill in explicitly.</li>
            <li><strong>Messages: the app will read the messages from your device and also send the message to the other devices.</strong></li>
        </ul>
        </p>
    </div>
</div>
</body>
</html>
