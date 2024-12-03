{{-- base css --}}
<link rel="stylesheet" href="{{ asset('plugins/flatpickr/css/flatpickr.min.css') }}">
<link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.5/dist/web/variable/pretendardvariable.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
<link type="text/css" rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}">
<link type="text/css" rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
{{-- addCss --}}
@yield('addStyle')
