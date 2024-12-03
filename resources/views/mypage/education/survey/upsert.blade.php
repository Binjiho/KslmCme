@extends('layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
<div class="popup-wrap" id="pop-survey" style="display: block;">
    <div class="popup-contents">
        <div class="popup-tit-wrap">
            <h3 class="popup-tit">
                교육명 노출
            </h3>
        </div>
        <div class="popup-conbox">
            <form id="searchF" name="searchF" action="{{ route('mypage.education.data') }}" class="sch-form-wrap" data-case="survey-upsert" data-send="N">
                <input type="hidden" name="ssid" value="{{ request()->ssid ?? 0 }}">
                <fieldset>
                    <ul class="quiz-list">
                        @foreach($survey as $key => $val)
                            @if($val->gubun == 'A'/*객관식*/)
                            <li>
                                <div class="question">
                                    <span class="num">설문 {{ $key+1 }}.</span>
                                    <p>
                                        {{ $val->quiz ?? '' }}
                                    </p>
                                </div>
                                <div class="answer">
                                    <div class="radio-wrap type3">
                                        @if($val->quiz_item_1)
                                            <label for="q{{$key}}-1" class="radio-group"><input type="radio" name="answer_{{ $val->sid }}[]" id="q{{$key}}-1" value="1">{{ $val->quiz_item_1 }}</label>
                                        @endif
                                        @if($val->quiz_item_2)
                                            <label for="q{{$key}}-2" class="radio-group"><input type="radio" name="answer_{{ $val->sid }}[]" id="q{{$key}}-2" value="2">{{ $val->quiz_item_2 }}</label>
                                        @endif
                                        @if($val->quiz_item_3)
                                            <label for="q{{$key}}-3" class="radio-group"><input type="radio" name="answer_{{ $val->sid }}[]" id="q{{$key}}-3" value="3">{{ $val->quiz_item_3 }}</label>
                                        @endif
                                        @if($val->quiz_item_4)
                                            <label for="q{{$key}}-4" class="radio-group"><input type="radio" name="answer_{{ $val->sid }}[]" id="q{{$key}}-4" value="4">{{ $val->quiz_item_4 }}</label>
                                        @endif
                                        @if($val->quiz_item_5)
                                            <label for="q{{$key}}-5" class="radio-group"><input type="radio" name="answer_{{ $val->sid }}[]" id="q{{$key}}-5" value="5">{{ $val->quiz_item_5 }}</label>
                                        @endif
                                    </div>
                                </div>
                            </li>
                            @else
                                <li>
                                    <div class="question">
                                        <span class="num">설문 {{ $key+1 }}.</span>
                                        <p>
                                            {{ $val->quiz ?? '' }}
                                        </p>
                                    </div>
                                    <div class="answer">
                                        <textarea name="answer_{{ $val->sid }}[]" id="answer_{{ $val->sid }}" class="form-item"></textarea>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 btn-round color-type8"><img src="/assets/image/sub/ic_quiz.png" alt="">설문 제출</button>
                    </div>
                </fieldset>
            </form>
        </div>

        <button type="button" class="btn-popup-close"><span class="hide">닫기</span></button>
    </div>
</div>
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
            $('input[type="radio"][name^="answer_"]').each(function() {
                let name = $(this).attr('name');
                if ($(`input[name="${name}"]:checked`).length === 0) {
                    isValid = false;
                }

            });

            $("textarea[name^='answer_']").each(function() {
                let name = $(this).attr('name');
                if ($(`textarea[name="${name}"]`).val().length === 0) {
                    isValid = false;
                }
            });

            if(!isValid){
                alert("모든 설문조사에 참여 후 제출 가능합니다.");
                return false;  // 유효하지 않으면 나가기
            }

            callMultiAjax(dataUrl, ajaxData);
        }

    </script>
@endsection
