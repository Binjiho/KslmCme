<!doctype html>
<html lang="ko" class="root-text-sm">
<head>
    @include('layouts.components.baseHead')
</head>
<body>

<div class="wrap {{ $main_menu == 'main' ? "main" : "sub" }}">
    <div id="dim" class="js-dim"></div>

    @include('layouts.include.header')

    <section id="container" >

        @yield('contents')

    </section>

    @include('layouts.include.footer')

</div>

@include('layouts.components.spinner')

{{--addScript--}}
@yield('addScript')

@if (config('app.debug'))
    {!! Debugbar::render() !!}
@endif
</body>
</html>
