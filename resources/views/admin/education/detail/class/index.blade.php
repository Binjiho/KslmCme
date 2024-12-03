@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="btn-wrap">
            <a href="{{ route('education.sac',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type4">신청회원</a>
            <a href="{{ route('education.sac.deleted',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type7">취소신청/취소 완료</a>
        </div>

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('member') }}" class="sch-form-wrap">
                <fieldset>
                    <legend class="hide">검색</legend>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <colgroup>
                                <col style="width: 15%;">
                                <col>
                            </colgroup>

                            <tbody>
                            <tr>
                                <th scope="row">교육유형</th>
                                <td class="text-left">
                                    @foreach($sacConfig['pay_status'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="pay_status" id="pay_status_{{ $key }}" value="{{ $key }}" {{ (request()->pay_status ?? '') == $key ? 'checked' : '' }}>
                                            <label for="pay_status_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">상세검색</th>
                                <td class="text-left">
                                    <div style="display: flex;">
                                        <select name="search_type" style="width: 33%;">
                                            <option value="">선택</option>
                                            <option value="uid">아이디</option>
                                            <option value="name_kr">이름</option>
                                            <option value="sosok_kr">소속</option>
                                            <option value="email">이메일</option>
                                        </select>

                                        <input type="text" name="search_target" value="{{ request()->search_target ?? '' }}" class="form-item">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 color-type17">검색</button>
                        <a href="{{ route('education.sac',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('education.sac.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

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
                        <th scope="col">아이디</th>
                        <th scope="col">이름</th>
                        <th scope="col">소속</th>
                        <th scope="col">이메일</th>

                        <th scope="col">연락처</th>
                        <th scope="col">신청일</th>
                        <th scope="col">금액</th>
                        <th scope="col">결제상태</th>
                        <th scope="col">결제일</th>
                    </tr>
                    </thead>

                    <tbody class="table_sortable">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $row->quiz }}</td>
                            <td>총인원 명</td>
                            <td>ㄱ</td>
                            <td>ㄴ</td>

                            <td>ㄷ</td>
                            <td>ㄹ</td>
                            <td>ㅁ</td>
                            <td>
                                <a href="{{ route('education.quiz.graph', ['sid' => $row->sid, 'esid'=>request()->esid ?? 0]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="quiz-graph" data-width="850" data-height="900">
                                    확인
                                </a>
                            </td>
                            <td>결제일</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">등록된 신청자가 없습니다.</td>
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
