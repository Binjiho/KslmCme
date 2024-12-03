@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}" >
{{--    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">--}}
@endsection

@section('contents')
        <article class="sub-visual">
            <div class="sub-visual-con inner-layer">
                <h2 class="sub-visual-tit">지원센터</h2>
                <p>
                    지원센터를 확인하실 수 있습니다.
                </p>
                <div class="breadcrumb">
                    <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                    <ul>
                        <li>지원센터</li>
                        <li>이용가이드</li>
                    </ul>
                </div>
            </div>
        </article>
        <article class="sub-contents">

            @include('layouts.include.sub-menu-wrap')

            <div class="sub-conbox inner-layer">
                <!-- s:준비중 -->
                <div class="ready-wrap">
                    <img src="/assets/image/sub/img_ready.png" alt="">
                    <p>
                        보다 나은 서비스를 위해 <strong>페이지 준비중</strong> 입니다. <br>
                        빠른 시일 내에 찾아뵙겠습니다.
                    </p>
                </div>
                <!-- //e:준비중 -->
            </div>
        </article>
@endsection

@section('addScript')
    @include("board.default-script")

    <script>

    </script>
@endsection
