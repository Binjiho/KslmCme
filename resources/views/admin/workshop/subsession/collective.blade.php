@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/handsontable/css/handsontable.full.min.css') }}"/>
@endsection

@section('contents')
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">세부 프로그램 정보 엑셀 등록</h3>
    </div>

    <div class="notice" style="font-size: large; line-height: normal;">
        세션번호 : 세션 정보 등록 시 입력한 세션 번호 중 해당 프로그램이 들어가야하는 세션 번호로 입력 / 숫자로만 입력
        <br>
        자료순번 : 각 세션별 자료가 노출될 순서 입력 / 숫자로만 입력
        <br>
        자료분야 : 아래와 같이 해당하는 자료분야 알파벳 입력, 여러 개일 경우 콤마로 구분하여 입력
        <br>
        ( A. 임상화학      B. 진단혈액       C. 임상미생물       D. 진단면역         E. 진단유전       F. 수혈의학      G.  검사실운영  
        H.고시  I.교육  J.정보  K.수련  L.의료정보  M.유전분자진단  Z.기타  )
        <br>
        세션명 및 좌장 : 텍스트로 입력
        <br>
        동영상 링크 : 비메오 링크 입력
        <br>
        <p style="color: #9e0505">** 위의 설명을 참고하여 엑셀에서 작성한 내용을 복사 붙여넣기 하여 입력하여 주시기 바랍니다. (직접 입력도 가능)</p>
    </div>

    <div class="popup-conbox">
        <div class="write-form-wrap">
            <form method="post" action="{{ route('workshop.subsession.data', ['wsid' => request()->wsid]) }}" id="collective-upload" data-case="collective-create">
                <input type="hidden" name="wsid" value="{{ request()->wsid }}">
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
            colHeaders: ['세션번호', '자료순번', '자료분야', '제목','발표자', '발표자소속','동영상링크'],
            colWidths: [100, 100, 100, 200, 150, 200, 200],
            data: [{
                name: '1',
                name2: '1',
                name3: 'A,B',
                name4: '테스트',
                name5: '테스터',
                name6: '테스트병원',
                name7: '',
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
                    'sort': excelData[1],
                    'tmp_field': excelData[2],
                    'title': excelData[3],
                    'pname': excelData[4],
                    'psosok': excelData[5],
                    'vod_link': excelData[6],
                }
                if(isEmpty(excelData.reg_num)) {
                    submitCheck = false;
                    actionAlert({'msg': '세션번호를 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.sort)) {
                    submitCheck = false;
                    actionAlert({'msg': '자료순번을 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.tmp_field)) {
                    submitCheck = false;
                    actionAlert({'msg': '자료분야를 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.title)) {
                    submitCheck = false;
                    actionAlert({'msg': '제목을 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.pname)) {
                    submitCheck = false;
                    actionAlert({'msg': '발표자를 입력해주세요.'});
                    return false;
                }
                if(isEmpty(excelData.psosok)) {
                    submitCheck = false;
                    actionAlert({'msg': '발표자소속을 입력해주세요.'});
                    return false;
                }

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
