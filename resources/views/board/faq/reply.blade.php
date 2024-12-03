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
                        <form id="board-frm" method="POST" data-sid="{{ $board->sid ?? 0 }}" data-case="board-reply">
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

                                    <li>
                                        <div class="form-tit"><strong class="required">*</strong> 질문</div>
                                        <div class="form-con">
                                            <input type="text" name="subject" id="subject" class="form-item" value="{{ $board->subject ?? '' }}" readonly>
                                        </div>
                                    </li>

                                    @if($boardConfig['use']['plupload'] && ($board->files_count ?? 0) > 0)
                                        <li>
                                            <div class="form-tit">첨부파일</div>
                                            <div class="form-con">
                                                @foreach($board->files as $key => $file)
                                                    <div style="display: flex; align-items: center">
                                                        <a href="{{ $file->downloadUrl() }}">
                                                            {{ $file->filename }}
                                                        </a>

                                                        <span style="margin-left: 0.3rem;">(다운 : {{ number_format($file->download) }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endif

                                    @if(isAdmin())
                                        <li>
                                            <div class="form-con">
                                                <textarea name="contents" id="contents" class="tinymce">{{ $board->contents ?? '' }}</textarea>
                                            </div>
                                        </li>
                                    @endif

                                </ul>
                                <div class="btn-wrap text-center">
                                    <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-cancel">취소</a>
                                    <button type="submit" class="btn btn-board btn-write">답변</button>
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

        defaultVaildation();

        // 게시판 폼 체크
        $(boardForm).validate({
            ignore: ['contents'],
            rules: {
                subject: {
                    isEmpty: true,
                },
                contents: {
                    isTinyEmpty: true,
                },
            },
            messages: {
                subject: {
                    isEmpty: `${boardConfig.subject}을 입력해주세요.`,
                },
                contents: {
                    isTinyEmpty: '내용을 입력해주세요.',
                },
            },
            submitHandler: function() {

                let ajaxData = newFormData(boardForm);
                ajaxData.append('contents', tinymce.get('contents').getContent());

                callMultiAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
