<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ env('APP_NAME') }}</title>
</head>

<body>

<table style="width:650px;max-width:650px;margin: 0 auto;padding:0;border:1px solid #ddd; border-collapse: collapse;border-spacing:0;">
    <tbody>

    <tr>
        <td style="padding: 40px;color: #2a2a66;font-size: 14px;line-height: 25px;font-family: 'Malgun Gothic', '맑은고딕', '돋움', 'dotum', sans-serif;">
            {!! $mail['contents'] !!}
        </td>
    </tr>

    @include('admin.mail.template.common-template')

    </tbody>
</table>

</body>
</html>
