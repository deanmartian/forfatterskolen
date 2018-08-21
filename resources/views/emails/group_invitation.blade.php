<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html charset=UTF-8" />
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" border="0" style='font-weight: 300!important;
        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"'>
            <tr> 
                <td style="padding-top: 1rem">Hi {{ $email_data['receiver'] }},</td>
            </tr>
            <tr> 
                <td style="padding-top: .3rem">{{ $email_data['sender'] }} has invited you to join "{{ $email_data['group_name'] }}" on Forfatterskolen.</td>
            </tr>
            @if($email_data['msg'])
                <tr>
                    <td style="padding-top: .3rem">
                        <p style="margin: 10px 0; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; padding: 10px 0;">
                            {{ $email_data['msg'] }}
                        </p>
                    </td>
                </tr>
            @endif
            <tr> 
                <td style="padding-top: .3rem">Would you like to,</td> 
            </tr>
            <tr>
                <td style="padding-top:1.2rem;padding-bottom:0.5rem;">
                    <a href="{{ url("/account/private-groups/invitation/1/".$email_data['token']) }}" style="text-decoration: none;
                    color: #fff;
                    background: #17a2b8;
                    border-color: #17a2b8;
                    padding-right: 1.1rem;
                    padding-left: 1.1rem;
                    padding-top: 0.5rem;
                    padding-bottom: 0.5rem;
                    -webkit-text-size-adjust: none;
                    line-height: 1.5;
                    border-radius: .2rem;
                    margin-right: 10px">
                        Accept
                    </a>
                    <a href="{{ url("/account/private-groups/invitation/2/".$email_data['token']) }}" style="text-decoration: none;
                                    color: #17a2b8;
                                    background: transparent;
                                    padding-right: 1.1rem;
                                    padding-left: 1.1rem;
                                    padding-top: 0.5rem;
                                    padding-bottom: 0.5rem;
                                    -webkit-text-size-adjust: none;
                                    line-height: 1.5;
                                    border-radius: .2rem;
                                    border: 1px solid #17a2b8;">
                        Decline
                    </a>
                </td>
            </tr>
            <tr>
                <small>*Note: you must be logged in before performing the action.</small>
            </tr>
        </table>
    </body>
</html>