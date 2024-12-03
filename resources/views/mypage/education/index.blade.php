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

            <form id="searchF" name="searchF" action="{{ route('mypage.education') }}" class="sch-form-wrap">
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
                            <div class="form-tit">교육분야</div>
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
                                        <input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="원하시는 자료를 찾아보세요." value="{{ request()->search_key ?? '' }}">
                                        <button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
                                        {{--                                        <button type="reset" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</button>--}}
                                        <a href="{{ route('mypage.education') }}" class="btn btn-reset"><img src="/assets/image/icon/ic_reset.png" alt="">필터초기화</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </fieldset>
            </form>
            <div class="sch-result-list">
                <ul class="lecture-list">
                    @forelse($list as $idx => $row)
                        @continue(empty($row->edu)) {{-- education이 삭제된 경우 continue --}}
                    <li>
                        <div class="lecture-info">
                            <p class="tit">{{ $row->edu->title ?? '' }}</p>
                            <p class="date">
                                <strong>수강기간 : </strong> {{ !empty($row->edu->edu_sdate) ? $row->edu->edu_sdate->format('Y-m-d') : '' }} ~ {{ ($row->edu->edu_limit_yn ?? '') == 'N' ? '기한없음' : (!empty($row->edu->edu_edate) ? $row->edu->edu_edate->format('Y-m-d') : '') }}
                            </p>
                        </div>
                        <ul class="write-wrap">
                            <li>
                                <div class="form-tit">수강 상태</div>
                                <div class="form-con">
                                    {{ $sacConfig['edu_status'][$row->edu_status] ?? '' }}
                                </div>
                            </li>
                            <li>
                                <div class="form-tit">이수 강의</div>
                                <div class="form-con">
                                    {{ $row->getLectureCnt(thisPK(), 'complete') ?? 0 }} / {{ $row->lectures->count() ?? 0 }}
                                </div>
                            </li>
                            <li>
                                <div class="form-tit">퀴즈</div>
                                <div class="form-con">{{ $sacConfig['quiz_status'][$row->quiz_status] ?? '' }}</div>
                            </li>
                            <li>
                                <div class="form-tit">설문</div>
                                <div class="form-con">
                                    <span class="state {{ $sacConfig['survey_css'][$row->survey_status] ?? '' }}">{{ $sacConfig['survey_status'][$row->survey_status] ?? '' }}</span>
                                </div>
                            </li>
                        </ul>

                        <div class="btn-wrap text-center">
                            <a href="{{ route('mypage.education.detail',['ssid'=>$row->sid]) }}" class="btn btn-type1 color-type4"><img src="/assets/image/sub/ic_play.png" alt=""> 강의 시청</a>
                            @if(($row->edu->certi_yn ?? '') == 'Y' && ($row->complete_yn ?? '') == 'Y')
                            <a href="{{ route('mypage.certi.detail',['ssid'=>$row->sid]) }}" class="btn btn-type1 color-type5 call-popup" data-popup_name="certi-pop" data-width="850" data-height="850"><img src="/assets/image/sub/ic_print.png" alt=""> 이수증 출력</a>
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
@endsection
