@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="sub-contents">
            <b style="color: #9e0505;">※ 강의 순서는 드래그하여 이동할 수 있습니다. 이미 진행 중인 교육의 강의 수정 시, 교육이 정상 이수 처리되지 않을 수 있습니다.</b>
            <div class="text-right">
                <a href="{{ route('education.lecture.upsert',['esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="education-lecture-upsert" data-width="950" data-height="800">
                    교육 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 10%;">
                        <col style="width: 15%;">
                        <col>
                        <col style="width: 10%;">

                        <col style="width: 10%;">
                        <col style="width: 8%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Type</th>
                        <th scope="col">강의분야</th>
                        <th scope="col">강의명</th>
                        <th scope="col">강사명</th>

                        <th scope="col">소속</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody class="table_sortable">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $lectureConfig['type'][($row->lec->type ?? '')] ?? '' }}</td>
                            <td>
                                @foreach($lectureConfig['field'] as $field_key => $field_val)
                                    @php
                                        if(in_array($field_key, $row->lec->field ?? []) ) {
                                            $field_arr[] = $field_val;
                                        }
                                    @endphp
                                @endforeach
                                {{ implode(', ',$field_arr) ?? '' }}
                            </td>
                            <td>{{ $row->lec->title ?? '' }}</td>
                            <td>{{ $row->lec->name_kr ?? '' }}</td>

                            <td>{{ $row->lec->sosok_kr ?? '' }}</td>
                            <td>
                                <a href="javascript:void(0);" class="btn-admin btn-del" data-cnt="">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">등록된 교육이 없습니다.</td>
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
        const dataUrl = '{{ route('education.lecture.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'lecture-delete',
            };

            if (confirm('강의 수강이 시작된 이후에는 강의 영상 삭제 시, 기 수강 회원의 이수 처리에 문제가 생길 수 있습니다. 그래도 삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(function() {
            /**
             * 순서 저장
             */
            $(".table_sortable").sortable({
                axis: "y",
                containment: "parent",
                update: function () {
                    BsJs_setOrd('edu_lec_list', 'sort');
                }
            }).disableSelection();
        });

        function BsJs_setOrd(targetDB, targetVAL) {
            if(!confirm("순서를 변경하시겠습니까?")){
                location.reload();
                return false;
            }

            // 순서대로 array_sid 가져와서 배열에 담기
            var array_sid = [];
            $("input[name='sid[]']").each(function() {
                array_sid.push($(this).val());
            });

            callAjax(dataUrl, {
                'case': 'change-sort',
                "array_sid": array_sid.join(','),
                "targetDB": targetDB,
                "targetVAL": targetVAL,
            });

        }
    </script>
@endsection
