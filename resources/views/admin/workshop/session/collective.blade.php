@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/handsontable/css/handsontable.full.min.css') }}"/>
@endsection

@section('contents')
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">세션 정보 엑셀 등록</h3>
    </div>

    <div class="notice" style="font-size: large; line-height: normal;">
        세션번호 : 숫자로 입력, 세션 순서에 따라 부여 (추후 세부 프로그램 등록시 연결할 구분 값)
        <br>
        행사일 : 해당 세션이 진행된 행사일 입력 (yyyy-mm-dd 형식으로 입력)
        <?/*
        <br>
        룸정보 : 행사 등록시 입력한 룸 명칭으로 입력
        */?>
        <br>
        세션명 및 좌장 : 텍스트로 입력
        <br>
        <p style="color: #9e0505">** 위의 설명을 참고하여 엑셀에서 작성한 내용을 복사 붙여넣기 하여 입력하여 주시기 바랍니다. (직접 입력도 가능)</p>
    </div>

    <div class="popup-conbox">
        <div class="write-form-wrap">
            <form method="post" action="{{ route('workshop.session.data', ['wsid' => request()->wsid]) }}" id="collective-upload" data-case="collective-create">
                <input type="hidden" name="w_sid" value="{{ request()->wsid }}">
                <div style="width:100%;" >
                    <div id="handsontable" class="hot handsontable htRowHeaders htColumnHeaders" ></div>
                </div>


                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type20" id="submit">등록</button>
                    <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/handsontable/js/handsontable.full.min.js') }}"></script>
    <script>
        const form = '#collective-upload';
        const rowHeader = "✚";
        const delimiter = '|::|';

        const handson = new Handsontable(document.getElementById('handsontable'), {
            colHeaders: ['세션번호','행사일' , '세션명', '좌장'],
            colWidths: [150,150, 150, 170],
            data: [{
                name: '1',
                name2: '2024-01-01',
                // name3: '진달래',
                name3: 'ABC',
                name4: 'CAN',
            }],
            licenseKey: 'non-commercial-and-evaluation',
            rowHeaders: "✚",
            contextMenu: true,
        });

        const exportPlugin = handson.getPlugin('exportFile');

        defaultVaildation();


        $(document).on('click', '#collective-upload button[type=submit]', function(e) {
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
            let ajaxData = new FormData();
            let submitCheck = true;

            $.each(obj, function (key, data) {
                let excelData = data.split(delimiter);
                excelData.shift();

                excelData = {
                    'reg_num': excelData[0],
                    'tmp_date': excelData[1],
                    // 'tmp_room': excelData[2],
                    'title': excelData[2],
                    'chair': excelData[3],
                }

                if(isEmpty(excelData.reg_num)) {
                    submitCheck = false;
                    actionAlert({'msg': '세션번호를 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.tmp_date)) {
                    submitCheck = false;
                    actionAlert({'msg': '행사일을 입력해주세요.'});
                    return false;
                }

                // if(isEmpty(excelData.tmp_room)) {
                //     submitCheck = false;
                //     actionAlert({'msg': 'ROOM을 입력해주세요.'});
                //     return false;
                // }

                if(isEmpty(excelData.title)) {
                    submitCheck = false;
                    actionAlert({'msg': '세션명을 입력해주세요.'});
                    return false;
                }

                // if(isEmpty(excelData.chair)) {
                //     submitCheck = false;
                //     actionAlert({'msg': '좌장을 입력해주세요.'});
                //     return false;
                // }

                formData.push(excelData);
            });

            if(submitCheck) {
                ajaxData.append('case', $(form).data('case'));
                ajaxData.append('data', JSON.stringify(formData));
                callMultiAjax($(form).attr('action'), ajaxData);
            }
        });
    </script>
@endsection
