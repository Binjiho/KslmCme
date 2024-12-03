@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/css/popup.css') }}">
@endsection

@section('contents')
    <article class="main-contents">
        <div class="main-sch-wrap">
            <div class="main-tit-wrap">
                <h3 class="main-tit">학술자료실 & <br class="m-br">E-learning 센터</h3>
            </div>
            <div class="sch-form-wrap">
                <form id="searchF" name="searchF" action="{{ route('unite') }}" class="sch-form-wrap">
                    <fieldset>
                        <legend class="hide">통합검색</legend>
                        <div class="form-group">
                            <input type="text" name="search_key" id="search_key" class="form-item" placeholder="원하시는 교육이나 자료를 찾아보세요.">
                            <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                        </div>
                    </fieldset>
                </form>
            </div>
            <ul>
                <li><a href="{{ route('board', ['code' => 'faq']) }}">FAQ</a></li>
                <li><a href="{{ route('board', ['code' => 'guide']) }}">이용가이드</a></li>
            </ul>
        </div>
    </article>
    <article class="main-contents">
        <div class="main-conbox">
            <div class="main-tit-wrap">
                <h3 class="main-tit">학술자료실 <a href="{{ route('workshop') }}" class="btn btn-more"><span class="hide">더보기</span></a></h3>
                <ul class="main-board-tab js-tab-menu">
                    <li class="on"><a href="#n">학술대회</a></li>
                    <li><a href="#n">월례집담회</a></li>
                    <li><a href="#n">기타</a></li>
                </ul>
            </div>
            <!-- s:학술대회 -->
            <div class="edu-board-conbox js-tab-con" style="display: block;">
                <div class="main-lib-board js-lib-rolling cf">
                    @foreach($workshop_list_a as $val)
                    <div class="edu-board-con">
                        <a href="{{ route('workshop.detail',['wsid'=>$val->sid]) }}">
                            <div class="img-wrap">
                            @if(!empty($val->realfile))
                                <img src="{{ $val->realfile }}" alt="">
                            @else
                                <img src="{{ asset('/assets/image/common/thumb_default.jpg') }}" alt="">
                            @endif
                            </div>
                            <div class="text-wrap">
                                <strong class="tit ellipsis3">{{ $val->title ?? '' }}</strong>
                                <span class="date">{{ $val->sdate ? $val->sdate->format('Y-m-d') : '' }} {{ $val->edate ? ' ~ '.$val->edate->format('Y-m-d') : '' }}</span>
                                <span class="btn-view">자료 보러가기 →</span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- //e:학술대회 -->

            <!-- s:월례집담회 -->
            <div class="edu-board-conbox js-tab-con">
                <div class="main-lib-board js-lib-rolling cf">
                    @foreach($workshop_list_b as $val)
                        <div class="edu-board-con">
                            <a href="{{ route('workshop.detail',['wsid'=>$val->sid]) }}">
                                <div class="img-wrap">
                                @if(!empty($val->realfile))
                                    <img src="{{ $val->realfile }}" alt="">
                                @else
                                    <img src="{{ asset('/assets/image/common/thumb_default.jpg') }}" alt="">
                                @endif
                                </div>
                                <div class="text-wrap">
                                    <strong class="tit ellipsis3">{{ $val->title ?? '' }}</strong>
                                    <span class="date">{{ $val->sdate ? $val->sdate->format('Y-m-d') : '' }} {{ $val->edate ? ' ~ '.$val->edate->format('Y-m-d') : '' }}</span>
                                    <span class="btn-view">자료 보러가기 →</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- //e:월례집담회 -->

            <!-- s:기타 -->
            <div class="edu-board-conbox js-tab-con">
                <div class="main-lib-board js-lib-rolling cf">
                    @foreach($workshop_list_z as $val)
                        <div class="edu-board-con">
                            <a href="{{ route('workshop.detail',['wsid'=>$val->sid]) }}">
                                <div class="img-wrap">
                                @if(!empty($val->realfile))
                                    <img src="{{ $val->realfile }}" alt="">
                                @else
                                    <img src="{{ asset('/assets/image/common/thumb_default.jpg') }}" alt="">
                                @endif
                                </div>
                                <div class="text-wrap">
                                    <strong class="tit ellipsis3">{{ $val->title ?? '' }}</strong>
                                    <span class="date">{{ $val->sdate ? $val->sdate->format('Y-m-d') : '' }} {{ $val->edate ? ' ~ '.$val->edate->format('Y-m-d') : '' }}</span>
                                    <span class="btn-view">자료 보러가기 →</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- //e:기타 -->
        </div>
        <div class="main-conbox">
            <div class="main-board-notice">
                <div class="notice-rolling-wrap">
                    <!-- s:공지사항 -->
                    <strong class="notice-cate">News</strong>
                    <div class="notice-con js-notice-rolling">
                        @forelse($notice_list as $notice)
                            <a href="{{ route('board.view', ['code' => 'notice', 'sid' => $notice->sid]) }}" class="notice-tit ellipsis">{{ $notice->subject ?? '' }}</a>
                        @empty
                            공지사항이 없습니다.
                        @endforelse
                    </div>
                    <span class="btn-view">자세히 보기 →</span>

                </div>
            </div>
        </div>
        <div class="main-conbox">
            <div class="main-tit-wrap">
                <h3 class="main-tit">온라인 강의 <a href="#n" class="btn btn-more"><span class="hide">더보기</span></a></h3>
            </div>
            <div class="edu-board-list js-board-rolling cf">
                @foreach($education_list as $val)
                <div class="edu-board-con">
                    <a href="#n">
                        <span class="cate">{{ $educationConfig['category'][$val->category] ?? '' }}</span>
                        <strong class="tit ellipsis2">{{ $val->title ?? '' }}</strong>
                        <p class="date">
                            <strong>신청기간</strong> <br>
                            @if($val->regist_limit_yn == 'N')
                                상시신청가능
                            @else
                                {{ $val->regist_sdate->format('Y-m-d') }} ~ {{ $val->regist_edate->format('Y-m-d') }}
                            @endif
                        </p>
                    </a>
                </div>
                @endforeach
{{--                    <div class="edu-board-con">--}}
{{--                        <a href="#n">--}}
{{--                            <span class="cate">학술대회</span>--}}
{{--                            <strong class="tit ellipsis2">교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다.</strong>--}}
{{--                            <p class="date">--}}
{{--                                <strong>신청기간</strong> <br>--}}
{{--                                2024-01-01 ~ 2024-01-02--}}
{{--                            </p>--}}
{{--                        </a>--}}
{{--                    </div>--}}
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    @foreach($boardPopupList as $row/* 게시판 팝업 */)
        {{ $row->subject }}
        @include('common.popup.template' . $row->popups->popup_skin, ['board' => $row, 'popup' => $row->popups])
    @endforeach

    <script>
        function setCookie24(name, value, expiredays) {
            var todayDate = new Date();

            todayDate.setDate(todayDate.getDate() + expiredays);

            document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";";
        }

        $(document).on('click', '.popup_close_btn', function () {
            $(this).closest('.win-popup-wrap').remove();
        });

        $(document).on('click', '.btn-pop-today-close', function () {
            const layer = $(this).closest('.win-popup-wrap');

            setCookie24(layer.attr('id'), 'done', 1);

            layer.remove();
        });
    </script>
@endsection
