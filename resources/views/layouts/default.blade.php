<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {!! SEOMeta::generate() !!}
        {!! OpenGraph::generate() !!}
        {!! Twitter::generate() !!}

        <link rel="shortcut icon" href="{{ asset('/img/movor_icon.png') }}">

        {{-- Styles --}}

        {{ Html::style(App::environment('production') ? mix('/css/vendor.min.css') : '/css/vendor.css') }}
        {{ Html::style(App::environment('production') ? mix('/css/app.min.css') : '/css/app.css') }}

        @yield('css-head')

        {{-- /Styles --}}

        {{-- Scripts --}}

        @include('partials.js_env')

        @yield('scripts-head')

        {{-- /Scripts --}}

        {{-- Google analytics (only on production) --}}
        @includeWhen(App::environment('production') && env('GOOGLE_ANALYTICS_KEY'), 'partials.google_analytics')

    </head>
    <body>
        <div id="app">
            <div class="py-5"></div>

            @include('partials.header')

            @includeWhen($messages->any() || $errors->any(), 'partials.flash')

            <main>@yield('content')</main>
            <div class="py-3"></div>

            @include('partials.footer')

        </div>

        {{-- Bottom Scripts --}}

        {{ Html::script(App::environment('production') ? mix('/js/vendor.min.js') : '/js/vendor.js') }}
        {{ Html::script(App::environment('production') ? mix('/js/app.min.js') : '/js/app.js') }}

        @yield('scripts-bottom')

        {{-- /Bottom Scripts --}}

    </body>
</html>
