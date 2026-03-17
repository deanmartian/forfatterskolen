<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takk for bidraget! – Juleantologi 2026 – Forfatterskolen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --midnight: #0a0e17;
            --frost: #c8d6e5;
            --gold: #d4a574;
            --gold-bright: #e8c49a;
            --snow-white: #f8fafc;
            --text-light: rgba(255, 255, 255, 0.85);
            --text-dim: rgba(255, 255, 255, 0.45);
            --font-display: 'Cormorant Garamond', 'Georgia', serif;
            --font-body: 'Libre Baskerville', 'Georgia', serif;
            --font-ui: 'Source Sans 3', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-body); background: var(--midnight); color: var(--text-light); overflow-x: hidden; }

        .snow-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 50; overflow: hidden; }
        .snowflake { position: absolute; top: -10px; background: #fff; border-radius: 50%; opacity: 0; animation: snowfall linear infinite; }
        @keyframes snowfall { 0% { opacity: 0; transform: translateX(0) rotate(0deg); } 10% { opacity: 0.8; } 90% { opacity: 0.6; } 100% { opacity: 0; transform: translateX(80px) rotate(360deg); top: 100vh; } }

        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 1.25rem 2.5rem; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(180deg, rgba(10,14,23,0.9) 0%, transparent 100%); }
        .nav__logo img { height: 24px; filter: brightness(10); opacity: 0.8; }

        .takk-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6rem 2rem;
            background:
                radial-gradient(ellipse at 50% 30%, rgba(134,39,54,0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 20% 80%, rgba(212,165,116,0.06) 0%, transparent 50%),
                var(--midnight);
        }

        .takk-card {
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .takk-card__ornament { font-size: 3rem; color: var(--gold); opacity: 0.5; margin-bottom: 1.5rem; }
        .takk-card__title { font-family: var(--font-display); font-size: 3rem; font-weight: 300; color: var(--snow-white); margin-bottom: 1rem; }
        .takk-card__subtitle { font-family: var(--font-ui); font-size: 1rem; color: var(--frost); margin-bottom: 2.5rem; line-height: 1.7; }

        .takk-steps { text-align: left; margin: 0 auto 2.5rem; max-width: 420px; }
        .takk-step { display: flex; gap: 1rem; margin-bottom: 1.25rem; align-items: flex-start; }
        .takk-step__num { font-family: var(--font-display); font-size: 1.5rem; font-weight: 600; color: var(--gold); line-height: 1; min-width: 30px; }
        .takk-step__text { font-family: var(--font-ui); font-size: 0.85rem; color: var(--frost); line-height: 1.5; }

        .takk-links { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem; }
        .takk-link { font-family: var(--font-ui); font-size: 0.85rem; font-weight: 600; text-decoration: none; padding: 0.75rem 1.75rem; border-radius: 4px; transition: all 0.3s; }
        .takk-link--primary { color: var(--midnight); background: linear-gradient(135deg, var(--gold), var(--gold-bright)); }
        .takk-link--primary:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(212,165,116,0.25); }
        .takk-link--secondary { color: var(--gold); border: 1px solid rgba(212,165,116,0.3); }
        .takk-link--secondary:hover { background: rgba(212,165,116,0.1); border-color: var(--gold); }

        .takk-cta { margin-top: 3rem; padding: 2rem; background: rgba(134,39,54,0.08); border: 1px solid rgba(134,39,54,0.15); border-radius: 12px; }
        .takk-cta__title { font-family: var(--font-display); font-size: 1.25rem; font-weight: 600; color: var(--snow-white); margin-bottom: 0.5rem; }
        .takk-cta__text { font-family: var(--font-ui); font-size: 0.85rem; color: var(--frost); margin-bottom: 1rem; }
    </style>
</head>
<body>

<div class="snow-container" id="snow"></div>

<nav class="nav">
    <a href="/"><img src="/images-new/forfatterskolen-logo.png" alt="Forfatterskolen" class="nav__logo"></a>
</nav>

<main class="takk-page">
    <div class="takk-card">
        <div class="takk-card__ornament">&#10052; &#10022; &#10052;</div>
        <h1 class="takk-card__title">Takk for bidraget!</h1>
        <p class="takk-card__subtitle">
            @if($submission)
                Vi har mottatt «{{ $submission->title }}». Du får en bekreftelse på e-post.
            @else
                Vi har mottatt teksten din. Du får en bekreftelse på e-post.
            @endif
        </p>

        <div class="takk-steps">
            <div class="takk-step">
                <div class="takk-step__num">1.</div>
                <div class="takk-step__text">Redaksjonen leser alle bidrag i september</div>
            </div>
            <div class="takk-step">
                <div class="takk-step__num">2.</div>
                <div class="takk-step__text">Alle får tilbakemelding — uansett utfall</div>
            </div>
            <div class="takk-step">
                <div class="takk-step__num">3.</div>
                <div class="takk-step__text">Utvalgte tekster redigeres i oktober</div>
            </div>
            <div class="takk-step">
                <div class="takk-step__num">4.</div>
                <div class="takk-step__text">Boken lanseres i november</div>
            </div>
        </div>

        <div class="takk-links">
            <a href="/juleantologi" class="takk-link takk-link--secondary">&#8592; Tilbake til juleantologien</a>
            <a href="/gratis-tekstvurdering" class="takk-link takk-link--primary">Gratis tekstvurdering &#10140;</a>
        </div>

        <div class="takk-cta">
            <div class="takk-cta__title">Mens du venter — har du prøvd vår gratis tekstvurdering?</div>
            <div class="takk-cta__text">Send inn en smakebit (opptil 500 ord) og få gratis tilbakemelding fra en profesjonell redaktør.</div>
        </div>
    </div>
</main>

@if(config('services.tracking.enabled'))
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ config("services.meta_pixel.id") }}');
fbq('track', 'PageView');
fbq('track', 'Lead');
</script>
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_ads.id') }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ config("services.google_ads.id") }}');gtag('event','conversion',{'send_to':'{{ config("services.google_ads.id") }}/{{ config("services.google_ads.conversion_lead") }}'});</script>
@endif

<script>
function createSnow() {
    var container = document.getElementById('snow');
    for (var i = 0; i < 40; i++) {
        var flake = document.createElement('div');
        flake.className = 'snowflake';
        flake.style.left = Math.random() * 100 + '%';
        flake.style.width = (2 + Math.random() * 4) + 'px';
        flake.style.height = flake.style.width;
        flake.style.animationDuration = (8 + Math.random() * 12) + 's';
        flake.style.animationDelay = Math.random() * 15 + 's';
        container.appendChild(flake);
    }
}
createSnow();
</script>

</body>
</html>
