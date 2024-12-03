@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">마이페이지</h2>
            <p>
                마이페이지를 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>마이페이지</li>
                    <li>개인정보</li>
                </ul>
            </div>
        </div>
    </article>
    <article class="sub-contents">
        <div class="sub-conbox mypage-info-conbox inner-layer">

            @include('layouts.include.sub-menu-wrap')

            <div class="btn-wrap text-right">
                <a href="https://www.kslm.org/intro.html" class="btn btn-type1 btn-line btn-link" target="_blank"><img src="/assets/image/sub/ic_btn_home.png" alt="">학회 홈페이지</a>
            </div>
            <div class="table-contop text-right">
                <p class="text-red2">* 개인정보수정은 학회 홈페이지에서만 가능합니다.</p>
            </div>
            <ul class="write-wrap">
                <li>
                    <div class="form-tit"><strong class="required">*</strong> 아이디</div>
                    <div class="form-con">
                        {{ $user->uid ?? '' }}
                    </div>
                </li>
                <li>
                    <div class="form-tit"><strong class="required">*</strong> 국문이름</div>
                    <div class="form-con">
                        {{ $user->name_kr ?? '' }}
                    </div>
                </li>
                <li class="n2">
                    <div class="form-tit"><strong class="required">*</strong> 이메일</div>
                    <div class="form-con">
                        {{ $user->email ?? '' }}
                    </div>
                    <div class="form-tit"><strong class="required">*</strong> 휴대전화</div>
                    <div class="form-con">
                        {{ $user->phone ?? '' }}
                    </div>
                </li>
                <li>
                    <div class="form-tit"><strong class="required">*</strong> 의사면허번호</div>
                    <div class="form-con">
                        {{ $user->license_number ?? '' }}
                    </div>
                </li>
                <li class="n2">
                    <div class="form-tit"><strong class="required">*</strong> 근무처명</div>
                    <div class="form-con">
                        {{ $user->sosok_kr ?? '' }}
                    </div>
                    <div class="form-tit"><strong class="required">*</strong> 회원구분</div>
                    <div class="form-con">
                        {{ $userConfig['level'][$user->level ?? 'B'] ?? '' }}
                    </div>
                </li>
                <li>
                    <div class="form-tit"><strong class="required">*</strong> 근무처 주소</div>
                    <div class="form-con">
                        {{ $user->office_addr1 ?? '' }} {{ $user->office_addr2 ?? '' }}
                    </div>
                </li>
            </ul>
        </div>
    </article>

@endsection

@section('addScript')
    <script>

    </script>
@endsection
