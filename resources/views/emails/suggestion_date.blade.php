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
        <td style="padding-top: .3rem"><strong><em>{{ $sender }}</em></strong> suggested new coaching timer dates: <br/>
            <?php
                $suggested_dates = json_decode($suggested_dates);
            ?>
            @for($i =0; $i <= 2; $i++)
                {{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($suggested_dates[$i]) }} <br>
            @endfor
        </td>
    </tr>
</table>
</body>
</html>
