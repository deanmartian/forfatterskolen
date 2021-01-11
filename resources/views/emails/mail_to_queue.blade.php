<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    <style>
        td p {
            margin: 0
        }
    </style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" style='font-weight: 300!important;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"'>
    <tr>
        <td style="padding-top: .3rem">{!! nl2br($email_message) !!} </td>
    </tr>
</table>

<a href="{{ route('front.email-track', $track_code) }}" data-saferedirecturl="{{ route('front.email-track', $track_code) }}"
style="margin-top: 20px; display: block">
    Trykk her for å bekrefte at du har lest meldingen
</a>

<img src="{{ route('front.email-track', $track_code) }}.png" width="1" height="1">
</body>
</html>
