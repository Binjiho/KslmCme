@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">

        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $education->title ?? '' }}</h3>
        </div>

        <div class="btn-wrap">
            <a href="{{ route('education.sac',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type7">신청회원</a>
            <a href="{{ route('education.sac.deleted',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type4">취소신청/취소 완료</a>
        </div>

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('education.sac.deleted',['esid'=>request()->esid]) }}" class="sch-form-wrap">
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
                                <th scope="row">취소신청</th>
                                <td class="text-left">
                                    @foreach($sacConfig['del_request'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="del_request" id="del_request_{{ $key }}" value="{{ $key }}" {{ (request()->del_request ?? '') == $key ? 'checked' : '' }}>
                                            <label for="del_request_{{ $key }}">{{ $val }}</label>
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
                                            <option value="email" {{ request()->search == 'email' ? 'selected' : '' }}>이메일</option>
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
                        <a href="{{ route('education.sac.deleted',['esid'=>request()->esid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('education.sac.cancle_excel',['esid'=>request()->esid], request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">

                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">

                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 5%;">
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
                        <th scope="col">결제방식</th>
                        <th scope="col">결제일</th>

                        <th scope="col">취소신청일</th>
                        <th scope="col">취소상태</th>
                        <th scope="col">복원</th>
                    </tr>
                    </thead>

                    <tbody class="table_sortable">
                    @forelse($list as $idx => $row)
                        <tr data-sid="{{ $row->sid }}">
                            <input type="hidden" name='sid[]' value="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $row->user->uid ?? '' }}</td>
                            <td>{{ $row->user->name_kr ?? '' }}</td>
                            <td>{{ $row->user->sosok_kr ?? '' }}</td>
                            <td>{{ $row->user->email ?? '' }}</td>

                            <td>{{ $row->user->phone ?? '' }}</td>
                            <td>{{ $row->created_at->format('Y-m-d') ?? '' }}</td>
                            <td>{{ $row->tot_pay == 0 ? '무료' : number_format($row->tot_pay).'원' }}</td>
                            <td>
                                {{ $sacConfig['pay_status'][$row->pay_status] ?? '' }}
                            </td>
                            <td>
                                {{ $row->pay_at ? $row->pay_at->format('Y-m-d') : '' }}
                            </td>
                            <td>
                                {{ $row->del_request_at ? $row->del_request_at->format('Y-m-d') : '' }}
                                @if($row->pay_method == 'B' && $row->pay_status == 'C')
                                    <a href="{{ route('education.refundinfo', ['sid' => $row->sid]) }}" class="btn btn-small btn-type1 color-type20 call-popup" data-popup_name="refundinfo-pop" data-width="850" data-height="600">
                                        환불정보
                                    </a>
                                @endif
                            </td>
                            <td>
                                <select name="change_status">
                                    @foreach($sacConfig['del_request'] as $key => $val)
                                        <option value="{{ $key }}" {{ $key === $row->del_request ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="btn-admin restore">
                                    <img src="/assets/image/admin/restore.png" alt="복원" style="width: 27px; height: 27px;">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">취소신청건이 없습니다.</td>
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

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'change-restore',
            };

            if (confirm('복원 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('change', "select[name='change_status']", function() {
            let ajaxData = {};
            ajaxData.case = 'change-cancle_status';
            ajaxData.sid = getPK($(this));
            ajaxData.target = $(this).val();

            if (confirm('변경 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

    </script>
@endsection
