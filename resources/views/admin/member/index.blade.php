@extends('admin.layouts.admin-layout')

@section('addStyle')
@endsection

@section('contents')
    <section id="container" class="inner-layer">
        @include('admin.layouts.include.sub-tit')

        <div class="sub-contents">
            <form id="searchF" name="searchF" action="{{ route('member') }}" class="sch-form-wrap">
                <fieldset>
                    <legend class="hide">검색</legend>
                    <div class="table-wrap">
                        <table class="cst-table">
                            <colgroup>
                                <col style="width: 20%;">
                                <col>
                            </colgroup>

                            <tbody>
                            <tr>
                                <th scope="row">회원구분</th>
                                <td class="text-left">
                                    @foreach($userConfig['level'] as $key => $val)
                                        <div class="radio-group">
                                            <input type="radio" name="level" id="level_{{ $key }}" value="{{ $key }}" {{ (request()->level ?? '') == $key ? 'checked' : '' }}>
                                            <label for="level_{{ $key }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">상세검색</th>
                                <td class="text-left" style="display: flex;">
                                    <select name="search_type" style="width: 33%;height: auto;">
                                        <option value="">선택</option>
                                        <option value="name_kr">이름</option>
                                        <option value="uid">아이디</option>
                                        <option value="email">이메일</option>
                                        <option value="sosok_kr">소속</option>
                                    </select>

                                    <input type="text" name="search_target" value="{{ request()->search_target ?? '' }}" class="form-item">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-wrap text-center">
                        <button type="submit" class="btn btn-type1 color-type17">검색</button>
                        <a href="{{ route('member') }}" class="btn btn-type1 color-type18">검색 초기화</a>
                        <a href="{{ route('member.excel', request()->except(['page'])) }}" class="btn btn-type1 color-type19">데이터 백업</a>
                    </div>
                </fieldset>
            </form>

            <div class="table-wrap" style="margin-top: 10px;">
                <table class="cst-table list-table">
                    <caption class="hide">목록</caption>

                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 8%;">
                        <col style="width: 12%;">
                        <col style="width: 8%;">
                        <col style="width: 6%;">

                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col style="width: 8%;">
                        <col>
                        <col style="width: 8%;">

                        <col style="width: 8%;">
                    </colgroup>

                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">회원구분</th>
                        <th scope="col">ID</th>
                        <th scope="col">이름</th>
                        <th scope="col">근무처</th>

                        <th scope="col">근무처주소</th>
                        <th scope="col">면허번호</th>
                        <th scope="col">연락처</th>
                        <th scope="col">이메일</th>
                        <th scope="col">수강내역</th>

                        <th scope="col">로그인</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($list as $row)
                        <tr data-sid="{{ $row->sid }}">
                            <td>{{ $row->seq }}</td>
                            <td>{{ $userConfig['level'][$row->level] ?? '' }}</td>
                            <td>{{ $row->uid ?? '' }}</td>
                            <td>{{ $row->name_kr ?? '' }}</td>
                            <td>{{ $row->sosok_kr ?? '' }}</td>

                            <td>{{ $row->office_addr1 ?? '' }} {{ $row->office_addr2 ?? '' }}</td>
                            <td>{{ $row->license_number ?? '' }}</td>
                            <td>{{ $row->phone ?? '' }}</td>
                            <td>{{ $row->email ?? '' }}</td>
                            <td><a href="{{ route('mail.detail', ['sid' => $row->sid]) }}" class="btn btn-small color-type10" style="margin-top: 5px;">확인</a></td>

                            <td><a href="javascript:;" class="btn btn-small color-type11 member-login" style="margin-top: 5px;">로그인</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">등록된 회원이 없습니다.</td>
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
        const dataUrl = '{{ route('member.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.member-login', function() {
            callAjax(dataUrl, {
                'case': 'member-login',
                'sid': $(this).closest('tr').data('sid'),
            })
        });
    </script>
@endsection
