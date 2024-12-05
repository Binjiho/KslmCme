@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $workshop->title ?? '' }}</h3>
        </div>

        <div class="sub-contents">

            <form id="searchF" name="searchF" action="{{ route('workshop.session',['wsid'=>request()->wsid ?? 0]) }}" class="sch-form-wrap">
                <input type="hidden" name="wsid" value="{{ request()->wsid ?? 0 }}">
                <input type="hidden" name="date_tab" value="{{ request()->date_tab ?? 'ALL' }}">
                <?/* 241119
                <input type="hidden" name="room_tab" value="{{ request()->room_tab ?? 0 }}">
                */?>
                <fieldset>
                    <legend class="hide">검색</legend>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <colgroup>
                                <col style="width: 20%;">
                                <col style="width: 80%;">
                            </colgroup>

                            <tbody>
                            <tr>
                                <th scope="row">자료분야</th>
                                <td class="text-left">
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
                                            <option value="title" {{ (request()->search ?? '') == 'title' ? 'selected' : '' }}>세션명</option>
                                            <option value="chair" {{ (request()->search ?? '') == 'chair' ? 'selected' : '' }}>좌장</option>
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
                        <a href="{{ route('workshop.session',['wsid'=>request()->wsid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('workshop.session.excel',['wsid'=>request()->wsid], request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>

                <div class="btn-wrap">
							<a href="javascript:;" data-type='date' data-tab=ALL class="tab-btn btn btn-type1 color-type{{ (request()->date_tab ?? 'ALL') == 'ALL' ? '24':'23' }}">ALL</a>
                    @foreach($workshop->date as $key => $val)
						@if($val == 'P')
							 <a href="javascript:;" data-type='date' data-tab={{ $key }} class="tab-btn btn btn-type1 color-type{{ (request()->date_tab ?? 'ALL') == (string)$key ? '24':'23' }}">Poster</a>
						@else	
							 <a href="javascript:;" data-type='date' data-tab={{ $key }} class="tab-btn btn btn-type1 color-type{{ (request()->date_tab ?? 'ALL') == (string)$key ? '24':'23' }}">{{ $val }}</a>
						@endif	
                    @endforeach
                </div>

                <?/* 241119
                <div class="btn-wrap">
                    @foreach($workshop->room as $key => $val)
                        <a href="javascript:;" data-type='room' data-tab={{ $key }} class="tab-btn btn btn-type1 color-type{{ (request()->room_tab ?? '') == $key ? '24':'23' }}">{{ $val }}</a>
                    @endforeach
                </div>
                */?>
            </form>

            <div class="text-right">
                <a href="{{ route('workshop.session.collective', ['wsid' => request()->wsid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="session-upsert" data-width="900" data-height="600">
                    세션정보 등록
                </a>

                <a href="{{ route('workshop.subsession.collective', ['wsid' => request()->wsid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-upsert" data-width="1200" data-height="600">
                    세부프로그램 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 5%;">
                        <col style="width: 10%;">
{{--                    <col style="width: 10%;">--}}
                        <col>

                        <col style="width: 15%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">세션번호</th>
                        <th scope="col">행사일</th>
{{--                    <th scope="col">ROOM</th>--}}
                        <th scope="col">세션명</th>

                        <th scope="col">좌장</th>
                        <th scope="col">세부프로그램</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody class="table_sortable">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $row->reg_num ?? '' }}</td>
                            <td>{{ $workshop->date[$row->date] ?? '' }}</td>
                            <?/* 241119
                            <td>{{ $workshop->room[$row->room] ?? '' }}</td>
                            */?>
                            <td class="text-left">{{ $row->title ?? '' }}</td>
                            <td>{{ $row->chair ?? '' }}</td>

                            <td>
                                <a href="{{ route('workshop.subsession', ['wsid'=>request()->wsid ?? 0,'reg_num'=>$row->reg_num ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="subsession-index" data-width="850" data-height="900">
                                    확인 ({{ $row->sub_session($row->wsid)->count() ?? 0 }})
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('workshop.session.upsert', ['sid'=>$row->sid ?? 0,'wsid'=>request()->wsid ?? 0]) }}" class="btn-admin call-popup" data-popup_name="session-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del" data-cnt="{{ $row->sub_session->count() ?? 0 }}">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">등록된 세션이 없습니다.</td>
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
        const dataUrl = '{{ route('workshop.session.data',['wsid'=>request()->wsid]) }}';
        const form = '#searchF';

        $(document).on('click', '.tab-btn', function(){
            if($(this).data('type') == 'date'){
                $("input[name='date_tab']").val($(this).data('tab'));
            }else if ($(this).data('type') == 'room'){
                $("input[name='room_tab']").val($(this).data('tab'));
            }
            // 폼을 제출
            $(form).submit();
        });


        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const del_cnt = $(this).data('cnt');
            if(del_cnt > 0){
                alert('상세자료가 있는 경우 삭제가 불가합니다.');
                return false;
            }
            const ajaxData = {
                'sid': getPK(this),
                'case': 'session-delete',
            };
            callAjax(dataUrl, ajaxData);
        });

        // $(function() {
        //     /**
        //      * 순서 저장
        //      */
        //     $(".table_sortable").sortable({
        //         axis: "y",
        //         containment: "parent",
        //         update: function () {
        //             BsJs_setOrd('sessions', 'sort');
        //         }
        //     }).disableSelection();
        // });
        //
        // function BsJs_setOrd(targetDB, targetVAL) {
        //     if(!confirm("순서를 변경하시겠습니까?")){
        //         location.reload();
        //         return false;
        //     }
        //
        //     // 순서대로 array_sid 가져와서 배열에 담기
        //     var array_sid = [];
        //     $("input[name='sid[]']").each(function() {
        //         array_sid.push($(this).val());
        //     });
        //
        //     callAjax(dataUrl, {
        //         'case': 'change-sort',
        //         "array_sid": array_sid.join(','),
        //         "targetDB": targetDB,
        //         "targetVAL": targetVAL,
        //     });
        //
        // }
    </script>
@endsection
