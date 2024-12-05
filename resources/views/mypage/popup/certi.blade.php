@extends('layouts.popup-layout')

@section('addStyle')
    <style>
        /* 인쇄할 때만 적용될 스타일 */
        @media print {
            body * {
                visibility: hidden; /* 페이지 전체를 숨김 */
            }
            .print-conbox * {
                visibility: visible; /* 인쇄할 영역만 보이도록 */
            }
            .no-print {
                display: none;
            }
        }
        .print-wrap{
            width: 600px;
            max-width: 600px;
            margin: 0 auto;
        }
        .btn-type1{
            min-width: 115px;
        }
        .print-conbox{
            position: relative;
            padding: 70px 50px;
            border: 1px solid #eee;
        }
        .print-conbox > img{
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: -1;
            transform: translate(-50%,-50%);
        }
        .print-conbox .tit{
            margin-bottom: 60px;
            font-size: 55px;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-align: center;
        }
        .print-conbox .dl-contents{
            margin-bottom: 80px;
        }
        .print-conbox .dl-contents dl{
            gap: 5px;
        }
        .print-conbox .dl-contents dt,
        .print-conbox .dl-contents dd{
            padding: 0;
            font-size: 18px;
            line-height: 1.6;
            color: #282828;
        }
        .print-conbox .dl-contents dt{
            width: auto;
            font-weight: 700;
        }
        .print-conbox p{
            font-size: 17px;
            line-height: 1.5;
        }
        .print-conbox .date{
            display: block;
            margin-top: 40px;
            font-weight: 600;
            color: #222222;
        }
        .print-logo{
            margin-top: 40px;
            font-size: 25px;
            font-weight: 600;
            letter-spacing: 0.3em;
            text-align: center;
        }
        .print-logo > img{
            position: relative;
            z-index: -1;
            margin-left: -80px;
        }
    </style>
@endsection

@section('contents')
    <div class="print-wrap">
        <div class="print-conbox">
            <img src="/assets/image/sub/img_print_logo.png" alt="">
            <h1 class="tit font-serif">이 수 증</h1>
            <div class="dl-contents">
                <dl>
                    <dt>성명 : </dt>
                    <dd>{{ thisUser()->name_kr ?? '' }}</dd>
                </dl>
                <dl>
                    <dt>소속 : </dt>
                    <dd>{{ thisUser()->sosok_kr ?? '' }}</dd>
                </dl>
                <dl>
                    <dt>이수과정 : </dt>
                    <dd>{{ $sac_info->edu->title ?? '' }}</dd>
                </dl>
                <dl>
                    <dt>이수일자 : </dt>
                    <dd>{{ $sac_info->getLectureViewMinCreatedAt() ?? '' }} ~ {{ $sac_info->complete_at->format('Y.m.d') ?? '' }}</dd>
                </dl>
            </div>
            <p class="text-center">
                위 사람은 대한진단검사의학회에서 주관하는 <br>
                교육과정을 이수하였기에 이 증서를 수여합니다.
                <strong class="date">{{ $sac_info->complete_at->format('Y.m.d') ?? '' }}</strong>
            </p>
            <div class="print-logo font-serif">
                대한진단검사의학회 <img src="/assets/image/sub/img_stamp.png" alt="대한진단검사의학회 직인">
            </div>
        </div>
    </div>
    <div class="btn-wrap text-center no-print">
        <a href="javascript:;" class="btn btn-type1 color-type2" onclick="self.close();">취소</a>
        <a href="javascript:;" class="btn btn-type1 color-type5" onclick="print_self();">인쇄하기</a>
    </div>
@endsection

@section('addScript')
    <script>
        function print_self(){
            if (isMobile()) {
                alert("이수증 출력은 PC에서만 가능합니다.");
                return false;
            }else{
                print();
            }
        }

    </script>
@endsection
