<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Tilbakestill passordet</title>
    <style type="text/css">
        @media only screen and (max-width: 500px) {
            .pr-card { padding: 30px 22px !important; }
            .pr-button { display: block !important; width: 100% !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; width:100%; background-color:#f9edef; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f9edef; padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:520px; background:#ffffff; border-radius:16px; box-shadow:0 10px 40px rgba(134,39,54,0.10);">
                    <tr>
                        <td class="pr-card" style="padding:48px 44px;">
                            {{-- Logo --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:24px;">
                                        <a href="https://www.forfatterskolen.no" target="_blank" style="text-decoration:none;">
                                            <img src="https://www.forfatterskolen.no/images/logo.png" alt="Forfatterskolen" style="height:40px; display:block;">
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Wine red icon circle --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="width:64px; height:64px; background-color:#862736; border-radius:50%; text-align:center; vertical-align:middle; color:#ffffff; font-size:30px; line-height:64px;">
                                                    🔒
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Heading --}}
                            <h1 style="margin:0 0 14px; padding:0; font-family:Georgia,'Playfair Display',serif; font-size:24px; font-weight:700; color:#1a1a1a; text-align:center; line-height:1.3;">
                                Tilbakestill passordet ditt
                            </h1>

                            {{-- Intro --}}
                            <p style="margin:0 0 28px; padding:0; font-size:15px; line-height:1.6; color:#5a5550; text-align:center;">
                                Vi mottok en forespørsel om å tilbakestille passordet ditt på Forfatterskolen.
                                Klikk på knappen nedenfor for å lage et nytt passord.
                            </p>

                            {{-- Button --}}
                            @if (isset($actionUrl))
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td align="center" style="padding-bottom:32px;">
                                            <a href="{{ $actionUrl }}" target="_blank" class="pr-button" style="display:inline-block; padding:14px 36px; background-color:#862736; color:#ffffff; font-size:15px; font-weight:600; text-decoration:none; border-radius:10px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
                                                Lag nytt passord
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Outro --}}
                            <p style="margin:0 0 14px; padding:0; font-size:14px; line-height:1.6; color:#5a5550; text-align:center;">
                                Om du ikke ba om å tilbakestille passordet, kan du trygt ignorere denne e-posten.
                            </p>

                            <p style="margin:0 0 28px; padding:0; font-size:14px; line-height:1.6; color:#5a5550; text-align:center;">
                                Lenken er gyldig i 24 timer.
                            </p>

                            {{-- Fallback URL --}}
                            @if (isset($actionUrl))
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #f0e8ea; padding-top:20px;">
                                    <tr>
                                        <td>
                                            <p style="margin:20px 0 8px; padding:0; font-size:12px; line-height:1.5; color:#999;">
                                                Hvis knappen ikke fungerer, kopier og lim inn denne lenken i nettleseren din:
                                            </p>
                                            <p style="margin:0; padding:0; font-size:11px; line-height:1.5; color:#862736; word-break:break-all;">
                                                <a href="{{ $actionUrl }}" target="_blank" style="color:#862736; text-decoration:underline;">{{ $actionUrl }}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- Footer --}}
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:520px;">
                    <tr>
                        <td align="center" style="padding:24px 20px; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
                            <p style="margin:0; padding:0; font-size:12px; line-height:1.6; color:#999;">
                                Spørsmål? Svar på denne e-posten eller ring 411 23 555<br>
                                Forfatterskolen · Lihagen 21, 3029 Drammen
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
