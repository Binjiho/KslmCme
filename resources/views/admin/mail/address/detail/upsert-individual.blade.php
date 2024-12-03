@extends('admin.layouts.popup-layout')

@section('addStyle')
@endsection

@section('contents')
    <div class="sub-tit-wrap">
        <h3 class="sub-tit">직접 입력</h3>
{{--        <h3 class="sub-tit">명단 개별 {{ empty($addressDetail->sid) ? '등록' : '수정' }}</h3>--}}
    </div>

    <form id="individual-frm" method="post" action="{{ route('mail.address.data') }}" data-ma_sid="{{ request()->ma_sid }}" data-sid="{{ $addressDetail->sid ?? 0 }}" data-case="individual-{{ empty($addressDetail->sid) ? 'create' : 'update' }}">
        <div class="write-wrap">
            <dl>
                <dt style="text-align: center;">이름</dt>
                <dd>
                    <input type="text" name="name" id="name" value="{{ $addressDetail->name ?? '' }}" class="form-item">
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;">이메일</dt>
                <dd>
                    <input type="text" name="email" id="email" value="{{ $addressDetail->email ?? '' }}" class="form-item">
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;">휴대폰</dt>
                <dd>
                    <input type="text" name="mobile" id="mobile" value="{{ $addressDetail->mobile ?? '' }}" class="form-item" phoneHyphen>
                </dd>
            </dl>

            <dl>
                <dt style="text-align: center;">소속</dt>
                <dd>
                    <input type="text" name="office" id="office" value="{{ $addressDetail->office ?? '' }}" class="form-item">
                </dd>
            </dl>
        </div>

        <div class="btn-wrap text-center">
            <button type="submit" class="btn btn-type1 color-type20" id="submit">{{ empty($addressDetail->sid) ? '등록' : '수정' }}</button>
            <a href="javascript:window.close();" class="btn btn-type1 color-type3">취소</a>
        </div>
    </form>
@endsection

@section('addScript')
    <script>
        const form = '#individual-frm';
        const dataUrl = $(form).attr('action');

        $(document).on('submit', form, function () {
            const name = $('input[name=name]');
            const email = $('input[name=email]');
            const mobile = $('input[name=mobile]');
            const office = $('input[name=office]');

            if (isEmpty(name.val())) {
                alert('이름을 입력 해주세요.');
                name.focus();
                return false;
            }

            if (isEmpty(email.val())) {
                alert('이메일을 입력 해주세요.');
                email.focus();
                return false;
            }

            if (!emailCheck(email.val())) {
                alert('올바른 이메일 형식이 아닙니다.');
                email.focus();
                return false;
            }

            if (isEmpty(mobile.val())) {
                alert('휴대폰 번호를 입력 해주세요.');
                mobile.focus();
                return false;
            }

            if (isEmpty(office.val())) {
                alert('소속을 입력 해주세요.');
                office.focus();
                return false;
            }

            let ajaxData = formSerialize(form);
            ajaxData.ma_sid = $(form).data('ma_sid');

            callAjax(dataUrl, ajaxData);
        });
    </script>
@endsection
