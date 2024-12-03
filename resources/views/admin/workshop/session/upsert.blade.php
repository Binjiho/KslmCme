@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">세션정보 {{ empty($session->sid) ? '등록' : '수정' }}</h3>
    </div>

    <div class="popup-conbox">
        <div class="write-form-wrap">
            <form id="mail-frm" method="post" action="{{ route('workshop.session.data') }}" data-sid="{{ $session->sid ?? 0 }}" data-case="session-{{ empty($session->sid) ? 'create' : 'update' }}" data-send="N">
                <input type="hidden" name="wsid" value="{{ request()->wsid ?? 0 }}" readonly>
                <div class="write-wrap">
                    <dl>
                        <dt style="text-align: center;"> 행사일 </dt>
                        <dd>
{{--                            <input type="text" name="date" id="date" value="{{ $workshop->date[$session->date] ?? '' }}" class="form-item" datepicker readonly>--}}
                            <select name="date" style="width: 80%;">
                                @foreach($workshop->date as $key => $val)
                                    <option value="{{ $key }}" {{ $session->date == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </dd>
                    </dl>
                    <?/* 241119
                    <dl>
                        <dt style="text-align: center;"> ROOM </dt>
                        <dd>
                            <select name="room" style="width: 80%;">
                                @foreach($workshop->room as $key => $val)
                                    <option value="{{ $key }}" {{ $session->room == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </dd>
                    </dl>
                    */?>
                    <dl>
                        <dt style="text-align: center;"> 세션명 </dt>
                        <dd>
                            <input type="text" name="title" id="title" value="{{ $session->title ?? '' }}" class="form-item" style="width: 80%" >
                        </dd>
                    </dl>
                    <dl>
                        <dt style="text-align: center;"> 좌장 </dt>
                        <dd style="display: flex;">
                            <input type="text" name="chair" id="chair" value="{{ $session->chair ?? '' }}" class="form-item" style="width: 80%" >
                        </dd>
                    </dl>

                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($session->sid) ? '등록' : '수정' }}</button>
                    <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('addScript')
{{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
{{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
{{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('click', '.file_del', function() {
            let ajaxData = {};
            ajaxData.case = 'file-delete';
            ajaxData.fileType = $(this).data('type');
            ajaxData.filePath = $(this).data('path');

            actionConfirmAlert('삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
        });

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],

            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {

            if(isEmpty( $("#title").val() ) ){
                alert("세션명을 입력해주세요.");
                return false;
            }

            // if(isEmpty( $("#chair").val() ) ){
            //     alert("좌장을 입력해주세요.");
            //     return false;
            // }

            let ajaxData = newFormData(form);
            // ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
