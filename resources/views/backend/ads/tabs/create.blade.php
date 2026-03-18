{{-- Opprett ny annonse --}}
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body" style="padding: 25px;">
                <h5>Opprett ny annonse</h5>

                <form id="createAdForm">
                    @csrf

                    {{-- Steg 1: Plattform og type --}}
                    <div class="form-group">
                        <label><strong>Plattform og type</strong></label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="platform" id="adPlatform" class="form-control" onchange="updateTypeOptions()">
                                    <option value="facebook">Facebook</option>
                                    <option value="google">Google</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="type" id="adType" class="form-control">
                                    <option value="lead">Lead Ad</option>
                                    <option value="retargeting">Retargeting</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 2: Koble til webinar --}}
                    <div class="form-group">
                        <label><strong>Koble til webinar (valgfritt)</strong></label>
                        <select name="free_webinar_id" id="webinarSelect" class="form-control" onchange="prefillFromWebinar()">
                            <option value="">— Ingen —</option>
                            @foreach($webinars as $w)
                                <option value="{{ $w->id }}" data-title="{{ $w->title }}" data-date="{{ $w->start_date?->format('d.m.Y H:i') }}">
                                    {{ $w->title }} ({{ $w->start_date?->format('d.m.Y') ?? 'Ingen dato' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Steg 3: Kampanjenavn og budsjett --}}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label><strong>Kampanjenavn</strong></label>
                                <input type="text" name="name" id="adName" class="form-control" placeholder="F.eks. Lead Ad — Gro Dahle webinar" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Daglig budsjett (kr)</strong></label>
                                <input type="number" name="daily_budget" class="form-control" value="200" min="50" step="50" required>
                            </div>
                        </div>
                    </div>

                    {{-- Steg 4: Annonsetekst --}}
                    <div class="form-group">
                        <label><strong>Overskrift</strong></label>
                        <input type="text" name="headline" id="adHeadline" class="form-control" placeholder="Gratis webinar med Gro Dahle" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label><strong>Annonsetekst</strong></label>
                        <textarea name="ad_text" id="adText" class="form-control" rows="4" placeholder="Lær å skape karakterer som lever..."></textarea>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="generateAiText()">
                            <i class="fa fa-magic"></i> Generer med AI
                        </button>
                    </div>

                    {{-- Google: Nøkkelord --}}
                    <div id="googleKeywords" style="display: none;">
                        <div class="form-group">
                            <label><strong>Nøkkelord (ett per linje)</strong></label>
                            <textarea name="keywords" id="adKeywords" class="form-control" rows="5" placeholder="skrivekurs&#10;romankurs&#10;forfatterkurs"></textarea>
                        </div>
                    </div>

                    {{-- Målgruppe --}}
                    <div class="form-group">
                        <label><strong>Målgruppe</strong></label>
                        <select name="audience" class="form-control">
                            <option value="all">Alle (prospecting)</option>
                            <option value="course_visitors">Kursside-besøkende siste 30 dager</option>
                            <option value="webinar_no_purchase">Webinar-påmeldte uten kurskjøp</option>
                            <option value="checkout_abandoners">Checkout-avbrytere siste 7 dager</option>
                            <option value="past_students">Tidligere elever (90+ dager)</option>
                        </select>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="submitDraft()">
                                <i class="fa fa-save"></i> Lagre som utkast
                            </button>
                        </div>
                        <div class="col-md-6 text-right" id="launchBtnContainer">
                            <button type="button" class="btn btn-success" onclick="launchFacebookLead()" id="launchFbBtn">
                                <i class="fa fa-rocket"></i> Opprett og publiser på Facebook
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Forhåndsvisning --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="padding: 20px;">
                <h6 class="text-muted">Forhåndsvisning</h6>
                <div id="adPreview" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-top: 10px; background: #fff;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: #862736; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 14px;">F</div>
                        <div style="margin-left: 8px;">
                            <div style="font-weight: bold; font-size: 13px;">Forfatterskolen</div>
                            <div style="font-size: 11px; color: #999;">Sponset</div>
                        </div>
                    </div>
                    <div id="previewText" style="font-size: 14px; margin-bottom: 10px; color: #333;">Din annonsetekst vises her...</div>
                    <div style="background: #f0f0f0; padding: 30px; text-align: center; border-radius: 4px; margin-bottom: 8px;">
                        <i class="fa fa-image" style="font-size: 36px; color: #ccc;"></i>
                    </div>
                    <div id="previewHeadline" style="font-weight: bold; font-size: 15px;">Overskrift</div>
                    <div style="font-size: 12px; color: #999;">forfatterskolen.no</div>
                    <div style="margin-top: 10px;">
                        <span style="background: #862736; color: #fff; padding: 6px 16px; border-radius: 4px; font-size: 13px;">Meld deg på</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTypeOptions() {
    const platform = $('#adPlatform').val();
    const $type = $('#adType');
    $type.empty();

    if (platform === 'facebook') {
        $type.append('<option value="lead">Lead Ad</option>');
        $type.append('<option value="retargeting">Retargeting</option>');
        $('#googleKeywords').hide();
        $('#launchFbBtn').show();
    } else {
        $type.append('<option value="search">Søkekampanje</option>');
        $type.append('<option value="display">Display Retargeting</option>');
        $('#googleKeywords').show();
        $('#launchFbBtn').hide();
    }
}

function prefillFromWebinar() {
    const $opt = $('#webinarSelect option:selected');
    const title = $opt.data('title');
    if (title) {
        $('#adName').val('Lead Ad — ' + title);
        $('#adHeadline').val('Gratis webinar: ' + title);
        updatePreview();
    }
}

// Live forhåndsvisning
$('#adHeadline, #adText').on('input', updatePreview);
function updatePreview() {
    $('#previewHeadline').text($('#adHeadline').val() || 'Overskrift');
    $('#previewText').text($('#adText').val() || 'Din annonsetekst vises her...');
}

function submitDraft() {
    const form = $('#createAdForm');
    $.post('{{ route("admin.ads.store") }}', form.serialize(), function() {
        window.location.href = '{{ route("admin.ads.index") }}?tab=overview';
    }).fail(function(xhr) {
        alert('Feil: ' + (xhr.responseJSON?.message || 'Ukjent feil'));
    });
}

function launchFacebookLead() {
    if (!confirm('Opprette og publisere denne Facebook Lead Ad nå?')) return;

    const form = $('#createAdForm');
    $.post('{{ route("admin.ads.launch-fb-lead") }}', form.serialize(), function() {
        window.location.href = '{{ route("admin.ads.index") }}?tab=overview';
    }).fail(function(xhr) {
        alert('Feil: ' + (xhr.responseJSON?.message || 'Ukjent feil'));
    });
}

function generateAiText() {
    const webinarId = $('#webinarSelect').val();
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Genererer...';

    $.get('{{ route("admin.ads.generate-text") }}', {free_webinar_id: webinarId}, function(data) {
        if (data.headlines && data.headlines.length) {
            $('#adHeadline').val(data.headlines[0]);
        }
        if (data.descriptions && data.descriptions.length) {
            $('#adText').val(data.descriptions[0]);
        }
        updatePreview();
    }).always(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-magic"></i> Generer med AI';
    });
}
</script>
