@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">온라인 강의</h2>
            <p>
                온라인 강의 수강 신청을 하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>온라인 강의</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <form id="searchF" name="searchF" action="{{ route('education') }}" class="sch-form-wrap">
                <fieldset>
                    <legend class="hide">검색</legend>
                    <ul class="write-wrap">
                        <li>
                            <div class="form-tit">유형</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($educationConfig['category'] as $key => $val)
                                        <label for="chk1_{{$key}}" class="checkbox-group"><input type="checkbox" name="category[]" id="chk1_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->category ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">학술대회 구분</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($educationConfig['gubun'] as $key => $val)
                                        <label for="chk2_{{$key}}" class="checkbox-group"><input type="checkbox" name="gubun[]" id="chk2_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->gubun ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">강의분야</div>
                            <div class="form-con">
                                <div class="checkbox-wrap type2">
                                    @foreach($lectureConfig['field'] as $key => $val)
                                        <label for="chk3_{{$key}}" class="checkbox-group"><input type="checkbox" name="field[]" id="chk3_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->field ?? []) ? 'checked' : '' }}>{{ $val }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-tit">상세검색</div>
                            <div class="form-con">
                                <div class="sch-wrap">
                                    <div class="form-group">
                                        <input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="원하시는 강의를 찾아보세요.(교육명, 강의명, 강사명, 소속 등)" value="{{ request()->search_key ?? '' }}">
                                        <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
{{--                                        <button type="reset" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</button>--}}
                                        <a href="{{ route('education') }}" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </fieldset>
            </form>
            <div class="edu-board-list sch-result-list cf">
                @forelse($list as $idx => $row)
                <div class="edu-board-con">
                    <a href="{{ route('education.detail',['esid'=>$row->sid]) }}">
                        <span class="cate {{ ($row->category ?? '') == 'Z' ? 'etc' : ''}}">{{ $educationConfig['category'][$row->category] ?? '' }} {{ ($education->category ?? '') == 'A' ? ' - '.$educationConfig['gubun'][$education->gubun ?? ''] : ''}}</span>
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
{{--                <div class="edu-board-con">--}}
{{--                    <a href="http://kslm.m2comm.co.kr/html/edu/view.html">--}}
{{--                        <span class="cate">학술대회</span>--}}
{{--                        <strong class="tit ellipsis2">교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다. 교육명 입니다.</strong>--}}
{{--                        <p class="date">--}}
{{--                            <strong>신청기간</strong> <br>--}}
{{--                            2024-01-01 ~ 2024-01-02--}}
{{--                        </p>--}}
{{--                    </a>--}}
{{--                </div>--}}
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
        const dataUrl = '{{ route('education.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'workshop-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('click', '.change-hide', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'change-hide',
                'target': $(this).val(),
            };

            if (confirm('노출여부를 변경 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }else{
                window.reload();
            }
        });

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
