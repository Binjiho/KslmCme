@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">로그인</h2>
            <p>
                로그인을 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>로그인</li>
                </ul>
            </div>
        </div>
    </article>
    <article class="sub-contents">
        <div class="sub-conbox inner-layer">
            <div class="login-wrap type3">
                <div class="login-form">
                    <form id="login-frm" method="post" >
                        <fieldset>
                            <legend class="hide">로그인</legend>
                            <div class="login-tit-wrap">
                                <span class="icon"><img src="/assets/image/sub/ic_login.png" alt=""></span>
                                <h3 class="login-tit">LOGIN</h3>
                            </div>
                            <div class="input-box">
                                <input type="text" name="uid" id="uid" class="form-item" placeholder="아이디를 입력하세요." noneSpace>
                                <input type="password" name="password" id="password" class="form-item" placeholder="비밀번호를 입력하세요." noneSpace>
                            </div>
                            <div class="btn-wrap">
                                <button type="submit" class="btn btn-login">로그인</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="login-info">
                    <img src="/assets/image/sub/login_logo.png" alt="대한진단검사의학회 Korean Society for Laboratory Medicine">
                    <p>
                        대한진단검사의학회 <br>
                        학술자료실 & E-learning 센터에 오신 것을 환영합니다. <br>
                        대한진단검사의학회에서 사용하시는 ID/PW로 로그인 하시기 바랍니다.
                    </p>
                    <div class="btn-wrap text-center">
                        <a href="https://www.kslm.org/member/find_user.html" target="_blank" class="btn btn-signup">아이디/비밀번호 찾기</a>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script src="{{ asset('plugins/jquery-validation/jquery.validate.min.js') }}"></script>

    <script>
        const form = '#login-frm';
        const dataUrl = '{{ route('login') }}';

        defaultVaildation();

        $(form).validate({
            rules: {
                uid: {
                    isEmpty: true,
                },
                password: {
                    isEmpty: true,
                },
            },
            messages: {
                uid: {
                    isEmpty: "아이디를 입력 해주세요.",
                },
                password: {
                    isEmpty: "비밀번호를 입력 해주세요.",
                },
            },
            submitHandler: function () {
                callAjax(dataUrl, formSerialize(form), true);
            }
            // submitHandler: function () {
            //     callAjax($(form).attr('action'), formSerialize(form), true);
            // }
        });
    </script>
@endsection
