@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">
{{--    <link href="/assets/css/editor.css" rel="stylesheet">--}}
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
                <div class="board-view">
                    <div class="view-contop">
                        <h4 class="view-tit">
                            <strong>{{ $board->subject }}</strong>
                        </h4>
                        <div class="view-info">
                            <span><strong>등록자 : </strong>{{ $board->name }}</span>
                            <span><strong>등록일 : </strong>{{ $board->created_at->format('Y-m-d') }}</span>
                            <span><strong>조회수 : </strong>{{ number_format($board->ref) }}</span>
                        </div>
                    </div>
                    @if($board->link_url)
                    <div class="view-link text-right">
                        <a href="{{ $board->link_url }}" target="_blank">{{ $board->link_url }}</a>
                    </div>
                    @endif
                    <div class="view-contents editor-contents">
                        {!! $board->contents !!}
                    </div>
                    @if($boardConfig['use']['plupload'] && $board->files_count > 0)
                        <div class="view-attach">
                            <div class="view-attach-con">
                                <div class="con">
                                    @foreach($board->files as $file)
                                        <a href="{{ $file->downloadUrl() }}">
{{--                                            <img src="/assets/image/board/ic_file2.png" alt="">--}}
                                            {{ $file->filename }} <!-- (다운 {{ number_format($file->download) }}건) -->
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="btn-wrap text-right">
                        <a href="{{ route('board', ['code' => $code]) }}" class="btn btn-board btn-list">목록</a>
                        @if(isAdmin() || thisPK() == $board->u_sid)
                        <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $board->sid]) }}" class="btn btn-board btn-modify">수정</a>
                        <a href="#n" class="btn btn-board btn-delete">삭제</a>
                        @endif
                    </div>

                    <!-- 이전글/다음글 type2 -->
                    <div class="view-move type2">
                        @if( !empty($board->getPrev($board->sid)) )
                            <div class="view-move-con view-prev">
                                <strong class="tit">이전글</strong>
                                <div class="con"><a href="{{ route('board.view', ['code' => $code, 'sid' => $board->getPrev($board->sid)->sid]) }}" class="ellipsis">{{ $board->getPrev($board->sid)->subject ?? '' }}</a></div>
                            </div>
                        @endif
                        @if( !empty($board->getNext($board->sid)) )
                        <div class="view-move-con view-next">
                            <strong class="tit">다음글</strong>
                            <div class="con"><a href="{{ route('board.view', ['code' => $code, 'sid' => $board->getNext($board->sid)->sid]) }}" class="ellipsis">{{ $board->getNext($board->sid)->subject ?? '' }}</a></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- //e:board -->

        </div>
    </article>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(isAdmin() || thisPK() == $board->u_sid)
        <script>
            $(document).on('click', '.btn-delete', function() {
                if (confirm('정말로 삭제 하시겠습니까?')) {
                    callAjax(dataUrl, { case: 'board-delete', sid: {{ $board->sid }} });
                }
            });
        </script>
    @endif
@endsection
