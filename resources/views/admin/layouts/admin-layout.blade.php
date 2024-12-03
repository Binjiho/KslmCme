<!doctype html>
<html lang="ko" class="root-text-sm">

<head>
    @include('admin.layouts.components.baseHead')
</head>

<body>
<div class="wrap admin">
    @include('admin.layouts.include.header')

    @yield('contents')

    @include('admin.layouts.include.footer')
</div>

@include('admin.layouts.components.spinner')

@yield('addScript')
</body>
</html>
