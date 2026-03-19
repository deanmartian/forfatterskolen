@extends('backend.layout')

@section('title')
<title>AI Assistent &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
<style>
    .ai-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 15px;
    }
    .ai-header {
        background: #862736;
        color: #fff;
        padding: 20px 25px;
        border-radius: 6px 6px 0 0;
    }
    .ai-header h3 {
        margin: 0;
        font-size: 20px;
    }
    .ai-header p {
        margin: 5px 0 0;
        opacity: 0.8;
        font-size: 13px;
    }
    .ai-body {
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 6px 6px;
        padding: 25px;
    }
    .ai-textarea {
        width: 100%;
        min-height: 100px;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 12px;
        font-size: 14px;
        resize: vertical;
    }
    .ai-textarea:focus {
        outline: none;
        border-color: #862736;
    }
    .ai-submit {
        background: #862736;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        margin-top: 10px;
    }
    .ai-submit:hover {
        background: #6e1f2d;
    }
    .ai-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .ai-result {
        margin-top: 25px;
        display: none;
    }
    .ai-result-card {
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 20px;
    }
    .ai-result-row {
        margin-bottom: 12px;
    }
    .ai-result-label {
        font-weight: 600;
        color: #555;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 3px;
    }
    .ai-result-value {
        font-size: 14px;
        color: #333;
    }
    .ai-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }
    .ai-badge--intent { background: #862736; }
    .ai-badge--confidence { background: #27ae60; }
    .ai-data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 13px;
    }
    .ai-data-table th,
    .ai-data-table td {
        padding: 6px 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
    .ai-data-table th {
        background: #f0f0f0;
        font-weight: 600;
    }
    .ai-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #fff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        vertical-align: middle;
        margin-right: 6px;
    }
    @@keyframes spin { to { transform: rotate(360deg); } }
    .ai-examples {
        margin-top: 15px;
        font-size: 13px;
        color: #888;
    }
    .ai-examples span {
        display: inline-block;
        background: #f5f5f5;
        padding: 3px 8px;
        border-radius: 3px;
        margin: 2px 4px 2px 0;
        cursor: pointer;
        color: #555;
    }
    .ai-examples span:hover {
        background: #e8e8e8;
    }
</style>
@stop

@section('content')
<div class="ai-container">
    <div class="ai-header">
        <h3><i class="fa fa-magic"></i> AI Assistent</h3>
        <p>Skriv hva du vil gjore - finn brukere, se kursoversikt, lag e-postutkast, og mer.</p>
    </div>
    <div class="ai-body">
        <textarea id="aiPrompt" class="ai-textarea" placeholder="F.eks: Finn brukeren med e-post ola@example.com"></textarea>
        <button id="aiSubmit" class="ai-submit" onclick="submitAiPrompt()">
            <i class="fa fa-paper-plane"></i> Send
        </button>

        <div class="ai-examples">
            Eksempler:
            <span onclick="setPrompt('Finn brukeren med e-post test@test.no')">Finn bruker</span>
            <span onclick="setPrompt('Vis alle aktive kurs')">Kursoversikt</span>
            <span onclick="setPrompt('Lag et e-postutkast til alle elever om sommerferien')">E-postutkast</span>
            <span onclick="setPrompt('Lag et kursutkast for et nybegynnerkurs i kreativ skriving')">Kursutkast</span>
        </div>

        <div id="aiResult" class="ai-result">
            <div class="ai-result-card">
                <div class="ai-result-row">
                    <div class="ai-result-label">Intent</div>
                    <div class="ai-result-value"><span id="resIntent" class="ai-badge ai-badge--intent"></span></div>
                </div>
                <div class="ai-result-row">
                    <div class="ai-result-label">Confidence</div>
                    <div class="ai-result-value"><span id="resConfidence" class="ai-badge ai-badge--confidence"></span></div>
                </div>
                <div class="ai-result-row">
                    <div class="ai-result-label">Reasoning</div>
                    <div class="ai-result-value" id="resReasoning"></div>
                </div>
                <div class="ai-result-row">
                    <div class="ai-result-label">AI Data</div>
                    <div class="ai-result-value"><pre id="resData" style="background:#f0f0f0;padding:10px;border-radius:4px;font-size:12px;overflow-x:auto;"></pre></div>
                </div>
                <div class="ai-result-row">
                    <div class="ai-result-label">Resultat</div>
                    <div class="ai-result-value" id="resExecution"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
function setPrompt(text) {
    document.getElementById('aiPrompt').value = text;
}

function submitAiPrompt() {
    var prompt = document.getElementById('aiPrompt').value.trim();
    if (!prompt) return;

    var btn = document.getElementById('aiSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="ai-spinner"></span> Tenker...';
    document.getElementById('aiResult').style.display = 'none';

    fetch('{{ url("/ai/execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ prompt: prompt })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('resIntent').textContent = data.intent || '-';
        document.getElementById('resConfidence').textContent = ((data.confidence || 0) * 100).toFixed(0) + '%';
        document.getElementById('resReasoning').textContent = data.reasoning || '-';
        document.getElementById('resData').textContent = JSON.stringify(data.data || {}, null, 2);

        var exec = data.execution || {};
        var html = '<strong>' + (exec.message || '') + '</strong>';
        if (exec.results && Array.isArray(exec.results) && exec.results.length > 0) {
            html += renderResultsTable(exec.results);
        } else if (exec.results && typeof exec.results === 'object' && !Array.isArray(exec.results)) {
            html += renderResultsTable([exec.results]);
        }
        document.getElementById('resExecution').innerHTML = html;
        document.getElementById('aiResult').style.display = 'block';
    })
    .catch(function(err) {
        alert('Feil: ' + err.message);
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send';
    });
}

function renderResultsTable(rows) {
    if (!rows.length) return '';
    var keys = Object.keys(rows[0]);
    var html = '<table class="ai-data-table"><thead><tr>';
    keys.forEach(function(k) { html += '<th>' + k + '</th>'; });
    html += '</tr></thead><tbody>';
    rows.forEach(function(row) {
        html += '<tr>';
        keys.forEach(function(k) {
            var val = row[k];
            if (val === null || val === undefined) val = '-';
            if (typeof val === 'object') val = JSON.stringify(val);
            html += '<td>' + val + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table>';
    return html;
}

document.getElementById('aiPrompt').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
        submitAiPrompt();
    }
});
</script>
@stop
