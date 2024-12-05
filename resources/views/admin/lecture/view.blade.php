@extends('admin.layouts.popup-layout')

@section('addStyle')

@endsection

@section('contents')
<div style="padding:25px;">
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">등록된 교육리스트</h3>
    </div>

        <div class="table-wrap" style="margin-top: 10px;">
            <table class="cst-table list-table">
                <caption class="hide">목록</caption>

                <colgroup>
                    <col style="width: 5%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                </colgroup>

                <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">교육구분</th>
                    <th scope="col">교육명</th>
                </tr>
                </thead>

                <tbody>
                @forelse($list ?? [] as $row)
                    <tr data-sid="{{ $row->sid }}">
                        <td>{{ $row->seq }}</td>
                        <td>{{ $educationConfig['category'][($row->edu->category ?? 'N')] ?? '' }}</td>
                        <td>{{ $row->edu->title ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">검색 결과가 없습니다.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
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