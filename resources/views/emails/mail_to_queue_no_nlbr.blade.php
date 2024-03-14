<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    <style>
        td p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" style='font-weight: 300!important;"'>
    <tr>
        <td style="padding-top: .3rem">{!! $email_message !!} </td>
    </tr>
</table>

<a href="{{ route('front.email-track', $track_code) }}" data-saferedirecturl="{{ route('front.email-track', $track_code) }}"
style="margin-top: 20px; display: block">
    Trykk her for å bekrefte at du har lest meldingen
</a>

<img src="{{ route('front.email-track', $track_code) }}.png" width="1" height="1">
</body>
</html>
