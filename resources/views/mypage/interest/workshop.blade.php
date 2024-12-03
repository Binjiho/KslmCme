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

            <div class="sch-result-list">
                <ul class="ev-list">
                    @forelse($list as $idx => $row)
                    <li>
                        <div class="img-wrap">
                            <a href="{{ route('workshop.detail',['wsid'=>$row->wsid]) }}">
                            @if($row->workshop->realfile)
                                <img src="{{ $row->workshop->realfile }}" alt="">
                            @endif
                            </a>
                        </div>
                        <div class="text-wrap">
                            <p class="tit ellipsis3"><a href="{{ route('workshop.detail',['wsid'=>$row->wsid]) }}">{{ $row->workshop->title ?? '' }}</a></p>
                            <p class="date">
                                {{ $row->workshop->sdate->format('Y-m-d') }} {{ $row->workshop->edate ? ' ~ '.$row->workshop->edate->format('Y-m-d') : '' }}
                            </p>
                            @if(thisUser())
                                <div class="btn-wrap">
                                    <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($row->workshop->getHeart($row->workshop->sid) ) ? 'on': '' }}" data-wsid="{{ $row->workshop->sid }}">관심자료</a>
                                    <a href="{{ route('workshop.detail',['wsid'=>$row->wsid]) }}" class="btn btn-small color-type3">자료보기</a>
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

            {{ $list->links('pagination::custom') }}

        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('mypage.workshop.data') }}';

        $(document).on('click', '.change-heart', function() {
            const ajaxData = {
                'wsid': $(this).data('wsid'),
                'case': 'change-heart',
                'target': $(this).hasClass('on') ? 'Y':'N',
                'type': 'W',
            };

            callAjax(dataUrl, ajaxData);
        });
    </script>
@endsection
