@extends('layouts.web-layout')

@section('addStyle')
    <link rel="stylesheet" href="{{ asset('assets/board/css/accordion.css') }}" >
{{--    <link rel="stylesheet" href="{{ asset('assets/board/css/board.css') }}">--}}
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

            @include('layouts.include.sub-menu-wrap')

                <div class="sub-conbox inner-layer">

                    <!-- s:board -->
                    <div id="board" class="board-wrap">
                        <ul class="acco-list js-acco-list">
                        @forelse($list as $row)
                            @continue(!isAdmin() && thisPK() != $row->u_sid)
                            <li class="ef01">
                                <div class="acco-tit">
                                    <a href="#n">{{ $row->subject ?? '' }}</a></div>
                                @if(!empty($row->contents) || isAdmin())
                                <div class="acco-con">
                                    <div class="view-contents editor-contents">
                                        {!! $row->contents ?? '' !!}
                                    </div>

                                    @if(($row->files_count ?? 0) > 0)
                                    <div class="view-attach">
                                        <div class="view-attach-con">
                                            <div class="con">
                                                @foreach($row->files as $key => $file)
                                                    <a href="{{ empty($preview) ? $file->downloadUrl() : "javascript:void(0);" }}">
                                                        {{ $file->filename }} (다운로드 : {{ number_format($file->download) }}회)
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(thisPK() != $row->u_sid || isAdmin())
                                        <div class="bbs-admin text-right">
                                            <select name="hide" id="hide" class="form-item change-hide">
                                                <option value="N" {{ ($row->hide ?? '') == 'N' ? 'selected' : '' }}>공개</option>
                                                <option value="Y" {{ ($row->hide ?? '') == 'Y' ? 'selected' : '' }}>비공개</option>
                                            </select>
                                            @if(isAdmin())
                                            <a href="{{ route('board.reply', ['code' => $code, 'sid' => $row->sid]) }}" class="btn btn-modify"><span class="hide">수정</span></a>
                                            <a href="#n" class="btn btn-delete"><span class="hide">삭제</span></a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </li>
                        @empty
                            <li class="no-data">
                                <p>등록된 게시글이 없습니다.</p>
                            </li>
                        @endforelse
                        </ul>

                        @if(thisUser())
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
        $(function(e){
            if($('.js-acco-list').length){
                $('.js-acco-list .acco-tit').on('click',function(){
                    $(this).parent('li').toggleClass('on');
                    $(this).parent('li').siblings().removeClass('on');
                    $(this).parent('li').siblings().children('.acco-con').stop().slideUp();
                    $(this).next('.acco-con').stop().slideToggle();
                    $(this).parent('li').siblings().children('.js-acco-con').stop().slideUp();
                });
            }
        });

        $(document).on('click', '.btn-delete', function() {
            const ajaxData = {
                case: 'board-delete',
                sid: getPK(this),
            }

            if (confirm('정말로 삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
