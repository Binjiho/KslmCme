@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">
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
                    <li>공지사항</li>
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
                        <form id="board-frm" data-sid="{{ $board->sid ?? 0 }}" data-case="board-{{ empty($board->sid) ? 'create' : 'update' }}">
                            <fieldset>
                                <legend class="hide">글쓰기</legend>
                                <div class="write-contop text-right">
                                    <div class="help-text"><strong class="required">*</strong> 표시는 필수입력 항목입니다.</div>
                                </div>
                                <ul class="write-wrap">
                                    <li>
                                        <div class="form-tit">작성자</div>
                                        <div class="form-con">
                                            <input type="text" name="writer" id="writer" class="form-item" value="{{ !empty($board->sid) ? $board->writer ?? '' : thisUser()->name_kr ?? '' }}">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-tit">이메일</div>
                                        <div class="form-con">
                                            <input type="text" name="email" id="email" class="form-item" value="{{ !empty($board->sid) ? $board->email ?? '' : thisUser()->email ?? '' }}">
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 제목</div>
                                        <div class="form-con">
                                            <input type="text" name="subject" id="subject" class="form-item" value="{{ $board->subject ?? '' }}">
                                            <div class="checkbox-wrap cst mt-10">
                                                @if($boardConfig['use']['notice'])
                                                    <label for="notice" class="checkbox-group"><input type="checkbox" name="notice" id="notice" value="Y" {{ (($board->notice ?? '') == 'Y') ? 'checked' : '' }}>공지</label>
                                                @endif
                                                @if($boardConfig['use']['main'])
                                                    <label for="main" class="checkbox-group"><input type="checkbox" name="main" id="main" value="Y" {{ (($board->main ?? '') == 'Y') ? 'checked' : '' }}>Push</label>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    @if($boardConfig['use']['link'])
                                        <li>
                                            <div class="form-tit">LINK URL</div>
                                            <div class="form-con">
                                                <input type="text" name="link_url" id="link_url" class="form-item" placeholder="http://" value="{{ $board->link_url ?? '' }}">
                                            </div>
                                        </li>
                                    @endif
                                    @if($boardConfig['use']['popup'])
                                        @php
                                            $popupDisplay = (($board->popup ?? 'N') === 'Y') ? '' : 'none';
                                            $popupDetailDisplay = (($popup->popup_detail ?? 'N') === 'Y') ? '' : 'none';
                                            $popupContentDisplay = (($popup->popup_select ?? '1') == '2') ? '' : 'none';
                                        @endphp

                                        <li >
                                            <div class="form-tit"><strong class="required">*</strong> 팝업 설정</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_yn'] as $key => $val)
                                                        <label for="popup_yn_{{$key}}" class="radio-group"><input type="radio" name="popup_yn" id="popup_yn_{{$key}}" value="{{ $key }}" {{ (($board->popup_yn ?? '') == $key) ? 'checked' : '' }}>{{ $val }}</label>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 템플릿</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_skin'] as $key => $val)
                                                        <label for="popup_skin_{{$key}}" class="radio-group"><input type="radio" name="popup_skin" id="popup_skin_{{$key}}" value="{{ $key }}" {{ (($popup->popup_skin ?? '') == $key) ? 'checked' : '' }}>{{ $val }}</label>
                                                    @endforeach


                                                    <a href="#n" id="popup_preview" class="btn btn-small">미리보기</a>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 내용 선택</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_select'] as $key => $val)
                                                        <label for="popup_select_{{ $key }}" class="radio-group"><input type="radio" name="popup_select" id="popup_select_{{ $key }}" value="{{$key}}" {{ (($popup->popup_select ?? '1') == $key) ? 'checked' : '' }}>{{ $val }}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 사이즈</div>
                                            <div class="form-con">
                                                <div class="form-group">
                                                    <span class="text">사이즈</span> : <input type="text" name="width" id="width" class="form-item w-10p" value="{{ $popup->width ?? '600' }}" maxlength="4" onlyNumber> X <input type="text" name="height" id="height" class="form-item w-10p" value="{{ $popup->height ?? '500' }}" maxlength="4" onlyNumber> (600x500 이상 입력할 것!, 팝업 미리보기에서 창 크기 조절로 자동반영 가능!)
                                                </div>
                                                <div class="form-group mt-10">
                                                    <span class="text">위치</span> : 위에서 <input type="text" name="position_y" id="position_y" class="form-item w-10p" value="{{ $popup->position_y ?? '0' }}" maxlength="4" onlyNumber> px, 왼쪽에서 <input type="text" name="position_x" id="position_x" class="form-item w-10p" value="{{ $popup->position_x ?? '0' }}" maxlength="4" onlyNumber> px
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 자세히 보기</div>
                                            <div class="form-con">
                                                <div class="radio-wrap cst">
                                                    @foreach($boardConfig['options']['popup_detail'] as $key => $val)
                                                        <label for="popup_detail_{{$key}}" class="radio-group"><input type="radio" name="popup_detail" id="popup_detail_{{$key}}" value="{{$key}}" {{ ($board->popup_detail ?? '') ==$key ? 'checked':'' }}>{{ $val }}</label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">자세히 보기 LINK</div>
                                            <div class="form-con">
                                                <input type="text" class="form-item w-200p" value="{{ $board->popup_link ?? '' }}" name="popup_link" id="popup_link" value="{{$key}}" {{ ($board->popup_detail ?? '') =='Y' ? '':'disabled' }}>
                                            </div>
                                        </li>

                                        {{--                                    <li>--}}
                                        {{--                                        <div class="form-tit">사이즈 변경</div>--}}
                                        {{--                                        <div class="form-con">--}}
                                        {{--                                            <div class="radio-wrap cst">--}}
                                        {{--                                                <label for="chk-popsize1" class="radio-group"><input type="radio" name="" id="chk-popsize1">설정함</label>--}}
                                        {{--                                                <label for="chk-popsize2" class="radio-group"><input type="radio" name="" id="chk-popsize2">설정안함</label>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </li>--}}

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-tit">팝업 시작일 / 종료일</div>
                                            <div class="form-con">
                                                <div class="form-group">
                                                    <span class="text">시작일 : </span> <input type="text" name="popup_sDate" id="popup_sDate" class="form-item w-20p datepicker" value="{{ $board->popup_sDate ?? '' }}" readonly datepicker> &nbsp;
                                                    <span class="text">종료일 : </span> <input type="text" name="popup_eDate" id="popup_eDate" class="form-item w-20p datepicker" value="{{ $board->popup_eDate ?? '' }}" readonly datepicker>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="popupBox" style="display: {{ $popupDisplay }}">
                                            <div class="form-con">
                                                팝업 내용
                                                <textarea name="popup_contents" id="popup_contents" class="tinymce">{{ $board->popup_contents ?? '' }}</textarea>
                                            </div>
                                        </li>
                                    @endif

                                    @if($boardConfig['use']['contents'])
                                        <li>
                                            <div class="form-con">
                                                공지사항 내용
                                                <textarea name="contents" id="contents" class="tinymce">{{ $board->contents ?? '' }}</textarea>
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

        // 공지설정 사용시
        if(boardConfig.use.notice) {
            $(document).on('click', 'input:radio[name=notice]', function() {
                if ($(this).val() === "Y") {
                    $("#notice_day").show();
                } else {
                    $("#notice_day").hide();
                    $("#notice_day").find("input:text").val('');
                }
            });

            // 공지 기간 체크
            $.validator.addMethod('isNoticeDate', function(value, element) {
                if ($('input:radio[name=notice]:checked').val() === 'Y') {
                    return !isEmpty(value);
                }

                return true;
            });
        }

        // 기간설정 사용시
        if(boardConfig.use.date) {
            $(document).on('click', 'input:radio[name=date_type]', function() {
                if ($(this).val() === "L") {
                    $("#event_day").show();
                } else {
                    $("#event_day").hide();
                    $('input[name=event_eDate]').val('');
                }
            });

            // 행사기간 날짜 체크
            $.validator.addMethod('isEventDateEmpty', function(value, element) {
                if (element.name == 'event_eDate') {
                    return $('input:radio[name=date_type]:checked').val() == 'D' ? true : !isEmpty(value);
                }

                return !isEmpty(value);
            });
        }

        // 구분 or 카테고리 사용시
        if(boardConfig.use.category || boardConfig.use.gubun) {
            $.validator.addMethod('isCategoryEmpty', function(value, element) {
                const name = element.name;

                if ($(`input:radio[name='${name}']`).length > 0) {
                    return $(`input:radio[name='${name}']:checked`).length > 0;
                }

                if ($(`select[name='${name}']`).length > 0) {
                    return !isEmpty($(`select[name='${name}']`).val());
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
                const name = $(this).attr('name');

                if (!fileDelCheck(`#${name}_del`)) {
                    e.preventDefault();
                }
            });

            $(document).on('change', 'input[type=file]', function (e) {
                const name = $(this).attr('name');
                fileCheck(this, `#${name}_name`);
            });
        }

        $(document).on('click',"input[name='popup_yn']", function(){
            if($(this).val() == 'Y'){
                $(".popupBox").show();
            }else{
                $(".popupBox").hide();
            }
        });

        $(document).on('click',"input[name='popup_detail']", function(){
            if($(this).val() == 'Y'){
                $("input[name='popup_link']").prop('disabled',false);
            }else{
                $("input[name='popup_link']").val('');
                $("input[name='popup_link']").prop('disabled',true);
            }
        });

        $(document).on('click',".popup_close_btn", function(){
            if ($('.win-popup-wrap').length > 0) {
                $('.win-popup-wrap').remove();
            }
        });

        // 팝업 사용시
        if(boardConfig.use.popup) {
            // 팝업 설정 radio
            $(document).on('click', 'input:radio[name=popup]', function() {
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

            // 팝업 미리보기 ## todo 수정필요
            $(document).on('click', '#popup_preview', function(e) {
                const subject = $("#subject").val();

                if (isEmpty(subject)) {
                    alert('제목을 입력해주세요.');
                    $('#subject').focus();
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

                const popupSelect = $('input:radio[name=popup_select]:checked').val();

                if (isEmpty(popupSelect)) {
                    alert(`팝업 내용 선택 을 해주세요.`);
                    $('#input:radio[name=popup_select]').focus();
                    return;
                }

                const contents = (popupSelect == "1")
                    ? 'contents'
                    : 'popup_contents';

                const tinyVal = tinymce.get(contents).getContent();
                let tinyValChk = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                tinyValChk = tinyValChk.replace(/\&nbsp;/g, ' '); // &nbsp 삭제;

                if (isEmpty(tinyValChk)) {
                    alert('내용을 입력해주세요.');
                    $('#' + contents).focus();
                    return;
                }

                if (isEmpty($('input:radio[name=popup_detail]:checked').val())) {
                    alert(`팝업 자세히 보기를 선택해주세요.`);
                    $('#input:radio[name=popup_detail]').focus();
                    return;
                }

                if( $('input:radio[name=popup_detail]:checked').val()== 'Y'){
                    if (isEmpty($('#popup_link').val())) {
                        alert(`팝업 자세히 보기 링크를 입력해주세요.`);
                        $('#popup_link').focus();
                        return;
                    }
                }

                // let ajaxData = newFormData(boardForm);
                // ajaxData.case = 'popup-preview';
                let previewData = newFormData(boardForm);
                previewData.append('case', 'popup-preview');
                previewData.append('contents', tinymce.get('contents').getContent());
                previewData.append('popup_contents', tinymce.get('popup_contents').getContent());
                // previewData.append('plupload_file', JSON.stringify(plupladFile));

                if ($('.win-popup-wrap').length > 0) {
                    $('.win-popup-wrap').remove();
                }

                callMultiAjax(dataUrl, previewData);

            });

            // 팝업 입력정보 체크
            $.validator.addMethod('popupIsEmpty', function(value, element) {
                if ($('input:radio[name=popup]:checked').val() === 'Y') {
                    return !isEmpty(value);
                }

                return true;
            });

            // 팝업 옵션정보 체크
            $.validator.addMethod('popupCheckEmpty', function(value, element) {
                return $(`input[name='${element.name}']:checked`).length > 0
            });

            // 팝업 사이즈 체크
            $.validator.addMethod('popupSize', function(value, element) {
                const size = (element.name === 'width')
                    ? popupMinWidth
                    : popupMinHeight;

                return (parseInt(uncomma(value)) >= size);
            });

            // 팝업 자세히보기 링크 체크
            $.validator.addMethod('popupLinkIsEmpty', function(value, element) {
                return !isEmpty(value);
            });

            // 팝업 내용 체크
            $.validator.addMethod('PopupIsTinyEmpty', function(value, element) {
                if ($('input:radio[name=popup_select]:checked').val() == '2') {
                    let tinyVal = tinymce.get(element.id).getContent(); // 내용 가져오기
                    tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
                    tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

                    return !isEmpty(tinyVal);
                }

                return true;
            });
        }

        defaultVaildation();

        // 게시판 폼 체크
        $(boardForm).validate({
            ignore: ['contents', 'popup_contents'],
            rules: {
                writer: {
                    isEmpty: true,
                },
                email: {
                    isEmpty: true,
                },
                subject: {
                    isEmpty: true,
                },

                popup_yn: {
                    checkEmpty: true,
                },
                popup_skin: {
                    // popupCheckEmpty: true,
                    popupCheckEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                popup_select: {
                    popupCheckEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                width: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                    popupSize: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                height: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                    popupSize: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                position_x: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                position_y: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                popup_detail: {
                    popupCheckEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },

                popup_sDate: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                popup_eDate: {
                    popupIsEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                popup_contents: {
                    PopupIsTinyEmpty: {
                        depends: function(el) {
                            return $("input[name='popup_yn']:checked").val()==='Y';
                        }
                    },
                },
                contents: {
                    isTinyEmpty: true,
                },
            },
            messages: {
                subject: {
                    isEmpty: `${boardConfig.subject}을 입력해주세요.`,
                },
                link_url: {
                    isEmpty: '링크를 입력해주세요.',
                },
                popup_yn: {
                    checkEmpty: '팝업 설정을 체크해주세요.',
                },
                popup_skin: {
                    popupCheckEmpty: '팝업 템플릿을 선택해주세요.',
                },
                popup_select: {
                    popupCheckEmpty: '팝업 내용을 선택해주세요.',
                },
                width: {
                    popupIsEmpty: '팝업 가로 사이즈를 입력해주세요.',
                    popupSize: (popupMinWidth + ' 이상 입력해주세요.'),
                },
                height: {
                    popupIsEmpty: '팝업 세로 사이즈를 입력해주세요.',
                    popupSize: (popupMinHeight + ' 이상 입력해주세요.'),
                },
                position_x: {
                    popupIsEmpty: '팝업 위에서 위치를 입력해주세요.',
                },
                position_y: {
                    popupIsEmpty: '팝업 왼쪽에서 위치를 입력해주세요.',
                },
                popup_detail: {
                    popupCheckEmpty: '팝업 자세히 보기를 선택해주세요.',
                },
                popup_link: {
                    popupLinkIsEmpty: '자세히 보기 LINK 를 입력해주세요. ',
                },
                popup_sDate: {
                    popupIsEmpty: '팝업 시작일을 선택해주세요.',
                },
                popup_eDate: {
                    popupIsEmpty: '팝업 종료일을 선택해주세요.',
                },
                popup_contents: {
                    PopupIsTinyEmpty: '팝업 내용을 입력해주세요.',
                },
                contents: {
                    isTinyEmpty: '내용을 입력해주세요.',
                },
            },
            submitHandler: function() {
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
                        });
                    }
                }

                let ajaxData = newFormData(boardForm);
                ajaxData.append('contents', tinymce.get('contents').getContent());
                ajaxData.append('popup_contents', tinymce.get('popup_contents').getContent());
                ajaxData.append('plupload_file', JSON.stringify(plupladFile));

                callMultiAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
