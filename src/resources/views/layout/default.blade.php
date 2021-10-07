<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }}</title>
    <style type="text/css">
        #outlook a {padding:0}
        body {width:100% !important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;margin:0;padding:0;background:#efefef;font-family:Arial,Verdana,sans-serif}
        #backgroundTable {margin:0;padding:0;width:100% !important;line-height:100% !important}
        img {outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;max-width:100%;height:auto}
        a img {border:none}
        p {margin:1em 0}
        table td {border-collapse:collapse}
        table {border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt}
        small {font-size:10px}
        small a {text-decoration:underline}
        .bg-white {background:#FFF}
        .mentions {text-align:center;padding:15px;color:#656565}
        .mentions a {color:#656565}
        .header {padding: 15px;}
        .logo {font-size:24px;font-weight:bold;text-decoration:none;color:#0d6aad}
        .content {padding:0 15px 15px 15px;color:#333;font-size:16px;line-height:22px}
        @media only screen and (max-device-width: 480px) {
            .container {width:100% !important;padding-right:10px;padding-left:10px}
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
    <tr>
        <td align="center">
            <table cellpadding="0" cellspacing="0" border="0" align="center" width="620" class="container">
                <tr>
                    <td class="mentions">
                        <small>
                            {!! __('boilerplate-email-editor::email.intro', ['email' => $sender_email ?? config('boilerplate.email-editor.from.address')]) !!}
                        </small>
                    </td>
                </tr>
                <tr>
                    <td class="bg-white header">
                        <a href="{{ url('/') }}" target="_blank" class="logo">
                            {{ config('app.name') }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="bg-white content">
                        {!! $content !!}
                    </td>
                </tr>
                <tr>
                    <td class="mentions">
                        <small>
                            {{ __('boilerplate-email-editor::email.automatic') }}
                        </small>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
