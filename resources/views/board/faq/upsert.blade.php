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
                            <input type="hidden" name="writer" id="writer" class="form-item" value="{{ !empty($board->sid) ? $board->writer ?? '' : thisUser()->name_kr ?? '' }}" readonly>
                            <fieldset>
                                <legend class="hide">글쓰기</legend>
                                <div class="write-contop text-right">
                                    <div class="help-text"><strong class="required">*</strong> 표시는 필수입력 항목입니다.</div>
                                </div>
                                <ul class="write-wrap">
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
                                                <label for="hide_{{$key}}" class="radio-group"><input type="radio" name="hide" id="hide_{{$key}}" value="{{$key}}" {{ ($board->hide ?? '') == $key ? 'checked' : '' }}>{{$val}}</label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </li>
                                    @endif

                                    @if($boardConfig['use']['plupload'] && ($board->files_count ?? 0) > 0)
                                        <li>
                                            <div class="form-tit">첨부파일</div>
                                            <div class="form-con">
                                                @foreach($board->files as $key => $file)
                                                    <div style="display: flex; align-items: center">
                                                        <input type="checkbox" name="plupload_file_del[]" id="plupload_file_del{{ $key }}" value="{{ $file->sid }}">
                                                        <label for="plupload_file_del{{ $key }}" style="margin-left: 0.3rem; margin-right: 0.5rem;"> <span style="color: red;"> 삭제</span> - </label>

                                                        <a href="{{ $file->downloadUrl() }}">
                                                            {{ $file->filename }}
                                                        </a>

                                                        <span style="margin-left: 0.3rem;">(다운 : {{ number_format($file->download) }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endif

                                @if($boardConfig['use']['contents'])
                                    <li>
                                        <div class="form-con">
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


        $(document).on('submit', boardForm, function () {
            // const writer = $('#writer');
            // if (isEmpty(writer.val())) {
            //     alert('작성자를 입력해주세요.');
            //     writer.focus();
            //     return false;
            // }

            if (boardConfig.use.subject) {
                const subject = $('#subject');
                if (isEmpty(subject.val())) {
                    alert(`${boardConfig.subject}을 입력해주세요.`);
                    subject.focus();
                    return false;
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


            let tinyVal = tinymce.get('contents').getContent(); // 내용 가져오기
            // tinyVal = tinyVal.replace(/<[^>]*>?/g, ''); // html 태그 삭제
            tinyVal = tinyVal.replace(/\&nbsp;/g, ' '); // &nbsp 삭제

            if (isEmpty(tinyVal)) {
                alert('내용을 입력해주세요.');
                return false;
            }

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


            // plupload 사용시
            if(boardConfig.use.plupload) {
                ajaxData.append('plupload_file', JSON.stringify(plupladFile));
            }

            callMultiAjax(dataUrl, ajaxData);
        }
    </script>
@endsection
