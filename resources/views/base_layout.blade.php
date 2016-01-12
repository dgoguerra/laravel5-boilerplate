<!DOCTYPE html>
<html>
<head>
    <title>Laravel</title>

    <link rel="stylesheet" href="/build/style.css">

    <style>
        body {
            padding-top: 70px;
        }
        html, body, .container {
            height: 100%;
        }
    </style>

    @yield('head')
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Laravel Base Project</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/') }}">Home</a></li>
                @if (\Illuminate\Support\Facades\Auth::guest())
                    <li><a href="{{ route('auth.login.show') }}">Login</a></li>
                @else
                    <li><a href="{{ route('settings.change_email.show') }}">Change Email</a></li>
                    <li><a href="{{ route('settings.change_password.show') }}">Change Password</a></li>
                    <li><a href="{{ route('auth.logout') }}">Logout</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    @yield('content')
</div>

<script type="text/javascript" src="/build/app.js"></script>

@yield('scripts')

</body>
</html>
