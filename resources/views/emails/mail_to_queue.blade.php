@extends('emails.layout')

@section('content')
<tr>
    <td style="background: #ffffff; padding: 8px 40px 32px; text-align: left; font-family: Georgia, 'Times New Roman', serif; font-size: 16px; line-height: 1.7; color: #333333;">
        @php
            $msg = $email_message ?? '';
            // Auto-convert newlines to <br> if content doesn't already have HTML tags
            if (!preg_match('/<(br|p|div|table|h[1-6])\b/i', $msg)) {
                $msg = nl2br(e($msg));
            }
        @endphp
        {!! $msg !!}
        @if(!empty($track_code))
        <img src="{{ route('front.email-track', $track_code) }}.png" width="1" height="1" style="display:block;">
        @endif
    </td>
</tr>
@endsection
