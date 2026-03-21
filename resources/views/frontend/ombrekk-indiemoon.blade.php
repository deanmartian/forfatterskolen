@extends('frontend.layout')

@section('title')
    <title>Ombrekk og bokproduksjon | Indiemoon Publishing</title>
    <meta name="description" content="Automatisert bokproduksjon for forfattere. Last opp Word-manus, velg bokmal, og last ned trykkeklar PDF, EPUB og Word.">
@endsection

@section('content')

<style>
    .ombrekk-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #fff;
        padding: 80px 0 60px;
        text-align: center;
    }
    .ombrekk-hero h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 3rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .ombrekk-hero .subtitle {
        font-size: 1.25rem;
        opacity: 0.85;
        max-width: 700px;
        margin: 0 auto 2rem;
        line-height: 1.6;
    }
    .ombrekk-hero .badge-free {
        display: inline-block;
        background: #e94560;
        color: #fff;
        padding: 6px 20px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .ombrekk-section {
        padding: 60px 0;
    }
    .ombrekk-section:nth-child(even) {
        background: #f8f6f3;
    }
    .ombrekk-section h2 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2rem;
        color: #1a1a2e;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .ombrekk-section .section-lead {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 2.5rem;
        color: #555;
        font-size: 1.05rem;
        line-height: 1.7;
    }

    /* Pipeline steg */
    .pipeline-steps {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
        max-width: 1000px;
        margin: 0 auto;
    }
    .pipeline-step {
        flex: 1;
        min-width: 200px;
        max-width: 280px;
        text-align: center;
        padding: 30px 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        position: relative;
    }
    .pipeline-step .step-number {
        width: 50px;
        height: 50px;
        background: #862736;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        margin: 0 auto 15px;
    }
    .pipeline-step h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    .pipeline-step p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.5;
    }

    /* Formater */
    .format-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        justify-content: center;
        max-width: 900px;
        margin: 0 auto;
    }
    .format-card {
        flex: 1;
        min-width: 220px;
        max-width: 260px;
        padding: 30px 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        text-align: center;
    }
    .format-card .format-icon {
        font-size: 2.5rem;
        margin-bottom: 12px;
    }
    .format-card h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    .format-card p {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.5;
    }

    /* Bokmaler */
    .theme-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        max-width: 1000px;
        margin: 0 auto;
    }
    .theme-card {
        width: 170px;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        text-align: center;
        transition: transform 0.2s;
    }
    .theme-card:hover {
        transform: translateY(-3px);
    }
    .theme-card .theme-preview {
        width: 80px;
        height: 110px;
        margin: 0 auto 12px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        color: #fff;
        font-weight: 600;
    }
    .theme-card h4 {
        font-size: 0.95rem;
        margin-bottom: 4px;
    }
    .theme-card p {
        font-size: 0.75rem;
        color: #888;
    }

    /* Typografi-features */
    .typo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        max-width: 900px;
        margin: 0 auto;
    }
    .typo-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
    }
    .typo-item .typo-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .typo-item strong {
        display: block;
        font-size: 0.9rem;
        color: #1a1a2e;
        margin-bottom: 2px;
    }
    .typo-item span {
        font-size: 0.8rem;
        color: #777;
    }

    /* Trim sizes */
    .trim-table {
        max-width: 700px;
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;
    }
    .trim-table th, .trim-table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .trim-table th {
        background: #1a1a2e;
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .trim-table td {
        font-size: 0.9rem;
    }
    .trim-table tr:hover {
        background: #f8f6f3;
    }

    /* Sammenligning */
    .compare-table {
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
        border-collapse: collapse;
    }
    .compare-table th, .compare-table td {
        padding: 12px 14px;
        text-align: center;
        border-bottom: 1px solid #eee;
        font-size: 0.85rem;
    }
    .compare-table th {
        background: #1a1a2e;
        color: #fff;
        font-weight: 600;
    }
    .compare-table td:first-child {
        text-align: left;
        font-weight: 500;
    }
    .compare-table .highlight-col {
        background: #fdf6f0;
    }

    /* CTA */
    .ombrekk-cta {
        background: linear-gradient(135deg, #862736 0%, #a83246 100%);
        color: #fff;
        padding: 60px 0;
        text-align: center;
    }
    .ombrekk-cta h2 {
        color: #fff;
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    .ombrekk-cta p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }
    .ombrekk-cta .btn-cta {
        display: inline-block;
        background: #fff;
        color: #862736;
        padding: 14px 40px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ombrekk-cta .btn-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        text-decoration: none;
        color: #862736;
    }

    @media (max-width: 768px) {
        .ombrekk-hero h1 { font-size: 2rem; }
        .pipeline-steps { flex-direction: column; align-items: center; }
        .format-grid { flex-direction: column; align-items: center; }
        .theme-grid { justify-content: center; }
    }
</style>

<!-- HERO -->
<div class="ombrekk-hero">
    <div class="container">
        <span class="badge-free">Inkludert i Indiemoon-pakken</span>
        <h1>Automatisert ombrekk og bokproduksjon</h1>
        <p class="subtitle">
            Last opp Word-manuset ditt og få tilbake trykkeklare filer: PDF, EPUB og formatert Word
            &mdash; med profesjonell norsk typografi, layout og design.
        </p>
    </div>
</div>

<!-- HVORDAN DET FUNGERER -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Slik fungerer det</h2>
        <p class="section-lead">
            Fra Word-manus til ferdig bok i fem enkle steg. Du trenger ingen teknisk kunnskap &mdash;
            systemet tar seg av alt.
        </p>
        <div class="pipeline-steps">
            <div class="pipeline-step">
                <div class="step-number">1</div>
                <h3>Last opp manus</h3>
                <p>Dra og slipp Word-filen din. Systemet analyserer struktur, kapitler og ordtelling.</p>
            </div>
            <div class="pipeline-step">
                <div class="step-number">2</div>
                <h3>Fyll inn metadata</h3>
                <p>Tittel, forfatter, ISBN, dedikasjon og kolofoninformasjon.</p>
            </div>
            <div class="pipeline-step">
                <div class="step-number">3</div>
                <h3>Velg design</h3>
                <p>Velg blant profesjonelle bokmaler tilpasset din sjanger. Velg trimstorrelse og papirtype.</p>
            </div>
            <div class="pipeline-step">
                <div class="step-number">4</div>
                <h3>Omslag</h3>
                <p>Last opp eget omslag eller bruk v&aring;r omslagsdesigner. Ryggbredde beregnes automatisk.</p>
            </div>
            <div class="pipeline-step">
                <div class="step-number">5</div>
                <h3>Generer og last ned</h3>
                <p>Forh&aring;ndsvis boken i nettleseren. Godkjenn og last ned trykkeklare filer.</p>
            </div>
        </div>
    </div>
</div>

<!-- FORMATER -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Tre formater &mdash; &eacute;n prosess</h2>
        <p class="section-lead">
            Alle formatene genereres samtidig fra samme manus, slik at innholdet alltid er synkronisert.
        </p>
        <div class="format-grid">
            <div class="format-card">
                <div class="format-icon">&#128214;</div>
                <h3>Trykkeklar PDF</h3>
                <p>PDF med riktige marger, bleed, sidetall og lop ende headers. Klar for print-on-demand eller offset-trykk.</p>
            </div>
            <div class="format-card">
                <div class="format-icon">&#128241;</div>
                <h3>EPUB 3</h3>
                <p>Validert e-bok som fungerer p&aring; alle lesebrett og apper. Med innholdsfortegnelse og metadata.</p>
            </div>
            <div class="format-card">
                <div class="format-icon">&#128196;</div>
                <h3>Formatert Word</h3>
                <p>Profesjonelt formatert Word-dokument for videre redigering eller korrektur.</p>
            </div>
        </div>
    </div>
</div>

<!-- BOKMALER -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Profesjonelle bokmaler</h2>
        <p class="section-lead">
            Velg blant sjangeroptimaliserte maler designet for norske b&oslash;ker.
            Hver mal har sin egen typografi, layout og stemning.
        </p>
        <div class="theme-grid">
            <div class="theme-card">
                <div class="theme-preview" style="background: linear-gradient(135deg, #2c1810, #4a2c1a);">Aa</div>
                <h4>Klassisk</h4>
                <p>Tidl&oslash;s romantypografi</p>
            </div>
            <div class="theme-card">
                <div class="theme-preview" style="background: linear-gradient(135deg, #333, #666);">Aa</div>
                <h4>Moderne</h4>
                <p>Ren og minimalistisk</p>
            </div>
            <div class="theme-card">
                <div class="theme-preview" style="background: linear-gradient(135deg, #1a1a1a, #333);">Aa</div>
                <h4>Krim</h4>
                <p>M&oslash;rk og stram</p>
            </div>
            <div class="theme-card">
                <div class="theme-preview" style="background: linear-gradient(135deg, #e8a87c, #d4a574);">Aa</div>
                <h4>Barnebok</h4>
                <p>Leken og fargerik</p>
            </div>
            <div class="theme-card">
                <div class="theme-preview" style="background: linear-gradient(135deg, #2d4059, #445e7a);">Aa</div>
                <h4>Sakprosa</h4>
                <p>Seri&oslash;s og akademisk</p>
            </div>
        </div>
    </div>
</div>

<!-- NORSK TYPOGRAFI -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Norsk typografi etter standarden</h2>
        <p class="section-lead">
            Pipelinen f&oslash;lger norske typografiske regler fra Spr&aring;kr&aring;det og Den store typografiboka.
        </p>
        <div class="typo-grid">
            <div class="typo-item">
                <span class="typo-icon">&laquo;&raquo;</span>
                <div>
                    <strong>Norske anf&oslash;rselstegn</strong>
                    <span>&laquo;Ytre&raquo; og &lsquo;indre&rsquo; anf&oslash;rselstegn</span>
                </div>
            </div>
            <div class="typo-item">
                <span class="typo-icon">&shy;</span>
                <div>
                    <strong>Automatisk orddeling</strong>
                    <span>Norsk bokm&aring;l og nynorsk med minst 3 tegn f&oslash;r/etter</span>
                </div>
            </div>
            <div class="typo-item">
                <span class="typo-icon">&ndash;</span>
                <div>
                    <strong>Tankestrek</strong>
                    <span>Riktig bruk av tankestrek (&ndash;) vs. bindestrek (-)</span>
                </div>
            </div>
            <div class="typo-item">
                <span class="typo-icon">&#182;</span>
                <div>
                    <strong>Ingen horunger eller enker</strong>
                    <span>Minst 3 linjer &oslash;verst og nederst p&aring; hver side</span>
                </div>
            </div>
            <div class="typo-item">
                <span class="typo-icon">A</span>
                <div>
                    <strong>Drop caps</strong>
                    <span>Dekorativ stor f&oslash;rstebokstav i hvert kapittel</span>
                </div>
            </div>
            <div class="typo-item">
                <span class="typo-icon">&#8239;</span>
                <div>
                    <strong>Hardt mellomrom</strong>
                    <span>Etter forkortelser: f.eks.&nbsp;dette, kl.&nbsp;18</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TRIMST&Oslash;RRELSER -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Tilgjengelige bokformater</h2>
        <p class="section-lead">
            Velg trimst&oslash;rrelse basert p&aring; sjanger og distribusjon.
        </p>
        <table class="trim-table">
            <thead>
                <tr>
                    <th>Format</th>
                    <th>St&oslash;rrelse</th>
                    <th>Passer for</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Pocket</strong></td>
                    <td>110 &times; 178 mm</td>
                    <td>Massemarket, billigutgaver</td>
                </tr>
                <tr>
                    <td><strong>B-format</strong></td>
                    <td>130 &times; 198 mm</td>
                    <td>Popul&aelig;r for krim og roman</td>
                </tr>
                <tr>
                    <td><strong>Trade</strong></td>
                    <td>140 &times; 216 mm</td>
                    <td>Standard handelsformat</td>
                </tr>
                <tr>
                    <td><strong>Royal</strong></td>
                    <td>156 &times; 234 mm</td>
                    <td>St&oslash;rre format, sakprosa</td>
                </tr>
                <tr>
                    <td><strong>A5</strong></td>
                    <td>148 &times; 210 mm</td>
                    <td>Standard A5</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- SAMMENLIGNING -->
<div class="ombrekk-section">
    <div class="container">
        <h2>Hvorfor Indiemoon?</h2>
        <table class="compare-table">
            <thead>
                <tr>
                    <th>Funksjon</th>
                    <th class="highlight-col">Indiemoon</th>
                    <th>Reedsy (gratis)</th>
                    <th>Manuell typesetter</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Automatisk formatering</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10007;</td>
                </tr>
                <tr>
                    <td>Norske bokmaler</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10007;</td>
                    <td>&#10003;</td>
                </tr>
                <tr>
                    <td>Trykkeklar PDF</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10003;</td>
                </tr>
                <tr>
                    <td>EPUB 3</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10003;</td>
                    <td>Varierer</td>
                </tr>
                <tr>
                    <td>Integrert omslagsdesign</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10007;</td>
                    <td>Separat</td>
                </tr>
                <tr>
                    <td>Norsk typografi</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10007;</td>
                    <td>&#10003;</td>
                </tr>
                <tr>
                    <td>Selvbetjening 24/7</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10003;</td>
                    <td>&#10007;</td>
                </tr>
                <tr>
                    <td>Pris per bok</td>
                    <td class="highlight-col"><strong>Inkludert</strong></td>
                    <td>Gratis</td>
                    <td>kr 3 000&ndash;8 000</td>
                </tr>
                <tr>
                    <td>Redaktorintegrasjon</td>
                    <td class="highlight-col">&#10003;</td>
                    <td>&#10007;</td>
                    <td>Separat</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- CTA -->
<div class="ombrekk-cta">
    <div class="container">
        <h2>Klar til &aring; gi ut boken din?</h2>
        <p>Komplett norsk l&oslash;sning med integrert omslag, redakt&oslash;rtjenester og kursportal i &eacute;n plattform.</p>
        <a href="{{ route('front.publishing') }}" class="btn-cta">Les mer om Indiemoon &rarr;</a>
    </div>
</div>

@endsection
