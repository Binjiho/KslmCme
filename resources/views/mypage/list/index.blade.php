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
                    <li>온라인 강의실</li>
                </ul>
            </div>
        </div>
    </article>

    <article class="sub-contents">
        <div class="sub-conbox inner-layer">

            @include('layouts.include.sub-menu-wrap')

            <div class="sch-result-list">
                <ul class="board-list lecture mypage">
                    <li class="list-head">
                        <div class="bbs-no bbs-col-xs">번호</div>
                        <div class="bbs-tit n-bar">교육명</div>
                        <div class="bbs-date bbs-col-xs2 n-bar">신청일</div>
                        <div class="bbs-col-xs2">이수여부</div>
                        <div class="bbs-col-xs2">결제 상태</div>
                        <div class="bbs-manage bbs-col-s n-bar">관리</div>
                    </li>

                    @forelse($list as $idx => $row)
{{--                        @continue(empty($row->edu)) --}}
                        <li data-sid="{{ $row->sid }}">
                            <div class="bbs-no bbs-col-xs">{{ $row->seq }}</div>
                            <div class="bbs-tit text-left n-bar">
                                {{ $row->edu->title ?? '' }}
                            </div>
                            <div class="bbs-date bbs-col-xs2 n-bar">{{ $row->created_at->format('Y.m.d') ?? '' }}</div>
                            <div class="bbs-col-xs2">{{ $sacConfig['edu_status'][$row->edu_status] ?? '' }}</div>
                            <div class="bbs-col-xs2">
                                @if(!empty($row->del_request))
                                    {{ $sacConfig['del_request'][$row->del_request] ?? '' }}
                                @else
                                    {{ $sacConfig['pay_status'][$row->pay_status] ?? '' }}
                                @endif
                            </div>
                            <div class="bbs-manage bbs-col-s n-bar">
                                @if(($row->del_request ?? '') == 'C')
                                    {{ $row->deleted_at->format('Y.m.d') ?? '' }}
                                @elseif(($row->del_request ?? '') == 'I')
                                    
                                @elseif(($row->pay_status ?? '') == 'I')
                                    <a href="{{ route('mypage.payinfo',['ssid'=>$row->sid]) }}" class="btn btn-small btn-pay-info call-popup" data-popup_name="payinfo-pop" data-width="850" data-height="600">입금정보</a>
                                @elseif(($row->pay_status ?? '') == 'C' /*취소 처리 중()에는 영수증 노출되지 않음*/ )
                                    무통장입금
                                    <a href="{{ route('mypage.receipt',['ssid'=>$row->sid]) }}" class="btn btn-small btn-receipt call-popup" data-popup_name="receipt-pop" data-width="850" data-height="800">영수증</a>
                                @endif
                                {{-- WARN : 수강 기간이 시작되기 전에는 취소 가능 --}}
                                @if( (date('Y-m-d') < $row->edu->edu_sdate->format('Y-m-d')) && $row->del_request != 'I')
                                    <a href="javascript:;" class="btn btn-small btn-cancel" data-sid="{{ $row->sid ?? 0 }}" data-method="{{ $row->pay_method ?? 'F' }}">취소</a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li>
                            신청한 교육 정보가 없습니다.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </article>
@endsection

@section('addScript')
    <script>
        const form = '#check-frm';
        const dataUrl = '{{ route('mypage.data') }}';

        const getPK = (_this) => {
            return $(_this).closest('li').data('sid');
        }

        $(document).on('click', '.btn-cancel', function() {
            const _method = $(this).data('method');
            // const _sid = getPK(this);

            if(_method === 'F'){
                _case = 'sac-delete-free';

                const ajaxData = {
                    'sid': getPK(this),
                    'case': _case,
                };
                if (confirm('삭제 하시겠습니까?')) {
                    callAjax(dataUrl, ajaxData);
                }
            }else{
                const _href = "{{ route('mypage.cancle') }}" + "?ssid=" + getPK(this);
                const popupY = (window.screen.height / 2) - (850 / 2);
                const popupX = (window.screen.width / 2) - (600 / 2);

                window.open(_href, 'canclePop', 'status=no, height=' + 850 + ', width=' + 600 + ', left=' + popupX + ', top=' + popupY);
            }

        });

        // $(document).on('click', '.btn_cancle', function(){
        //     let ajaxData = formSerialize(form);
        //
        //     callbackAjax(dataUrl, ajaxData, function(data, error) {
        //         if (data) {
        //             if (data.result['res'] == 'notCondition') {
        //                 alert(data.result['msg']);
        //                 return false;
        //             }
        //
        //             let ajaxData = formSerialize(form);
        //             ajaxData.case = 'sac-check';
        //             if(confirm("교육을 신청하시겠습니까?")){
        //                 callAjax(dataUrl, ajaxData, true);
        //             }else{
        //                 return false;
        //             }
        //
        //         }
        //
        //     }, true);
        // });
    </script>
@endsection
