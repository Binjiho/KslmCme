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
            <img src="{{ asset('/assets/image/mail/mail_header.jpg') }}" alt="대한진단검사의학회" style="display: inline-block;border:0 none;vertical-align: top;" />
        </td>
    </tr>
    <tr>
        <td style="padding: 40px;color: #2a2a66;font-size: 14px;line-height: 25px;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;">
            {!! $mail['contents'] !!}
        </td>
    </tr>

    @include('admin.mail.template.common-template')

    <tr>
        <td>
            <img src="{{ asset('/assets/image/mail/mail_footer.jpg') }}" alt="대한진단검사의학회. 주소 : (04323) 서울시 용산구 한강대로 372 (동자동) 센트레빌아스테리움서울 A동 1105호. Tel : +82-2-795-9914  |  Fax : +82-790-4760  |  E-mail : kscp2@kams.or.kr. 사업자등록번호 : 106-82-31239  |  대표 : 전사일  |  개인정보관리책임자 : 김희순" style="display: inline-block;border:0 none;vertical-align: top;" />
        </td>
    </tr>
    </tbody>
</table>

</body>
</html>
