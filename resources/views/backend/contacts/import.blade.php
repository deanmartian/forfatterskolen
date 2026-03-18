@extends('backend.layout')

@section('title')
    <title>Importer kontakter</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 900px;">
    <h3><i class="fa fa-download"></i> Importer kontakter fra ActiveCampaign</h3>
    <p class="text-muted">{{ number_format($contactCount) }} kontakter finnes allerede i systemet.</p>

    <div id="importApp">
        {{-- Steg 1: Velg metode --}}
        <div id="setupPanel" class="card" style="margin-top: 20px;">
            <div class="card-body" style="padding: 25px;">
                <h5>Velg importmetode</h5>

                <div class="form-group" style="margin-top: 15px;">
                    <label class="d-block">
                        <input type="radio" name="method" value="api" checked onchange="toggleMethod()">
                        <strong>Via ActiveCampaign API</strong> <span class="badge badge-success">Anbefalt</span>
                        <div class="text-muted" style="margin-left: 20px;">Henter alle kontakter direkte fra AC med tags</div>
                    </label>
                </div>
                <div class="form-group">
                    <label class="d-block">
                        <input type="radio" name="method" value="csv" onchange="toggleMethod()">
                        <strong>Last opp CSV-fil</strong>
                        <div class="text-muted" style="margin-left: 20px;">Eksporter CSV fra ActiveCampaign og last opp her</div>
                    </label>
                </div>

                {{-- API-status --}}
                <div id="apiStatus" style="margin: 15px 0; padding: 12px; border-radius: 6px; background: #f8f9fa;">
                    <span id="apiStatusText"><i class="fa fa-spinner fa-spin"></i> Sjekker API-tilkobling...</span>
                </div>

                {{-- CSV-opplasting --}}
                <div id="csvUpload" style="display: none; margin: 15px 0;">
                    <input type="file" id="csvFile" accept=".csv,.txt" class="form-control">
                </div>

                <hr>
                <h5>Importalternativer</h5>
                <div class="form-check"><label><input type="checkbox" id="importTags" checked> Importer tags</label></div>
                <div class="form-check"><label><input type="checkbox" id="matchUsers" checked> Match mot eksisterende brukere</label></div>
                <div class="form-check"><label><input type="checkbox" id="skipDuplicates" checked> Hopp over duplikater</label></div>
                <div class="form-check"><label><input type="checkbox" id="importUnsubscribed" checked> Importer avmeldte (status: unsubscribed)</label></div>

                <div style="margin-top: 20px;">
                    <button id="startBtn" class="btn btn-primary" onclick="startImport()" disabled>
                        <i class="fa fa-play"></i> Start import
                    </button>
                </div>
            </div>
        </div>

        {{-- Steg 2: Fremdrift --}}
        <div id="progressPanel" class="card" style="margin-top: 20px; display: none;">
            <div class="card-body" style="padding: 25px;">
                <h5 id="progressTitle">Importerer kontakter...</h5>

                <div style="margin: 20px 0;">
                    <div class="progress" style="height: 25px; border-radius: 12px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 0%; background: #862736;">
                            <span id="progressPercent">0%</span>
                        </div>
                    </div>
                </div>

                <div id="progressStats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <div style="padding: 10px; background: #d4edda; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #155724;" id="statImported">0</div>
                        <div style="font-size: 12px; color: #155724;">Importert</div>
                    </div>
                    <div style="padding: 10px; background: #cce5ff; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #004085;" id="statMatched">0</div>
                        <div style="font-size: 12px; color: #004085;">Matchet bruker</div>
                    </div>
                    <div style="padding: 10px; background: #fff3cd; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #856404;" id="statDuplicates">0</div>
                        <div style="font-size: 12px; color: #856404;">Duplikater</div>
                    </div>
                    <div style="padding: 10px; background: #f8d7da; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #721c24;" id="statFailed">0</div>
                        <div style="font-size: 12px; color: #721c24;">Feilet</div>
                    </div>
                    <div style="padding: 10px; background: #e2e3e5; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #383d41;" id="statUnsubscribed">0</div>
                        <div style="font-size: 12px; color: #383d41;">Avmeldte</div>
                    </div>
                    <div style="padding: 10px; background: #e2e3e5; border-radius: 6px; text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: #383d41;" id="statProcessed">0</div>
                        <div style="font-size: 12px; color: #383d41;">Totalt behandlet</div>
                    </div>
                </div>

                <p id="progressMessage" class="text-muted" style="margin-top: 15px;"></p>
            </div>
        </div>

        {{-- Steg 3: Ferdig --}}
        <div id="completedPanel" class="card" style="margin-top: 20px; display: none;">
            <div class="card-body" style="padding: 25px; text-align: center;">
                <div style="font-size: 48px;">🎉</div>
                <h4>Import fullført!</h4>
                <div id="completedStats" style="margin: 20px 0;"></div>
                <a href="{{ route('admin.crm.contacts.index') }}" class="btn btn-primary">
                    <i class="fa fa-address-book"></i> Gå til kontakter
                </a>
                <button class="btn btn-outline-secondary" onclick="resetImport()">
                    <i class="fa fa-refresh"></i> Ny import
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let pollInterval = null;
let totalEstimate = 0;

// Sjekk API-tilkobling ved innlasting
$(document).ready(function() {
    checkApi();
});

function toggleMethod() {
    const method = $('input[name="method"]:checked').val();
    $('#csvUpload').toggle(method === 'csv');
    $('#apiStatus').toggle(method === 'api');

    if (method === 'csv') {
        $('#startBtn').prop('disabled', !$('#csvFile').val());
        $('#csvFile').off('change').on('change', function() {
            $('#startBtn').prop('disabled', !$(this).val());
        });
    } else {
        checkApi();
    }
}

function checkApi() {
    $('#apiStatusText').html('<i class="fa fa-spinner fa-spin"></i> Sjekker API-tilkobling...');

    $.get('{{ route("admin.contacts.import.test-api") }}', function(data) {
        if (data.connected) {
            totalEstimate = data.total;
            $('#apiStatus').css('background', '#d4edda');
            $('#apiStatusText').html('✅ Tilkoblet — ' + Number(data.total).toLocaleString('nb-NO') + ' kontakter i ActiveCampaign');
            $('#startBtn').prop('disabled', false);
        } else {
            $('#apiStatus').css('background', '#f8d7da');
            $('#apiStatusText').html('❌ Kunne ikke koble til: ' + (data.error || 'Ukjent feil'));
            $('#startBtn').prop('disabled', true);
        }
    }).fail(function() {
        $('#apiStatus').css('background', '#f8d7da');
        $('#apiStatusText').html('❌ Nettverksfeil — sjekk .env');
        $('#startBtn').prop('disabled', true);
    });
}

function startImport() {
    const method = $('input[name="method"]:checked').val();
    const formData = new FormData();

    formData.append('_token', '{{ csrf_token() }}');
    formData.append('method', method);
    formData.append('import_tags', $('#importTags').is(':checked') ? 1 : 0);
    formData.append('match_users', $('#matchUsers').is(':checked') ? 1 : 0);
    formData.append('skip_duplicates', $('#skipDuplicates').is(':checked') ? 1 : 0);
    formData.append('import_unsubscribed', $('#importUnsubscribed').is(':checked') ? 1 : 0);

    if (method === 'csv') {
        const file = $('#csvFile')[0].files[0];
        if (!file) { alert('Velg en CSV-fil først'); return; }
        formData.append('csv_file', file);
    }

    $('#setupPanel').hide();
    $('#progressPanel').show();

    $.ajax({
        url: '{{ route("admin.contacts.import.start") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function() {
            pollProgress();
            pollInterval = setInterval(pollProgress, 2000);
        },
        error: function(xhr) {
            alert('Feil ved start: ' + (xhr.responseJSON?.message || 'Ukjent feil'));
            $('#setupPanel').show();
            $('#progressPanel').hide();
        }
    });
}

function pollProgress() {
    $.get('{{ route("admin.contacts.import.progress") }}', function(data) {
        if (data.status === 'idle') return;

        const stats = data.stats || {};
        const processed = data.processed || 0;
        const percent = totalEstimate > 0 ? Math.min(99, Math.round((processed / totalEstimate) * 100)) : 0;

        $('#statImported').text(Number(stats.imported || 0).toLocaleString('nb-NO'));
        $('#statMatched').text(Number(stats.matched || 0).toLocaleString('nb-NO'));
        $('#statDuplicates').text(Number(stats.duplicates || 0).toLocaleString('nb-NO'));
        $('#statFailed').text(Number(stats.failed || 0).toLocaleString('nb-NO'));
        $('#statUnsubscribed').text(Number(stats.unsubscribed || 0).toLocaleString('nb-NO'));
        $('#statProcessed').text(Number(processed).toLocaleString('nb-NO'));

        if (data.status === 'completed') {
            clearInterval(pollInterval);
            $('#progressBar').css('width', '100%').removeClass('progress-bar-animated');
            $('#progressPercent').text('100%');
            $('#progressTitle').text('Import fullført!');
            $('#progressMessage').text(data.message || '');

            setTimeout(function() {
                $('#progressPanel').hide();
                $('#completedPanel').show();
                $('#completedStats').html(
                    '<p>✅ Nye kontakter: <strong>' + Number(stats.imported || 0).toLocaleString('nb-NO') + '</strong></p>' +
                    '<p>🔗 Matchet mot brukere: <strong>' + Number(stats.matched || 0).toLocaleString('nb-NO') + '</strong></p>' +
                    '<p>⚠️ Duplikater: <strong>' + Number(stats.duplicates || 0).toLocaleString('nb-NO') + '</strong></p>' +
                    '<p>🚫 Avmeldte: <strong>' + Number(stats.unsubscribed || 0).toLocaleString('nb-NO') + '</strong></p>' +
                    '<p>❌ Feilet: <strong>' + Number(stats.failed || 0).toLocaleString('nb-NO') + '</strong></p>'
                );
            }, 1500);
        } else {
            $('#progressBar').css('width', percent + '%');
            $('#progressPercent').text(percent + '%');
            $('#progressMessage').text(data.message || 'Behandler kontakter...');
        }
    });
}

function resetImport() {
    $.post('{{ route("admin.contacts.import.reset") }}', {_token: '{{ csrf_token() }}'}, function() {
        $('#completedPanel').hide();
        $('#progressPanel').hide();
        $('#setupPanel').show();
        checkApi();
    });
}
</script>
@endsection
