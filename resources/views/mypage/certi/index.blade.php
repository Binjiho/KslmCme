@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">마이페이지</h2>
            <p>
                마이페이지를 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>마이페이지</li>
                    <li>온라인 강의실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            @include('layouts.include.sub-menu-wrap')

            <div class="sch-result-list">
                <div class="table-contop text-right">
                    <p class="text-red2">* 이수증 출력은 PC에서만 가능합니다. <br class="m-br"> (모바일에서는 이수내역 확인만 가능합니다.)</p>
                </div>
                <ul class="board-list lecture mypage">
                    <li class="list-head">
                        <div class="bbs-no bbs-col-xs">번호</div>
                        <div class="bbs-tit n-bar">교육명</div>
                        <div class="bbs-date bbs-col-s2 n-bar">수강기간</div>
                        <div class="bbs-manage bbs-col-s n-bar">이수증</div>
                    </li>

                    @forelse($list as $idx => $row)
                    <li>
                        <div class="bbs-no bbs-col-xs">{{ $row->seq }}</div>
                        <div class="bbs-tit text-left n-bar">
                            {{ $row->edu->title ?? '' }}
                        </div>
                        <div class="bbs-date bbs-col-s2 n-bar">{{ $row->getLectureViewMinCreatedAt() ?? '' }} ~ {{ $row->complete_at->format('Y.m.d') ?? '' }}</div>
                        @if(($row->edu->certi_yn ?? '') == 'Y' && ($row->complete_yn ?? '') == 'Y')
                            <div class="bbs-manage bbs-col-s n-bar">
                                <a href="{{ route('mypage.certi.detail',['ssid'=>$row->sid]) }}" class="btn btn-small btn-print call-popup" data-popup_name="certi-pop" data-width="600" data-height="800">이수증 출력</a>
                            </div>
                        @endif
                    </li>
                    @empty
{{--                        <li>--}}
{{--                            완료한 교육이 없습니다.--}}
{{--                        </li>--}}
                    @endforelse
                </ul>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script>

    </script>
@endsection
