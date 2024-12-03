@extends('admin.layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">메일 {{ empty($user->sid) ? '등록' : '수정' }}</h3>
    </div>

    <form id="user-frm" method="post" action="{{ route('member.data') }}" data-sid="{{ $user->sid ?? 0 }}" data-case="user-update">

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">수정</button>
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
        </div>
    </form>
@endsection

@section('addScript')
    <script>
        const form = '#user-frm';
    </script>

    @section('signupScript')
@endsection
