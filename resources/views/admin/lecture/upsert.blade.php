@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">교육 {{ empty($lecture->sid) ? '등록' : '수정' }}</h3>
    </div>

    <form id="mail-frm" method="post" action="{{ route('lecture.data') }}" data-sid="{{ $lecture->sid ?? 0 }}" data-case="lecture-{{ empty($lecture->sid) ? 'create' : 'update' }}" data-send="N">
        <div class="write-wrap">
            <dl>
                <dt style="text-align: center;"> 강의타입</dt>
                <dd>
                    <div class="radio-wrap">
                        @foreach($lectureConfig['type'] as $key => $val)
                            <div class="radio-group">
                                <input type="radio" name="type" id="type_{{ $key }}" value="{{ $key }}" {{ ($lecture->type ?? '') == $key ? 'checked' : '' }}>
                                <label for="type_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"> 강의분야</dt>
                <dd>
                    <div class="checkbox-wrap">
                        @foreach($lectureConfig['field'] as $key => $val)
                            <div class="checkbox-group">
                                <input type="checkbox" name="field[]" id="field_{{ $key }}" value="{{ $key }}" {{ in_array( $key, ($lecture->field ?? [] ) )  ? 'checked' : '' }}>
                                <label for="field_{{ $key }}">{{ $val }}</label>
                            </div>
                        @endforeach
                    </div>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;"> 강의명</dt>
                <dd>
                    <input type="text" name="title" id="title" value="{{ $lecture->title ?? '' }}" class="form-item">
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"> 강사명</dt>
                <dd>
                    <input type="text" name="name_kr" id="name_kr" value="{{ $lecture->name_kr ?? '' }}" class="form-item">
                </dd>
            </dl>
            <dl>
                <dt style="text-align: center;"> 강사소속</dt>
                <dd>
                    <input type="text" name="sosok_kr" id="sosok_kr" value="{{ $lecture->sosok_kr ?? '' }}" class="form-item">
                </dd>
            </dl>

            <div class="vod_div" style="{{ ($lecture->type ?? '') == 'V' ? '':'display:none;' }}">
                <dl>
                    <dt style="text-align: center;"> 강의영상 주소</dt>
                    <dd>
                        <input type="text" name="link_url" id="link_url" value="{{ $lecture->link_url ?? '' }}" class="form-item">
                    </dd>
                </dl>
                <dl>
                    <dt style="text-align: center;"> 강의시간</dt>
                    <dd>
                        <div id="video_container" class="bm10" style="display: none;">
                            <video poster="http://media.w3.org/2010/05/sintel/poster.png" preload="none" controls="true" autoplay="true" id="video" tabindex="0" width=235 style="border-top:1px solid#dddddd;border-right:1px solid#dddddd;border-left:1px solid#dddddd">
                                <source type="video/mp4" src="{{ $lecture->link_url ?? '' }}" id="mp4">
                            </video>
                        </div>

                        <span class="text-red" id="runningTimeHtml">{{ $lecture->lecture_time ?? '강의시간이 표기될 때 까지 기다려주세요.' }}</span>

                        <input type="hidden" name="lecture_time" id="lecture_time" value="{{ $lecture->lecture_time ?? '' }}" class="form-item" readonly>
                    </dd>
                </dl>
                <dl>
                    <dt style="text-align: center;"> 플레이 확인창 시간</dt>
                    <dd style="display: flex;">
                        <input type="checkbox" name="play_yn" id="play_yn" value="N" {{ ( $lecture->play_yn ?? '') == 'N' ? 'checked':'' }} >
                        <label for="play_yn">미사용</label>
                        <input type="text" name="play_time" id="play_time" value="{{ $lecture->play_time ?? '' }}" class="form-item" >
                    </dd>
                </dl>
            </div>

            <div class="pdf_div" style="{{ ($lecture->type ?? '') == 'P' ? '':'display:none;' }}">
                <dl>
                    <dt style="text-align: center;"> PDF 강의파일</dt>
                    <dd>
                        <div class="filebox">
                            <input class="upload-name form-item" id="pdf_file_text" name="pdf_file_text" placeholder="파일 업로드" readonly="readonly" value="{{ $lecture->filename1 ?? '' }}">
                            <label for="pdf_file">파일 업로드</label>
                            <input type="file" id="pdf_file" name="pdf_file" class="file-upload"  accept="application/pdf" data-accept="pdf" onchange="fileCheck(this,$('#pdf_file_text'))">

                            @if(!empty($lecture->sid) && $lecture->realfile1)
                                <a href="{{ $lecture->downloadUrl() }}">{{ $lecture->filename1 }} (다운)</a>

                                <a href="#n" class="btn-file-delete text-red file_del" data-type="pdf_file" data-path="{{ $lecture->realfile1 }}">X</a>

                            @endif
                            <b style="color: #e95d5d;">* PDF 파일만 업로드가능합니다.</b>
                        </div>
                    </dd>
                </dl>
            </div>

            <dl>
                <dt style="text-align: center;"> 자료</dt>
                <dd>
                    <div class="filebox">
                        <input class="upload-name form-item" id="item_file_text" placeholder="파일 업로드" readonly="readonly">
                        <label for="item_file">파일 업로드</label>
                        <input type="file" id="item_file" name="item_file" class="file-upload" onchange="fileCheck(this,$('#item_file_text'))">

                        @if(!empty($lecture->sid) && $lecture->realfile2)
                            <a href="{{ $lecture->downloadUrl() }}">{{ $lecture->filename2 }} (다운)</a>

                            <a href="#n" class="btn-file-delete text-red file_del" data-type="item_file" data-path="{{ $lecture->realfile2 }}">X</a>
                            
                        @endif
                    </div>
                </dd>
            </dl>
        </div>

        <b style="color: #e95d5d; font-size:20px;">※ 강의 시간 표기 후 등록해야 강의가 정상 이수 됩니다.</b>

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($lecture->sid) ? '등록' : '수정' }}</button>
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

        /**
         * 영상 시간 불러오기
         */
        $(document).on('keyup', '#link_url', function() {
            geturls = $(this).val();

            if( geturls == "" ){ alert("등록된 동영상이 없습니다."); return false; }

            $("#mp4").attr("src",geturls);
            $("#video_container").find("video").load();

            // $("#running_box").show();

            // 재생후 러닝타임을 가져올수있기때문에 1초간격으로 값을 체크하고 실행종료
            var make_lunningTime = setInterval(function(){
                if( isNaN($("#video_container").find("video").get(0).duration) == false ){

                    $("#runningTimeHtml").html($("#video_container").find("video").get(0).duration);

                    $("input[name='lecture_time']").val($("#video_container").find("video").get(0).duration);
                    $("input[name='play_time']").val( $("#video_container").find("video").get(0).duration/2 );


                    clearInterval(make_lunningTime);
                }
            },1500)
        });

        $(document).on('click', '.file_del', function() {
            let ajaxData = {};
            ajaxData.case = 'file-delete';
            ajaxData.fileType = $(this).data('type');
            ajaxData.filePath = $(this).data('path');

            actionConfirmAlert('삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
        });

        $(document).on('click', 'input[name=type]', function() {
            if ( $(this).val() == 'V' ){
                $(".vod_div").show();
                $(".pdf_div").hide();
            }else{
                $(".vod_div").hide();
                $(".pdf_div").show();
            }
        });

        $(document).on('click', 'input[name=play_yn]', function() {
            if ( $("input[name='play_yn']:checked").val() == 'N' ){
                $("input[name='play_time']").val('');
                $("input[name='play_time']").prop("disabled",true);
            }else{
                $("input[name='play_time']").prop("disabled",false);
            }
        });


        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            // ignore: ['content', 'popup_content'],
            rules: {
                type: {
                    checkEmpty: true,
                },
                "field[]": {
                    checkEmpty: true,
                },
                title: {
                    isEmpty: true,
                },
                name_kr: {
                    isEmpty: true,
                },
                sosok_kr: {
                    isEmpty: true,
                },
                link_url: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='type']:checked").val() === 'V';
                        }
                    },
                },
                lecture_time: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='type']:checked").val() === 'V';
                        }
                    },
                },
                play_time: {
                    isEmpty: {
                        depends: function (element) {
                            return ($("input[name='type']:checked").val() === 'V') && ($("input[name='play_yn']:checked").val() !== 'N');
                        }
                    },
                },
                pdf_file_text: {
                    isEmpty: {
                        depends: function (element) {
                            return $("input[name='type']:checked").val() === 'P';
                        }
                    },
                },
            },
            messages: {
                type: {
                    checkEmpty: '강의타입을 선택해주세요.',
                },
                "field[]": {
                    checkEmpty: '강의분야를 선택해주세요.',
                },
                title: {
                    isEmpty: `강의명을 입력해주세요.`,
                },
                name_kr: {
                    isEmpty: `강사명을 입력해주세요.`,
                },
                sosok_kr: {
                    isEmpty: `강사소속을 입력해주세요.`,
                },
                link_url: {
                    isEmpty: `강의영상 주소를 입력해주세요.`,
                },
                lecture_time: {
                    isEmpty: `강의시간이 표기될 때 까지 기다려주세요.`,
                },
                play_time: {
                    isEmpty: `플레이 확인창 시간을 입력해주세요.`,
                },
                pdf_file_text: {
                    isEmpty: `pdf 강의파일을 첨부해주세요.`,
                },

            },
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
