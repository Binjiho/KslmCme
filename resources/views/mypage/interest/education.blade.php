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
                    <li>온라인 강의실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            @include('layouts.include.sub-menu-wrap')

            <div class="edu-board-list sch-result-list cf">

                @forelse($list as $idx => $row)
                    <div class="edu-board-con">
                        <span class="cate {{ ($row->edu->category ?? '') == 'Z' ? 'etc' : ''}}">{{ $educationConfig['category'][$row->edu->category] ?? '' }} {{ ($row->edu->category ?? '') == 'A' ? ' - '.$educationConfig['gubun'][$row->edu->gubun ?? ''] : ''}}</span>
                        <a href="{{ route('education.detail',['esid'=>$row->edu->sid]) }}"><strong class="tit ellipsis2">{{ $row->edu->title ?? '' }}</strong></a>
                        <p class="date">
                            <strong>신청기간</strong> <br>
                            @if($row->edu->regist_limit_yn == 'N')
                                상시신청가능
                            @else
                                {{ $row->edu->regist_sdate->format('Y-m-d') }} ~ {{ $row->edu->regist_edate->format('Y-m-d') }}
                            @endif
                        </p>
                        @if(thisUser())
                            <div class="btn-wrap">
                                <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($row->edu->getHeart($row->edu->sid) ) ? 'on': '' }}" data-esid="{{ $row->edu->sid }}">관심교육</a>
                                <a href="{{ route('education.detail',['esid'=>$row->edu->sid]) }}" class="btn btn-small color-type3">교육보기</a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="edu-board-con no-data">
                        <img src="/assets/image/sub/img_nodata.png" alt="">
                        <p>검색된 결과가 없습니다.</p>
                    </div>
                @endforelse

            </div>

            {{ $list->links('pagination::custom') }}

        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('mypage.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('li').data('sid');
        }

        $(document).on('click', '.change-heart', function() {
            const ajaxData = {
                'esid': $(this).data('esid'),
                'case': 'change-heart',
                'target': $(this).hasClass('on') ? 'Y':'N',
            };

            callAjax(dataUrl, ajaxData);
        });
    </script>
@endsection
