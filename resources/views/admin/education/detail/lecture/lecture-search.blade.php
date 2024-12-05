@extends('admin.layouts.popup-layout')

@section('addStyle')

@endsection

@section('contents')
<div style="padding:25px;">
    <form id="searchF" action="{{ route('education.lecture.upsert',['esid'=>request()->esid ?? 0]) }}" data-case="lecture-search" data-esid="{{ request()->esid ?? 0 }}">
        <fieldset>
            <legend class="hide">검색</legend>
            <div class="table-wrap">
                <table class="cst-table">
                    <tbody>
                    <tr>
                        <td class="text-left" style="display: flex;">
                            <select name="type" style="width: 15%;">
                                <option value="">강의Type</option>
                                @foreach($lectureConfig['type'] as $key => $val)
                                    <option value="{{ $key }}" {{ request()->type == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>

                            <select name="field" style="width: 15%;">
                                <option value="">강의분야</option>
                                @foreach($lectureConfig['field'] as $key => $val)
                                    <option value="{{ $key }}" {{ request()->field == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>

                            <select name="search_type" style="width: 15%;">
                                <option value="title" {{ request()->search_type == 'title' ? 'selected' : '' }}>강의명</option>
                                <option value="name_kr" {{ request()->search_type == 'name_kr' ? 'selected' : '' }}>강사명</option>
                            </select>

                            <input type="text" name="search_target" style="width: 30%;" value="{{ request()->search_target ?? '' }}" class="form-item">

                            <button type="submit" class="btn btn-small color-type10">검색</button>

                            <a href="{{ route('education.lecture.upsert',['esid'=>request()->esid ?? 0]) }}" class="btn btn-small color-type18">검색 초기화</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </fieldset>
    </form>

    <div id="lecture-result">
        @include('admin.education.detail.lecture.lecture-result')
    </div>

    <div class="btn-wrap text-center">
        <a href="javascript:window.close();" class="btn btn-type1 color-type3">닫기</a>
    </div>
</div>
@endsection

@section('addScript')
    <script>
        const form = '#searchF';
        const dataUrl = '{{ route('education.lecture.data') }}';
        const esid = $(form).data('esid');

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.lecture-create', function() {
            const ajaxData = {
                'lsid': getPK(this),
                'esid': esid,
                'case': 'lecture-create',
            };

            if (confirm('강의를 등록 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }else{
                window.reload();
            }
        });
    </script>
@endsection