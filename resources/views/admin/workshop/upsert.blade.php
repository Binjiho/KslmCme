@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">행사 {{ empty($workshop->sid) ? '등록' : '수정' }}</h3>
    </div>

    <form id="mail-frm" method="post" action="{{ route('workshop.data') }}" data-sid="{{ $workshop->sid ?? 0 }}" data-case="workshop-{{ empty($workshop->sid) ? 'create' : 'update' }}" data-send="N">
        <div class="write-wrap">
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 자료 구분</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($workshopConfig['category'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" {{ ($workshop->category ?? '') == $key ? 'checked' : '' }}>
                                <label for="category_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>
            <dl class="gubun_dl" style="{{ ($workshop->category ?? '') == 'A' ? '' : 'display:none;' }}">
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 학술대회 구분</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($workshopConfig['gubun'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="gubun" id="gubun_{{ $key }}" value="{{ $key }}" {{ ($workshop->gubun ?? '') == $key ? 'checked' : '' }}>
                                <label for="gubun_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 행사명</dt>
                <dd>
                    <input type="text" name="title" id="title" value="{{ $workshop->title ?? '' }}" class="form-item">
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 장소</dt>
                <dd>
                    <input type="text" name="place" id="place" value="{{ $workshop->place ?? '' }}" class="form-item">
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 행사기간</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($workshopConfig['date_type'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="date_type" id="date_type_{{ $key }}" value="{{ $key }}" {{ ($workshop->date_type ?? '') == $key ? 'checked' : '' }}>
                                <label for="date_type_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 행사일</dt>
                <dd style="display: flex;">
                    <input type="text" name="sdate" id="sdate" value="{{ $workshop->sdate ?? '' }}" class="form-item" datepicker readonly>
                      -
                    <input type="text" name="edate" id="edate" value="{{ $workshop->edate ?? '' }}" class="form-item" datepicker readonly >

                    {{-- 241120 포스터세션 사용 --}}
                    <div class="checkbox-wrap">
                        @foreach($workshopConfig['poster_yn'] as $key => $val)
                            <div class="checkbox-group">
                                <input type="checkbox" name="poster_yn" id="poster_yn_{{ $key }}" value="{{ $key }}" {{ ($workshop->poster_yn ?? 'N' ) == $key  ? 'checked' : '' }}>
                                <label for="poster_yn_{{ $key }}">포스터 {{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

            <?/* 241119
            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> ROOM</dt>
                <dd id="room_tbl" >
                    @if(!empty($workshop->sid))
                        @foreach($workshop->room as $key => $val)
                        <div style="display: flex;">
                            <input type="text" name="rooms[]" value="{{ $val ?? '' }}" class="form-item">

                            <a href="javascript:;" onclick="change_tr(this,'add');" class="btn btn-util color-type5">추가</a>
                            @if(!$loop->first)
                            <a href="javascript:;" onclick="change_tr(this,'del');" class="btn btn-util color-type11">삭제</a>
                            @endif
                        </div>
                      @endforeach
                    @else
                        <div style="display: flex;">
                            <input type="text" name="rooms[]" value="{{ $workshop->title ?? '' }}" class="form-item">

                            <a href="javascript:;" onclick="change_tr(this,'add');" class="btn btn-util color-type5">추가</a>
    {{--                        <a href="javascript:;" onclick="change_tr(this,'del');" class="btn btn-util color-type11">삭제</a>--}}
                        </div>
                    @endif
                </dd>
            </dl>
            */?>


            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 썸네일 파일 </dt>
                <dd style="display: flex;">
                    <div class="filebox">
                        <input class="upload-name form-item" id="thumbnail_text" placeholder="파일 업로드" value="{{ $workshop->filename  ?? '' }}" readonly="readonly">
                        <label for="thumbnail">파일 업로드</label>
                        <input type="file" id="thumbnail" name="thumbnail" class="file-upload" accept=".jpg, .jpeg, .png, .pptx, .ppt, .pdf, .hwp" data-accept="jpeg|jpg|png|pptx|ppt|pdf|hwp" onchange="fileCheck(this,$('#thumbnail_text'))">

                        @if(!empty($workshop->sid) && $workshop->realfile)
                            <a href="{{ $workshop->downloadUrl() }}">{{ $workshop->filename }} (다운)</a>

                            <a href="#n" class="btn-file-delete text-red file_del" data-type="thumbnail" data-path="{{ $workshop->realfile }}">X</a>

                        @endif
                    </div>
                </dd>
            </dl>

            

            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 노출여부</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($workshopConfig['hide'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="hide" id="hide_{{ $key }}" value="{{ $key }}" {{ ($workshop->hide ?? '') == $key ? 'checked' : '' }}>
                                <label for="hide_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

			<dl class="main_dl" style="{{ ($workshop->hide ?? '') == 'N' ? '' : 'display:none;' }}">
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 메인 노출여부</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($workshopConfig['main_yn'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="main_yn" id="main_yn_{{ $key }}" value="{{ $key }}" {{ ($workshop->main_yn ?? '') == $key ? 'checked' : '' }}>
                                <label for="main_yn_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"><b style="color: #e95d5d;">*</b> 자료열람권한 레벨</dt>
                <dd>
                    <div class="checkbox-wrap">
                        @foreach($userConfig['level'] as $key => $val)
                            @continue($key=='M')
                            <div class="checkbox-group">
                                <input type="checkbox" name="limit_level[]" id="limit_level_{{ $key }}" value="{{ $key }}" {{ in_array( $key, ($workshop->limit_level ?? [] ) )  ? 'checked' : '' }}>
                                <label for="limit_level_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"> 초록집 파일 등록 </dt>
                <dd style="display: flex;">
                    <div class="filebox">
                        <input class="upload-name form-item" id="absfile_text" placeholder="파일 업로드" value="{{ $workshop->abs_filename  ?? '' }}" readonly="readonly">
                        <label for="absfile">파일 업로드</label>
                        <input type="file" id="absfile" name="absfile" class="file-upload" accept=".jpg, .jpeg, .png, .pptx, .ppt, .pdf, .hwp" data-accept="jpeg|jpg|png|pptx|ppt|pdf|hwp" data-size="500" onchange="fileCheck(this,$('#absfile_text'))">

                        @if(!empty($workshop->sid) && $workshop->abs_realfile)
                            <a href="{{ $workshop->downloadUrl('abs') }}">{{ $workshop->abs_filename }} (다운)</a>

                            <a href="#n" class="btn-file-delete text-red file_del" data-type="absfile" data-path="{{ $workshop->abs_realfile }}">X</a>

                        @endif
                    </div>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"> 프로그램 북 파일 등록 </dt>
                <dd style="display: block;">
                    <div class="filebox">
                        <input class="upload-name form-item" id="bookfile_text" placeholder="파일 업로드" value="{{ $workshop->book_filename  ?? '' }}" readonly="readonly">
                        <label for="bookfile">파일 업로드</label>
                        <input type="file" id="bookfile" name="bookfile" class="file-upload" accept=".jpg, .jpeg, .png, .pptx, .ppt, .pdf, .hwp" data-accept="jpeg|jpg|png|pptx|ppt|pdf|hwp" data-size="500" onchange="fileCheck(this,$('#bookfile_text'))">

                        @if(!empty($workshop->sid) && $workshop->book_realfile)
                            <a href="{{ $workshop->downloadUrl('book') }}">{{ $workshop->book_filename }} (다운)</a>

                            <a href="#n" class="btn-file-delete text-red file_del" data-type="bookfile" data-path="{{ $workshop->book_realfile }}">X</a>

                        @endif
                    </div>

                    <div class="filebox">
                        <input class="upload-name form-item" id="bookfile_text2" placeholder="파일 업로드" value="{{ $workshop->book_filename  ?? '' }}" readonly="readonly">
                        <label for="bookfile2">파일 업로드</label>
                        <input type="file" id="bookfile2" name="bookfile2" class="file-upload" accept=".jpg, .jpeg, .png, .pptx, .ppt, .pdf, .hwp" data-accept="jpeg|jpg|png|pptx|ppt|pdf|hwp" data-size="500" onchange="fileCheck(this,$('#bookfile_text2'))">

                        @if(!empty($workshop->sid) && $workshop->book_realfile2)
                            <a href="{{ $workshop->downloadUrl('book2') }}">{{ $workshop->book_filename2 }} (다운)</a>

                            <a href="#n" class="btn-file-delete text-red file_del" data-type="bookfile2" data-path="{{ $workshop->book_realfile2 }}">X</a>

                        @endif
                    </div>
                </dd>
            </dl>


        </div>

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($workshop->sid) ? '등록' : '수정' }}</button>
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
        </div>
    </form>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>
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

        function change_tr(el, mode){
            if(mode == 'add'){
                var _html = "";
                _html += "<div style=\"display:flex;\">";
                _html += "<input type=\"text\" name=\"rooms[]\" class=\"form-item\">";
                _html += "<a href=\"javascript:;\" onclick=\"change_tr(this,'add');\" class=\"btn btn-util color-type5\">추가</a>";
                _html += "<a href=\"javascript:;\" onclick=\"change_tr(this,'del');\" class=\"btn btn-util color-type11\">삭제</a>";
                _html += "</div>";

                $("#room_tbl").append(_html);
            }else{
                if($("#room_tbl").find("div").length < 2){
                    alert('최소 한개 이상은 입력해주세요.');
                    return false;
                }else{
                    $(el).parent().remove();
                }
            }
        }

        $(document).on('click', 'input[name=category]', function() {
            if ( $(this).val() == 'A' ){
                $(".gubun_dl").show();
            }else{
                $(".gubun_dl").hide();
                $("input[name='gubun']").prop('checked',false);
            }
        });

        //메인 노출여부
        $(document).on('click', 'input[name=hide]', function() {
            if ( $(this).val() == 'N' ){
                $(".main_dl").show();
            }else{
                $(".main_dl").hide();
                $("input[name='main_yn']").prop('checked',false);
            }
        });

        //행사기간
        $(document).on('click', 'input:radio[name="date_type"]', function() {
            if( $(this).val() == 'D'/*하루행사*/) {
                $('#edate').attr('disabled', 'disabled');
                $('#edate').val('');
            }else{
                $('#edate').attr('disabled', false);
            }
        });

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],
            rules: {

            },
            messages: {

            },
            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            if( $("input[name='category']:checked").length < 1){
                alert("자료구분을 선택해주세요.");
                return false;
            }
            if($("input[name='category']:checked").val() == 'A'){
                if( $("input[name='gubun']:checked").length < 1){
                    alert("학술대회 구분을 선택해주세요.");
                    return false;
                }
            }
            if(isEmpty( $("#title").val() ) ){
                alert("행사명을 입력해주세요.");
                return false;
            }
            if(isEmpty( $("#place").val() ) ){
                alert("행사장소를 입력해주세요.");
                return false;
            }
            if( $("input[name='date_type']:checked").length < 1){
                alert("행사 기간을 선택해주세요.");
                return false;
            }
            if(isEmpty( $("#sdate").val() ) ){
                alert("행사시작일을 입력해주세요.");
                return false;
            }
            if($("input[name='date_type']:checked").val()=='L'){
                if(isEmpty( $("#edate").val() ) ){
                    alert("행사마감일을 입력해주세요.");
                    return false;
                }
            }

            let count = $("input[name='rooms[]']").filter(function() {
                return $(this).val().trim() === "";
            }).length;

            if(count > 0){
                alert("ROOM 정보를 입력해주세요.");
                return false;
            }

            // if( $("#thumbnail_text").val().length < 1){
            //     alert("썸네일 파일을 첨부해주세요.");
            //     return false;
            // }

            if( $("input[name='hide']:checked").length < 1){
                alert("노출여부를 선택해주세요.");
                return false;
            }

            if($("input[name='hide']:checked").val() == 'N'){
                if( $("input[name='main_yn']:checked").length < 1){
                    alert("메인 노출여부를 선택해주세요.");
                    return false;
                }
            }

            if( $("input[name='limit_level[]']:checked").length < 1){
                alert("자료열람권한 레벨을 선택해주세요.");
                return false;
            }

            let ajaxData = newFormData(form);
            // ajaxData.append('contents', tinymce.get('contents').getContent());

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
