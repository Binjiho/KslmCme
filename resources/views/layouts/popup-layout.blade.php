<!DOCTYPE html>
<html lang="ko">
<head>
    @include('layouts.components.baseHead')
</head>
<body>
{{--<div class="popup-wrap" id="pop-survey" style="display: block;">--}}
    @yield('contents')
{{--</div>--}}

@include('layouts.components.spinner')

{{--addScript--}}
@yield('addScript')
</body>
</html>
