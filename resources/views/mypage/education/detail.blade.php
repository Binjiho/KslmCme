@extends('layouts.web-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
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

            <div class="lecture-view-con">
                <div class="lecture-view-contop">
                    <div class="lecture-tit">
                        <p class="tit">{{ $sac_info->edu->title ?? '' }}</p>
                        <p class="date"><strong>수강기간 : </strong>{{ $sac_info->edu->edu_sdate->format('Y-m-d') ?? '' }} ~ {{ ($sac_info->edu->edu_limit_yn ?? '') == 'N' ? '기한없음' : $sac_info->edu->edu_edate->format('Y-m-d') ?? '' }}</p>
                    </div>
                    @if($sac_info->edu->realfile)
                    <div class="file">
                        교육자료 <a href="{{ $sac_info->edu->downloadUrl() }}" class="btn btn-file" download><img src="/assets/image/sub/ic_file.png" alt="">{{ $sac_info->edu->filename }}</a>
                    </div>
                    @endif
                </div>
                <div class="edu-view-wrap">
                    <div class="edu-view-conbox">
                        <div class="edu-info">
                            <h4 class="edu-tit type1">교육 소개</h4>
                            <div class="editors-contents">
                                {!! $sac_info->edu->contents !!}
                            </div>
                        </div>
                    </div>
                    <div class="edu-view-conbox">
                        <h4 class="edu-tit type2">교육 이수 조건</h4>
                        <ul class="edu-info-list">
                            <li>강의 영상 <br>100% 시청</li>
                            @if(($sac_info->edu->quiz_yn ?? '') == 'Y')
                            <li>퀴즈 풀이 합격 조건 <br><strong class="text-red">{{ $sac_info->edu->pass_cnt ?? 0 }}/{{ $sac_info->edu->quiz_cnt ?? 0 }}</strong></li>
                            @endif
                            @if(($sac_info->edu->survey_yn ?? '') == 'Y')
                            <li>설문 참여</li>
                            @endif
                            <li>이수완료</li>
                        </ul>
                        @if(($sac_info->edu->quiz_yn ?? '') == 'Y')
                        <div class="help-text text-right mt-10">
                            퀴즈는 합격 하실 때까지 재시험 가능합니다.
                        </div>
                        @endif
                    </div>
                </div>

                @if($sac_info->edu->isEduOpen())
                <div class="btn-wrap text-right">
                    @if($sac_info->edu->quiz_yn == 'Y')
                        @if(($sac_info->edu_status ?? 'I') == 'C')
                            <a href="{{ ($sac_info->quiz_status ?? '') == 'U' ? route('mypage.quiz',['ssid'=>$sac_info->sid]) : route('mypage.quiz_result',['ssid'=>$sac_info->sid]) }}" class="btn btn-lecture btn-quiz {{ ($sac_info->quiz_status ?? '') == 'C' ? 'on':'' }}">퀴즈 {{ ($sac_info->quiz_status ?? '') == 'C' ? '합격' : ( ($sac_info->quiz_status ?? '') == 'F' ? '불합격' : '' ) }}</a>
                        @else
                            <a href="javascript:alert('모든 강의 수강이 완료된 후 진행 가능합니다.');" class="btn btn-lecture btn-quiz {{ ($sac_info->quiz_status ?? '') == 'C' ? 'on':'' }}">퀴즈</a>
                        @endif
                    @endif
                    @if($sac_info->edu->survey_yn == 'Y')
                            @if(($sac_info->edu_status ?? 'I') == 'C')
                                @if(($sac_info->edu->quiz_yn ?? 'N') == 'Y')
                                    @if(($sac_info->quiz_status ?? 'N') != 'C')
                                    <a href="javascript:alert('퀴즈 합격 후 참여 가능합니다.');" class="btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}">설문</a>
                                    @else
                                        @if(($sac_info->survey_status ?? '') == 'C')
                                            <a href="javascript:alert('이미 설문을 완료하였습니다.');" class="call-popup btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}" data-popup_name="survey-upsert" data-width="850" data-height="900">설문 {{ ($sac_info->survey_status ?? '') == 'C' ? '참여완료':'' }}</a>
                                        @else
                                            <a href="{{ route('mypage.survey',['ssid'=>$sac_info->sid]) }}" class="call-popup btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}" data-popup_name="survey-upsert" data-width="850" data-height="900">설문</a>
                                        @endif
                                    @endif
                                @else
                                    @if(($sac_info->survey_status ?? '') == 'C')
                                        <a href="javascript:alert('이미 설문을 완료하였습니다.');" class="btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}" >설문 {{ ($sac_info->survey_status ?? '') == 'C' ? '참여완료':'' }}</a>
                                    @else
                                        <a href="{{ route('mypage.survey',['ssid'=>$sac_info->sid]) }}" class="call-popup btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}" data-popup_name="survey-upsert" data-width="850" data-height="900">설문</a>
                                    @endif
                                @endif
                            @else
                                <a href="javascript:alert('모든 강의 수강이 완료된 후 진행 가능합니다.');" class="btn btn-lecture btn-survey {{ ($sac_info->survey_status ?? '') == 'C' ? 'on':'' }}">설문</a>
                            @endif
                    @endif
                </div>
                @endif
                <p class="table-contop text-right">
                    수강 기간이 종료된 경우, 강의 수강이 불가능합니다. <br class="m-br">(강의 리스트 확인만 가능)
                </p>

                <ul class="board-list lecture">
                    <li class="list-head">
                        <div class="bbs-col-xs">강의</div>
                        <div class="bbs-tit">강의명</div>
                        <div class="bbs-col-s">강의 영상 진행률</div>
                        <div class="bbs-col-s">강의 수강</div>
                    </li>

                    @foreach($sac_info->lectures as $key => $lecture)
                    <li>
                        <div class="bbs-col-xs n-bar">{{ $key+1 }}강</div>
                        <div class="bbs-tit text-left n-bar">
                            <strong>{{ $lecture->title ?? '' }}</strong>
                            <p>
                                {{ $lecture->name_kr ?? '' }} ({{ $lecture->sosok_kr ?? '' }})
                            </p>
                        </div>
                        <div class="bbs-col-s n-bar">{{ $lecture->getPercent($sac_info->sid, $lecture->sid) }}%</div>
                        <div class="bbs-col-s n-bar">
                            @if($sac_info->edu->isEduOpen())
                                @if(($sac_info->pay_method ?? '') == 'F' || ($sac_info->pay_status ?? '') == 'C')
                                    <a href="{{ route('education.play',['ssid'=>request()->ssid, 'lsid'=>$lecture->sid,'type'=>$lecture->type]) }}" class="btn btn-small btn-view">{{ ($lecture->lec_view()->complete_status ?? '') == 'Y' ? '다시보기' : '강의보기' }}</a>
                                @endif
                            @endif
                        </div>
                    </li>
                    @endforeach

                </ul>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
{{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
{{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
    {{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>


    </script>
@endsection
