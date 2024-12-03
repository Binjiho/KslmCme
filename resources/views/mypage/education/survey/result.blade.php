@extends('layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-contents">
        <div class="popup-conbox">
            <div class="complete-conbox text-center">
                <img src="/assets/image/sub/img_complete.png" alt="">
                <strong class="tit">
                    <strong>모든 수강이 종료</strong>되었습니다. <br>
                    설문조사에 참여해 주셔서 감사합니다.
                </strong>
                @if( ($sac_info->edu->certi_yn ?? '') == 'Y')
                <p>
                    이수증 출력 메뉴에서 온라인 이수증 출력이 바로 가능합니다. <a href="#n" class="btn btn-type1 color-type5"><img src="/assets/image/sub/ic_print.png" alt=""> 이수증 출력</a>
                </p>
                @endif
            </div>
            <div class="btn-wrap text-center">
                <a href="javascript:opener.location.reload(); self.close();" class="btn btn-type1 color-type2">닫기</a>
            </div>
        </div>
    </div>
@endsection

@section('addScript')
    <script>
        const dataUrl = '{{ route('mypage.education.data') }}';
        const form = '#searchF';


    </script>
@endsection
