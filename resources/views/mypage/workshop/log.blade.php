@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">마이페이지</h2>
            <p>
                마이페이지를 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>마이페이지</li>
                    <li>나의 자료실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            @include('layouts.include.sub-menu-wrap')
            
            <div class="sch-form-wrap">
                <form id="searchF" name="searchF" action="{{ route('mypage.workshop_log') }}" class="sch-form-wrap">
                    <fieldset>
                        <legend class="hide">통합검색</legend>
                        <div class="sch-wrap">
                            <div class="form-group">
                                <input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="검색하실 내용을 입력하세요." value="{{ request()->search_key ?? '' }}">
                                <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                                <a href="{{ route('mypage.workshop_log') }}" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">검색 초기화</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="sch-result-list">
                <ul class="board-list">

                    <li class="list-head">
                        <div class="bbs-tit bbs-col-m n-bar">행사명</div>
                        <div class="bbs-col-s n-bar">행사일</div>
                        <div class="bbs-info n-bar">세부정보</div>
                    </li>

                    @forelse($list as $idx => $row)
                    <li>
                        <div class="bbs-tit bbs-col-m text-left n-bar">
                            <a href="javascript:;">{{ $row->workshop->title ?? '' }}</a>
                        </div>
                        <div class="bbs-col-s n-bar">
                            {{ $row->workshop->sdate->format('Y-m-d') }} {{ $row->workshop->edate && isValidTimestamp($row->workshop->edate) ? ' ~ '.$row->workshop->edate->format('Y-m-d') : '' }}
                        </div>
                        <div class="bbs-info text-left n-bar">
                            <div class="bbs-info-wrap">
                                <a href="javascript:;">
                                    @php
                                        $field_arr=array();
                                        foreach($workshopConfig['field'] as $field_key => $field_val){
                                            if(in_array($field_key, $row->sub->field ?? []) ) {
                                                $field_arr[] = $field_val;
                                            }
                                        }
                                    @endphp
                                    <span class="cate">[{{ implode(',',$field_arr) }}]</span> {{ $row->sub->title ?? '' }}
                                    <p>
                                        {{ $row->sub->pname ?? '' }}({{ $row->sub->psosok ?? '' }})
                                    </p>
                                </a>
                                <div class="file">
                                    @if(!empty($row->sub->video_link))
                                    <a href="{{ route('workshop.popup',['sid'=>$row->sub->sid,'wsid'=>$row->wsid]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-popup" data-width="850" data-height="900"><img src="/assets/image/sub/ic_video.png" alt=""></a>
                                    @endif
                                    @if(!empty($row->sub->realfile))
                                    <a href="{{ $row->sub->downloadUrl() }}" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="no-data">
                        <img src="/assets/image/sub/img_nodata.png" alt="">
                        <p>검색된 결과가 없습니다.</p>
                    </li>
                    @endforelse
                </ul>
            </div>

            {{ $list->links('pagination::custom') }}

        </div>
    </article>
@endsection

@section('addScript')
    <script>

    </script>
@endsection
