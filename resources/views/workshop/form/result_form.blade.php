<div class="sch-result-list">

    <div class="filter text-right">
        <p class="full-left">총 <strong class="cnt">{{ $total ?? 0 }}</strong>건의 자료가 검색되었습니다.</p>
        <a href="javascript:;" data-sort="asc" class="sort {{ (request()->sort ?? 'desc') == 'asc' ? 'on' : '' }}">오래된순</a>
        <a href="javascript:;" data-sort="desc" class="sort {{ (request()->sort ?? 'desc') == 'desc' ? 'on' : '' }}">최신순</a>
    </div>

    <ul class="board-list">
        <li class="list-head">
            <div class="bbs-tit bbs-col-m n-bar">행사명</div>
            <div class="bbs-col-s n-bar">행사일</div>
            <div class="bbs-info n-bar">세부정보</div>
        </li>

        @forelse($list as $idx => $row)
        <li>
            <div class="bbs-tit text-left bbs-col-m n-bar">
                <a href="{{ route('workshop.detail',['wsid'=>$row->workshop->sid]) }}">{{ $row->workshop->title ?? '' }}</a>
            </div>
            <div class="bbs-col-s n-bar">
                {{ $row->workshop->sdate->format('Y-m-d') }} {{ $row->workshop->edate && isValidTimestamp($row->workshop->edate) ? ' ~ '.$row->workshop->edate->format('Y-m-d') : '' }}
            </div>
            <div class="bbs-info text-left n-bar">
                <div class="bbs-info-wrap">
                    @php
                        $field_arr=array();
                        foreach($workshopConfig['field'] as $field_key => $field_val){
                            if(in_array($field_key, $row->field ?? []) ) {
                                $field_arr[] = $field_val;
                            }
                        }
                    @endphp
                    <a href="{{ route('workshop.detail',['wsid'=>$row->workshop->sid]) }}">
                        <span class="cate">[{{ implode(',',$field_arr) }}]</span> {{ $row->title ?? '' }}
                        <p>
                            {{ $row->pname ?? '' }}({{ $row->psosok ?? '' }})
                        </p>
                    </a>

                    <div class="file">
                        @if(!empty($row->video_link))
                            @if(thisLevel()=='M' || in_array(thisLevel(),$row->workshop->limit_level) !== false)
                                <a href="{{ route('workshop.popup',['sid'=>$row->sid,'wsid'=>$row->workshop->sid]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-popup" data-width="850" data-height="900">
                                    <img src="/assets/image/sub/ic_video.png" alt="">
                                </a>
                            @else
                                <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" class="btn btn-small btn-type1 color-type20 ">
                                    <img src="/assets/image/sub/ic_video.png" alt="">
                                </a>
                            @endif
                        @endif
                        @if(!empty($row->realfile))
                            @if(thisLevel()=='M' || in_array(thisLevel(),$row->workshop->limit_level) !== false)
                                <a href="{{ $row->downloadUrl() }}" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                            @else
                                <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                            @endif
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