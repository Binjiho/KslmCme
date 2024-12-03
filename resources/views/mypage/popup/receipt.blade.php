@extends('layouts.popup-layout')

@section('addStyle')
    <style>
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
            margin-bottom: 35px;
            font-size: 55px;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-align: center;
        }
        .print-conbox .name{
            margin-bottom: 55px;
            font-size: 16px;
            font-weight: 500;
        }
        .print-conbox .name > strong{
            display: inline-block;
            margin: 0 10px;
            padding-bottom: 5px;
            font-size: 20px;
            font-weight: 700;
            border-bottom: 2px solid #dcdcdc;
        }
        .print-conbox .table-wrap{
            margin-bottom: 50px;
            border-color: #747474;
        }
        .print-conbox .cst-table th,
        .print-conbox .cst-table td{
            padding: 10px 20px;
            border-left: 0;
            border-right: 0;
            border-color: #cdcdcd;
            font-size: 16px;
            line-height: 1.3;
        }
        .print-conbox .cst-table th{
            background-color: #fafafa;
            color: #222222;
            font-weight: 500;
        }
        .print-conbox p{
            font-size: 17px;
            line-height: 1.5;
        }
        .print-conbox .date{
            display: block;
            margin-top: 20px;
            font-weight: 600;
            color: #222222;
        }
        .print-logo{
            margin-top: 80px;
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
    @php
        function numberToKorean($number) {
        $units = ['', '만', '억'];
        $numStr = (string)$number;
        $length = strlen($numStr);
        $koreanNumber = '';

        $unitPos = 0;
        while ($length > 0) {
            $part = substr($numStr, max(0, $length - 4), min(4, $length));
            $length -= 4;

            if ($part !== '0000') {
                $koreanPart = convertPartToKorean((int)$part);
                $koreanNumber = $koreanPart . $units[$unitPos] . $koreanNumber;
            }
            $unitPos++;
        }

        return trim($koreanNumber) . '원';
    }

    function convertPartToKorean($number) {
        $koreanDigits = ['','일','이','삼','사','오','육','칠','팔','구'];
        $positions = ['천', '백', '십', ''];

        $result = '';
        $i = 0;
        foreach (str_split(str_pad($number, 4, '0', STR_PAD_LEFT)) as $digit) {
            if ($digit !== '0') {
                $result .= $koreanDigits[$digit] . $positions[$i];
            }
            $i++;
        }

        return $result;
    }
    @endphp
    <div class="print-wrap">
        <div class="print-conbox">
            <img src="/assets/image/sub/img_print_logo.png" alt="">
            <h1 class="tit font-serif">영 수 증</h1>
            <div class="name text-right">
                <strong>{{ thisUser()->name_kr ?? '' }}</strong> 귀하
            </div>
            <div class="table-wrap">
                <table class="cst-table">
                    <caption class="hide">영수증</caption>
                    <colgroup>
                        <col style="width: 35%;">
                        <col>
                    </colgroup>
                    <tbody>
                    <tr>
                        <th scope="row">교육명</th>
                        <td class="text-left">
                            {{ $sac_info->edu->title ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">금액</th>
                        <td class="text-left">
                            {{ numberToKorean($sac_info->edu->cost) ?? 0 }}정 (&#8361;{{ $sac_info->edu->cost ? number_format($sac_info->edu->cost) : 0 }})
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-center">
                상기 금액을 정히 영수함.
                <strong class="date">{{ date('Y.m.d') }}</strong>
            </p>
            <div class="print-logo font-serif">
                대한진단검사의학회 <img src="/assets/image/sub/img_stamp.png" alt="대한진단검사의학회 직인">
            </div>
        </div>
    </div>
    <div class="btn-wrap text-center">
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
