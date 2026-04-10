@extends('frontend.layout')

@section('page_title', 'Manusutvikling &rsaquo; Forfatterskolen')
@section('meta_desc', 'Profesjonell manusutvikling og tekstvurdering fra erfarne redaktører. Få detaljert tilbakemelding på ditt manus hos Forfatterskolen.')

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/manusutvikling.css') }}">
@stop

@section('content')

<div class="manus-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="manus-hero">
        <div class="manus-hero__inner">
            <div>
                <p class="manus-hero__eyebrow">Profesjonell manusvurdering</p>
                <h1 class="manus-hero__heading">Få ditt manus vurdert av <em>erfarne redaktører</em></h1>
                <p class="manus-hero__description">Er du usikker på om utkastet ditt holder? Våre redaktører gir deg grundig tilbakemelding med kommentarer i margen — tekstens svake og sterke sider.</p>
                <a href="#priskalkulator" class="manus-hero__cta">Beregn pris →</a>
            </div>
            <div class="manus-hero__features">
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Grundig tilbakemelding</div>
                        <div class="manus-feature-card__desc">Detaljerte kommentarer i margen med fokus på styrker og svakheter</div>
                    </div>
                </div>
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Erfarne redaktører</div>
                        <div class="manus-feature-card__desc">Våre redaktører har lang erfaring med å utvikle manuskripter</div>
                    </div>
                </div>
                <div class="manus-feature-card">
                    <div class="manus-feature-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#c45" stroke-width="1.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div class="manus-feature-card__title">Veien til forlag</div>
                        <div class="manus-feature-card__desc">Vi har hjulpet mange forfattere med å bli utgitt</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ HOW IT WORKS ═══════════ --}}
    <section class="manus-how-it-works">
        <h2 class="manus-section-heading">Slik fungerer manusutvikling</h2>
        <p class="manus-section-sub">Forfatterskolen tilbyr profesjonell tilbakemelding på ditt manus. En erfaren redaktør vil gi deg en grundig og detaljert tilbakemelding med kommentarer i margen — tekstens svake og sterke sider.</p>
        <div class="manus-steps">
            <div class="manus-step">
                <div class="manus-step__number">1</div>
                <div class="manus-step__title">Beregn pris</div>
                <div class="manus-step__desc">Bruk kalkulatoren eller last opp manuset ditt for å få pris basert på antall ord.</div>
            </div>
            <div class="manus-step">
                <div class="manus-step__number">2</div>
                <div class="manus-step__title">Send inn manus</div>
                <div class="manus-step__desc">Bestill og last opp manuset. Vi matcher deg med en redaktør som passer din sjanger.</div>
            </div>
            <div class="manus-step">
                <div class="manus-step__number">3</div>
                <div class="manus-step__title">Få tilbakemelding</div>
                <div class="manus-step__desc">Motta grundig tilbakemelding med kommentarer i margen innen avtalt tid.</div>
            </div>
        </div>
    </section>

    {{-- ═══════════ PRICING CARDS ═══════════ --}}
    <section class="manus-pricing-section">
        <h2 class="manus-section-heading">Priser</h2>
        <p class="manus-section-sub">Alle priser er eks. mva.</p>

        <div class="manus-pricing-cards">
            <div class="manus-pricing-card">
                <div class="manus-pricing-card__header">
                    <div class="manus-pricing-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                    </div>
                    <div class="manus-pricing-card__title">Grunnpris</div>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Inntil 5 000 ord</span>
                    <span class="manus-pricing-row__value">1 500 kr</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">5 000 – 17 500 ord</span>
                    <span class="manus-pricing-row__value">0,112 kr/ord</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Over 17 500 ord</span>
                    <span class="manus-pricing-row__value">2 900 kr <small>+ 0,15 kr/ord</small></span>
                </div>
            </div>
            <div class="manus-pricing-card">
                <div class="manus-pricing-card__header">
                    <div class="manus-pricing-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
                    </div>
                    <div class="manus-pricing-card__title">Påslag for sjanger</div>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Novelle</span>
                    <span class="manus-pricing-row__value">+30%</span>
                </div>
                <div class="manus-pricing-row">
                    <span class="manus-pricing-row__label">Lyrikk</span>
                    <span class="manus-pricing-row__value">+50%</span>
                </div>
                <p class="manus-pricing-card__note">Kortprosa og poesi krever ofte mer detaljert arbeid per ord.</p>
            </div>
        </div>
    </section>

    {{-- ═══════════ CALCULATOR ═══════════ --}}
    <section class="manus-calculator-section" id="priskalkulator">
        <div class="manus-calculator">
            <div class="manus-calculator__header">
                <h2 class="manus-calculator__heading">Beregn pris for ditt manus</h2>
                <p class="manus-calculator__sub">Velg antall ord eller last opp manuset ditt for å se pris</p>
                <div class="manus-calc-tabs">
                    <button class="manus-calc-tab active" data-tab="slider" onclick="manusCalcSwitchTab('slider')">Bruk slider</button>
                    <button class="manus-calc-tab" data-tab="upload" onclick="manusCalcSwitchTab('upload')">Last opp manus</button>
                </div>
            </div>

            <div class="manus-calculator__body">
                {{-- Slider panel --}}
                <div class="manus-calc-panel active" id="manus-panel-slider">
                    <div class="manus-slider-panel">
                        <div class="manus-word-count-display" id="manusWordCountDisplay">17 500 <span>ord</span></div>
                        <div class="manus-page-estimate" id="manusPageEstimate">ca. 50 sider</div>
                        <input type="range" class="manus-word-slider" id="manusWordSlider" min="1000" max="175000" value="17500" step="500">
                        <div class="manus-slider-labels">
                            <span>1 000</span>
                            <span>50 000</span>
                            <span>100 000</span>
                            <span>175 000</span>
                        </div>
                        <div class="manus-genre-select">
                            <label for="manusGenreSelect">Sjanger:</label>
                            <select id="manusGenreSelect" onchange="manusCalcUpdatePrice()">
                                <option value="standard">Roman / sakprosa (standard)</option>
                                <option value="novelle">Novelle (+30%)</option>
                                <option value="lyrikk">Lyrikk (+50%)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Upload panel --}}
                <div class="manus-calc-panel" id="manus-panel-upload">
                    <div class="manus-upload-panel">
                        <div class="manus-upload-zone" id="manusUploadZone">
                            <div class="manus-upload-zone__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <div class="manus-upload-zone__title" id="manusUploadTitle">Dra og slipp fil her</div>
                            <div class="manus-upload-zone__sub" id="manusUploadSub">eller klikk for å velge</div>
                            <div class="manus-upload-zone__formats" id="manusUploadFormats">
                                <span class="manus-format-badge">.doc</span>
                                <span class="manus-format-badge">.docx</span>
                                <span class="manus-format-badge">.odt</span>
                                <span class="manus-format-badge">.pdf</span>
                                <span class="manus-format-badge">.pages</span>
                            </div>
                        </div>
                        <input type="file" id="manusFileInput" hidden accept=".doc,.docx,.pdf,.odt,.pages,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
                        <div class="manus-upload-message" id="manusUploadMessage" style="display:none;"></div>
                        <div class="manus-genre-select" style="margin-top: 1.5rem;">
                            <label for="manusGenreSelect2">Sjanger:</label>
                            <select id="manusGenreSelect2" onchange="manusCalcUpdatePrice()">
                                <option value="standard">Roman / sakprosa (standard)</option>
                                <option value="novelle">Novelle (+30%)</option>
                                <option value="lyrikk">Lyrikk (+50%)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Price result --}}
                <div class="manus-price-result">
                    <div class="manus-price-result__row">
                        <span class="manus-price-result__label">Antall ord</span>
                        <span class="manus-price-result__value" id="manusResultWords">17 500</span>
                    </div>
                    <div class="manus-price-result__row">
                        <span class="manus-price-result__label">Grunnpris</span>
                        <span class="manus-price-result__value" id="manusResultBase">2 900 kr</span>
                    </div>
                    <div class="manus-price-result__row" id="manusSurchargeRow" style="display: none;">
                        <span class="manus-price-result__label">Sjangerpåslag</span>
                        <span class="manus-price-result__value" id="manusSurchargeValue">+30%</span>
                    </div>
                    <div class="manus-price-result__total">
                        <span class="manus-price-result__total-label">Totalt</span>
                        <span class="manus-price-result__total-price" id="manusTotalPrice">2 900 <span>kr</span></span>
                    </div>
                    <div class="manus-price-result__note">Alle priser eks. mva. for ikke-elever.</div>
                    <a href="#" class="manus-price-result__cta" id="manusCheckoutCta">Bestill manusutvikling →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ INFO BANNER ═══════════ --}}
    <div class="manus-info-banner">
        <div class="manus-info-banner__inner">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span><strong>NB!</strong> Dersom du ikke er elev ved Forfatterskolen, er tjenesten momsbelagt (25%).</span>
        </div>
    </div>

    {{-- ═══════════ TESTIMONIAL ═══════════ --}}
    <section class="manus-testimonial">
        <div class="manus-testimonial__inner">
            <p class="manus-testimonial__quote">"Tilbakemeldingen fra redaktøren var grundig og konstruktiv. Det ga meg akkurat det dyttet jeg trengte for å ferdigstille manuset og sende det til forlag."</p>
            <p class="manus-testimonial__author"><strong>Utgitt elev</strong> — via Forfatterskolen</p>
        </div>
    </section>

    {{-- ═══════════ COACHING ═══════════ --}}
    <section class="manus-coaching" id="coaching">
        <div class="manus-coaching__inner">
            <span class="manus-coaching__badge">Tilleggstjeneste</span>
            <h2 class="manus-coaching__title">Coaching med redaktør</h2>
            <p class="manus-coaching__desc">Book en personlig gjennomgang med en av våre erfarne redaktører. Perfekt som supplement til manusutvikling &mdash; eller som en selvstendig tjeneste.</p>

            <div class="manus-coaching__cards">
                {{-- Halvtime --}}
                <div class="manus-coaching-card">
                    <div class="manus-coaching-card__label">Halvtime</div>
                    <div class="manus-coaching-card__price">kr 1 190</div>
                    <div class="manus-coaching-card__price-note">eks. mva</div>
                    <ul class="manus-coaching-card__features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 30 min en-til-en med redaktør</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Fokus på ditt manus</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Video eller telefon</li>
                    </ul>
                    <a href="{{ route('front.shop-manuscript.checkout', 3) }}" class="manus-coaching-card__cta">Bestill 30 min &rarr;</a>
                </div>

                {{-- Hel time --}}
                <div class="manus-coaching-card manus-coaching-card--popular">
                    <span class="manus-coaching-card__popular-badge">Mest populær</span>
                    <div class="manus-coaching-card__label">Hel time</div>
                    <div class="manus-coaching-card__price">kr 1 690</div>
                    <div class="manus-coaching-card__price-note">eks. mva</div>
                    <ul class="manus-coaching-card__features">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> 60 min en-til-en med redaktør</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Grundig gjennomgang av manus</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Konkrete råd til neste steg</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Video eller telefon</li>
                    </ul>
                    <a href="{{ route('front.shop-manuscript.checkout', 3) }}" class="manus-coaching-card__cta">Bestill 60 min &rarr;</a>
                </div>
            </div>

            <p class="manus-coaching__note">Spar 10% på coaching når du bestiller sammen med manusutvikling.</p>
        </div>
    </section>

</div>

{{-- ═══════════ MODALS (beholdes) ═══════════ --}}
<div class="modal fade" role="dialog" id="editorsModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-columns">
                    @foreach($editors->chunk(3) as $editor_chunk)
                        <div class="card-container">
                        @foreach($editor_chunk as $editor)
                            <div class="card">
                                <div class="card-header">
                                </div>
                                <div class="card-body text-center">
                                    <div class="editor-circle">
                                        <img src="{{ asset($editor['editor_image']) }}" alt="editor image" class="rounded-circle">
                                    </div>
                                    <p>
                                        <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@if(Session::has('manuscript_test'))
    <div id="manuscriptTestModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    {!! Session::get('manuscript_test') !!}
                </div>
            </div>
        </div>
    </div>
@endif

@if(Session::has('manuscript_test_error'))
    <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                    {!! Session::get('manuscript_test_error') !!}
                </div>
            </div>
        </div>
    </div>
@endif

@stop

@section('scripts')
    <script src="https://unpkg.com/mammoth@1.4.21/mammoth.browser.min.js"></script>
    <script>
        // ── Product data for dynamic checkout URL ──────────
        var manusProducts = [
            @foreach($shopManuscripts->sortBy('max_words') as $product)
            {
                id: {{ $product->id }},
                maxWords: {{ $product->max_words }},
                price: {{ $product->full_payment_price }},
                checkoutUrl: "{{ route($checkoutRoute, $product->id) }}"
            },
            @endforeach
        ];

        // ── Pricing logic ──────────────────────────────────
        var manusGenreMultipliers = { standard: 1, novelle: 1.3, lyrikk: 1.5 };

        function manusCalcBasePrice(words) {
            if (words <= 5000) return 1500;
            if (words <= 17500) return 1500 + Math.round((words - 5000) * 0.112);
            return 2900 + Math.round((words - 17500) * 0.15);
        }

        function manusFormatNumber(n) {
            return n.toLocaleString('nb-NO');
        }

        function manusGetCheckoutUrl(words) {
            var url = '#';
            for (var i = 0; i < manusProducts.length; i++) {
                if (words <= manusProducts[i].maxWords) {
                    url = manusProducts[i].checkoutUrl;
                    break;
                }
            }
            if (url === '#' && manusProducts.length) {
                url = manusProducts[manusProducts.length - 1].checkoutUrl;
            }
            // Legg til ordtelling som URL-parameter
            return url + (url.indexOf('?') >= 0 ? '&' : '?') + 'words=' + words;
        }

        var manusSlider = document.getElementById('manusWordSlider');
        var manusActiveTab = 'slider';

        function manusCalcUpdatePrice() {
            var words = parseInt(manusSlider.value);
            var genreId = manusActiveTab === 'slider' ? 'manusGenreSelect' : 'manusGenreSelect2';
            var genre = document.getElementById(genreId).value;
            var multiplier = manusGenreMultipliers[genre];
            var basePrice = manusCalcBasePrice(words);
            var total = Math.round(basePrice * multiplier);

            document.getElementById('manusWordCountDisplay').innerHTML = manusFormatNumber(words) + ' <span>ord</span>';
            document.getElementById('manusPageEstimate').textContent = 'ca. ' + Math.round(words / 350) + ' sider';
            document.getElementById('manusResultWords').textContent = manusFormatNumber(words);
            document.getElementById('manusResultBase').textContent = manusFormatNumber(basePrice) + ' kr';

            var surchargeRow = document.getElementById('manusSurchargeRow');
            if (genre !== 'standard') {
                surchargeRow.style.display = 'flex';
                var surchargeAmount = Math.round(basePrice * (multiplier - 1));
                document.getElementById('manusSurchargeValue').textContent =
                    (genre === 'novelle' ? '+30%' : '+50%') + ' → ' + manusFormatNumber(surchargeAmount) + ' kr';
            } else {
                surchargeRow.style.display = 'none';
            }

            document.getElementById('manusTotalPrice').innerHTML = manusFormatNumber(total) + ' <span>kr</span>';
            document.getElementById('manusCheckoutCta').href = manusGetCheckoutUrl(words);
        }

        manusSlider.addEventListener('input', manusCalcUpdatePrice);
        manusCalcUpdatePrice();

        function manusCalcSwitchTab(tabName) {
            document.querySelectorAll('.manus-calc-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.manus-calc-panel').forEach(function(p) { p.classList.remove('active'); });
            document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');
            document.getElementById('manus-panel-' + tabName).classList.add('active');
            manusActiveTab = tabName;
        }

        // ── File upload & word count extraction ────────────
        (function() {
            var uploadZone = document.getElementById('manusUploadZone');
            var fileInput = document.getElementById('manusFileInput');
            var uploadTitle = document.getElementById('manusUploadTitle');
            var uploadSub = document.getElementById('manusUploadSub');
            var uploadFormats = document.getElementById('manusUploadFormats');
            var uploadMessage = document.getElementById('manusUploadMessage');

            var mammothAvailable = typeof window.mammoth !== 'undefined'
                && typeof window.mammoth.extractRawText === 'function';
            var mammothExtensions = ['doc', 'docx'];

            function getFileExtension(fileName) {
                if (!fileName) return '';
                var match = fileName.toLowerCase().match(/\.([^.]+)$/);
                return match ? match[1] : '';
            }

            function getCsrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : null;
            }

            function createDocxFileName(name) {
                if (!name) return 'document.docx';
                var dot = name.lastIndexOf('.');
                if (dot <= 0) return name + '.docx';
                return name.substring(0, dot) + '.docx';
            }

            function showMessage(text, type) {
                uploadMessage.textContent = text;
                uploadMessage.className = 'manus-upload-message' + (type ? ' ' + type : '');
                uploadMessage.style.display = 'block';
            }

            function hideMessage() {
                uploadMessage.style.display = 'none';
            }

            function resetUploadUI() {
                uploadTitle.textContent = 'Dra og slipp fil her';
                uploadSub.textContent = 'eller klikk for å velge';
                uploadFormats.style.display = 'flex';
                hideMessage();
            }

            function countWords(text) {
                if (typeof text !== 'string') return 0;
                var normalised = text.replace(/[\r\n\t]+/g, ' ').trim();
                if (!normalised) return 0;
                var matches = normalised.match(/\S+/g);
                return matches ? matches.length : 0;
            }

            function extractWithMammoth(file) {
                return new Promise(function(resolve, reject) {
                    if (!mammothAvailable) { resolve(null); return; }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var buf = e.target ? e.target.result : null;
                        if (!buf) { resolve(null); return; }
                        window.mammoth.extractRawText({ arrayBuffer: buf })
                            .then(function(result) {
                                resolve(countWords(result && result.value ? result.value : ''));
                            })
                            .catch(reject);
                    };
                    reader.onerror = function() { reject(reader.error); };
                    reader.readAsArrayBuffer(file);
                });
            }

            async function convertToDocx(file) {
                var formData = new FormData();
                formData.append('document', file);
                var csrf = getCsrfToken();
                if (csrf) formData.append('_token', csrf);

                var headers = { 'X-Requested-With': 'XMLHttpRequest' };
                if (csrf) headers['X-CSRF-TOKEN'] = csrf;

                var response = await fetch('/documents/convert-to-docx', {
                    method: 'POST',
                    body: formData,
                    headers: headers
                });

                if (!response.ok) {
                    throw new Error('Konvertering feilet');
                }

                var blob = await response.blob();
                var mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                return new File([blob], createDocxFileName(file.name), { type: mime, lastModified: Date.now() });
            }

            async function handleFile(files) {
                if (!files || !files.length) return;
                var file = files[0];

                uploadTitle.textContent = file.name;
                uploadSub.textContent = 'Teller ord...';
                uploadFormats.style.display = 'none';
                hideMessage();

                var ext = getFileExtension(file.name);
                var processedFile = file;

                // Convert all non-docx formats (doc, pdf, odt, pages) to docx via server
                if (ext !== 'docx') {
                    try {
                        showMessage('Konverterer fil...', '');
                        processedFile = await convertToDocx(file);
                        hideMessage();
                    } catch (err) {
                        uploadSub.textContent = '';
                        showMessage('Kunne ikke konvertere filen. Last opp som .docx i stedet, eller bruk slideren for å beregne pris manuelt.', 'error');
                        return;
                    }
                }

                // Extract word count with mammoth
                try {
                    var wordCount = await extractWithMammoth(processedFile);
                    if (wordCount && wordCount > 0) {
                        uploadSub.textContent = manusFormatNumber(wordCount) + ' ord funnet';
                        showMessage('Ordtelling fullført! Prisen er oppdatert nedenfor.', 'success');

                        // Update slider and price
                        var clampedWords = Math.min(Math.max(wordCount, 1000), 175000);
                        manusSlider.value = clampedWords;
                        manusCalcUpdatePrice();
                    } else {
                        uploadSub.textContent = '';
                        showMessage('Kunne ikke telle ord i filen. Bruk slideren i stedet.', 'error');
                    }
                } catch (err) {
                    uploadSub.textContent = '';
                    showMessage('Kunne ikke lese filen. Bruk slideren i stedet.', 'error');
                }
            }

            // Click to upload
            uploadZone.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                handleFile(e.target.files);
            });

            // Drag and drop
            uploadZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });

            uploadZone.addEventListener('dragleave', function() {
                uploadZone.classList.remove('dragover');
            });

            uploadZone.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                var files = e.dataTransfer ? e.dataTransfer.files : null;
                if (files && files.length) {
                    handleFile(files);
                }
            });
        })();

        // ── Session modals ─────────────────────────────────
        @if(Session::has('manuscript_test'))
            (function() {
                var el = document.getElementById('manuscriptTestModal');
                if (el) new bootstrap.Modal(el).show();
            })();
        @endif
        @if(Session::has('manuscript_test_error'))
            (function() {
                var el = document.getElementById('manuscriptTestErrorModal');
                if (el) new bootstrap.Modal(el).show();
            })();
        @endif
    </script>
@stop
