<div class="sch-result-list">

    <div class="filter text-right">
        <a href="javascript:;" data-sort="asc" class="sort {{ (request()->sort ?? 'desc') == 'asc' ? 'on' : '' }}">오래된순</a>
        <a href="javascript:;" data-sort="desc" class="sort {{ (request()->sort ?? 'desc') == 'desc' ? 'on' : '' }}">최신순</a>
    </div>

    <ul class="ev-list">
        @forelse($list as $idx => $row)
            <li>
                <div class="img-wrap">
                    <a href="{{ route('workshop.detail',['wsid'=>$row->sid]) }}">
                        @if(!empty($row->realfile))
                            <img src="{{ $row->realfile }}" alt="">
                        @else
                            <img src="{{ asset('/assets/image/common/thumb_default.jpg') }}" alt="">
                        @endif
                    </a>
                </div>
                <div class="text-wrap">
                    <p class="tit ellipsis3">
                        <a href="{{ route('workshop.detail',['wsid'=>$row->sid]) }}">
                            {{ $row->title ?? '' }}
                        </a>
                    </p>
                    <p class="date">
                        {{ $row->sdate->format('Y-m-d') }} {{ $row->edate && isValidTimestamp($row->edate) ? ' ~ '.$row->edate->format('Y-m-d') : '' }}
                    </p>
                    @if(thisUser())
                        <div class="btn-wrap">
                            <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($row->getHeart($row->sid) ) ? 'on': '' }}" data-wsid="{{ $row->sid }}">관심자료</a>
                            <a href="{{ route('workshop.detail',['wsid'=>$row->sid]) }}" class="btn btn-small color-type3">자료보기</a>
                        </div>
                    @endif
                </div>
            </li>

        @empty
            <div class="edu-board-con no-data">
                <img src="/assets/image/sub/img_nodata.png" alt="">
                <p>검색된 결과가 없습니다.</p>
            </div>
        @endforelse
    </ul>

</div>