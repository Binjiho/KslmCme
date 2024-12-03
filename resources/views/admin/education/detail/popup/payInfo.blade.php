@extends('admin.layouts.popup-layout')

@section('addStyle')

@endsection

@section('contents')
    <div class="popup-wrap" id="pop-survey" style="display: block;">
        <div class="popup-contents">
        <div class="popup-tit-wrap">
            <h3 class="popup-tit">
                입금 정보
            </h3>
        </div>
        <div class="popup-conbox">
            <div class="write-form-wrap">
                <form action="" method="">
                    <fieldset>
                        <legend class="hide">결제 정보</legend>
                        <ul class="write-wrap">
                            <li>
                                <div class="form-tit">입금자명</div>
                                <div class="form-con">
                                    {{ $sac_info->send_name ?? '' }}
                                </div>
                            </li>
                            <li>
                                <div class="form-tit">입금예정일</div>
                                <div class="form-con">
                                    {{ $sac_info->send_at->format('Y-m-d') ?? '' }}
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </form>
            </div>
            <div class="btn-wrap text-center">
                <button type="button" class="btn btn-type1 color-type4" onclick="self.close();">확인</button>
            </div>
        </div>
        <button type="button" class="btn-popup-close" onclick="self.close();"><span class="hide">닫기</span></button>
    </div>
    </div>
@endsection

@section('addScript')

@endsection
