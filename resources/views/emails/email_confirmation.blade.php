<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" style='font-weight: 300!important;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"'>
    <tr>
        <td style="padding-top: 1rem">Hei {{ $email_data['name'] }},</td>
    </tr>
    <tr>
        <td style="padding-top: .3rem">Dette er en automatisk epost fra Forfatterskolen for å bekrefte at du eier denne epost adressen:  <strong>{{ $email_data['email'] }}</strong>.</td>
    </tr>
    <tr>
        <td style="padding-top:1.2rem;padding-bottom:0.5rem;">
            <a href="{{ route("front.email-confirmation",$email_data['token']) }}" style="text-decoration: none;
                    color: #fff;
                    background: #e83945;
                    border-color: #e83945;
                    padding-right: 1.1rem;
                    padding-left: 1.1rem;
                    padding-top: 0.5rem;
                    padding-bottom: 0.5rem;
                    -webkit-text-size-adjust: none;
                    line-height: 1.5;
                    border-radius: .2rem;
                    margin-right: 10px">
                Klikk her for å bekrefte epostadressen
            </a>
        </td>
    </tr>
    <tr>
        <td style="padding-top: .3rem">
            Hvis du mottok denne meldingen ved en feil, kan du se bort fra denne e-posten. Hvis du har spørsmål, vennligst gi oss beskjed via:  <strong>post@forfatterskolen.no</strong>.
        </td>
    </tr>
</table>
</body>
</html>
