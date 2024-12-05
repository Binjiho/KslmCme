@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
    <div class="sub-visual-con inner-layer">
        <h2 class="sub-visual-tit">통합검색</h2>
        <p>
            통합검색을 확인하실 수 있습니다.
        </p>
        <div class="breadcrumb">
            <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
            <ul>
                <li>통합검색</li>
            </ul>
        </div>
    </div>
    </article>
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <div class="sch-form-wrap">
                <form id="searchF" name="searchF" action="{{ route('unite') }}" class="sch-form-wrap">
                    <fieldset>
                        <legend class="hide">통합검색</legend>
                        <div class="sch-wrap">
                            <div class="form-group">
                                <input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="검색하실 내용을 입력하세요." value="{{ request()->search_key ?? '' }}">
                                <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
{{--                                <button type="reset" class="btn btn-reset">검색 초기화</button>--}}
                                <a href="{{ route('unite') }}" class="btn btn-reset">검색 초기화</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="sub-tit-wrap">
                <h3 class="sub-tit">수강/열람신청 ({{ $education_cnt ?? 0 }}건)</h3>
                <a href="{{ route('education',['search_key'=>request()->search_key ?? '']) }}" class="btn btn-more">더보기</a>
            </div>
            <div class="edu-board-list">
                @forelse($education_list as $row)
                <div class="edu-board-con">
                    <a href="#n">
                        <span class="cate {{ ($row->category ?? '') == 'Z' ? 'etc' : ''}}">{{ $educationConfig['category'][$row->category] ?? '' }}</span>
                        <strong class="tit ellipsis2">{{ $row->title ?? '' }}</strong>
                        <p class="date">
                            <strong>신청기간</strong> <br>
                            @if($row->regist_limit_yn == 'N')
                                상시신청가능
                            @else
                                {{ $row->regist_sdate->format('Y-m-d') }} ~ {{ $row->regist_edate->format('Y-m-d') }}
                            @endif
                        </p>

                        @if(thisUser())
                        <div class="btn-wrap">
                            <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($row->getHeart($row->sid) ) ? 'on': '' }}" data-esid="{{ $row->sid }}">관심교육</a>
                            <a href="{{ route('education.detail',['esid'=>$row->sid]) }}" class="btn btn-small color-type3">교육보기</a>
                        </div>
                        @endif
                    </a>
                </div>
                @empty
                <div class="edu-board-con no-data">
                    <img src="/assets/image/sub/img_nodata.png" alt="">
                    <p>검색된 결과가 없습니다.</p>
                </div>
                @endforelse
            </div>

            <div class="sub-tit-wrap n-bd">
                <h3 class="sub-tit">학술자료실 ({{ $workshop_cnt ?? 0 }}건)</h3>
                <a href="{{ route('workshop',['search_key'=>request()->search_key ?? '']) }}" class="btn btn-more">더보기</a>
            </div>

            <ul class="board-list">
                <li class="list-head">
                    <div class="bbs-tit bbs-col-m n-bar">행사명</div>
                    <div class="bbs-col-s n-bar">행사일</div>
                    <div class="bbs-info n-bar">세부정보</div>
                </li>
                @forelse($workshop_list as $row)
                <li>
                    <div class="bbs-tit bbs-col-m text-left n-bar">
                        <a href="{{ route('workshop.detail',['wsid'=>$row->sid]) }}">{{ $row->title ?? '' }}</a>
                    </div>
                    <div class="bbs-col-s n-bar">
                        @if($row->date_type =='D')
                            {{ $row->sdate->format('Y-m-d') }}
                        @else
                            {{ $row->sdate->format('Y-m-d') }} ~ {{ $row->edate->format('Y-m-d') }}
                        @endif
                    </div>
                    <div class="bbs-info text-left n-bar">
                        @foreach($row->sub_session as $key => $val)
                            @php
                                $field_arr = [];
                                foreach($workshopConfig['field'] as $fkey => $fval){
                                    if(in_array($fkey, $val->field ?? []) ) {
                                        $field_arr[] = $fval;
                                    }
                                }
                            @endphp
                        <div class="bbs-info-wrap">
                            <a href="#n">
                                <span class="cate">[ {{ implode(',',$field_arr) }} ]</span> {{ $val->title ?? '' }}
                                <p>
                                    {{ $val->pname ?? '' }} ({{ $val->psosok ?? '' }})
                                </p>
                            </a>
                            <div class="file">
                                @if(!empty($val->video_link))
                                    @if(thisLevel()=='M' || in_array(thisLevel(),$val->workshop->limit_level) !== false)
                                        <a href="{{ route('workshop.popup',['sid'=>$val->sid,'wsid'=>$val->wsid]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-popup" data-width="850" data-height="900">
                                            <img src="/assets/image/sub/ic_video.png" alt="">
                                        </a>
                                    @else
                                        <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" class="btn btn-small btn-type1 color-type20 ">
                                            <img src="/assets/image/sub/ic_video.png" alt="">
                                        </a>
                                    @endif
                                @endif

                                    @if(!empty($val->realfile))
                                        @if(thisLevel()=='M' || in_array(thisLevel(),$val->workshop->limit_level) !== false)
                                            <a href="{{ $val->downloadUrl() }}" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                        @else
                                            <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                        @endif
                                    @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </li>

{{--                <li>--}}
{{--                    <div class="bbs-tit text-left bbs-col-m n-bar">--}}
{{--                        <a href="#n">제66회 대한소화기내시경학회 추계학술대회</a>--}}
{{--                    </div>--}}
{{--                    <div class="bbs-col-s n-bar">--}}
{{--                        2016-11-26--}}
{{--                    </div>--}}
{{--                    <div class="bbs-info text-left n-bar">--}}
{{--                        <div class="bbs-info-wrap">--}}
{{--                            <a href="#n">--}}
{{--                                <span class="cate">[임상화학]</span> 자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명자료명--}}
{{--                                <p>--}}
{{--                                    발표자이름 (소속)--}}
{{--                                </p>--}}
{{--                            </a>--}}
{{--                            <div class="file">--}}
{{--                                <a href="#n" target="_blank" title="동영상 보기"><img src="/assets/image/sub/ic_video.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_excel.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_word.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_hwp.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_png.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_jpg.png" alt=""></a>--}}
{{--                                <a href="#n" download title="파일 다운로드"><img src="/assets/image/sub/ic_gif.png" alt=""></a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </li>--}}
                @empty
                <li class="no-data">
                    <img src="/assets/image/sub/img_nodata.png" alt="">
                    <p>검색된 결과가 없습니다.</p>
                </li>
                @endforelse
            </ul>
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('education.data') }}';

        // const getPK = (_this) => {
        //     return $(_this).closest('tr').data('sid');
        // }

        $(document).on('click', '.change-heart', function() {
            const ajaxData = {
                // 'sid': getPK(this),
                'esid': $(this).data('esid'),
                'case': 'change-heart',
                'target': $(this).hasClass('on') ? 'Y':'N',
            };

            callAjax(dataUrl, ajaxData);
            // if (confirm('노출여부를 변경 하시겠습니까?')) {
            //     callAjax(dataUrl, ajaxData);
            // }else{
            //     window.reload();
            // }
        });
    </script>
@endsection
