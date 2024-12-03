@extends('layouts.popup-layout')

@section('addStyle')
    <link type="text/css" rel="stylesheet" href="{{ asset('plugins/plupload/2.3.6/jquery.plupload.queue/css/jquery.plupload.queue.css') }}" />
@endsection

@section('contents')
    <div class="popup-wrap" style="display: block;">
        <div class="popup-contents">
            <div class="popup-tit-wrap">
                <h3 class="popup-tit">
                    수강/열람신청
                </h3>
            </div>

            <div class="popup-conbox">
                <div class="write-form-wrap">
                    <form id="mail-frm" method="post" action="{{ route('education.data') }}" data-sid="{{ $education->sid ?? 0 }}" data-case="sac-create" data-send="N">
                        <input type="hidden" name="user_sid" value="{{ thisPk() ?? 0 }}" readonly>
                        <input type="hidden" name="esid" value="{{ request()->esid ?? 0 }}" readonly>
                        <fieldset>
                            <legend class="hide">신청</legend>
                            <ul class="write-wrap">
                                <li>
                                    <div class="form-tit">이름</div>
                                    <div class="form-con">
                                        {{ $user->name_kr ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">소속</div>
                                    <div class="form-con">
                                        {{ $user->sosok_kr ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">교육명</div>
                                    <div class="form-con">
                                        {{ $education->title ?? '' }}
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">금액</div>
                                    <div class="form-con">
                                        {{ number_format($education->cost) ?? 0 }}원
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">결제방법</div>
                                    <div class="form-con">
                                        <div class="radio-wrap cst">
                                            <label for="pay_method" class="radio-group">
                                                <input type="radio" name="pay_method" id="pay_method" value="B" checked>무통장입금
                                            </label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">결제 정보</div>
                                    <div class="form-con">
                                        <div class="form-group form-group-text n2">
                                            <span class="text">입금자명 : </span>
                                            <input type="text" name="send_name" id="send_name" class="form-item">
                                        </div>
                                        <div class="form-group form-group-text n2 mt-10">
                                            <span class="text">입금예정일 : </span>
                                            <input type="text" name="send_at" id="send_at" class="form-item datepicker">
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-tit">입금 계좌</div>
                                    <div class="form-con">
                                        <img src="/assets/image/sub/img_bank.png" alt="국민은행" class="mr-10"> 123-123-123 (예금주 : 진단검사의학회)
                                    </div>
                                </li>
                            </ul>
                        </fieldset>

                        <div class="btn-wrap text-center">
                            <a href="javascript:;" class="btn btn-type1 color-type2" onclick="self.close();">닫기</a>
                            <button type="submit" class="btn btn-type1 color-type1">신청</button>
                        </div>

                    </form>
                </div>
            </div>
            <button type="button" class="btn-popup-close" onclick="self.close();"><span class="hide">닫기</span></button>
        </div>
    </div>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('script/app/plupload-tinymce.common.js') }}?v={{ config('site.app.asset_version') }}"></script>
    {{--    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>--}}

    <script>
        const form = '#mail-frm';
        const dataUrl = $(form).attr('action');

        defaultVaildation();

        // 게시판 폼 체크
        $(form).validate({
            ignore: ['content', 'popup_content'],
            rules: {
                send_name: {
                    isEmpty: true,
                },
                send_at: {
                    isEmpty: true,
                },
            },
            messages: {
                send_name: {
                    isEmpty: '입금자명을 입력해주세요.',
                },
                send_at: {
                    isEmpty: '입금예정일을 입력해주세요.',
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
