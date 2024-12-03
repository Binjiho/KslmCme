@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">
@endsection

@section('contents')
    <article class="sub-contents">
        <div class="page-tit-wrap inner-layer">
            <h3 class="page-tit">Notice</h3>
        </div>

        <div class="sub-conbox inner-layer">
            <div id="board" class="board-wrap">
                <div class="board-view">
                    <div class="view-contop">
                        <h4 class="view-tit">
                            [Notice] Vol.1 [M2community 2025] website is open!(For Korean)
                        </h4>

                        <div class="view-info text-right">
                            <span><strong>Writer : </strong>M2community 2025</span>
                            <span><strong>Date : </strong>2024-06-04</span>
                            <span><strong>Hits : </strong>46</span>
                        </div>
                    </div>
                    <div class="view-contents editor-contents">
                    </div>
                    <div class="view-attach">
                        <div class="view-attach-con">
                            <div class="con">
                                <a href="#n" target="_blank">파일명.jpg (다운로드 N회)</a>
                            </div>
                        </div>
                    </div>
                    <div class="btn-wrap text-right">
                        <a href="./list.html" class="btn btn-board btn-list">List</a>
                        <a href="./write.html" class="btn btn-board btn-modify">Modify</a>
                        <a href="#n" class="btn btn-board btn-delete">Delete</a>
                    </div>

                    <!-- 이전글/다음글 type2 -->
                    <div class="view-move type2">
                        <div class="view-move-con view-prev">
                            <strong class="tit">Prev</strong>
                            <div class="con"><a href="#n" class="ellipsis">No Contents.</a></div>
                        </div>
                        <div class="view-move-con view-next">
                            <strong class="tit">Next</strong>
                            <div class="con"><a href="#n" class="ellipsis">Vol.1 [M2community 2025] website is open!(For Overseas)</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //e:board -->
        </div>
    </article>

    <div class="contents">
        <div class="bbsView">
            <h4>
                @if($boardConfig['use']['gubun'])
                    <span>{{ $board->gubunTxt() }}</span>
                @endif

                @if($boardConfig['use']['category'])
                    <span>{{ $board->categoryTxt() }}</span>
                @endif

                {{ $board->subject }}
            </h4>

            <table>
                <colgroup>
                    <col style="width: 20%;">
                    <col style="width: 30%;">
                    <col style="width: 20%;">
                    <col style="width: 30%;">
                </colgroup>

                <tbody>
                    <tr>
                        <th>작성일</th>
                        <td>{{ $board->created_at->format('Y.m.d') }}</td>
                        <th>조회수</th>
                        <td>{{ number_format($board->ref) }}</td>
                    </tr>

                    @if ($boardConfig['use']['date'])
                        <tr>
                            <th>{{ $boardConfig['date']['name'] }}</th>
                            <td colspan="3">
                                {{ $board->eventPeriod() }}
                            </td>
                        </tr>
                    @endif

                    @if($boardConfig['use']['link'] && !empty($board->link_url))
                        <tr>
                            <th>URL</th>
                            <td colspan="3">
                                <a href="{{ $board->link_url ?? '' }}" target="_blank">{{ $board->link_url ?? '' }}</a>
                            </td>
                        </tr>
                    @endif

                    @if($boardConfig['use']['place'])
                        <tr>
                            <th>장소</th>
                            <td colspan="3">{{ $board->place ?? '' }}</td>
                        </tr>
                    @endif

                    @if($boardConfig['use']['file'])
                        @foreach($boardConfig['file'] as $key => $val)
                            @php
                                $nameField = "filename{$key}";
                                if (empty($board->{$nameField})) { // 파일 없으면 패스
                                    continue;
                                }
                            @endphp

                            <tr>
                                <th>{{ $val }}</th>
                                <td colspan="3">
                                    <a href="{{ $board->downloadUrl($key) }}">
                                        {{ $board->{$nameField} }} ({{ number_format($board->{"file{$key}_download"}) }})
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    @if($boardConfig['use']['plupload'] && $board->files_count > 0)
                        <tr>
                            <th>첨부파일</th>
                            <td colspan="3">
                                @foreach($board->files as $file)
                                    <a href="{{ $file->downloadUrl() }}">
                                        {{ $file->filename }}  (다운 : {{ number_format($file->download) }})
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="bbsCon">{!! $board->contents !!}</div>
        </div>
        <!-- //bbs -->

        <div class="bbsUtil">
            <div class="btn">
                <a href="{{ route('board', ['code' => $code]) }}" class="list">
                    <img src="/image/icon/icon_list.png" alt="">목록
                </a>

                @if(isAdmin() || thisPK() == $board->u_sid)
                    <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $board->sid]) }}">
                        <img src="/image/icon/icon_upload.png" alt="">수정
                    </a>

                    <a href="javascript:void(0);" id="bbs-del">삭제</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('addScript')
    @include("board.default-script")

    @if(isAdmin() || thisPK() == $board->u_sid)
        <script>
            $(document).on('click', '#bbs-del', function() {
                if (confirm('정말로 삭제 하시겠습니까?')) {
                    callAjax(dataUrl, { case: 'board-delete', sid: {{ $board->sid }} });
                }
            });
        </script>
    @endif
@endsection
