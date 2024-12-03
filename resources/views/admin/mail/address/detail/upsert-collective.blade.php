@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/handsontable/css/handsontable.full.min.css') }}"/>
    <style>
        .handson-headers {
            font-size: 1.2rem;
            color: #ea3737;
        }
    </style>
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">명단 일괄 {{ empty($addressDetail->sid) ? '등록' : '수정' }}</h3>
    </div>

    <div style="font-weight: bold; padding-bottom: 15px;">
        <p style="font-size: 1.5rem; padding: 3px;">* 아래 형식에 맞게 엑셀내용을 복사하여 붙여넣기 해주시기 바랍니다. (아래 한칸은 예시입니다.)</p>
        <p style="font-size: 1.5rem; padding: 3px;">* 빨간색으로 표기된 부분은 필수 값이니 꼭 입력해 주세요.</p>
        <p style="font-size: 1.5rem; padding: 3px;">* 없는 항목의경우 빈셀로넣어서 아래의 내역은 모두 표시되어야 합니다.</p>
        <p style="font-size: 1.5rem; padding: 3px;">* 엑셀 데이터 작성 예) .</p>
    </div>

    <form id="collective-frm" method="post" action="{{ route('mail.address.data') }}" data-ma_sid="{{ request()->ma_sid }}" data-case="collective-create">
        <div style="width:100%;" >
            <div id="handsontable" class="hot handsontable htRowHeaders htColumnHeaders" ></div>
        </div>

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($addressDetail->sid) ? '등록' : '수정' }}</button>
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
        </div>
    </form>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/handsontable/js/handsontable.full.min.js') }}"></script>
    <script>
        const form = '#collective-frm';
        const dataUrl = $(form).attr('action');
        const rowHeader = "✚";
        const delimiter = '|::|';
        const hadsonHeaers = [
            '<b class="handson-headers">이름</b>',
            '<b class="handson-headers">이메일</b>',
            '<b class="handson-headers">휴대전화</b>',
            '<b class="handson-headers">근무처명</b>',
        ];

        const handson = new Handsontable(document.getElementById('handsontable'), {
            colHeaders: hadsonHeaers,
            colWidths: [200, 250,200,200],
            data: [{
                name: '홍길동',
                name1: 'hongildong@gie.or.kr',
                name2: '010-1111-1234',
                name3: '서울아산병원',
            }],
            licenseKey: 'non-commercial-and-evaluation',
            rowHeaders: "✚",
            contextMenu: true,
        });

        const exportPlugin = handson.getPlugin('exportFile');

        $(document).on('submit', form, function(e) {
            e.preventDefault();

            const resText = exportPlugin.exportAsString('csv', {
                exportHiddenRows: true,     // default false, exports the hidden rows
                exportHiddenColumns: true,  // default false, exports the hidden columns
                columnHeaders: false,        // default false, exports the column headers
                rowHeaders: true,           // default false, exports the row headers
                columnDelimiter: delimiter,       // default ',', the data delimiter
            });

            let obj = resText.split(rowHeader);
            obj.shift();

            let formData = [];
            let submitCheck = true;

            $.each(obj, function (key, data) {
                let excelData = data.split(delimiter);
                excelData.shift();

                excelData = {
                    name: excelData[0],
                    email: excelData[1],
                    mobile: excelData[2],
                    office: excelData[3],
                }

                if(isEmpty(excelData.name)) {
                    submitCheck = false;
                    alert('이름을 입력해주세요.');
                    return false;
                }

                if(isEmpty(excelData.email)) {
                    submitCheck = false;
                    alert('이메일을 입력해주세요.');
                    return false;
                }

                if(!emailCheck(excelData.email)) {
                    submitCheck = false;
                    alert('올바른 이메일 형식이 아닙니다.');
                    return false;
                }

                if(isEmpty(excelData.mobile)) {
                    submitCheck = false;
                    alert('휴대전화를 입력해주세요.');
                    return false;
                }

                if(isEmpty(excelData.office)) {
                    submitCheck = false;
                    alert('근무처명을 입력해주세요.');
                    return false;
                }

                formData.push(excelData)
            });

            if(submitCheck) {
                let ajaxData = newFormData(form);
                ajaxData.append('ma_sid', $(form).data('ma_sid'));
                ajaxData.append('data', JSON.stringify(formData));

                callMultiAjax(dataUrl, ajaxData);
            }
        });
    </script>
@endsection
