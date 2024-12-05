@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        @include('admin.layouts.include.sub-tit')

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('lecture') }}" class="sch-form-wrap">
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
                                <th scope="row">강의구분</th>
                                <td class="text-left">
                                    @foreach($lectureConfig['type'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="type" id="type_{{ $key }}" value="{{ $key }}" {{ (request()->type ?? '') == $key ? 'checked' : '' }}>
                                            <label for="type_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>

                                <th scope="row">강의분야</th>
                                <td class="text-left">
                                    @foreach($lectureConfig['field'] as $key => $val)
                                        <div class="checkbox-group">
                                            <input type="checkbox" name="field[]" id="field_{{ $key }}" value="{{ $key }}" {{ in_array($key, request()->field ?? [] ) !== false ? 'checked' : '' }}>
                                            <label for="field_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr >
                                <th scope="row" colspan="1">상세검색</th>
                                <td colspan="3" class="text-left">
                                    <div style="display: flex;">
                                        <select name="search" style="width: 33%;height: auto;">
                                            <option value="">선택</option>
                                            <option value="title" {{ request()->search == 'title' ? 'selected' : '' }}>강의명</option>
                                            <option value="name_kr" {{ request()->search == 'name_kr' ? 'selected' : '' }}>강사명</option>
                                            <option value="sosok_kr" {{ request()->search == 'sosok_kr' ? 'selected' : '' }}>강사소속</option>
                                            <option value="filename1" {{ request()->search == 'filename1' ? 'selected' : '' }}>강의자료</option>
                                        </select>

                                        <input type="text" name="keyword" value="{{ request()->keyword ?? '' }}" class="form-item">
                                    </div>
                                </td>
                            </tr >
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 color-type17">검색</button>
                        <a href="{{ route('lecture') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('lecture.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="text-right">
                <a href="{{ route('lecture.upsert') }}" class="btn btn-type1 color-type20 call-popup" data-popup_name="lecture-upsert" data-width="850" data-height="900">
                    강의 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 5%;">
                        <col style="width: 10%;">
                        <col>
                        <col style="width: 15%;">

                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 7%;">
                        <col style="width: 8%;">
                        <col style="width: 5%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">강의구분</th>
                        <th scope="col">강의분야</th>
                        <th scope="col">강의명</th>
                        <th scope="col">강사명<br>(강사소속)</th>

                        <th scope="col">강의 시간</th>
                        <th scope="col">강의 파일</th>
                        <th scope="col">등록교육</th>
                        <th scope="col">등록일</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $idx => $row)

                        <tr data-sid="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $lectureConfig['type'][($row->type ?? '')] ?? '' }}</td>
                            <td>
                                @php
                                    $field_arr = array();
                                    foreach($lectureConfig['field'] as $field_key => $field_val){
                                        if(in_array($field_key, $row->field ?? []) ) {
                                            $field_arr[] = $field_val;
                                        }
                                    }
                                @endphp
                                {{ implode(', ',$field_arr) }}
                            </td>
                            <td class="text-left">{{ $row->title ?? '' }}</td>
                            <td>{{ $row->name_kr ?? '' }}<br>({{ $row->sosok_kr ?? '' }})</td>
						 
							<td>
								@if(($row->type ?? '')=='V')
                                {{ $row->lecture_time ?? '' }}
								@else
								-
								@endif 
							</td>
							<td>
								@if(($row->type ?? '')!='V')
                                <a href="{{ $row->downloadUrl() }}" class="btn btn-small btn-type1 color-type15" >
                                   자료 다운로드
                                </a>
								@else
								-
								@endif 
							</td>
                                                   

                            <td>
                                <a href="{{ route('lecture.view', ['lsid' => $row->sid]) }}" class="btn btn-small btn-type1 color-type13 call-popup" data-popup_name="lecture-view" data-width="850" data-height="600">
                                    확인
                                </a>
                            </td>
                            <td>{{ $row->created_at->format('Y-m-d') ?? '' }}</td>

                            <td>
                                <a href="{{ route('lecture.upsert', ['sid' => $row->sid]) }}" class="btn-admin call-popup" data-popup_name="lecture-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">등록된 강의가 없습니다.</td>
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
        const dataUrl = '{{ route('lecture.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'lecture-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        // $(document).on('click', '.change-hide', function() {
        //     const ajaxData = {
        //         'sid': getPK(this),
        //         'case': 'change-hide',
        //         'target': $(this).val(),
        //     };
        //
        //     if (confirm('노출여부를 변경 하시겠습니까?')) {
        //         callAjax(dataUrl, ajaxData);
        //     }else{
        //         window.reload();
        //     }
        // });
    </script>
@endsection
