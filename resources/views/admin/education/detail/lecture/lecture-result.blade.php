<div class="table-wrap" style="margin-top: 10px;">
    <table class="cst-table list-table">
        <caption class="hide">목록</caption>

        <colgroup>
            <col style="width: 5%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">

            <col style="width: 10%;">
            <col style="width: 5%;">
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

        <tbody>
        @forelse($list ?? [] as $row)
            <tr data-sid="{{ $row->sid }}">
                <td>{{ $row->seq }}</td>
                <td>{{ $lectureConfig['type'][($row->type ?? 'N')] ?? '' }}</td>
                <td>
                    @foreach($lectureConfig['field'] as $field_key => $field_val)
                        @php
                            if(in_array($field_key, $row->field ?? []) ) {
                                $field_arr[] = $field_val;
                            }
                        @endphp
                    @endforeach
                    {{ implode(', ',$field_arr) }}
                </td>
                <td>{{ $row->title ?? '' }}</td>
                <td>{{ $row->name_kr ?? '' }}</td>

                <td>{{ $row->sosok_kr ?? '' }}</td>
                <td>
{{--                    <div class="util btn" style="display: flex; justify-content: center; margin-top: 20px;">--}}
{{--                        <input class="btnSmall btnDel select-member" type="button" value="선택">--}}
{{--                    </div>--}}
                    <a href="javascript:;" class="btn btn-small btn-type2 color-type20 lecture-create" >
                        등록
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">검색 결과가 없습니다.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>