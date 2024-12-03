@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-tit-wrap">
        <h3 class="popup-tit">{{ $session->title ?? '' }}</h3>
    </div>

    <div class="popup-conbox">
        <div class="write-form-wrap">

            <form id="mail-frm" method="post" action="{{ route('workshop.subsession.data',['wsid'=>request()->wsid]) }}" data-sid="{{ $session->sid ?? 0 }}" data-case="subsession-update" data-send="N">
                <input type="hidden" name="wsid" value="{{ request()->wsid ?? 0 }}" readonly>
                <div class="write-wrap">
                    @foreach($sub_session as $key => $val)
                    <div>
                        <div class="text-right">
                            <a href="javascript:;" class="btn btn-small btn-type1 color-type11 btn-delete" data-sid="{{ $val->sid ?? 0 }}">
                                삭제
                            </a>
                        </div>
                        <dl>
                            <dt style="text-align: center;"> 자료분야 </dt>
                            <dd>
                                @foreach($workshopConfig['field'] as $fkey => $fval)
                                    <div class="checkbox-group">
                                        <input type="checkbox" name="field_arr[{{ $val->sid }}][]" id="field_{{ $val->sid }}_{{ $fkey }}" value="{{ $fkey }}" {{ in_array($fkey,$val->field ?? []) ? 'checked' : '' }}>
                                        <label for="field_{{ $val->sid }}_{{ $fkey }}">{{ $fval }}</label>
                                    </div>
                                @endforeach
                            </dd>
                        </dl>
                        <dl>
                            <dt style="text-align: center;"> 제목 </dt>
                            <dd>
                                <input type="text" name="title_arr[{{ $val->sid }}]" id="title" value="{{ $val->title ?? '' }}" class="form-item" style="width: 80%" >
                            </dd>
                        </dl>
                        <dl>
                            <dt style="text-align: center;"> 발표자 </dt>
                            <dd>
                                <input type="text" name="pname_arr[{{ $val->sid }}]" id="pname" value="{{ $val->pname ?? '' }}" class="form-item" style="width: 80%" >
                            </dd>
                            <dt style="text-align: center;"> 소속 </dt>
                            <dd>
                                <input type="text" name="psosok_arr[{{ $val->sid }}]" id="psosok" value="{{ $val->psosok ?? '' }}" class="form-item" style="width: 80%" >
                            </dd>
                        </dl>
                        <dl>
                            <dt style="text-align: center;"> 동영상링크 </dt>
                            <dd style="display: flex;">
                                <input type="text" name="video_link_arr[{{ $val->sid }}]" id="video_link" value="{{ $val->video_link ?? '' }}" class="form-item" style="width: 100%" >
                            </dd>
                        </dl>
                        <dl>
                            <dt style="text-align: center;"> 자료파일 </dt>
                            <dd style="display: flex;">
                                <div class="filebox">
                                    <input class="upload-name form-item" id="thumbnail_text" placeholder="파일 업로드" readonly="readonly">
                                    <label for="thumbnail[{{ $val->sid }}]">파일 업로드</label>
                                    <input type="file" id="thumbnail[{{ $val->sid }}]" name="thumbnail_arr[{{ $val->sid }}]" class="file-upload" accept=".jpg,.jpeg,.png,.gif,.pdf,.ppt,.pptx,.doc,.docx,.hwp" data-accept="jpg|jpeg|png|gif|pdf|ppt|pptx|doc|docx|hwp" onchange="fileCheck(this,$('#thumbnail_text'))">

                                    @if(!empty($val->sid) && $val->realfile)
                                        <a href="{{ $val->downloadUrl() }}">{{ $val->filename }} (다운)</a>

                                        <a href="#n" class="btn-file-delete text-red file_del" data-type="thumbnail" data-path="{{ $val->realfile }}">X</a>

                                    @endif
                                </div>
                            </dd>
                        </dl>

                        <dl>
                            <dt style="text-align: center;"> 초록파일 </dt>
                            <dd style="display: flex;">
                                <div class="filebox">
                                    <input class="upload-name form-item" id="absfile_text" placeholder="파일 업로드" readonly="readonly">
                                    <label for="absfile[{{ $val->sid }}]">파일 업로드</label>
                                    <input type="file" id="absfile[{{ $val->sid }}]" name="absfile_arr[{{ $val->sid }}]" class="file-upload" accept=".jpg,.jpeg,.png,.gif,.pdf,.ppt,.pptx,.doc,.docx,.hwp" data-accept="jpg|jpeg|png|gif|pdf|ppt|pptx|doc|docx|hwp" onchange="fileCheck(this,$('#absfile_text'))">

                                    @if(!empty($val->sid) && $val->abs_realfile)
                                        <a href="{{ $val->downloadUrl('abs') }}">{{ $val->abs_filename }} (다운)</a>

                                        <a href="#n" class="btn-file-delete text-red file_del" data-type="absfile" data-path="{{ $val->abs_realfile }}">X</a>

                                    @endif
                                </div>
                            </dd>
                        </dl>

                    </div>
                    @endforeach
                </div>

                <div class="btn-wrap text-center">
                    <button type="submit" class="btn btn-type1 color-type20" id="submit">저장</button>
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

        $(document).on('click', '.btn-delete', function() {
            let ajaxData = {};
            ajaxData.case = 'subsession-delete';
            ajaxData.sid = $(this).data('sid');

            actionConfirmAlert('해당 서브세션을 삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
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

            let ajaxData = newFormData(form);
            // ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
