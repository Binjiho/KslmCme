@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        @include('admin.layouts.include.sub-tit')

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('workshop.log') }}" class="sch-form-wrap">
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
                                <th scope="row">자료구분</th>
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
                                <th scope="row">열람기간</th>
                                <td class="text-left">
                                    <input type="text" name="sdate" value="{{ request()->sdate ?? '' }}" class="form-item" readonly datepicker style="width: 46%">
                                    <span>~</span>
                                    <input type="text" name="edate" value="{{ request()->edate ?? '' }}" class="form-item" readonly datepicker style="width: 46%">
                                </td>

                                <th scope="row">열람구분</th>
                                <td class="text-left">
                                    @foreach($workshopConfig['log_type'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="log_type" id="log_type_{{ $key }}" value="{{ $key }}" {{ (request()->log_type ?? '') == $key ? 'checked' : '' }}>
                                            <label for="log_type_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">자료분야</th>
                                <td colspan="3" class="text-left">
                                    @foreach($workshopConfig['field'] as $key => $val)
                                        <div class="checkbox-group">
                                            <input type="checkbox" name="field[]" id="field_{{ $key }}" value="{{ $key }}" {{ in_array($key, request()->field ?? [] ) !== false ? 'checked' : '' }}>
                                            <label for="field_{{ $key }}">{{ $val }}</label>
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
                                            <option value="title" {{ (request()->search ?? '') == 'title' ? 'selected':'' }}>행사명</option>
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
                        <a href="{{ route('workshop.log') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('workshop.log.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 8%;">
                        <col style="width: 7%;">
                        <col>
                        <col style="width: 15%;">

                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 9%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">자료구분</th>
                        <th scope="col">학술대회구분</th>
                        <th scope="col">행사명</th>
                        <th scope="col">자료분야</th>

                        <th scope="col">자료명</th>
                        <th scope="col">발표자</th>
                        <th scope="col">열람자</th>
                        <th scope="col">열람구분</th>
                        <th scope="col">열람일</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $workshopConfig['category'][($row->workshop->category ?? 'N')] ?? '' }}</td>
                            <td>{{ $workshopConfig['gubun'][($row->workshop->gubun ?? 'N')] ?? '' }}</td>
                            <td>{{ $row->workshop->title ?? '' }}</td>
                            <td>
                                @php
                                    $field_arr=array();
                                    foreach($workshopConfig['field'] as $field_key => $field_val){
                                        if(in_array($field_key, $row->sub->field ?? []) ) {
                                            $field_arr[] = $field_val;
                                        }
                                    }
                                @endphp
                                {{ implode(',',$field_arr) }}
                            </td>

                            <td>{{ $row->sub->title ?? '' }}</td>
                            <td>
                                {{ $row->sub->pname ?? '' }}
                                <br>({{ $row->sub->psosok ?? '' }})
                            </td>
                            <td>
                                {{ $row->user->name_kr ?? '' }}
                                <br>({{ $row->user->uid ?? '' }})
                            </td>
                            <td>{{ $workshopConfig['log_type'][($row->log_type ?? '')] ?? '' }}</td>
                            <td>{{ $row->updated_at ?? '' }}</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">자료열람 기록이 없습니다.</td>
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
