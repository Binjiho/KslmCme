@extends('layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-contents">
        <div class="popup-conbox">
            <div class="view-wrap">
                <div class="view-conbox">
                    <div class="play-wrap">
                        <input type="hidden" name="vod_link" id="vod_link" value="{{ $sub_session->video_link ?? '' }}" class="form-item">

                        <div id="video_container">
                            <video preload="none" controls="true" autoplay="true" id="video" tabindex="0">
                                <source type="video/mp4" src="" id="mp4">
                            </video>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- <button type="button" class="btn-popup-close" onclick="self.close();"><span class="hide">닫기</span></button> -->
    </div>
@endsection

@section('addScript')
{{--    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>--}}
{{--    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>--}}
{{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        /**
         * 영상 시간 불러오기
         */
        $(document).ready(function() {
        // $(document).on('click',"#btn_setting", function () {
            geturls = $("#vod_link").val();

            if( geturls == "" ){ alert("등록된 동영상이 없습니다."); return false; }

            $("#mp4").attr("src", geturls);
            $("#video_container").show();
            $("#video_container").find("video").load();

            // $("#video_container").append('<video id="myVideo" src="https://player.vimeo.com/progressive_redirect/playback/925700063/rendition/720p/file.mp4?loc=external&signature=d74c4a486f871817cbef8af3da803be66520500c8f3b0333a39a49415fe89082"></video>');
            //
            // var videoElement = $("#video_container").find("video");
            // if (videoElement) {
            //     console.log('here');
            //     videoElement.play().catch(error => {
            //         console.log("Autoplay failed:", error);
            //     });
            // }


        });

        $(document).on('click', '.file_del', function() {
            let ajaxData = {};
            ajaxData.case = 'file-delete';
            ajaxData.fileType = $(this).data('type');
            ajaxData.filePath = $(this).data('path');

            actionConfirmAlert('삭제 하시겠습니까?', {'ajax': actionAjax(dataUrl, ajaxData)});
        });

    </script>
@endsection
