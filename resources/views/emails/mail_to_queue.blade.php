@extends('emails.layout')

@section('content')
<tr>
    <td style="background: #ffffff; padding: 8px 40px 32px; text-align: left; font-family: Georgia, 'Times New Roman', serif; font-size: 16px; line-height: 1.7; color: #333333;">
        {!! nl2br($email_message ?? '') !!}
        @if(!empty($track_code))
        <img src="{{ route('front.email-track', $track_code) }}.png" width="1" height="1" style="display:block;">
        @endif
    </td>
</tr>
@endsection
