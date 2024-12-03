@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        <div class="sub-tit-wrap">
            <h3 class="sub-tit">{{ $menu['main'][$main_key]['name'] }} - [ {{ $address->title }} ] 주소록 명단</h3>
        </div>

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('mail.address.detail', ['ma_sid' => $address->sid]) }}" class="sch-form-wrap">
                <input type="hidden" name="ma_sid" value="{{ $address->sid ?? 0 }}" readonly>
                <fieldset>
                    <legend class="hide">검색</legend>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <colgroup>
                                <col style="width: 30%;">
                                <col>
                            </colgroup>

                            <tbody>
                            <tr>
                                <th scope="row">상세검색</th>
                                <td class="text-left" colspan="3">
                                    <div style="display: flex;">
                                        <select name="search" style="width: 33%; height: auto;">
                                            <option value="">선택</option>
                                            <option value="name" {{ (request()->search ?? '') === 'name' ? 'selected' : '' }}>이름</option>
                                            <option value="email" {{ (request()->search ?? '') === 'email' ? 'selected' : '' }}>이메일</option>
                                            <option value="mobile" {{ (request()->search ?? '') === 'mobile' ? 'selected' : '' }}>휴대폰</option>
                                            <option value="office" {{ (request()->search ?? '') === 'office' ? 'selected' : '' }}>소속</option>
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
                        <a href="{{ route('mail.address.detail', ['ma_sid' => $address->sid]) }}" class="btn btn-type1 color-type18">검색 초기화</a>
                    </div>
                </fieldset>
            </form>

            <div class="text-right">
                <a href="javascript:;" class="btn btn-small btn-type1 color-type11 individual_delete">개별삭제</a>
                <a href="javascript:;" class="btn btn-small btn-type1 color-type11 all_delete">전체삭제</a>
                <a href="{{ route('mail.address') }}" class="btn btn-small btn-type1 color-type20">목록으로</a>
                <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'collective']) }}" class="btn btn-small btn-type1 color-type10 call-popup" data-popup_name="address-upsert-collective" data-width="900" data-height="700">일괄등록</a>
                <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'individual']) }}" class="btn btn-small btn-type1 color-type8 call-popup" data-popup_name="address-upsert-individual" data-width="700" data-height="500">직접등록</a>
            </div>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width:3%;">
                        <col style="width:6%;">
                        <col style="width:15%;">
                        <col>
                        <col style="width:15%;">

                        <col style="width:15%;">
                        <col style="width:7%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col"><input type="checkbox" name="" id="check_all" value=""></th>
                        <th scope="col">No</th>
                        <th scope="col">이름</th>
                        <th scope="col">이메일</th>
                        <th scope="col">휴대폰</th>

                        <th scope="col">소속</th>
                        <th scope="col">관리</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $row)
                        <tr data-sid="{{ $row->sid }}">
                            <td><input type="checkbox" name="sid[]" id="sid_{{$row->sid}}" value="{{ $row->sid }}"></td>
                            <td>{{ $row->seq }}</td>
                            <td>{{ $row->name ?? '' }}</td>
                            <td>{{ $row->email ?? '' }}</td>
                            <td>{{ $row->mobile ?? '' }}</td>

                            <td>{{ $row->office ?? '' }}</td>
                            <td>
                                <a href="{{ route('mail.address.detail.upsert', ['ma_sid' => $address->sid, 'type' => 'individual', 'sid' => $row->sid]) }}" class="btn-admin call-popup" data-popup_name="address-upsert-individual" data-width="700" data-height="400">
                                    <img src="/assets/image/admin/ic_modify.png" alt="수정">
                                </a>

                                <a href="javascript:void(0);" class="btn-admin btn-del">
                                    <img src="/assets/image/admin/ic_del.png" alt="삭제">
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">등록된 명단이 없습니다.</td>
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
        const dataUrl = '{{ route('mail.address.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'addressDetail-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('click', '#check_all', function() {
            const _checked = $("#check_all").is(":checked");
            if (_checked) {
                $("input[name='sid[]']").prop("checked",true);
            }else{
                $("input[name='sid[]']").prop("checked",false);
            }
        });

        $(document).on('click', '.individual_delete', function() {
            const _sid = $("input[name='sid[]']:checked").map(function() {
                return this.value;
            }).get();
            
            const ajaxData = {
                'sid': _sid,
                'case': 'individual-delete',
                'ma_sid': $("input[name='ma_sid']").val(),
            };

            if (confirm('개별삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('click', '.all_delete', function() {
            const ajaxData = {
                'ma_sid': $("input[name='ma_sid']").val(),
                'case': 'all-delete',
            };

            if (confirm('전체삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
