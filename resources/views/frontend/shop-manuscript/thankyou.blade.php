@extends('frontend.layout')

@section('page_title', 'Ordrebekreftelse &rsaquo; Forfatterskolen')

@section('content')
<style>
@@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@@keyframes checkPop { 0% { transform: scale(0) rotate(-45deg); } 60% { transform: scale(1.2) rotate(0); } 100% { transform: scale(1) rotate(0); } }
@@keyframes confetti1 { 0% { transform: translateY(0) rotate(0); opacity:1; } 100% { transform: translateY(-80px) translateX(30px) rotate(360deg); opacity:0; } }
@@keyframes confetti2 { 0% { transform: translateY(0) rotate(0); opacity:1; } 100% { transform: translateY(-60px) translateX(-25px) rotate(-270deg); opacity:0; } }
@@keyframes confetti3 { 0% { transform: translateY(0) rotate(0); opacity:1; } 100% { transform: translateY(-90px) translateX(15px) rotate(200deg); opacity:0; } }
@@keyframes confetti4 { 0% { transform: translateY(0) rotate(0); opacity:1; } 100% { transform: translateY(-70px) translateX(-40px) rotate(310deg); opacity:0; } }
@@keyframes slideRight { from { width: 0; } to { width: 33.3%; } }
@@keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(134,39,54,0.3); } 50% { box-shadow: 0 0 0 12px rgba(134,39,54,0); } }
@@keyframes stepIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
@@keyframes penWrite { 0% { transform: translateX(0) rotate(-5deg); } 50% { transform: translateX(6px) rotate(5deg); } 100% { transform: translateX(0) rotate(-5deg); } }
@@keyframes dotBounce1 { 0%,80%,100% { transform: scale(0); } 40% { transform: scale(1); } }
@@keyframes dotBounce2 { 0%,80%,100% { transform: scale(0); } 50% { transform: scale(1); } }
@@keyframes dotBounce3 { 0%,80%,100% { transform: scale(0); } 60% { transform: scale(1); } }

.ty-anim1 { animation: fadeInUp 0.6s ease both; }
.ty-anim2 { animation: fadeInUp 0.6s ease 0.2s both; }
.ty-anim3 { animation: fadeInUp 0.6s ease 0.4s both; }
.ty-anim4 { animation: fadeInUp 0.6s ease 0.6s both; }
.ty-anim5 { animation: fadeInUp 0.6s ease 0.8s both; }
.ty-step { animation: stepIn 0.5s ease both; }
.ty-step:nth-child(1) { animation-delay: 0.6s; }
.ty-step:nth-child(2) { animation-delay: 0.9s; }
.ty-step:nth-child(3) { animation-delay: 1.2s; }
.ty-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f0f0f0; }

@@media (max-width: 640px) {
    .ty-bottom { flex-direction: column !important; }
}
</style>

<div style="max-width: 680px; margin: 0 auto; padding: 40px 20px;">

    {{-- SUKSESS-HEADER MED KONFETTI --}}
    <div class="ty-anim1" style="text-align: center; margin-bottom: 28px; position: relative;">
        <div style="position: absolute; top: 10px; left: 50%; margin-left: -70px;">
            <div style="position:absolute; width:8px; height:8px; background:#862736; border-radius:2px; animation:confetti1 1.5s ease 0.8s both;"></div>
            <div style="position:absolute; left:20px; width:6px; height:10px; background:#D4A574; border-radius:2px; animation:confetti2 1.3s ease 0.9s both;"></div>
            <div style="position:absolute; left:-15px; top:5px; width:7px; height:7px; background:#5DCAA5; border-radius:50%; animation:confetti3 1.4s ease 1s both;"></div>
            <div style="position:absolute; left:110px; width:6px; height:9px; background:#ED93B1; border-radius:2px; animation:confetti1 1.6s ease 1.1s both;"></div>
            <div style="position:absolute; left:90px; top:3px; width:8px; height:6px; background:#85B7EB; border-radius:2px; animation:confetti2 1.2s ease 0.7s both;"></div>
            <div style="position:absolute; left:130px; top:-5px; width:5px; height:8px; background:#FAC775; border-radius:50%; animation:confetti3 1.5s ease 0.85s both;"></div>
            <div style="position:absolute; left:-30px; top:-8px; width:7px; height:5px; background:#AFA9EC; border-radius:2px; animation:confetti4 1.4s ease 0.95s both;"></div>
            <div style="position:absolute; left:60px; top:-10px; width:5px; height:7px; background:#97C459; border-radius:2px; animation:confetti4 1.7s ease 1.05s both;"></div>
        </div>

        <div style="display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:50%; background:#E8F5E9; margin-bottom:12px; animation:checkPop 0.6s ease 0.3s both;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>

        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #1a1a1a;">Takk for din bestilling!</h1>
        @if($orderData)
        <p style="margin: 6px 0 0; font-size: 14px; color: #999;">
            Bekreftelse sendt til <strong>{{ $orderData['email'] }}</strong>
        </p>
        @endif
    </div>

    {{-- ORDREBEKREFTELSE --}}
    @if($orderData)
    <div class="ty-card ty-anim2" style="margin-bottom: 16px;">
        <div style="font-size: 11px; font-weight: 600; letter-spacing: 1px; color: #999; margin-bottom: 12px;">ORDREBEKREFTELSE</div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 14px;">
            <span style="color: #999;">Ordrenr:</span>
            <span style="font-weight: 500;">#{{ $orderData['id'] }}</span>

            <span style="color: #999;">Tjeneste:</span>
            <span style="font-weight: 500;">{{ $orderData['product_name'] }}</span>

            <span style="color: #999;">Manus:</span>
            <span style="display: flex; align-items: center; gap: 4px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                <span style="color: #2E7D32; font-size: 13px;">Lastet opp</span>
            </span>

            <span style="color: #999;">Dato:</span>
            <span>{{ $orderData['date'] ? $orderData['date']->format('d.m.Y') : now()->format('d.m.Y') }}</span>

            <span style="color: #999;">Beløp:</span>
            <span style="font-weight: 700; font-size: 16px;">kr {{ number_format($orderData['price'], 0, ',', ' ') }}</span>
        </div>

        <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
            <span style="color: #999; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
                Faktura er sendt til din e-postadresse
            </span>
            @if($orderData['invoice_pdf_url'])
                <a href="{{ $orderData['invoice_pdf_url'] }}" style="color: #862736; font-size: 12px; font-weight: 600; text-decoration: none;">Last ned PDF</a>
            @endif
        </div>
    </div>
    @endif

    {{-- HVA SKJER NÅ — STEPPER --}}
    <div class="ty-card ty-anim3" style="margin-bottom: 16px;">
        <div style="font-size: 11px; font-weight: 600; letter-spacing: 1px; color: #999; margin-bottom: 16px;">HVA SKJER NÅ?</div>

        <div style="height: 3px; background: #eee; border-radius: 3px; margin-bottom: 20px; overflow: hidden;">
            <div style="height: 100%; background: #862736; border-radius: 3px; animation: slideRight 1.5s ease 1s both;"></div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Steg 1: Manus mottatt --}}
            <div class="ty-step" style="display: flex; gap: 14px;">
                <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; background:#2E7D32; display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div style="font-weight: 500; font-size: 14px;">Manus mottatt</div>
                    <p style="color: #999; font-size: 12px; margin: 4px 0 0; line-height: 1.5;">Vi har mottatt manuset ditt og det er klart for vurdering.</p>
                </div>
            </div>

            {{-- Steg 2: Finner redaktør (aktiv, animert) --}}
            <div class="ty-step" style="display: flex; gap: 14px;">
                <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; background:#862736; display:flex; align-items:center; justify-content:center;">
                    <span style="animation: penWrite 2s ease-in-out infinite;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    </span>
                </div>
                <div>
                    <div style="font-weight: 500; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                        Vi finner riktig redaktør
                        <span style="display: inline-flex; gap: 3px;">
                            <span style="width:4px; height:4px; border-radius:50%; background:#862736; animation:dotBounce1 1.4s infinite;"></span>
                            <span style="width:4px; height:4px; border-radius:50%; background:#862736; animation:dotBounce2 1.4s infinite;"></span>
                            <span style="width:4px; height:4px; border-radius:50%; background:#862736; animation:dotBounce3 1.4s infinite;"></span>
                        </span>
                    </div>
                    <p style="color: #999; font-size: 12px; margin: 4px 0 0; line-height: 1.5;">Vi vil finne en passende redaktør for ditt manus og gi deg en forventet tilbakemeldingsdato.</p>
                </div>
            </div>

            {{-- Steg 3: Redaktørvurdering (venter) --}}
            <div class="ty-step" style="display: flex; gap: 14px;">
                <div style="flex-shrink:0; width:32px; height:32px; border-radius:50%; background:#f5f5f5; border:1.5px solid #ddd; display:flex; align-items:center; justify-content:center; color:#999; font-weight:600; font-size:14px;">3</div>
                <div>
                    <div style="font-weight: 500; font-size: 14px;">Redaktørvurdering</div>
                    <p style="color: #999; font-size: 12px; margin: 4px 0 0; line-height: 1.5;">Normal behandlingstid hos redaktør er ca. 3 uker. Du mottar vurderingen per e-post og på Min side.</p>
                </div>
            </div>

        </div>
    </div>

    {{-- SITAT --}}
    <div class="ty-anim4" style="text-align: center; padding: 16px 20px; margin-bottom: 16px; font-style: italic; font-size: 14px; color: #999; line-height: 1.6;">
        &laquo;Vi gleder oss til å lese manuset ditt.&raquo;
    </div>

    {{-- BUNN: KONTAKT + CTA --}}
    <div class="ty-anim5 ty-bottom" style="display: flex; gap: 12px; align-items: stretch;">
        <div style="flex: 1; background: #f8f8f8; border-radius: 10px; padding: 14px 18px; text-align: center;">
            <div style="font-size: 12px; color: #999; margin-bottom: 4px;">Spørsmål?</div>
            <div style="font-size: 13px;">
                <a href="mailto:post@forfatterskolen.no" style="color: #862736; text-decoration: none;">post@forfatterskolen.no</a>
            </div>
        </div>

        <a href="{{ route('learner.shop-manuscript') }}"
           style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; background: #862736; color: white; border-radius: 10px; padding: 14px 18px; text-decoration: none; font-size: 14px; font-weight: 600; animation: pulse 2s ease-in-out 2s infinite; transition: transform 0.15s;"
           onmouseover="this.style.transform='scale(1.02)'"
           onmouseout="this.style.transform=''">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
            Se på mine manuskripter
        </a>
    </div>

</div>
@endsection
