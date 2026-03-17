{{-- Egendefinert melding fra admin --}}
@if(!empty($customMessage))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
    <tr><td style="border-left:3px solid #862736;padding:12px 20px;">
        <div style="font-size:14px;color:#5a5550;line-height:1.7;font-family:-apple-system,sans-serif;">{!! $customMessage !!}</div>
    </td></tr>
</table>
@endif
