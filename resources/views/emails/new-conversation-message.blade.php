<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #F2F4F6;">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <table width="570" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF; border-radius: 4px; border: 1px solid #EDEFF2;">
                    <tr>
                        <td style="padding: 25px 35px; text-align: center; background-color: #862736;">
                            <span style="font-size: 16px; font-weight: bold; color: #FFFFFF; text-decoration: none;">Forfatterskolen</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 35px;">
                            <p style="margin: 0 0 15px; font-size: 16px; color: #2F3133;">
                                Hei {{ $recipientName }},
                            </p>
                            <p style="margin: 0 0 15px; font-size: 14px; color: #74787E;">
                                Du har mottatt en ny melding fra <strong>{{ $senderName }}</strong> i samtalen <strong>{{ $conversation->subject }}</strong>:
                            </p>
                            <div style="padding: 15px; background-color: #F6F4F0; border-left: 3px solid #862736; margin: 0 0 20px; font-size: 14px; color: #2F3133;">
                                {{ $messagePreview }}
                            </div>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 15px 0;">
                                        <a href="{{ $conversationUrl }}" style="display: inline-block; padding: 10px 30px; background-color: #862736; color: #FFFFFF; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold;">
                                            Se samtalen
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 35px; text-align: center; font-size: 12px; color: #AEAEAE; border-top: 1px solid #EDEFF2;">
                            &copy; {{ date('Y') }} Forfatterskolen
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
