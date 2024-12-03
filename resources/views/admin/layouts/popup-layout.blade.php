<!DOCTYPE html>
<html lang="ko">
<head>
    @include('admin.layouts.components.baseHead')
</head>
<body>
<div id="popup-wrap" style="display: block;">
    <div class="popup-contents">
        @yield('contents')
    </div>
</div>

@include('admin.layouts.components.spinner')

{{--addScript--}}
@yield('addScript')
</body>
</html>
