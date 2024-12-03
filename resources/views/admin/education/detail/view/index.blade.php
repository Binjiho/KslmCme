@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('education.view',['esid'=>request()->esid]) }}" class="sch-form-wrap">
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
                                    @foreach($sacConfig['edu_status'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="edu_status" id="edu_status_{{ $key }}" value="{{ $key }}" {{ (request()->edu_status ?? '') == $key ? 'checked' : '' }}>
                                            <label for="edu_status_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">상세검색</th>
                                <td class="text-left">
                                    <div style="display: flex;">
                                        <select name="search" style="width: 33%;">
                                            <option value="">선택</option>
                                            <option value="uid" {{ request()->search == 'uid' ? 'selected' : '' }}>아이디</option>
                                            <option value="name_kr" {{ request()->search == 'name_kr' ? 'selected' : '' }}>이름</option>
                                            <option value="sosok_kr" {{ request()->search == 'sosok_kr' ? 'selected' : '' }}>소속</option>
{{--                                            <option value="email" {{ request()->search == 'email' ? 'selected' : '' }}>이메일</option>--}}
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
                        <a href="{{ route('education.view',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('education.view.excel',['esid'=>request()->esid], request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="table-wrap">
                <table class="cst-table">
                    <colgroup>
                        <col style="width: 15%;">
                        <col>
                        <col style="width: 15%;">
                        <col>
                        <col style="width: 15%;">
                        <col>
                    </colgroup>

                    <tbody>
                    <tr>
                        <th scope="row">신청인원</th>
                        <td class="text-center">
                            {{ $education->sac_cnt() ?? 0 }}명
                        </td>

                        <th scope="row">이수완료</th>
                        <td class="text-center" colspan="3">
                            {{ $education->sac_cnt('complete') ?? 0 }}명
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">강의 수강완료</th>
                        <td class="text-center">
                            {{ $education->sac_cnt('education') ?? 0 }}명
                        </td>
                        <th scope="row">퀴즈완료</th>
                        <td class="text-center">
                            {{ $education->sac_cnt('quiz') ?? 0 }}명
                        </td>
                        <th scope="row">설문완료</th>
                        <td class="text-center">
                            {{ $education->sac_cnt('survey') ?? 0 }}명
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-wrap" style="margin-top: 50px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col>
                        <col style="width: 10%;">
                        <col style="width: 10%;">
                        <col style="width: 15%;">

                        <col style="width: 10%;">
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
                        <th scope="col">수강 완료<br>강의 수</th>

                        <th scope="col">퀴즈</th>
                        <th scope="col">설문</th>
                        <th scope="col">수강시작</th>
                        <th scope="col">수강종료</th>
                        <th scope="col">수료상태</th>
                    </tr>
                    </thead>

                    <tbody class="table">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $row->user->uid ?? '' }}</td>
                            <td>{{ $row->user->name_kr ?? '' }}</td>
                            <td>{{ $row->user->sosok_kr ?? '' }}</td>
                            <td>{{ $row->getLectureCnt($row->user_sid,'complete') ?? 0 }} / {{ $row->lectures()->count() ?? 0 }}</td>

                            <td>{{ $row->edu->quiz_yn == 'N' ? '-' : ($row->quiz_status == 'C' ? '합격' : '불합격') }}</td>
                            <td>{{ $row->edu->survey_yn == 'N' ? '-' : ($row->survey_status == 'C' ? '완료' : '대기') }}</td>
                            <td>{{ !empty($row->edu_start_at()) ? $row->edu_start_at()->format('Y-m-d') : '' }}</td>
                            <td>{{ !empty($row->edu_at) ? $row->edu_at->format('Y-m-d') : '' }}</td>
                            <td>{{ $sacConfig['edu_status'][$row->edu_status ?? ''] ?? '' }}</td>
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
        const dataUrl = '{{ route('education.sac.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('change', "select[name='change_status']", function() {
            let ajaxData = {};
            ajaxData.case = 'change-status';
            ajaxData.sid = getPK($(this));
            ajaxData.target = $(this).val();

            if (confirm('변경 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
