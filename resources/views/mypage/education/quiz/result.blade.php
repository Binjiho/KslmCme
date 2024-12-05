@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="sub-conbox edu-view">
            <div class="view-wrap inner-layer">
                <h3 class="edu-view-tit">
                    <strong>{{ $sac_info->edu->title ?? '' }}</strong>
                </h3>
                <div class="btn-wrap text-right">
                    <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 color-type6">강의 목록으로 이동 <img src="/assets/image/sub/ic_btn_arrow.png" alt="" class="arrow"></a>
                </div>
                <div class="view-conbox quiz-result-conbox">
                    <span class="pass-cnt">
                        합격 기준 : {{ $sac_info->edu->pass_cnt ?? 0 }}/{{ $sac_info->edu->quiz_cnt ?? 0 }}
                    </span>
                    <div class="quiz-result">
                        <div class="chart-wrap">
                            <div class="chart">
                                <span class="graph" style="background: conic-gradient(#dd6014 0% {{ $sac_info->getQuizViewCnt(thisPk(), 'percent') }}%, #bfbfbf 1% 100%)">
                                    <span class="text"><strong>{{ $sac_info->getQuizViewCnt(thisPk(), 'complete') }} </strong> / {{ $sac_info->getQuizViewCnt(thisPk()) }}</span>
                                </span>
                            </div>
                            @if( ($sac_info->quiz_status ?? '') == 'C')
                            <div class="result-text">
                                <strong>합격</strong> 입니다.
                            </div>
                            @else
                            <div class="result-text fail">
                                <strong>불합격</strong> 입니다.
                                <p>
                                    재시험을 진행해 주세요.
                                </p>
                            </div>
                            @endif
                        </div>
                        <div class="table-wrap">
                            <table class="cst-table">
                                <caption class="hide">퀴즈 채점표</caption>
                                <tbody>
                                @foreach($quiz_view->chunk(5) as $chunk)
                                <tr>
                                    @foreach($chunk as $index => $view)
                                    <th scope="col">{{ $index+1 }}번</th>
                                    @endforeach
                                </tr>
                                <tr>
                                    @foreach($chunk as $index => $view)
                                        @if($view->my_answer == $view->quiz_answer)
                                            <td>정답</td>
                                        @else
                                            <td><span class="text-red2">오답</span></td>
                                        @endif
                                    @endforeach
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="btn-wrap text-center">
                        <a href="{{ route('mypage.education.detail',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 btn-round color-type8">확인 (강의목록으로 이동)</a>
                        @if( ($sac_info->quiz_status ?? '') == 'F')
                        <br>
                        <a href="{{ route('mypage.requiz',['ssid'=>$sac_info->sid]) }}" class="btn btn-type1 btn-round color-type7"><img src="/assets/image/sub/ic_refresh.png" alt=""> 재시험보기</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')

@endsection
