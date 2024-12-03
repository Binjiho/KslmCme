@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">학술자료실</h2>
            <p>
                학술자료실을 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>학술자료실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <div class="ev-view-con">
                    <div class="img-wrap">
                    @if($workshop->realfile)
                        <img src="{{ $workshop->realfile }}" alt="세미나">
                    @else
                        <img src="{{ asset('/assets/image/common/thumb_default.jpg') }}" alt="">
                    @endif
                    </div>
                <div class="text-wrap">
                    <span class="cate {{ ($workshop->category ?? '') == 'Z' ? 'etc' : ''}}">{{ $workshopConfig['category'][$workshop->category] ?? '' }}</span>
                    <p class="tit">{{ $workshop->title ?? '' }}</p>
                    <p class="date">
                        {{ $workshop->sdate->format('Y-m-d') }} {{ $workshop->edate && isValidTimestamp($workshop->edate) ? ' ~ '.$workshop->edate->format('Y-m-d') : '' }} <br>
                        {{ $workshop->place ?? '' }}
                    </p>

                    <div class="btn-wrap">
                        @if($workshop->abs_realfile)
                        <a href="{{ $workshop->downloadUrl('abs') }}" class="btn btn-small2 color-type3">초록집</a>
                        @endif
                        @if($workshop->book_realfile)
                        <a href="{{ $workshop->downloadUrl('book') }}" class="btn btn-small2 color-type1">프로그램북</a>
                        @endif
                        @if($workshop->book_realfile2)
                            <a href="{{ $workshop->downloadUrl('book2') }}" class="btn btn-small2 color-type1">프로그램북2</a>
                        @endif
                    </div>

                    <!-- 관심자료 선택 시 class="on" 추가 -->
                    <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($workshop->getHeart($workshop->sid) ) ? 'on': '' }}" data-wsid="{{ $workshop->sid }}">관심자료</a>
                </div>
            </div>

            <div class="sch-form-wrap">
                <form id="searchF" name="searchF" action="{{ route('workshop.detail',['wsid'=>request()->wsid]) }}" class="sch-form-wrap">
                    <input type="hidden" name="wsid" value="{{ request()->wsid ?? 0 }}">
                    <input type="hidden" name="date_tab" value="{{ request()->date_tab ?? 0 }}">
                    <?/* 241119
                    <input type="hidden" name="room_tab" value="{{ request()->room_tab ?? 0 }}">
                    */?>
                    <fieldset>
                        <legend class="hide">통합검색</legend>

						 <ul class="write-wrap">
							<li>						
								<div class="form-tit">자료분야</div>
								<div class="form-con">
									<div class="checkbox-wrap type2">
										@foreach($workshopConfig['field'] as $key => $val)
											<label for="chk3_{{$key}}" class="checkbox-group"><input type="checkbox" name="field[]" id="chk3_{{$key}}" value="{{ $key }}" {{ in_array($key , request()->field ?? []) ? 'checked' : '' }}>{{ $val }}</label>
										@endforeach
									</div>
								</div>
							</li>
							<li>						
								<div class="form-tit">검색</div>
								<div class="form-con">
									<div class="sch-wrap">
										<div class="form-group">
											<input type="text" name="search_key" id="search_key" class="form-item sch-key" placeholder="검색어 입력 (행사명, 강의명, 초록명, 저자, 소속 등)" value="{{ request()->search_key ?? '' }}">
											<button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
										</div>
									</div>
								</div>
							</li>
							<li>						
								<div class="form-tit">결과 내 재검색</div>
								<div class="form-con">
									<div class="sch-wrap">
										<div class="form-group">
											<input type="text" name="search_key2" id="search_key2" class="form-item sch-key" placeholder="결과 내 재검색 할 키워드 입력 " value="{{ request()->search_key2 ?? '' }}">
											<button type="submit" class="btn btn-sch"><span class="hide">검색</span></button>
										</div>
									</div>
								</div>
							</li>
						</ul>
						<div class="sch-wrap">
							<div class="form-group">
							<a href="{{ route('workshop.detail',['wsid'=>request()->wsid]) }}" class="btn btn-reset-big"><img src="/assets/image/icon/ic_reset.png" alt="">검색초기화</a>
							</div>
						</div>
                    </fieldset>
                </form>
            </div>

            @if(count($workshop->date) > 1)
                <div class="sub-tab-wrap type1">
                    <ul class="sub-tab-menu">
                        <li class="{{ (request()->date_tab ?? 'ALL') == 'ALL' ? 'on':'' }}">
                            <a href="javascript:;" data-type='date' data-tab=ALL class="tab-btn">전체</a>
                        </li>
                        @foreach($workshop->date as $key => $val)
                            <li class="{{ (request()->date_tab ?? 'ALL') == (string)$key ? 'on':'' }}">
                                <a href="javascript:;" data-type='date' data-tab={{ $key }} class="tab-btn">{{ $val }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <?/* 241119
            <div class="sub-tab-wrap type2">
                <ul class="sub-tab-menu">
                    @foreach($workshop->room as $key => $val)
                        <li class="{{ (request()->room_tab ?? 'NULL') == $key ? 'on':'' }}">
                            <a href="javascript:;" data-type='room' data-tab={{ $key }} class="tab-btn">{{ $val }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            */?>


            <div class="session-con">

                    <br>
{{--                    <p class="tit">{{ $workshop->room[$room_key] ?? '' }}</p>--}}

                    @foreach($session_arr as $session_key => $sub_session_item)
                    <div class="session-tit">
                        <strong>{{ $sub_session_item[0]->session->title ?? '' }}</strong>
                        @if(!empty($sub_session_item[0]->session->chair))
                        <p class="full-right">좌장: {{ $sub_session_item[0]->session->chair ?? '' }}</p>
                        @endif
                    </div>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <caption class="hide">세션</caption>
                            <colgroup>
                                <col>
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                                <col style="width: 10%;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th scope="col">
                                    제목 <br>발표자
                                </th>
                                <th scope="col">영상</th>
                                <th scope="col">파일</th>
                                <th scope="col">초록</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($sub_session_item as $key => $val)
                                @php
                                    $field_arr=array();
                                    foreach($workshopConfig['field'] as $field_key => $field_val){
                                        if(in_array($field_key, $val->field ?? []) ) {
                                            $field_arr[] = $field_val;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-left">
                                        <strong class="tit"><span class="text-pink2">[{{ implode(',',$field_arr) }}]</span> {{ $val->title ?? '' }}</strong>
                                        <p class="text-grey">
                                            {{ $val->pname ?? '' }}({{ $val->psosok ?? '' }})
                                        </p>
                                    </td>
                                    <td>
                                        @if(!empty($val->video_link))
                                            @if(thisLevel()=='M' || in_array(thisLevel(),$val->workshop->limit_level) !== false)
                                                <a href="{{ route('workshop.popup',['sid'=>$val->sid,'wsid'=>request()->wsid]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-popup" data-width="850" data-height="900">
                                                    <img src="/assets/image/sub/ic_video.png" alt="">
                                                </a>
                                            @else
                                                <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" class="btn btn-small btn-type1 color-type20 ">
                                                    <img src="/assets/image/sub/ic_video.png" alt="">
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($val->realfile))
                                            @if(thisLevel()=='M' || in_array(thisLevel(),$val->workshop->limit_level) !== false)
                                                <a href="{{ $val->downloadUrl() }}" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                            @else
                                                <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($val->abs_realfile))
                                            @if(thisLevel()=='M' || in_array(thisLevel(),$val->workshop->limit_level) !== false)
                                                <a href="{{ $val->downloadUrl('abs') }}" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                            @else
                                                <a href="javascript:alert('해당 자료의 열람 권한이 없습니다.');" download title="파일 다운로드"><img src="/assets/image/sub/ic_pdf.png" alt=""></a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const form = '#searchF';
        const dataUrl = '{{ route('workshop.data') }}';

        @php
            if( $workshop->hide=='Y' || $workshop->del== 'Y') {
                if(!isAdmin()){
        @endphp
                location.href="/";
        @php
                }
            }
        @endphp

$(document).on('click', '.tab-btn', function(){
    if($(this).data('type') == 'date'){
        $("input[name='date_tab']").val($(this).data('tab'));
    }
    // else if ($(this).data('type') == 'room'){
    //     $("input[name='room_tab']").val($(this).data('tab'));
    // }
    // 폼을 제출
    $(form).submit();
});

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
