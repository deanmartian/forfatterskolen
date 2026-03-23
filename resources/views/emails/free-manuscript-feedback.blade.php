@extends('emails.layout')

@section('content')
<tr>
    <td style="background: #ffffff; padding: 8px 40px 32px; text-align: left; font-family: Georgia, 'Times New Roman', serif; font-size: 16px; line-height: 1.7; color: #333333;">
        {!! $email_content ?? '' !!}
    </td>
</tr>
@endsection
