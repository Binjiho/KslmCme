@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}" >
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
				@include('layouts.include.sub-menu-wrap')

                <!-- s:board -->
                <div class="sch-wrap type3 skin1">
                    <form id="bbsSearch" action="{{ route('board', ['code' => $code]) }}" method="get">
                        <feildset>
                            <legend class="hide">검색</legend>
                            <div class="form-group">
                                <select name="search" id="search" class="form-item sch-cate">
                                    <option value="contents" {{ request()->search == 'contents' ? 'selected':'' }}>제목+내용</option>
                                    <option value="writer" {{ request()->search == 'writer' ? 'selected':'' }}>작성자</option>
                                </select>
                                <input type="text" name="keyword" id="keyword" class="form-item sch-key" placeholder="검색어를 입력하세요." value="{{ request()->keyword ?? ''}}">
                                <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                            </div>
                        </feildset>
                    </form>
                </div>

                <div id="board" class="board-wrap">
                    <ul class="board-list">
                    @foreach($notice_list as $row)
                        <li class="ef01" data-sid="{{ $row->sid ?? 0 }}">
                            <div class="list-con">
                                <div class="bbs-tit">
                                    <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                        {{ $row->subject ?? '' }}
                                    </a>
                                    @if($row->notice == 'Y')
                                        <span class="ic-new">N</span>
                                    @endif
                                </div>
                                <span class="bbs-name">{{ $row->writer ?? '' }}</span>
                                <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                                <span class="bbs-hit">{{ number_format($row->ref) }}</span>
                            </div>
                            <div>
                                @if(isAdmin())
                                    <div class="bbs-admin">
                                        <select name="hide" id="hide" class="form-item change-hide">
                                            <option value="N" {{ ($row->hide ?? '') == 'N' ? 'selected' : '' }}>공개</option>
                                            <option value="Y" {{ ($row->hide ?? '') == 'Y' ? 'selected' : '' }}>비공개</option>
                                        </select>
                                        <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                        <a href="#n" class="btn btn-delete"><span class="hide">삭제</span></a>
                                    </div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                    @forelse($list as $row)
                        <li class="ef01" data-sid="{{ $row->sid ?? 0 }}">
                            <div class="list-con">
                                <div class="bbs-tit">
                                    <a href="{{ route('board.view', ['code' => $code, 'sid' => $row->sid]) }}" class="ellipsis2">
                                        {{ $row->subject ?? '' }}
                                    </a>
                                    @if($row->notice == 'Y')
                                        <span class="ic-new">N</span>
                                    @endif
                                </div>
                                <span class="bbs-name">{{ $row->writer ?? '' }}</span>
                                <span class="bbs-date">{{ $row->created_at->format('Y-m-d') }}</span>
                                <span class="bbs-hit">{{ number_format($row->ref) }}</span>
                            </div>
                            <div>
                                @if(isAdmin())
                                <div class="bbs-admin">
                                    <select name="hide" id="hide" class="form-item change-hide">
                                        <option value="N" {{ ($row->hide ?? '') == 'N' ? 'selected' : '' }}>공개</option>
                                        <option value="Y" {{ ($row->hide ?? '') == 'Y' ? 'selected' : '' }}>비공개</option>
                                    </select>
                                    <a href="{{ route('board.upsert', ['code' => $code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                    <a href="#n" class="btn btn-delete"><span class="hide">삭제</span></a>
                                </div>
                                @endif
                            </div>
                        </li>
                        @empty
                        <!-- no data -->
                        <li class="no-data text-center">
                            <img src="{{ asset('assets/image/board/ic_nodata.png') }}" alt=""> <br>
                            등록된 게시글이 없습니다.
                        </li>
                        @endforelse
                    </ul>
                    @if(isAdmin())
                        <div class="btn-wrap text-right">
                            <a href="{{ route('board.upsert',['code'=>$code]) }}" class="btn btn-board btn-write">등록</a>
                        </div>
                    @endif

                    {{ $list->links('pagination::custom') }}

                </div>
                <!-- //e:board -->

            </div>
        </article>
@endsection

@section('addScript')
    @include("board.default-script")

    <script>
        $(document).on('click', '.btn-delete', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'board-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('change', '.change-hide', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'board-hide',
                'target': $(this).val(),
            };

            if (confirm('게시글 공개 여부를 변경 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }else{
                window.reload();
            }
        });
    </script>
@endsection
