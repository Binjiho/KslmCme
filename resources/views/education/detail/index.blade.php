@extends('layouts.web-layout')

@section('addStyle')
@endsection

@section('contents')
    <article class="sub-visual">
        <div class="sub-visual-con inner-layer">
            <h2 class="sub-visual-tit">수강/열람신청</h2>
            <p>
                수강/열람신청을 확인하실 수 있습니다.
            </p>
            <div class="breadcrumb">
                <a href="/" class="btn btn-home"><span class="hide">HOME</span></a>
                <ul>
                    <li>수강/열람신청</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <!-- editor css -->
        <link rel="stylesheet" href="/assets/css/editor.css">

        <div class="sub-conbox edu-conbox inner-layer">
            <div class="btn-wrap text-right">
                <a href="{{ route('education') }}" class="btn btn-type1 color-type1"><img src="/assets/image/sub/ic_view.png" alt="">다른 교육 보기</a>
            </div>
            <div class="edu-view-wrap">
                <div class="edu-view-conbox">
                    <span class="cate {{ ($education->category ?? '') == 'Z' ? 'etc' : ''}}">
                        {{ $educationConfig['category'][$education->category ?? ''] ?? '' }} {{ ($education->category ?? '') == 'A' ? ' - '.$educationConfig['gubun'][$education->gubun ?? ''] : ''}}
                    </span>
                    <form action="{{ route('education.data') }}" id="check-frm" method="post" onsubmit="return false;" data-case="education-check" data-sid="{{ request()->esid ?? 0 }}">
                        <input type="hidden" name="esid" value="{{ request()->esid ?? 0 }}" readonly>
                        <button type="submit" class="btn btn-small color-type1 full-right">교육 신청</button>
{{--                    <a href="{{ route('education.detail.upsert',['esid'=>request()->esid ?? 0]) }}" class="btn btn-small color-type1 full-right call-popup" data-popup_name="education-upsert" data-width="850" data-height="900">교육 신청</a>--}}
                    </form>

                    <p class="tit">{{ $education->title ?? '' }}</p>

                    @if(thisUser())
                        <div class="btn-wrap">
                            <a href="javascript:;" class="change-heart btn btn-small btn-line btn-like color-type2 {{ !empty($education->getHeart($education->sid) ) ? 'on': '' }}" data-esid="{{ $education->sid }}">관심교육</a>
                        </div>
                    @endif

{{--                    <div class="btn-wrap">--}}
{{--                        <a href="javascript:;" class="btn btn-small btn-line btn-like color-type2 on">관심교육</a>--}}
{{--                        <a href="javascript:;" class="btn btn-small btn-line btn-like color-type2">관심교육</a>--}}
{{--                    </div>--}}

                    <div class="dl-contents">
                        <dl>
                            <dt>교육신청</dt>
                            <dd>
                                @if(($education->regist_limit_yn ?? '') == 'N')
                                    상시신청가능
                                @else
                                    {{ $education->regist_sdate ? $education->regist_sdate->format('Y-m-d') : '' }} ~ {{ $education->regist_edate ? $education->regist_edate->format('Y-m-d') : '' }}
                                @endif
                            </dd>
                        </dl>
                        <dl>
                            <dt>교육수강</dt>
                            <dd>
                                @if(($education->edu_limit_yn ?? '') == 'N')
                                    상시수강가능
                                @else
                                    {{ $education->edu_sdate ? $education->edu_sdate->format('Y-m-d') : '' }} ~ {{ $education->edu_edate ? $education->edu_edate->format('Y-m-d') : '' }}
                                @endif
                            </dd>
                        </dl>
                        <dl>
                            <dt>금액</dt>
                            <dd>
                                @if(($education->free_yn ?? '') == 'Y')
                                    무료
                                @else
                                    {{ number_format($education->cost) }} 원
                                @endif
                            </dd>
                        </dl>
                        @if(($education->condition_yn ?? '') == 'Y')
                        <dl>
                            <dt>신청조건</dt>
                            <dd>
                                {{ $education->selfEducation($education->pre_esid ?? 0)->title ?? '' }} 교육 이수한 사람
                            </dd>
                        </dl>
                        @endif
                    </div>
                    <div class="edu-info">
                        <h4 class="edu-tit type1">교육 소개</h4>
                        <div class="editors-contents">
                            {!! $education->contents ?? '' !!}
                        </div>
                        <h4 class="edu-tit type2">교육 이수 조건</h4>
                        <ul class="edu-info-list">
                            <li>강의 영상 <br>100% 시청</li>
                            @if(($education->quiz_yn ?? '') == 'Y')
                            <li>퀴즈 풀이 합격 조건 <br><strong class="text-red">{{ $education->pass_cnt ?? 0 }}/{{ $education->quiz_cnt ?? 0 }}</strong></li>
                            @endif
                            @if(($education->survey_yn ?? '') == 'Y')
                            <li>설문 참여</li>
                            @endif
                            <li>이수완료</li>
                        </ul>
                        @if(($education->quiz_yn ?? '') == 'Y')
                        <div class="help-text text-right mt-10">
                            퀴즈는 합격 하실 때까지 재시험 가능합니다.
                        </div>
                        @endif
                    </div>
                </div>
                <div class="edu-view-conbox">
                    <h4 class="edu-tit type3">강의 목록</h4>
                    <ul class="edu-list">
                        @foreach($education->lectures as $val)
                        <li>
                            <a href="javascript:;">
                                <span class="subject ellipsis">{{ $val->title ?? '' }}</span>
                                <span class="name">{{ $val->name_kr ?? '' }} ({{ $val->sosok_kr ?? '' }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const form = '#check-frm';
        const dataUrl = '{{ route('education.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('tr').data('sid');
        }

        $(document).on('click', '.btn-del', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'workshop-delete',
            };

            if (confirm('삭제 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }
        });

        $(document).on('click', '.change-hide', function() {
            const ajaxData = {
                'sid': getPK(this),
                'case': 'change-hide',
                'target': $(this).val(),
            };

            if (confirm('노출여부를 변경 하시겠습니까?')) {
                callAjax(dataUrl, ajaxData);
            }else{
                window.reload();
            }
        });

        defaultVaildation();

        $(form).validate({
            rules: {
                esid: {
                    isEmpty: true,
                },
            },
            messages: {
                esid: {
                    isEmpty: '교육 강의번호가 없습니다. 관리자에게 문의해주세요.',
                },
            },
            submitHandler: function () {
                let ajaxData = formSerialize(form);

                callbackAjax(dataUrl, ajaxData, function(data, error) {
                    if (data) {
                        if (data.result['res'] == 'notCondition') {
                            alert(data.result['msg']);
                            return false;
                        }

                        let ajaxData = formSerialize(form);
                        ajaxData.case = 'sac-check';
                        if(confirm("교육을 신청하시겠습니까?")){
                            callAjax(dataUrl, ajaxData, true);
                        }else{
                            return false;
                        }

                    }

                }, true);

            }
        });

        $(document).on('click', '.change-heart', function() {
            const ajaxData = {
                'esid': $(this).data('esid'),
                'case': 'change-heart',
                'target': $(this).hasClass('on') ? 'Y':'N',
            };

            callAjax(dataUrl, ajaxData);
        });
    </script>
@endsection
