@extends('layouts.popup-layout')

@section('addStyle')

@endsection

@section('contents')
    <div class="popup-wrap" id="pop-survey" style="display: block;">
        <div class="popup-contents">
        <div class="popup-tit-wrap">
            <h3 class="popup-tit">
                결제 정보
            </h3>
        </div>
        <div class="popup-conbox">
            <div class="write-form-wrap">
                <form action="" method="">
                    <fieldset>
                        <legend class="hide">결제 정보</legend>
                        <ul class="write-wrap">
                            <li>
                                <div class="form-tit">교육명</div>
                                <div class="form-con">
                                    {{ $sac_info->edu->title ?? '' }}
                                </div>
                            </li>
                            <li>
                                <div class="form-tit">금액</div>
                                <div class="form-con">
                                    {{ $sac_info->edu->cost ? number_format($sac_info->edu->cost) : 0 }}원
                                </div>
                            </li>
                            <li>
                                <div class="form-tit">입금 정보</div>
                                <div class="form-con">
                                    {{ $sac_info->edu->bank_name ?? '' }}은행 / {{ $sac_info->edu->account_num ?? '' }} / 예금주 : {{ $sac_info->edu->account_name ?? '' }}
                                </div>
                            </li>
                            @if($sac_info->edu->pay_info)
                            <li>
                                <div class="form-tit">입금 안내사항</div>
                                <div class="form-con">
                                    {{ $sac_info->edu->pay_info ?? '' }}
                                </div>
                            </li>
                            @endif
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
