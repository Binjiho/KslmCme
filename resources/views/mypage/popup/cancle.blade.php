@extends('layouts.popup-layout')

@section('addStyle')

@endsection

@section('contents')
    <div class="popup-wrap" style="display: block;">
        <div class="popup-contents">
            <div class="popup-tit-wrap">
                <h3 class="popup-tit">
                    교육 취소
                </h3>
            </div>
            <div class="popup-conbox">
                <div class="write-form-wrap">
                    <form id="mail-frm" method="post" action="{{ route('mypage.data') }}" data-sid="{{ $sac_info->sid ?? 0 }}" data-case="sac-delete" data-send="N">
                        <fieldset>
                            <legend class="hide">교육 취소</legend>
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
                                    <div class="form-tit">결제방법</div>
                                    <div class="form-con">
                                        무통장입금
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">환불 계좌정보</div>
                                    <div class="form-con">
                                        <div class="form-group n2">
                                            <input type="text" name="bank_name" id="bank_name" class="form-item" placeholder="은행명" onlyKo>
                                            <input type="text" name="account_name" id="account_name" class="form-item" placeholder="예금주명" onlyKo>
                                        </div>
                                        <div class="form-group mt-10">
                                            <input type="text" name="account_no" id="account_no" class="form-item" placeholder="계좌번호" onlyNumber>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="mt-20">
                                취소 신청 시 환불까지 1-2주 정도 소요될 수 있습니다.
                            </div>
                        </fieldset>

                        <div class="btn-wrap text-center">
                            <a href="javascript:;" class="btn btn-type1 color-type2" onclick="self.close();">취소</a>
                            <button type="submit" class="btn btn-type1 color-type4">확인</button>
                        </div>

                    </form>
                </div>

            </div>
            <button type="button" class="btn-popup-close" onclick="self.close();"><span class="hide" >닫기</span></button>
        </div>
    </div>
@endsection

@section('addScript')
    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],
            rules: {
                bank_name: {
                    isEmpty: true,
                },
                account_name: {
                    isEmpty: true,
                },
                account_no: {
                    isEmpty: true,
                },
            },
            messages: {
                bank_name: {
                    isEmpty: '환불계좌정보 은행명을 입력해주세요.',
                },
                account_name: {
                    isEmpty: '환불계좌정보 예금주명을 입력해주세요.',
                },
                account_no: {
                    isEmpty: '환불계좌정보 계좌번호를 입력해주세요.',
                },
            },
            submitHandler: function() {
                boardSubmit();
            }
        });

        const boardSubmit = () => {
            let ajaxData = newFormData(form);

            callMultiAjax(dataUrl, ajaxData);
        }


    </script>
@endsection
