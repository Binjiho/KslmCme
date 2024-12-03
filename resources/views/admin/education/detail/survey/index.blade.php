@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="sub-contents">
            <div class="text-right">
                <a href="{{ route('education.survey.upsert',['esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="education-survey-upsert" data-width="950" data-height="800">
                    설문 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 8%;">
                        <col>
                        <col style="width: 8%;">
                        <col style="width: 8%;">

                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">

                        <col style="width: 8%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">설문타입</th>
                        <th scope="col">질문</th>
                        <th scope="col">응답인원</th>
                        <th scope="col">ㄱ</th>

                        <th scope="col">ㄴ</th>
                        <th scope="col">ㄷ</th>
                        <th scope="col">ㄹ</th>
                        <th scope="col">ㅁ</th>
                        <th scope="col">통계</th>

                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody class="table_sortable">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $surveyConfig['gubun'][$row->gubun ?? ''] ?? '' }}</td>
                            <td>{{ $row->quiz }}</td>
                            <td>{{ $row->survey_view_cnt() ?? 0 }} 명</td>
                            <td>
                                @if($row->gubun == 'A' /*객관식*/)
                                    @if(!empty($row->quiz_item_1))
                                    {{ $row->survey_static(1,'cnt') }}<br>({{$row->survey_static(1,'percent')}}%)
                                   @endif
                                @endif
                            </td>
                            <td>
                                @if($row->gubun == 'A' /*객관식*/)
                                    @if(!empty($row->quiz_item_2))
                                    {{ $row->survey_static(2,'cnt') }}<br>({{$row->survey_static(2,'percent')}}%)
                                @endif
                                @endif
                            </td>

                            <td>
                                @if($row->gubun == 'A' /*객관식*/)
                                    @if(!empty($row->quiz_item_3))
                                    {{ $row->survey_static(3,'cnt') }}<br>({{$row->survey_static(3,'percent')}}%)
                                @endif
                                @endif
                            </td>
                            <td>
                                @if($row->gubun == 'A' /*객관식*/)
                                    @if(!empty($row->quiz_item_4))
                                    {{ $row->survey_static(4,'cnt') }}<br>({{$row->survey_static(4,'percent')}}%)
                                @endif
                                @endif
                            </td>
                            <td>
                                @if($row->gubun == 'A' /*객관식*/)
                                    @if(!empty($row->quiz_item_5))
                                    {{ $row->survey_static(5,'cnt') }}<br>({{$row->survey_static(5,'percent')}}%)
                                @endif
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('education.survey.graph', ['sid' => $row->sid, 'esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="survey-graph" data-width="850" data-height="900">
                                    확인
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('education.survey.upsert', ['sid' => $row->sid, 'esid'=>request()->esid ?? 0]) }}" class="btn-admin call-popup" data-popup_name="survey-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del" data-cnt="">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">등록된 설문이 없습니다.</td>
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
        const dataUrl = '{{ route('education.survey.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'education-delete',
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
