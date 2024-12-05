@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
<div style="padding:25px;">
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">퀴즈 {{ empty($quiz->sid) ? '등록' : '수정' }}</h3>
    </div>

    <div class="popup-conbox">
        <div class="write-form-wrap">
            <form id="mail-frm" method="post" action="{{ route('education.quiz.data') }}" data-sid="{{ $quiz->sid ?? 0 }}" data-case="quiz-{{ empty($quiz->sid) ? 'create' : 'update' }}" data-send="N">
                <input type="hidden" name="esid" value="{{ request()->esid ?? 0 }}" readonly>
                <div class="write-wrap">
                    <dl>
                        <dt style="text-align: center;"> <b style="color: #e95d5d;">*</b>문제 </dt>
                        <dd>
                            <textarea name="quiz" id="quiz">{{ $quiz->quiz ?? '' }}</textarea>
                        </dd>
                    </dl>

                    <dl>
                        <dt style="text-align: center;"> 이미지1</dt>
                        <dd>
                            <div class="filebox">
                                <input class="upload-name form-item" id="file1_text" placeholder="파일 업로드" readonly="readonly">
                                <label for="file1">파일 업로드</label>
                                <input type="file" id="file1" name="file1" class="file-upload" accept="image/jpg, image/jpeg, image/png" data-accept="jpeg|jpg|png" onchange="fileCheck(this,$('#file1_text'))">

                                @if(!empty($quiz->sid) && $quiz->realfile1)
                                    <a href="{{ $quiz->downloadUrl() }}">{{ $quiz->filename1 }} (다운)</a>

                                    <a href="#n" class="btn-file-delete text-red file_del" data-type="file1" data-path="{{ $quiz->realfile1 }}">X</a>

                                @endif
                            </div>
                        </dd>
                    </dl>
                    <dl>
                        <dt style="text-align: center;"> 이미지2</dt>
                        <dd>
                            <div class="filebox">
                                <input class="upload-name form-item" id="file2_text" placeholder="파일 업로드" readonly="readonly">
                                <label for="file2">파일 업로드</label>
                                <input type="file" id="file2" name="file2" class="file-upload" accept="image/jpg, image/jpeg, image/png" data-accept="jpeg|jpg|png" onchange="fileCheck(this,$('#file2_text'))">

                                @if(!empty($quiz->sid) && $quiz->realfile2)
                                    <a href="{{ $quiz->downloadUrl() }}">{{ $quiz->filename2 }} (다운)</a>

                                    <a href="#n" class="btn-file-delete text-red file_del" data-type="file2" data-path="{{ $quiz->realfile2 }}">X</a>

                                @endif
                            </div>
                        </dd>
                    </dl>

                    <dl>
                        <dt style="text-align: center;"> 보기 및 정답</dt>
                        <dd style="display: flex;">
                            <div class="radio-wrap">
                                @for($i=1; $i<=5; $i++)
                                    @php
                                        $quiz_item = 'quiz_item_'.$i;
                                    @endphp
                                    <div class="radio-group" style="display: flex;">
                                        <input type="radio" name="answer" id="answer_{{ $i }}" value="{{ $i }}" {{ ($quiz->answer ?? '') == $i ? 'checked' : '' }}>
                                        <label for="answer_{{ $i }}"></label>

                                        <input type="text" name="quiz_item_{{ $i }}" id="quiz_item_{{ $i }}" value="{{ $quiz->{$quiz_item} ?? '' }}" class="form-item" style="width: 80%" >
                                    </div>
                                @endfor
                            </div>
                        </dd>
                    </dl>

                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($quiz->sid) ? '등록' : '수정' }}</button>
                    <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('addScript')
{{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
{{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
{{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('click', '.file_del', function() {
            let ajaxData = {};
            ajaxData.case = 'file-delete';
            ajaxData.fileType = $(this).data('type');
            ajaxData.filePath = $(this).data('path');

            actionConfirmAlert('삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
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
                alert("문제를 입력해주세요.");
                return false;
            }

            let count = $("input[name^='quiz_item']").filter(function() {
                return $(this).val().trim() !== "";
            }).length;

            if(count < 2){
                alert("보기는 최소 2개 이상 입력해주세요.");
                return false;
            }

            if($("input[name='answer']").is(":checked") === false){
                alert("정답을 체크해주세요.");
                return false;
            }

            let ajaxData = newFormData(form);
            // ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
