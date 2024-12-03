@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">설문 {{ empty($survey->sid) ? '등록' : '수정' }}</h3>
    </div>

    <form id="mail-frm" method="post" action="{{ route('education.survey.data') }}" data-sid="{{ $survey->sid ?? 0 }}" data-case="survey-{{ empty($survey->sid) ? 'create' : 'update' }}" data-send="N">
        <input type="hidden" name="esid" value="{{ request()->esid ?? 0 }}" readonly>
        <div class="write-wrap">
            <dl>
                <dt style="text-align: center;"> <b style="color: #e95d5d;">*</b> 질문 </dt>
                <dd>
                    <textarea name="quiz" id="quiz">{{ $survey->quiz ?? '' }}</textarea>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 설문타입</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($surveyConfig['gubun'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ ($survey->gubun ?? '') == $key ? 'checked' : '' }}>
                                <label for="gubun_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 보기 </dt>
                <dd style="display: flex;">
                    <div class="radio-wrap">
                        <div class="radio-group">
                            <input type="text" name="quiz_item_1" id="quiz_item_1" value="{{ $survey->quiz_item_1 ?? '전혀 아니다' }}" class="form-item" style="width: 80%" {{ $survey->gubun == 'B' ? 'disabled' : '' }}>
                            <input type="text" name="quiz_item_2" id="quiz_item_2" value="{{ $survey->quiz_item_2 ?? '아니다' }}" class="form-item" style="width: 80%" {{ $survey->gubun == 'B' ? 'disabled' : '' }}>
                            <input type="text" name="quiz_item_3" id="quiz_item_3" value="{{ $survey->quiz_item_3 ?? '보통이다' }}" class="form-item" style="width: 80%" {{ $survey->gubun == 'B' ? 'disabled' : '' }}>
                            <input type="text" name="quiz_item_4" id="quiz_item_4" value="{{ $survey->quiz_item_4 ?? '그렇다' }}" class="form-item" style="width: 80%" {{ $survey->gubun == 'B' ? 'disabled' : '' }}>
                            <input type="text" name="quiz_item_5" id="quiz_item_5" value="{{ $survey->quiz_item_5 ?? '매우 그렇다' }}" class="form-item" style="width: 80%" {{ $survey->gubun == 'B' ? 'disabled' : '' }}>
                        </div>
                    </div>
                </dd>
            </dl>

        </div>

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($survey->sid) ? '등록' : '수정' }}</button>
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
        </div>
    </form>
@endsection

@section('addScript')
{{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
{{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
{{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('click', "input[name='gubun']", function() {
            if ( $("input[name='gubun']:checked").val() == 'B' ){
                $("input[name^='quiz_item_']").val('');
                $("input[name^='quiz_item_']").prop("disabled",true);
            }else{
                $("input[name^='quiz_item_']").prop("disabled",false);
            }
        });

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],

            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {

            if(isEmpty( $("#quiz").val() ) ){
                alert("설문을 입력해주세요.");
                return false;
            }

            if( $("input[name='gubun']:checked").length < 1){
                alert("설문타입을 체크해주세요.");
                return false;
            }

            let count = $("input[name^='quiz_item']").filter(function() {
                return $(this).val().trim() !== "";
            }).length;

            if($("input[name='gubun']:checked").val()=='A'){
                if(count < 2){
                    alert("보기는 최소 2개 이상 입력해주세요.");
                    return false;
                }
            }

            let ajaxData = newFormData(form);
            // ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
