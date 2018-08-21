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
                <td style="padding-top: 1rem">
                    You haven't read <em>{{ $email_data['book_title'] }}</em> in {{ $email_data['days_diff'] }} days.
                    {{ $email_data['book_author'] }} is eagerly awaiting your feedback. Ready to jump back in?</td>
            </tr>

            <tr>
                <td style="padding-top:1.2rem;padding-bottom:0.5rem;">
                    <a href="{{ $email_data['book_link'] }}" style="text-decoration: none;
                        border: 1px solid #095a98;
                        background: #095a98;
                        color: #fff;
                        padding-right: 1.1rem;
                        padding-left: 1.1rem;
                        padding-top: 0.5rem;
                        padding-bottom: 0.5rem;
                        -webkit-text-size-adjust: none;
                        line-height: 1.5;
                        border-radius: .2rem;
                        margin-right: 10px">
                        Start Reading
                    </a>
                </td>
            </tr>
        </table>
    </body>
</html>
