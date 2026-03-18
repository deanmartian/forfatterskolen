@extends('emails.layout')

@section('content')
<tr><td style="background:#fff;padding:40px 40px 32px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:16px;line-height:1.7;color:#1a1a1a;">
    {!! $email_message !!}
</td></tr>
@if(isset($unsubscribe_url))
<tr><td style="background:#fff;padding:0 40px 20px;text-align:center;">
    <a href="{{ $unsubscribe_url }}" style="font-size:11px;color:#b5b0ab;text-decoration:none;">Meld deg av nyhetsbrev</a>
</td></tr>
@endif
@endsection
