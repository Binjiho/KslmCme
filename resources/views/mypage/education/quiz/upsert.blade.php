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
                <div class="view-conbox">
                    <form id="searchF" name="searchF" action="{{ route('mypage.education.data') }}" class="sch-form-wrap" data-case="quiz-upsert" data-send="N">
                        <input type="hidden" name="ssid" value="{{ request()->ssid ?? 0 }}">
                        <fieldset>
                            <legend class="hide">퀴즈</legend>
                            <ul class="quiz-list scroll-y">
                                @foreach($quiz as $key => $val)
                                <li>
                                    <div class="question">
                                        <span class="num">{{ $key+1 }}.</span>
                                        <p>
                                            {!! $val->quiz ?? '' !!}
                                        </p>

                                        @if($val->realfile1)
                                        <div class="img-wrap">
                                            <img src="{{ $val->realfile1 }}" alt="">
                                        </div>
                                        @endif
                                        @if($val->realfile2)
                                            <div class="img-wrap">
                                                <img src="{{ $val->realfile2 }}" alt="">
                                            </div>
                                        @endif
                                    </div>
                                    <div class="answer">
                                        <!-- 보기 한줄에 하나씩 나오게 하려면 class="full" 추가 -->
                                        <div class="radio-wrap cst">
                                            @if($val->quiz_item_1)
                                                <label for="q{{$key}}-1" class="radio-group"><input type="radio" name="question_{{ $val->sid }}[]" id="q{{$key}}-1" value="1">{{ $val->quiz_item_1 }}</label>
                                            @endif
                                            @if($val->quiz_item_2)
                                                <label for="q{{$key}}-2" class="radio-group"><input type="radio" name="question_{{ $val->sid }}[]" id="q{{$key}}-2" value="2">{{ $val->quiz_item_2 }}</label>
                                            @endif
                                            @if($val->quiz_item_3)
                                                <label for="q{{$key}}-3" class="radio-group"><input type="radio" name="question_{{ $val->sid }}[]" id="q{{$key}}-3" value="3">{{ $val->quiz_item_3 }}</label>
                                            @endif
                                            @if($val->quiz_item_4)
                                                <label for="q{{$key}}-4" class="radio-group"><input type="radio" name="question_{{ $val->sid }}[]" id="q{{$key}}-4" value="4">{{ $val->quiz_item_4 }}</label>
                                            @endif
                                            @if($val->quiz_item_5)
                                                <label for="q{{$key}}-5" class="radio-group"><input type="radio" name="question_{{ $val->sid }}[]" id="q{{$key}}-5" value="5">{{ $val->quiz_item_5 }}</label>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="btn-wrap text-center">
                                <button type="submit" class="btn btn-type1 btn-round color-type8"><img src="/assets/image/sub/ic_quiz.png" alt=""> 답안 제출</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('mypage.education.data') }}';
        const form = '#searchF';

        // 게시판 폼 체크
        $(form).validate({
            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(form);
            let isValid = true;

            // 각 질문 그룹을 검사
            $('input[type="radio"][name^="question_"]').each(function() {
                let name = $(this).attr('name');
                if ($(`input[name="${name}"]:checked`).length === 0) {
                    isValid = false;
                }
            });

            if(!isValid){
                alert("정답을 체크하지 않은 항목이 있습니다.");
                return false;  // 유효하지 않으면 나가기
            }

            callMultiAjax(dataUrl, ajaxData);
        }

    </script>
@endsection
