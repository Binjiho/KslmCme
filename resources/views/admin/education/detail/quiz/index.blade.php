@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="sub-contents">
            <b style="color: #9e0505;">※ 등록된 퀴즈 문제 중 랜덤으로 노출되며 최소 합격 기준 이상의 문제를 등록해야 합니다.</b>
            <h3>합격 기준 : {{ $education->pass_cnt ?? 0 }} / {{ $education->quiz_cnt ?? 0 }}</h3>
            <div class="text-right">
                <a href="{{ route('education.quiz.upsert',['esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="education-quiz-upsert" data-width="950" data-height="800">
                    퀴즈 등록
                </a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col>
                        <col style="width: 10%;">
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
                        <th scope="col">시험 문제</th>
                        <th scope="col">총인원</th>
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
                            <td>{{ $row->quiz }}</td>
                            <td>{{ $row->quiz_view_cnt() ?? 0 }} 명</td>
                            <td>
                                @if(!empty($row->quiz_item_1))
                                {{ $row->quiz_static(1,'cnt') }}<br>({{$row->quiz_static(1,'percent')}}%)
                                @endif
                            </td>
                            <td>
                                @if(!empty($row->quiz_item_2))
                                {{ $row->quiz_static(2,'cnt') }}<br>({{$row->quiz_static(2,'percent')}}%)
                                @endif
                            </td>

                            <td>
                                @if(!empty($row->quiz_item_3))
                                {{ $row->quiz_static(3,'cnt') }}<br>({{$row->quiz_static(3,'percent')}}%)
                                @endif
                            </td>
                            <td>
                                @if(!empty($row->quiz_item_4))
                                {{ $row->quiz_static(4,'cnt') }}<br>({{$row->quiz_static(4,'percent')}}%)
                                @endif
                            </td>
                            <td>
                                @if(!empty($row->quiz_item_5))
                                {{ $row->quiz_static(5,'cnt') }}<br>({{$row->quiz_static(5,'percent')}}%)
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('education.quiz.graph', ['sid' => $row->sid, 'esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="quiz-graph" data-width="850" data-height="900">
                                    확인
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('education.quiz.upsert', ['sid' => $row->sid, 'esid'=>request()->esid ?? 0]) }}" class="btn-admin call-popup" data-popup_name="quiz-upsert" data-width="850" data-height="900">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del" data-cnt="">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">등록된 퀴즈가 없습니다.</td>
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
        const dataUrl = '{{ route('education.quiz.data') }}';

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
