@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        @include('admin.layouts.include.sub-tit')

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('workshop') }}" class="sch-form-wrap">
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
                                    @foreach($workshopConfig['category'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" {{ (request()->category ?? '') == $key ? 'checked' : '' }}>
                                            <label for="category_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>

                                <th scope="row">학술대회구분</th>
                                <td class="text-left">
                                    @foreach($workshopConfig['gubun'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ (request()->gubun ?? '') == $key ? 'checked' : '' }}>
                                            <label for="gubun_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">상세검색</th>
                                <td class="text-left" colspan="3">
                                    <div style="display: flex;">
                                        <select name="search" style="width: 33%;height: auto;">
                                            <option value="">선택</option>
                                            <option value="title" {{ (request()->search == 'title') ? 'selected' : '' }}>행사명</option>
                                        </select>

                                        <input type="text" name="keyword" value="{{ request()->keyword ?? '' }}" class="form-item">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 color-type17">검색</button>
                        <a href="{{ route('workshop') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('workshop.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="text-right">
                <a href="{{ route('workshop.upsert') }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="workshop-upsert" data-width="850" data-height="900">
                    행사 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 7%;">
                        <col style="width: 7%;">
                        <col>
                        <col style="width: 15%;">

                        <col style="width: 7%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 5%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">자료구분</th>
                        <th scope="col">학술대회<br>구분</th>
                        <th scope="col">행사명</th>
                        <th scope="col">행사일</th>

                        <th scope="col">상세자료</th>
                        <th scope="col">등록일</th>
                        <th scope="col">노출여부</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $workshopConfig['category'][($row->category ?? 'N')] ?? '' }}</td>
                            <td>{{ $workshopConfig['gubun'][($row->gubun ?? 'N')] ?? '' }}</td>
                            <td class="text-left">{{ $row->title ?? '' }}</td>
                            <td>
                                {{ $row->sdate->format('Y-m-d') ?? '' }} {{ ($row->date_type ?? '') == 'L' ? ' ~ '.$row->edate->format('Y-m-d') ?? '' : '' }}
                            </td>

                            <td>
                                <a href="{{ route('workshop.session',['wsid'=>$row->sid]) }}" class="btn btn-small color-type20 ">
                                   보기                               
								</a>
                            </td>
                            <td>{{ $row->created_at->format('Y-m-d') ?? '' }}</td>
                            <td>
                                <div class="radio-wrap">
                                    @foreach($workshopConfig['hide'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio"  class="change-hide" id="hide_{{ $key }}_{{ $idx }}" value="{{ $key }}" {{ ($row->hide ?? '') == $key ? 'checked' : '' }} >
                                            <label for="hide_{{ $key }}_{{ $idx }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('workshop.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-popup_name="workshop-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">등록된 자료가 없습니다.</td>
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
        const dataUrl = '{{ route('workshop.data') }}';

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
    </script>
@endsection
