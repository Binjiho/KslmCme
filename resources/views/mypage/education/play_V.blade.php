@extends('layouts.web-layout')

@section('addStyle')
{{--    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />--}}

{{--<link rel="stylesheet" href="//cdn.flowplayer.com/releases/native/3/stable/style/flowplayer.css" />--}}
<link rel="stylesheet" href="//releases.flowplayer.org/6.0.2/skin/functional.css">
<style>
    /* .view-conbox .play-wrap{
        padding-top: 1.25%;
    } */
    /* ****************************** flowplayer controllbar setting ********************************** */
    .fp-time {display:block !important}
    .fp-controls {display:block !important}
    .fp-timeline {top: 100;}
    .flowplayer .fp-remaining,
    .flowplayer .fp-duration {right: 11225px; /* orginally: 180px */}
    .flowplayer .fp-embed {right: 11110px; /* originally 50px */}
    .flowplayer .fp-embed-code {right: 111147px; /* originally 67px */}

    .fp-timeline {
        {{ isAdmin() || ($lecture_view->complete_status ?? '')=='Y' ? '' : 'display:none !important;' }}
    }
    /* position new fullscreen button */
    .flowplayer .fp-fullscreen {top: -500;}
    /* **************************** //flowplayer controllbar setting ********************************** */
</style>
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox edu-view">
            <div class="view-wrap inner-layer">
                <h3 class="edu-view-tit">
                    {{--<strong>[교육]</strong>--}} {{ $sac_info->edu->title ?? '' }}
                </h3>
                <div class="btn-wrap text-right">
                    <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 color-type6">강의 목록으로 이동 <img src="/assets/image/sub/ic_btn_arrow.png" alt="" class="arrow"></a>
                </div>
                <div class="view-conbox">
                    <div class="play-wrap">
                        <input type="hidden" name="complete_status" id="complete_status" value="{{ $lecture_view->complete_status ?? 'N' }}" readonly>
                        <input type="hidden" name="ing_time" id="ing_time" value="{{ $lecture_view->ing_time ?? 0 }}" readonly>
                        <input type="hidden" name="play_time" id="play_time" value="{{ $lecture->play_time ?? 0 }}" readonly>
                        <input type="hidden" name="play_yn" id="play_yn" value="{{ empty($lecture_view->sid) ? 'Y':'N' }}" readonly>
                        <input type="hidden" name="play_click" id="play_click" value="N" readonly>
                        <input type="hidden" name="ssid" id="ssid" value="{{ $sac_info->sid ?? '' }}" readonly>
                        <input type="hidden" name="lsid" id="lsid" value="{{ $lecture->sid ?? '' }}" readonly>
                        <input type="hidden" name="esid" id="esid" value="{{ $sac_info->edu->sid ?? '' }}" readonly>
                        <!-- <div class="flowplayer" data-ratio="0.4167" data-key="{{ env('APP_PLAYER_KEY') }}" style="height:550px; width:100%"> -->
                        <div class="flowplayer" data-ratio="0.4167" data-key="{{ env('APP_PLAYER_KEY') }}">
                            <video>
                                <source type="video/mp4" id="video" src="{{ $lecture['link_url'] }}">
                            </video>
                        </div>
                    </div>
                    <div class="play-info text-right">
                        @if(($lecture_view->complete_status ?? '') == 'Y')
                            <div class="btn-wrap">
                                <a href="#n" class="btn btn-speed on" data="1">X 1배속</a>
                                <a href="#n" class="btn btn-speed" data="1.2">X 1.2배속</a>
                                <a href="#n" class="btn btn-speed" data="1.5">X 1.5배속</a>
                            </div>
                        @else
                            최초 재생일 경우 구간 재생이 불가합니다.
                        @endif
                    </div>
                    <div class="lecture-tit-wrap">
                        <h4 class="lecture-tit">{{ $lecture->title ?? '' }}</h4>
                        <p class="name">{{ $lecture->name_kr ?? '' }} / {{ $lecture->sosok_kr ?? '' }}</p>
                    </div>
                    <div class="progress-box">
                        <div class="progress">
                            <strong>수강진도율</strong>
                            <div class="bar">
                                <span class="percent" style="width: {{ $lecture->getPercent($sac_info->sid, $lecture->sid) }}%;"></span>
                            </div>
                            <span class="percent_txt">{{ $lecture->getPercent($sac_info->sid, $lecture->sid) }}%</span>
                        </div>
                        <p>
                            강의 수강을 완료한 경우, 수상 진도율이 100%가 된 후 교육 목록으로 이동해주세요.
                        </p>
                    </div>
                    <div class="btn-wrap text-center">
                        <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 btn-round color-type7"><img src="/assets/image/sub/ic_power.png" alt=""> 강의 종료 (강의 목록으로 이동)</a>
                    </div>

                    <div class="popup-wrap" id="surprise_pop" style="display: none;">
                        <div class="popup-contents">
                            <div class="popup-conbox">
                                <img src="/assets/image/sub/ic_pop_play.png" alt="">
                                <p>
                                    강의 계속 듣기 버튼을 클릭하면, <br>
                                    현재 시청중인 강의가 계속 플레이됩니다.
                                </p>
                                <div class="btn-wrap">
                                    <a href="javascript:;" id="play_btn" class="btn btn-type1">강의 계속 듣기</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
{{--    <script src="//cdn.flowplayer.com/releases/native/3/stable/default/flowplayer.min.js"></script>--}}
    <script src="{{ asset('plugins/flowplayer/flowplayer.min.js') }}"></script>

    <script>
        const dataUrl = '{{ route('mypage.education.data') }}';

        $(document).ready(function(){
            const api = flowplayer();
            // console.log(flowplayer.version);

            let intervalId;

            let ing_time = $("input[name='ing_time']").val();
            let play_time = $("input[name='play_time']").val();

            api.on("ready", function (e) {
                api.seek(ing_time);
            });
            // 비디오가 일시 정지될 때 호출되는 이벤트
            api.on("pause", function (e) {
                stopInterval();
            });
            // 비디오가 다시 재생될 때 호출되는 이벤트
            api.on("resume", function (e) {
                startInterval();
            });

            // 비디오가 완료되었을 때 호출되는 이벤트
            api.on("finish", function(e) {
                console.log("비디오가 완료되었습니다."); // 비디오 완료 시 로그 출력
                // interval이 5초마다 돌아서 짧은 영상은 완료처리 안될 수 있음
                if($("input[name='complete_status']").val()=='N'/*수강완료 하지않은상태*/){

                    let ajaxData = {
                        'case': 'vod-finish',
                        'ssid': $("input[name='ssid']").val(),
                        'lsid': $("input[name='lsid']").val(),
                        'esid': $("input[name='esid']").val(),
                    };

                    callbackAjax(dataUrl, ajaxData, function(data, error) {
                        if (data) {
                            if (data.result['res'] == "complete") {
                                location.reload();
                            }else if(data.result['res'] == "error"){
                                alert(data.result['msg']);
                                // clearInterval(intervalId);
                                stopInterval();
                                location.reload();
                            }else{
                                var percent = parseInt(data.result['percent']);
                                $(".percent").css("width",percent+"%");
                                $(".percent_text").html(percent+"%");
                            }
                        }
                    }, true);

                }
            });

            if ($("input[name='complete_status']").val()=='N'/*수강완료 하지않은상태*/){
                startInterval();
            }

            // 버튼 클릭 시 인터벌 재시작
            $(document).on("click", "#play_btn", function () {
                $("#play_click").val('Y');
                $("#surprise_pop").hide();
                startInterval();
            });

            $(".btn-speed").click(function(){
                var speed = $(this).attr("data");
                $(".btn-speed").removeClass("on");
                $(this).addClass("on");
                api.speed(speed);
            });

            // 인터벌 멈추는 함수
            function stopInterval() {
                api.pause();
                clearInterval(intervalId);
                intervalId = null;
            }

            // 인터벌 시작하는 함수
            function startInterval() {
                api.resume();

                if (intervalId) return; // 이미 인터벌이 실행 중이면 중복 실행 방지

                intervalId = setInterval(function(){
                    let currentPos = api.ready ? api.video.time : 0;
                    let now_time = currentPos.toString();

                    // console.log('currentPos + :'+currentPos);
                    // console.log('now_time + :'+now_time);
                    // console.log('play_time + :'+play_time);
                    // console.log('ing_time + :'+ing_time);
                    // console.log('ing_time_seq + :'+Math.round(ing_time * 10) / 10);
                    // console.log(now_time > play_time);

                    //계속재생 깜짝 팝업띄우기
                    if( $("input[name='play_yn']").val() == 'Y'){
                        if ( (Math.round(now_time * 10) / 10) > (Math.round(play_time * 10) / 10) && $("#play_click").val() == 'N'){
                            stopInterval();
                            $("#surprise_pop").show();
                        }
                    }

                    const _complete_status = $("input[name='complete_status']").val();
                    if(_complete_status != 'Y'){
                        if( now_time != Math.round(ing_time * 10) / 10 ){

                            let ajaxData = {
                                'case': 'vod-play',
                                'ing_time': now_time,
                                'ssid': $("input[name='ssid']").val(),
                                'lsid': $("input[name='lsid']").val(),
                                'esid': $("input[name='esid']").val(),
                            };

                            callbackAjax(dataUrl, ajaxData, function(data, error) {
                                if (data) {
                                    if (data.result['res'] == "complete") {
                                        location.reload();
                                    }else if(data.result['res'] == "error"){
                                        alert(data.result['msg']);
                                        // clearInterval(intervalId);
                                        stopInterval();
                                        location.reload();
                                    }else{
                                        var percent = parseInt(data.result['percent']);
                                        $(".percent").css("width",percent+"%");
                                        // $(".percent").html(percent+"%");
                                        $(".percent_txt").html(percent+"%");

                                    }
                                }
                            }, true);
                        }
                    }
                },5000);
            }

        });

    </script>
@endsection
