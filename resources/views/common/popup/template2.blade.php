<!DOCTYPE html>
<html lang="ko">
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes,viewport-fit=cover">--}}
{{--    <meta name="format-detection" content="telephone=no, address=no, email=no">--}}
{{--    <meta name="apple-mobile-web-app-capable" content="yes">--}}
{{--    <meta http-equiv="Cache-Control" content="no-cache">--}}
{{--    <meta http-equiv="Expires" content="0">--}}
{{--    <meta http-equiv="Pragma" content="no-cache">--}}
{{--    <meta name="Author" content="대한진단검사의학회 CME">--}}
{{--    <meta name="Keywords" content="대한진단검사의학회 CME">--}}
{{--    <meta name="description" content="대한진단검사의학회 CME">--}}
{{--    <title>대한진단검사의학회 CME</title>--}}
{{--    <link rel="icon" href="/assets/image/favicon.ico">--}}
{{--    <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.5/dist/web/variable/pretendardvariable.css" rel="stylesheet">--}}
{{--    <link type="text/css" rel="stylesheet" href="/assets/css/slick.css">--}}
{{--    <link type="text/css" rel="stylesheet" href="/assets/css/jquery-ui.min.css">--}}
{{--    <link type="text/css" rel="stylesheet" href="/assets/css/common.css">--}}
{{--    <script type="text/javascript" src="/assets/js/jquery-1.12.4.min.js"></script>--}}
{{--    <script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>--}}
{{--    <script type="text/javascript" src="/assets/js/slick.min.js"></script>--}}
{{--    <script type="text/javascript" src="/assets/js/common.js"></script>--}}
{{--</head>--}}
<body>
<!-- popup css -->
<link rel="stylesheet" href="{{ asset('assets/css/popup.css') }}">

<div class="win-popup-wrap popup-wrap type2" style="top: {{ $popup->position_y }}px; left: {{ $popup->position_x }}px; width: {{ $popup->width }}px; height: {{ $popup->height }}px; display: block;" id="popup_{{ $popup->sid }}">
    <div class="popup-contents">
        <div class="popup-conbox">
            <div class="popup-contit-wrap">
                <h2 class="popup-contit">{!! $popup->subject !!}</h2>
            </div>
            <div class="popup-con">
                {!! $popup->contents ?? $popup->popup_contents !!}
            </div>

            @if(($board->files_count ?? 0) > 0)
                <div class="popup-attach-con">
                    @foreach($board->files as $key => $file)
                        <a href="{{ empty($preview) ? $file->downloadUrl() : "javascript:void(0);" }}">
                            {{ $file->filename }} (다운로드 : {{ number_format($file->download) }}회)
                        </a>
                    @endforeach
                </div>
            @endif

            @if($popup->popup_detail === "Y")
                <div class="btn-wrap text-center">
                    <a href="{{ $popup->popup_link ?? '' }}" class="btn btn-pop-link">자세히보기</a>
                </div>
            @endif
        </div>
        <div class="popup-footer">
            <input type="checkbox" name="popup_yn" id="popup_yn_{{ $popup->sid }}" value="Y">
            <label for="popup_yn_{{ $popup->sid }}">오늘하루 그만보기</label>

            <a href="#n" class="popup_close_btn btn full-right" data-sid="{{ $popup->sid }}">닫기</a>
        </div>
    </div>
</div>
</body>
</html>