@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/accordion.css') }}" >
{{--    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">--}}
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
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
                    <li>FAQ</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            <!-- s:board -->
            <div id="board" class="board-wrap">
                <div class="board-write">
                    <div class="write-form-wrap">
                        <form id="board-frm" method="POST" data-sid="{{ $board->sid ?? 0 }}" data-case="board-{{ empty($board->sid) ? 'create' : 'update' }}">
                            <fieldset>
                                <legend class="hide">글쓰기</legend>
                                <div class="write-contop text-right">
                                    <div class="help-text"><strong class="required">*</strong> 표시는 필수입력 항목입니다.</div>
                                </div>
                                <ul class="write-wrap">
                                    <li>
                                        <div class="form-tit">작성자</div>
                                        <div class="form-con">
                                            <input type="text" name="writer" id="writer" class="form-item" value="{{ !empty($board->sid) ? $board->writer ?? '' : thisUser()->name_kr ?? '' }}" readonly>
                                        </div>
                                    </li>

{{--                                    <li>--}}
{{--                                        <div class="form-tit">카테고리</div>--}}
{{--                                        <div class="form-con">--}}
{{--                                            <select name="" id="" class="form-item">--}}
{{--                                                <option value="">카테고리 1</option>--}}
{{--                                                <option value="">카테고리 2</option>--}}
{{--                                                <option value="">카테고리 3</option>--}}
{{--                                                <option value="">카테고리 4</option>--}}
{{--                                                <option value="">카테고리 5</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </li>--}}

                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 질문</div>
                                        <div class="form-con">
                                            <input type="text" name="subject" id="subject" class="form-item" value="{{ $board->subject ?? '' }}">
                                        </div>
                                    </li>
                                    @if($boardConfig['use']['hide'])
                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 공개 여부</div>
                                        <div class="form-con">
                                            <div class="radio-wrap cst">
                                                @foreach($boardConfig['options']['hide'] as $key => $val)
                                                <label for="hide_{{$key}}" class="radio-group"><input type="radio" name="hide" id="hide_{{$key}}" value="{{$key}}">{{$val}}</label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                    @endif

                                    @if($boardConfig['use']['plupload'])
                                    <li>
                                        <div class="form-con">
                                            <div id="plupload"></div>
                                        </div>
                                    </li>
                                    @endif

                                </ul>
                                <div class="btn-wrap text-center">
                                    <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-cancel">취소</a>
                                    <button type="submit" class="btn btn-board btn-write">{{ empty($board->sid) ? '등록' : '수정' }}</button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <!-- //e:board -->

        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    <script>
        // 게시글 작성 취소
        $(document).on('click', '.btn-cancel', function(e) {
            e.preventDefault();

            const msg = ($(boardForm).data('sid') == 0) ?
                '등록을 취소하시겠습니까?' :
                '수정을 취소하시겠습니까?';

            if (confirm(msg)) {
                location.replace('{{ route('board', ['code' => $code]) }}');
            }
        });

        //팝업 미리보기 창 닫기
        $(document).on('click',".popup_close_btn", function(){
            if ($('.win-popup-wrap').length > 0) {
                $('.win-popup-wrap').remove();
            }
        });

        // 자세히보기 LINK
        $(document).on('click',"input[name='popup_detail']", function(){
            if($(this).val() == 'Y'){
                $("input[name='popup_link']").prop('disabled',false);
            }else{
                $("input[name='popup_link']").val('');
                $("input[name='popup_link']").prop('disabled',true);
            }
        });

        // 기간설정 사용시
        if(boardConfig.use.date) {
            $(document).on('change', 'input:radio[name=date_type]', function() {
                const target = $('.eDate-display');

                if ($(this).val() === "L") {
                    target.show();
                } else {
                    target.hide();
                    target.find('input').val('');
                }
            });
        }

        // 첨부파일 (plupload) 사용시
        if(boardConfig.use.plupload) {
            pluploadInit({
                multipart_params: {
                    directory: boardConfig.directory,
                },
                filters: {
                    max_file_size: '20mb'
                },
            });
        }

        // 첨부파일 (단일파일) or 썸네일 사용시
        if(boardConfig.use.file || boardConfig.use.thumbnail) {
            $(document).on('click', 'input[type=file]', function (e) {
                const target = $(this).closest('.filebox').find('.attach-file');

                if (!fileDelCheck(target)) {
                    e.preventDefault();
                }
            });

            $(document).on('change', 'input[type=file]', function () {
                const name = $(this).attr('name');
                fileCheck(this, `#${name}_name`);
            });

            $(document).on('click', '.btn-file-delete', function () {
                const name = $(this).closest('.filebox').find('input[type=file]').attr('name');
                const target = $(this).closest('.filebox').find('.attach-file');

                target.remove();
                $(`#${name}_del`).val('Y');
            });
        }

        // 팝업 사용시
        if(boardConfig.use.popup) {
            // 팝업 설정 radio
            $(document).on('click', 'input:radio[name=popup_yn]', function() {
                if ($(this).val() === "Y") {
                    $(".popupBox").show();
                } else {
                    $(".popupBox").hide();
                    $(".popupBox").find("input:text").val('');
                    tinymce.get('popup_contents').getContent('');
                }
            });

            // 팝업 내용 선택
            $(document).on('click', 'input:radio[name=popup_select]', function() {
                $('.popupContentBox').css('display', $(this).val() == '2' ? 'table-row' : 'none');
            });

            // 팝업 자세히 보기 radio
            $(document).on('click', 'input:radio[name=popup_detail]', function() {
                if ($(this).val() === "Y") {
                    $(".popupDetailBox").show();
                } else {
                    $(".popupDetailBox").hide();
                    $(".popupDetailBox").find("input:text").val('');
                }
            });

            // 팝업 미리보기
            $(document).on('click', '#popup_preview', function(e) {
                const subject = $("#subject").val();

                if (isEmpty(subject)) {
                    alert('제목을 입력해주세요.');
                    $('#subject').focus();
                    return;
                }

                if (!$('input[name=popup_skin]').is(':checked')) {
                    alert('팝업 템플릿을 선택해주세요.');
                    $('input[name=popup_skin]').focus();
                    return;
                }

                if (parseInt($("#width").val()) < popupMinWidth) {
                    alert(`${popupMinWidth} 이상 입력해주세요.`);
                    $('#width').focus();
                    return;
                }

                if (parseInt($("#height").val()) < popupMinHeight) {
                    alert(`${popupMinHeight} 이상 입력해주세요.`);
                    $('#height').focus();
                    return;
                }

                const contents = ($('input:radio[name=popup_select]:checked').val() == "1")
                    ? 'contents'
                    : 'popup_contents';

                const tinyVal = tinymce.get(contents).getContent();
                // let tinyValChk = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                const tinyValChk = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제;

                if (isEmpty(tinyValChk)) {
                    alert('내용을 입력해주세요.');
                    $('#' + contents).focus();
                    return;
                }

                let ajaxData = newFormData(boardForm);
                ajaxData.append('case', 'popup-preview');
                ajaxData.append('contents', tinymce.get('contents').getContent());
                ajaxData.append('popup_contents', tinymce.get('popup_contents').getContent());

                const plupload_queue = $('#plupload').pluploadQueue();

                $(plupload_queue.files).each(function (k, v) {
                    ajaxData.append('plupload[]', v.name);
                });

                callMultiAjax(dataUrl, ajaxData);
            });

            // 팝업 미리보기 닫기
            $(document).on('click', '.btn-pop-close, .btn-pop-today-close', function () {
                $(this).closest('.popup-wrap').remove();
            });
        }

        $(document).on('submit', boardForm, function () {
            const writer = $('#writer');
            if (isEmpty(writer.val())) {
                alert('작성자를 입력해주세요.');
                writer.focus();
                return false;
            }

            if (boardConfig.use.gubun) {
                switch (boardConfig.gubun.type) {
                    case 'radio':
                        if ($('input[name=gubun]').is(':checked')) {
                            alert(`${boardConfig.gubun.name}를 선택해주세요.`);
                            $('input[name=gubun]').focus();
                            return false;
                        }
                        break;

                    case 'select':
                        if (isEmpty($('select[name=gubun]').val())) {
                            alert(`${boardConfig.gubun.name}를 선택해주세요.`);
                            $('select[name=gubun]').focus();
                            return false;
                        }
                        break;
                }
            }

            if (boardConfig.use.category) {
                switch (boardConfig.category.type) {
                    case 'radio':
                        if ($('input[name=category]').is(':checked')) {
                            alert(`${boardConfig.category.name}를 선택해주세요.`);
                            $('input[name=category]').focus();
                            return false;
                        }
                        break;

                    case 'select':
                        if (isEmpty($('select[name=category]').val())) {
                            alert(`${boardConfig.category.name}를 선택해주세요.`);
                            $('select[name=category]').focus();
                            return false;
                        }
                        break;
                }
            }

            if (boardConfig.use.subject) {
                const subject = $('#subject');
                if (isEmpty(subject.val())) {
                    alert(`${boardConfig.subject}을 입력해주세요.`);
                    subject.focus();
                    return false;
                }
            }

            if (boardConfig.use.date) {
                const date_type = $('input[name=date_type]');
                if (!date_type.is(':checked')) {
                    alert('행사기간을 선택해주세요.');
                    date_type.focus();
                    return false;
                }

                const event_sDate = $('input[name=event_sDate]');
                if (isEmpty(event_sDate.val())) {
                    alert('행사 시작일을 선택해주세요.');
                    event_sDate.focus();
                    return false;
                }

                if ($('input[name=date_type]:checked').val() == 'L') {
                    const event_eDate = $('input[name=event_eDate]');
                    if (isEmpty(event_eDate.val())) {
                        alert('행사 종료일을 선택해주세요.');
                        event_eDate.focus();
                        return false;
                    }
                }
            }

            if (boardConfig.use.hide) {
                const hide = $('input[name=hide]');
                if (!hide.is(':checked')) {
                    alert('공개여부를 선택해주세요.');
                    hide.focus();
                    return false;
                }
            }

            if (boardConfig.use.popup) {
                const popup = $('input[name=popup_yn]');
                if (!popup.is(':checked')) {
                    alert('팝업설정를 선택해주세요.');
                    popup.focus();
                    return false;
                }

                // 팝업 사용 선택시
                if($('input[name=popup_yn]:checked').val() == 'Y') {
                    const popup_skin = $('input[name=popup_skin]');
                    if (!popup_skin.is(':checked')) {
                        alert('팝업 탬플릿을 선택해주세요.');
                        popup_skin.focus();
                        return false;
                    }

                    const popup_select = $('input[name=popup_select]');
                    if (!popup_select.is(':checked')) {
                        alert('팝업 내용을 선택해주세요.');
                        popup_select.focus();
                        return false;
                    }

                    const width = $('input[name=width]');
                    if (isEmpty(width.val())) {
                        alert('팝업 가로 사이즈를 입력해주세요.');
                        width.focus();
                        return false;
                    }

                    if (popupMinWidth > parseInt(width.val())) {
                        alert(`${popupMinHeight} 이상 입력해주세요.`);
                        width.focus();
                        return false;
                    }

                    const height = $('input[name=height]');
                    if (isEmpty(height.val())) {
                        alert('팝업 세로 사이즈를 입력해주세요.');
                        height.focus();
                        return false;
                    }

                    if (popupMinHeight > parseInt(height.val())) {
                        alert(`${popupMinHeight} 이상 입력해주세요.`);
                        height.focus();
                        return false;
                    }

                    const position_x = $('input[name=position_x]');
                    if (isEmpty(position_x.val())) {
                        alert('팝업 위에서 위치를 입력해주세요.');
                        position_x.focus();
                        return false;
                    }

                    const position_y = $('input[name=position_y]');
                    if (isEmpty(position_y.val())) {
                        alert('팝업 왼쪽에서 위치를 입력해주세요.');
                        position_y.focus();
                        return false;
                    }

                    const popup_detail = $('input[name=popup_detail]');
                    if (!popup_detail.is(':checked')) {
                        alert('팝업 자세히 보기를 선택해주세요.');
                        popup_detail.focus();
                        return false;
                    }

                    if ($('input[name=popup_detail]:checked').val() == 'Y') {
                        const popup_link = $('input[name=popup_link]');
                        if (isEmpty(popup_link.val())) {
                            alert('팝업 자세히 보기 LINK 를 입력해주세요.');
                            popup_link.focus();
                            return false;
                        }
                    }

                    const popup_sDate = $('input[name=popup_sDate]');
                    if (isEmpty(popup_sDate.val())) {
                        alert('팝업 시작일을 선택해주세요.');
                        popup_sDate.focus();
                        return false;
                    }

                    const popup_eDate = $('input[name=popup_eDate]');
                    if (isEmpty(popup_eDate.val())) {
                        alert('팝업 종료일을 선택해주세요.');
                        popup_eDate.focus();
                        return false;
                    }

                    if ($('input[name=popup_select]:checked').val() == '2') {
                        let popupTinyVal = tinymce.get('popup_contents').getContent(); // 내용 가져오기
                        // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                        popupTinyVal = popupTinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

                        if (isEmpty(popupTinyVal)) {
                            alert('팝업 내용을 입력해주세요.');
                            return false;
                        }
                    }
                }
                /* END 팝업사용선택 */
            }

            // let tinyVal = tinymce.get('contents').getContent(); // 내용 가져오기
            // // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            // tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제
            //
            // if (isEmpty(tinyVal)) {
            //     alert('내용을 입력해주세요.');
            //     return false;
            // }

            if(boardConfig.use.plupload) { // plupload 사용할때
                const plupload_queue = $('#plupload').pluploadQueue();

                let fileCnt = plupload_queue.files.length;
                fileCnt = (fileCnt - previousUploadedFilesCount);

                if (fileCnt > 0) {
                    spinnerShow();
                    plupload_queue.start();
                    plupload_queue.bind('UploadComplete', function(up, files) {
                        spinnerHide();

                        if (plupload_queue.total.failed !== 0) {
                            alert('파일 업로드 실패');
                            location.reload();
                            return false;
                        }

                        // 업로드된 파일 수를 저장
                        previousUploadedFilesCount = up.files.length;
                        boardSubmit();
                    });

                    return false;
                }
            }

            boardSubmit();
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(boardForm);

            // 내용 사용시
            if(boardConfig.use.contents) {
                ajaxData.append('contents', tinymce.get('contents').getContent());
            }

            // 팝업 사용시
            if(boardConfig.use.popup) {
                ajaxData.append('popup_contents', tinymce.get('popup_contents').getContent());
            }

            // plupload 사용시
            if(boardConfig.use.plupload) {
                ajaxData.append('plupload_file', JSON.stringify(plupladFile));
            }

            callMultiAjax(dataUrl, ajaxData);
        }
    </script>
@endsection
