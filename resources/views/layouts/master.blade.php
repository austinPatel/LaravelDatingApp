<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.head')
    @yield('addstyle')
</head>

<body>
    <div class="layout">
        <header class="carry-header scrolled">
            <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
                @include('layouts.nav')
            </nav>
        </header>
        <div class="content spacing">
            <div class="container">
                @yield('content')
            </div>

        </div>
        <div class="overlay">
            <div class="spinner-box">
                <div class="pulse-container">
                    <div class="pulse-bubble pulse-bubble-1"></div>
                    <div class="pulse-bubble pulse-bubble-2"></div>
                    <div class="pulse-bubble pulse-bubble-3"></div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
    @include('layouts.scripts')
    @yield('addscript')
</body>

</html>