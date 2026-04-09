{{-- Impersonation banner — vises alltid øverst når en admin er logget inn
     som en annen bruker via ImpersonateController. Inkluderes i frontend/layout,
     editor/layout, og backend/layout slik at advarselen ALDRI kan bli glemt
     uansett hvilken portal admin havner på. --}}
@if(session('impersonator_id'))
    @php
        $impersonatedUser = auth()->user();
        $impersonatorId = session('impersonator_id');
        $impersonatorUser = \App\User::find($impersonatorId);
    @endphp
    <div style="background:#dc2626;color:#fff;padding:10px 20px;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:14px;line-height:1.5;box-shadow:0 2px 8px rgba(0,0,0,0.15);position:sticky;top:0;z-index:9999;">
        <span style="display:inline-flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:center;">
            <span><strong>⚠️ Du er logget inn som {{ $impersonatedUser?->first_name }} {{ $impersonatedUser?->last_name }}</strong> ({{ $impersonatedUser?->email }})</span>
            @if($impersonatorUser)
                <span style="opacity:0.85;">Original admin: {{ $impersonatorUser->first_name }} {{ $impersonatorUser->last_name }}</span>
            @endif
            <a href="https://admin.forfatterskolen.no/impersonate/stop" style="display:inline-block;background:#fff;color:#dc2626;padding:5px 14px;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px;">
                ← Gå tilbake til admin
            </a>
        </span>
    </div>
@endif
