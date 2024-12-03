@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        @include('admin.layouts.include.sub-tit')

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('education') }}" class="sch-form-wrap">
                <fieldset>
                    <legend class="hide">검색</legend>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <colgroup>
                                <col style="width: 15%;">
                                <col>
                                <col style="width: 15%;">
                                <col>
                            </colgroup>

                            <tbody>
                            <tr>
                                <th scope="row">교육유형</th>
                                <td class="text-left">
                                    @foreach($educationConfig['category'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" {{ (request()->category ?? '') == $key ? 'checked' : '' }}>
                                            <label for="category_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>

                                <th scope="row">학술대회구분</th>
                                <td class="text-left">
                                    @foreach($educationConfig['gubun'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ (request()->gubun ?? '') == $key ? 'checked' : '' }}>
                                            <label for="gubun_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">노출여부</th>
                                <td class="text-left">
                                    @foreach($educationConfig['hide'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="hide" id="hide_{{ $key }}" value="{{ $key }}" {{ (request()->hide ?? '') == $key ? 'checked' : '' }}>
                                            <label for="hide_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>

                                <th scope="row">교육명</th>
                                <td class="text-left">
                                    <input type="text" name="title" value="{{ request()->title ?? '' }}" class="form-item">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 color-type17">검색</button>
                        <a href="{{ route('education') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('education.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="text-right">
                <a href="{{ route('education.upsert') }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="education-upsert" data-width="850" data-height="900">
                    교육 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col>
                        <col style="width: 15%;">

                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 5%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">노출여부</th>
                        <th scope="col">교육유형</th>
                        <th scope="col"><a href="{{ route('education',['sort'=>'title','ord'=>( request()->ord == 'ASC' ? 'DESC' : 'ASC')]) }}">교육명▼▲</a></th>
                        <th scope="col">수강기간</th>

                        <th scope="col">등록일</th>
                        <th scope="col">신청자</th>
                        <th scope="col">교육정보</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>
                                <div class="radio-wrap">
                                    @foreach($educationConfig['hide'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio"  class="change-hide" id="hide_{{ $key }}_{{ $idx }}" value="{{ $key }}" {{ ($row->hide ?? '') == $key ? 'checked' : '' }} >
                                            <label for="hide_{{ $key }}_{{ $idx }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $educationConfig['category'][($row->category ?? 'N')] ?? '' }}</td>
                            <td>{{ $row->title ?? '' }}</td>
                            <td>{{ $row->edu_sdate->format('Y-m-d') ?? '' }} - {{ ($row->edu_limit_yn ?? '') == 'N' ? '기한없음' : ($row->edu_edate->format('Y-m-d') ?? '') }}</td>

                            <td>{{ $row->created_at->format('Y-m-d') ?? '' }}</td>
                            <td>
                                {{ $row->sac_cnt() ?? 0 }} 명
                                <a href="{{ route('education.sac',['esid'=>$row->sid]) }}" class="btn btn-small btn-type1 color-type20">
                                    신청내역
                                </a>
                                <a href="{{ route('education.view',['esid'=>$row->sid]) }}" class="btn btn-small btn-type1 color-type20" >
                                    수강기록
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('education.lecture',['esid'=>$row->sid]) }}" class="btn btn-small btn-type1 color-type20 ">
                                    강의등록
                                </a>

                                @if(($row->quiz_yn ?? '') == 'N')
                                    <br>퀴즈등록 N
                                @else
                                    <a href="{{ route('education.quiz',['esid'=>$row->sid]) }}" class="btn btn-small btn-type1 color-type20" >
                                        퀴즈등록
                                    </a>
                                @endif

                                @if(($row->survey_yn ?? '') == 'N')
                                    <br>설문등록 N
                                @else
                                    <a href="{{ route('education.survey',['esid'=>$row->sid]) }}" class="btn btn-small btn-type1 color-type20" >
                                        설문등록
                                    </a>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('education.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-popup_name="education-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">등록된 교육이 없습니다.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $list->links('pagination::custom') }}
        </div>
    </section>
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
                'case': 'education-delete',
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
    </script>
@endsection
