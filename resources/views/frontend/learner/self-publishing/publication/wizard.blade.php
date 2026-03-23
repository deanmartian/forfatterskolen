@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>{{ $publication ? $publication->title : 'Ny bok' }} | Indiemoon Publishing</title>
@endsection

@section('breadcrumbs')
    <a href="{{ route('learner.publication.index') }}">Ombrekk</a>
    <span class="sep">›</span>
    {{ $publication ? $publication->title : 'Ny bok' }}
@endsection

@section('content')
<style>
    .pub-wizard { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
    .pub-steps { display: flex; gap: 0; margin-bottom: 40px; }
    .pub-step-indicator {
        flex: 1; text-align: center; padding: 12px 8px;
        font-size: 0.85rem; color: #999; position: relative;
        border-bottom: 3px solid #ddd;
    }
    .pub-step-indicator.active { color: #862736; border-bottom-color: #862736; font-weight: 600; }
    .pub-step-indicator.done { color: #28a745; border-bottom-color: #28a745; }
    .pub-card { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 30px; margin-bottom: 20px; }
    .pub-card h2 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.5rem; margin-bottom: 20px; }
    .pub-field { margin-bottom: 16px; }
    .pub-field label { display: block; font-weight: 600; font-size: 0.85rem; color: #444; margin-bottom: 4px; }
    .pub-field input, .pub-field select, .pub-field textarea {
        width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 6px;
        font-size: 0.95rem; font-family: inherit;
    }
    .pub-field textarea { min-height: 80px; resize: vertical; }
    .pub-btn {
        display: inline-block; background: #862736; color: #fff;
        padding: 12px 30px; border-radius: 6px; border: none;
        font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none;
    }
    .pub-btn:hover { background: #a83246; color: #fff; text-decoration: none; }
    .pub-btn-outline {
        display: inline-block; background: transparent; color: #862736;
        padding: 12px 30px; border-radius: 6px; border: 2px solid #862736;
        font-size: 1rem; cursor: pointer; text-decoration: none;
    }
    .theme-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; }
    .theme-option { border: 2px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; cursor: pointer; transition: border-color 0.2s; }
    .theme-option:hover, .theme-option.selected { border-color: #862736; }
    .theme-option input { display: none; }
    .theme-option h4 { font-size: 0.9rem; margin: 8px 0 2px; }
    .theme-option p { font-size: 0.75rem; color: #888; }
    .theme-preview { width: 60px; height: 80px; margin: 0 auto; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; }
    .status-bar { padding: 20px; border-radius: 8px; text-align: center; }
    .status-processing { background: #fff3cd; color: #856404; }
    .status-ready { background: #d4edda; color: #155724; }
    .status-error { background: #f8d7da; color: #721c24; }
</style>

<div class="pub-wizard">
    <h1 style="font-family: 'Cormorant Garamond', Georgia, serif; font-size: 2rem; margin-bottom: 10px;">
        {{ $publication ? $publication->title : 'Ny bok' }}
    </h1>

    <!-- Step indicators -->
    <div class="pub-steps">
        @foreach(['Last opp', 'Metadata', 'Design', 'Omslag', 'Generer'] as $i => $label)
            @php $s = $i + 1; @endphp
            <div class="pub-step-indicator {{ $step == $s ? 'active' : '' }} {{ $step > $s ? 'done' : '' }}">
                {{ $s }}. {{ $label }}
            </div>
        @endforeach
    </div>

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- STEP 1: Upload -->
    @if(!$publication)
    <div class="pub-card">
        <h2>Last opp manuskript</h2>
        <form action="{{ route('learner.publication.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="pub-field">
                <label>Tittel *</label>
                <input type="text" name="title" required placeholder="Den faktiske boktittelen (brukes på tittelside, halvtittel og løpende header)">
                <p style="font-size: 0.8rem; color: #888; margin-top: 4px;">Skriv den endelige tittelen på boken, ikke filnavnet.</p>
            </div>
            <div class="pub-field">
                <label>Forfatter *</label>
                <input type="text" name="author_name" required value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}">
            </div>
            <div class="pub-field">
                <label>Word-manus (.docx) *</label>
                <input type="file" name="manuscript" accept=".docx" required>
                <p style="font-size: 0.8rem; color: #888; margin-top: 4px;">Maks 50 MB. Bruk overskrifter (Heading 1) for kapittelstart.</p>
            </div>
            <button type="submit" class="pub-btn">Last opp og fortsett</button>
        </form>
    </div>
    @endif

    <!-- STEP 2: Metadata -->
    @if($publication && $step == 2)
    <div class="pub-card">
        <h2>Bokinformasjon</h2>
        <form action="{{ route('learner.publication.update-step', [$publication->id, 2]) }}" method="POST">
            @csrf @method('PUT')
            <div class="pub-field">
                <label>Tittel *</label>
                <input type="text" name="title" required value="{{ $publication->title }}">
            </div>
            <div class="pub-field">
                <label>Undertittel</label>
                <input type="text" name="subtitle" value="{{ $publication->subtitle }}">
            </div>
            <div class="pub-field">
                <label>Forfatter *</label>
                <input type="text" name="author_name" required value="{{ $publication->author_name }}">
            </div>
            <div class="pub-field">
                <label>ISBN</label>
                <input type="text" name="isbn" value="{{ $publication->isbn }}" placeholder="978-82-...">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="pub-field">
                    <label>Språk</label>
                    <select name="language">
                        <option value="nb" {{ $publication->language == 'nb' ? 'selected' : '' }}>Norsk bokmål</option>
                        <option value="nn" {{ $publication->language == 'nn' ? 'selected' : '' }}>Nynorsk</option>
                        <option value="en" {{ $publication->language == 'en' ? 'selected' : '' }}>Engelsk</option>
                    </select>
                </div>
                <div class="pub-field">
                    <label>Sjanger</label>
                    <input type="text" name="genre" value="{{ $publication->genre }}" placeholder="Roman, krim, sakprosa...">
                </div>
            </div>
            <div class="pub-field">
                <label>Dedikasjon</label>
                <textarea name="dedication" placeholder="Valgfritt...">{{ $publication->dedication }}</textarea>
            </div>
            <div class="pub-field">
                <label>Kolofoninformasjon (ekstra)</label>
                <textarea name="colophon_extra" placeholder="Ekstra tekst til kolofonsiden...">{{ $publication->colophon_extra }}</textarea>
            </div>
            <div class="pub-field">
                <label>Startmarkør for bokinnhold (valgfritt)</label>
                <input type="text" name="content_start_marker" value="{{ $publication->content_start_marker }}" placeholder="F.eks. «Kapittel 1» eller første setning i romanen">
                <p style="font-size: 0.8rem; color: #888; margin-top: 4px;">Om filen inneholder redaktørvurdering eller annet som ikke skal med i boken, skriv inn teksten der romanen starter. Alt før dette fjernes.</p>
            </div>
            <button type="submit" class="pub-btn">Lagre og fortsett</button>
        </form>
    </div>
    @endif

    <!-- STEP 3: Design -->
    @if($publication && $step == 3)
    <div class="pub-card">
        <h2>Velg design</h2>
        <form action="{{ route('learner.publication.update-step', [$publication->id, 3]) }}" method="POST">
            @csrf @method('PUT')

            <h3 style="font-size: 1rem; margin-bottom: 12px;">Bokmal</h3>
            <div class="theme-grid" style="margin-bottom: 24px;">
                @foreach([
                    'classic' => ['Klassisk', 'Tidløs romantypografi', '#2c1810'],
                    'modern' => ['Moderne', 'Ren og minimalistisk', '#444'],
                    'crime' => ['Krim', 'Mørk og stram', '#1a1a1a'],
                    'children' => ['Barnebok', 'Leken og fargerik', '#e8a87c'],
                    'nonfiction' => ['Sakprosa', 'Seriøs og akademisk', '#2d4059'],
                ] as $key => [$name, $desc, $color])
                    <label class="theme-option {{ ($publication->theme ?? 'classic') == $key ? 'selected' : '' }}" onclick="this.querySelector('input').checked=true; document.querySelectorAll('.theme-option').forEach(e=>e.classList.remove('selected')); this.classList.add('selected');">
                        <input type="radio" name="theme" value="{{ $key }}" {{ ($publication->theme ?? 'classic') == $key ? 'checked' : '' }}>
                        <div class="theme-preview" style="background: {{ $color }};">Aa</div>
                        <h4>{{ $name }}</h4>
                        <p>{{ $desc }}</p>
                    </label>
                @endforeach
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="pub-field">
                    <label>Trimstørrelse</label>
                    <select name="trim_size">
                        @foreach($trimSizes as $ts)
                            <option value="{{ $ts->value }}" {{ ($publication->trim_size ?? '140x220') == $ts->value ? 'selected' : '' }}>{{ $ts->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pub-field">
                    <label>Papirtype</label>
                    <select name="paper_type">
                        @foreach($paperTypes as $pt)
                            <option value="{{ $pt->value }}" {{ ($publication->paper_type ?? 'munken_cream_100') == $pt->value ? 'selected' : '' }}>{{ $pt->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pub-field">
                    <label>Innbinding</label>
                    <select name="binding_type">
                        @foreach($bindingTypes as $bt)
                            <option value="{{ $bt->value }}" {{ ($publication->binding_type ?? 'paperback') == $bt->value ? 'selected' : '' }}>{{ $bt->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pub-field">
                    <label>Kachering</label>
                    <select name="cover_lamination">
                        @foreach($coverLaminations as $cl)
                            <option value="{{ $cl->value }}" {{ ($publication->cover_lamination ?? 'matt') == $cl->value ? 'selected' : '' }}>{{ $cl->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="pub-btn">Lagre og fortsett</button>
        </form>
    </div>
    @endif

    <!-- STEP 4: Cover -->
    @if($publication && $step == 4)
    <div class="pub-card">
        <h2>Omslag</h2>
        @php
            $trimSize = \App\Services\Publishing\TrimSize::tryFrom($publication->trim_size);
            $paperType = \App\Services\Publishing\PaperType::tryFrom($publication->paper_type);
            $bindingType = \App\Services\Publishing\BindingType::tryFrom($publication->binding_type);
            $spineWidth = $publication->spine_width_mm ?? 10;
            $coverCalc = app(\App\Services\Publishing\CoverDimensionCalculator::class);
            $coverDims = $coverCalc->calculate(
                $trimSize?->dimensions()['width'] ?? 140,
                $trimSize?->dimensions()['height'] ?? 220,
                $spineWidth,
                $bindingType ?? \App\Services\Publishing\BindingType::PAPERBACK,
            );
        @endphp

        <div style="background: #f8f6f3; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Omslagsdimensjoner (beregnet):</strong><br>
            Total: {{ $coverDims->totalWidth }} &times; {{ $coverDims->totalHeight }} mm &middot;
            Rygg: {{ $coverDims->spineWidth }}mm (&plusmn;{{ $coverDims->spineToleranceMm() }}mm) &middot;
            Innbinding: {{ $bindingType?->label() ?? 'Paperback' }}
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 24px;">
            <div style="flex: 1; border: 1px solid #ddd; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 1rem; margin-bottom: 12px;">Alternativ 1: Design omslaget her</h3>
                <form action="{{ route('learner.publication.generate-cover', $publication->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="pub-field">
                        <label>Omslagsmal</label>
                        <select name="cover_template" id="coverTemplate" onchange="document.getElementById('coverImageField').style.display = this.value === 'image-full' ? 'block' : 'none';">
                            <option value="classic">Klassisk (serif)</option>
                            <option value="modern">Moderne (sans-serif)</option>
                            <option value="bold">Bold (stor typografi)</option>
                            <option value="image-full">Forsidebilde (fullbleed)</option>
                        </select>
                    </div>
                    <div class="pub-field" id="coverImageField" style="display: none;">
                        <label>Forsidebilde</label>
                        <input type="file" name="cover_image" accept="image/*">
                        <p style="font-size: 0.8rem; color: #888; margin-top: 4px;">Bildet dekker hele forsiden. Anbefalt: høyoppløst JPG/PNG (min 300 DPI).</p>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="pub-field">
                            <label>Bakgrunnsfarge</label>
                            <input type="color" name="background_color" value="#1a1a2e" style="height: 40px; width: 100%; cursor: pointer;">
                        </div>
                        <div class="pub-field">
                            <label>Tekstfarge</label>
                            <input type="color" name="text_color" value="#ffffff" style="height: 40px; width: 100%; cursor: pointer;">
                        </div>
                    </div>
                    <div class="pub-field">
                        <label>Baksidtekst (blurb)</label>
                        <textarea name="blurb" rows="4" placeholder="Kort beskrivelse av boken som vises på baksiden..."></textarea>
                    </div>
                    <button type="submit" class="pub-btn">Generer omslag</button>
                </form>
            </div>

            <div style="flex: 1; border: 1px solid #ddd; border-radius: 8px; padding: 20px;">
                <h3 style="font-size: 1rem; margin-bottom: 12px;">Alternativ 2: Last opp eget omslag</h3>
                <form action="{{ route('learner.publication.update-step', [$publication->id, 4]) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="pub-field">
                        <label>Omslagsfil (PDF eller bilde)</label>
                        <input type="file" name="cover_front" accept="image/*,.pdf">
                        <p style="font-size: 0.8rem; color: #888; margin-top: 4px;">
                            Anbefalt: PDF med riktige dimensjoner ({{ $coverDims->totalWidth }} &times; {{ $coverDims->totalHeight }} mm) inkl. 3mm bleed.
                        </p>
                    </div>
                    <button type="submit" class="pub-btn-outline">Last opp og fortsett</button>
                </form>

                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
                    <h4 style="font-size: 0.85rem; margin-bottom: 8px;">Lag selv i Canva/InDesign?</h4>
                    <a href="{{ route('learner.publication.download', [$publication->id, 'cover-template']) }}" class="pub-btn-outline" style="font-size: 0.85rem; padding: 8px 16px;">
                        Last ned tom template-PDF med hjelplinjer
                    </a>
                </div>
            </div>
        </div>

        @if($publication->cover_front)
            <div style="background: #d4edda; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px;">
                Omslag er generert!
                <a href="{{ route('learner.publication.download', [$publication->id, 'cover']) }}" style="color: #155724; font-weight: 600;">Last ned trykkeklar PDF</a> &middot;
                <a href="{{ route('learner.publication.download', [$publication->id, 'cover-preview']) }}" style="color: #155724;">Forhåndsvisning med hjelplinjer</a>
            </div>
        @endif

        <form action="{{ route('learner.publication.update-step', [$publication->id, 4]) }}" method="POST" style="margin-top: 10px;">
            @csrf @method('PUT')
            <button type="submit" class="pub-btn">Fortsett til generering av innmat</button>
        </form>
    </div>
    @endif

    <!-- STEP 5: Generate -->
    @if($publication && $step == 5)
    <div class="pub-card">
        <h2>Generer boken din</h2>

        @if($publication->isProcessing())
            <div class="status-bar status-processing" id="status-bar">
                <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px;">Boken genereres...</p>
                <p id="status-text">Status: {{ ucfirst($publication->status) }}</p>
            </div>
            <script>
                (function poll() {
                    setTimeout(function() {
                        fetch('{{ route("learner.publication.status", $publication->id) }}')
                            .then(r => r.json())
                            .then(data => {
                                const labels = {parsing:'Analyserer manuskript...', composing:'Setter opp bok...', generating:'Genererer PDF...'};
                                document.getElementById('status-text').textContent = 'Status: ' + (labels[data.status] || data.status);
                                if (['parsing','composing','generating'].includes(data.status)) {
                                    poll();
                                } else {
                                    location.reload();
                                }
                            }).catch(() => setTimeout(poll, 5000));
                    }, 3000);
                })();
            </script>
        @elseif($publication->hasError())
            <div class="status-bar status-error">
                <p style="font-weight: 600;">Noe gikk galt</p>
                <p>{{ $publication->error_message }}</p>
            </div>
            <form action="{{ route('learner.publication.generate', $publication->id) }}" method="POST" style="margin-top: 16px;">
                @csrf
                <button type="submit" class="pub-btn">Prøv igjen</button>
            </form>
        @elseif($publication->isReady())
            <div class="status-bar status-ready" style="margin-bottom: 20px;">
                <p style="font-weight: 600; font-size: 1.1rem;">Boken er klar!</p>
                @if($publication->word_count)
                    <p>{{ number_format($publication->word_count) }} ord &middot; {{ $publication->page_count }} sider &middot; {{ $publication->chapter_count }} kapitler</p>
                @endif
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                @if($publication->output_pdf)
                    <a href="{{ route('learner.publication.preview', $publication->id) }}" target="_blank" class="pub-btn-outline">Forhåndsvis PDF</a>
                    <a href="{{ route('learner.publication.download', [$publication->id, 'pdf']) }}" class="pub-btn">Last ned PDF</a>
                @endif
                @if($publication->output_epub)
                    <a href="{{ route('learner.publication.download', [$publication->id, 'epub']) }}" class="pub-btn-outline">Last ned EPUB</a>
                @endif
                @if($publication->output_docx)
                    <a href="{{ route('learner.publication.download', [$publication->id, 'docx']) }}" class="pub-btn-outline">Last ned Word</a>
                @endif
                @if($publication->output_pdf)
                    <a href="{{ route('learner.publication.download', [$publication->id, 'idml']) }}" class="pub-btn-outline">Last ned InDesign (IDML)</a>
                @endif
            </div>
            <form action="{{ route('learner.publication.generate', $publication->id) }}" method="POST" style="margin-top: 20px;">
                @csrf
                <button type="submit" class="pub-btn-outline">Generer på nytt</button>
            </form>
        @else
            <p style="margin-bottom: 16px;">Alt er klart. Klikk knappen under for å generere trykkeklare filer fra manuskriptet ditt.</p>
            <div style="background: #f8f6f3; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                <strong>Oppsummering:</strong><br>
                Tema: {{ ucfirst($publication->theme ?? 'classic') }} &middot;
                Format: {{ $publication->trim_size ?? '140x220' }} mm &middot;
                Papir: {{ \App\Services\Publishing\PaperType::tryFrom($publication->paper_type ?? 'munken_cream_100')?->label() ?? $publication->paper_type }}
            </div>
            <form action="{{ route('learner.publication.generate', $publication->id) }}" method="POST">
                @csrf
                <button type="submit" class="pub-btn">Generer bok</button>
            </form>
        @endif
    </div>
    @endif
</div>
@endsection
