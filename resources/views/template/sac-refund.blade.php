
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0;padding: 0;">
<table style="width:650px;max-width:650px;margin: 0 auto;padding:0;border:1px solid #ddd;border-collapse: collapse;border-spacing:0;box-sizing:border-box;">
    <tbody>
    <tr>
        <td style="padding: 0;text-align: center;text-align: center;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 20px;color: #050505;">
            <img src="{{ env('APP_URL') }}/assets/image/mail/mail_header.jpg" alt="대한진단검사의학회" style="display: inline-block;border:0 none;vertical-align: top;" />
        </td>
    </tr>
    <tr>
        <td style="padding: 30px 50px 50px;font-size: 26px;line-height: 1.7;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;box-sizing:border-box;">
            <table style="width: 100%;border-collapse: collapse;border-spacing: 0;">
                <tbody>
                <tr>
                    <th scope="col" style="font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 30px;font-weight: 400;line-height: 1.2;color: #4d4d4d;text-align: center;letter-spacing: -0.05em;">
                        <strong style="font-weight: 700;color: #191919;">{{ $sac_info->edu->title ?? '' }}</strong> <br/>신청 비용 환불 처리 안내드립니다.
                    </th>
                </tr>
                <tr>
                    <td style="padding-top: 30px;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.5;">
                        아래 교육 내역에 대하여 환불 처리가 완료되었습니다. <br/>
                        교육 관련 문의가 있으신 경우 대한진단검사의학회로 문의하여 주시기 바랍니다.
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;">
                        <table style="width: 100%;border-collapse: collapse;border-spacing: 0;">
                            <colgroup>
                                <col style="width: 160px;">
                                <col style="width: 390px;">
                            </colgroup>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    교육명
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->edu->title ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    이름
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->user->name_kr ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    소속
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->user->sosok_kr ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    신청일
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->created_at ? $sac_info->created_at->format('Y.m.d') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    금액
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->tot_pay ? number_format($sac_info->tot_pay) : 0 }}원
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="padding: 10px 15px;background-color: #f4f4f4;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;font-weight: 700;color: #444444;line-height: 1.3;text-align: center;">
                                    환불계좌
                                </th>
                                <td style="padding: 10px 15px;border-top: 1px solid #dddddd;border-bottom: 1px solid #dddddd;border-left: 1px solid #dddddd;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;font-size: 14px;color: #444444;line-height: 1.3">
                                    {{ $sac_info->bank_name ?? '' }} {{ $sac_info->account_no ?? '' }} (예금주 : {{ $sac_info->account_name ?? '' }})
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 100px;text-align: center;">
                        <a href="{{ env('APP_URL') }}/" target="_blank" style="display: inline-block;margin: 0 2px;vertical-align: top;"><img src="{{ env('APP_URL') }}/assets/image/mail/btn_mail_website.png" alt="홈페이지 바로가기" ></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <img src="{{ env('APP_URL') }}/assets/image/mail/mail_footer.jpg" alt="대한진단검사의학회. 주소 : (04323) 서울시 용산구 한강대로 372 (동자동) 센트레빌아스테리움서울 A동 1105호. Tel : +82-2-795-9914  |  Fax : +82-790-4760  |  E-mail : {{ env('PUBLIC_MAIL') }}. 사업자등록번호 : 106-82-31239  |  대표 : 전사일  |  개인정보관리책임자 : 김희순" style="display: inline-block;border:0 none;vertical-align: top;" />
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>