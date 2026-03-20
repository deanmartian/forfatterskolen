@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-magic"></i> Ad OS - AI Strategist</h3>
    <a href="{{ route('admin.ads.dashboard') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake til dashboard</a>
</div>

<div class="col-md-12">
    {{-- Instruction Input --}}
    <div class="panel panel-primary">
        <div class="panel-heading">
            <strong><i class="fa fa-commenting"></i> Gi en instruksjon</strong>
        </div>
        <div class="panel-body">
            <p class="text-muted" style="margin-bottom: 15px;">
                Skriv hva du vil gjøre med annonsene, f.eks:
                <em>"Bruk 10.000 kr neste uke på leads til skriveverkstedet"</em>,
                <em>"Skaler winnerne og pause kampanjer med CPA over 50 kr"</em>, eller
                <em>"Reduser budsjett med 30% på alt unntatt Feelgood-kampanjene"</em>
            </p>
            <div class="input-group input-group-lg">
                <input type="text" id="strategist-input" class="form-control" placeholder="Hva vil du gjøre med annonsene?" autofocus>
                <span class="input-group-btn">
                    <button id="strategist-submit" class="btn btn-primary" type="button">
                        <i class="fa fa-paper-plane"></i> Analyser
                    </button>
                </span>
            </div>
        </div>
    </div>

    {{-- Loading indicator --}}
    <div id="strategist-loading" style="display:none; text-align:center; padding:40px;">
        <i class="fa fa-spinner fa-spin fa-3x"></i>
        <p style="margin-top:15px; font-size:16px;">AI-en analyserer kampanjedataene og lager en handlingsplan...</p>
    </div>

    {{-- Error display --}}
    <div id="strategist-error" class="alert alert-danger" style="display:none;"></div>

    {{-- Plan Result --}}
    <div id="strategist-plan" style="display:none;">
        <div class="panel panel-success">
            <div class="panel-heading">
                <strong><i class="fa fa-lightbulb-o"></i> AI-handlingsplan</strong>
            </div>
            <div class="panel-body">
                <div id="plan-summary" style="font-size:16px; margin-bottom:15px;"></div>
                <div id="plan-reasoning" class="well well-sm" style="margin-bottom:20px; background:#f0f8ff;"></div>

                {{-- Warnings --}}
                <div id="plan-warnings" style="display:none; margin-bottom:20px;"></div>

                {{-- Actions --}}
                <div id="plan-actions"></div>

                {{-- Execute button --}}
                <div style="margin-top:20px; text-align:right; border-top:1px solid #eee; padding-top:15px;">
                    <button id="btn-select-all" class="btn btn-default btn-sm" onclick="toggleAllActions(true)">
                        <i class="fa fa-check-square-o"></i> Velg alle
                    </button>
                    <button id="btn-deselect-all" class="btn btn-default btn-sm" onclick="toggleAllActions(false)">
                        <i class="fa fa-square-o"></i> Fjern alle
                    </button>
                    <button id="btn-execute" class="btn btn-success btn-lg" onclick="executeApproved()" style="margin-left:15px;">
                        <i class="fa fa-play"></i> Utfør godkjente handlinger
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Execution Results --}}
    <div id="strategist-results" style="display:none;">
        <div class="panel panel-info">
            <div class="panel-heading">
                <strong><i class="fa fa-check-circle"></i> Resultat</strong>
            </div>
            <div class="panel-body" id="results-content"></div>
        </div>
    </div>

    {{-- History --}}
    @if($history->count() > 0)
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><i class="fa fa-history"></i> Tidligere instruksjoner</strong>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Tid</th>
                        <th>Instruksjon</th>
                        <th>Handlinger</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $conv)
                    <tr>
                        <td>{{ $conv->created_at->format('d.m H:i') }}</td>
                        <td>{{ Str::limit($conv->instruction, 80) }}</td>
                        <td>
                            {{ count($conv->ai_response['actions'] ?? []) }} foreslått
                            @if($conv->execution_results)
                                / {{ collect($conv->execution_results)->where('success', true)->count() }} utført
                            @endif
                        </td>
                        <td>
                            <span class="label label-{{ $conv->status === 'executed' ? 'success' : ($conv->status === 'failed' ? 'danger' : 'warning') }}">
                                {{ $conv->status === 'executed' ? 'Utført' : ($conv->status === 'failed' ? 'Feilet' : 'Venter') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
var currentConversationId = null;
var currentPlan = null;

document.getElementById('strategist-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') document.getElementById('strategist-submit').click();
});

document.getElementById('strategist-submit').addEventListener('click', function() {
    var instruction = document.getElementById('strategist-input').value.trim();
    if (!instruction) return;

    // Reset UI
    document.getElementById('strategist-plan').style.display = 'none';
    document.getElementById('strategist-results').style.display = 'none';
    document.getElementById('strategist-error').style.display = 'none';
    document.getElementById('strategist-loading').style.display = 'block';
    document.getElementById('strategist-submit').disabled = true;

    fetch('{{ route("admin.ads.strategist.ask") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ instruction: instruction }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('strategist-loading').style.display = 'none';
        document.getElementById('strategist-submit').disabled = false;

        if (!data.success) {
            document.getElementById('strategist-error').textContent = data.error || 'Noe gikk galt.';
            document.getElementById('strategist-error').style.display = 'block';
            return;
        }

        currentConversationId = data.conversation_id;
        currentPlan = data.plan;
        renderPlan(data.plan);
    })
    .catch(function(err) {
        document.getElementById('strategist-loading').style.display = 'none';
        document.getElementById('strategist-submit').disabled = false;
        document.getElementById('strategist-error').textContent = 'Nettverksfeil: ' + err.message;
        document.getElementById('strategist-error').style.display = 'block';
    });
});

function renderPlan(plan) {
    document.getElementById('plan-summary').innerHTML = '<strong>' + escapeHtml(plan.summary) + '</strong>';
    document.getElementById('plan-reasoning').innerHTML = '<i class="fa fa-brain"></i> ' + escapeHtml(plan.reasoning || '');

    // Warnings
    var warningsEl = document.getElementById('plan-warnings');
    if (plan.warnings && plan.warnings.length > 0) {
        var html = '';
        plan.warnings.forEach(function(w) {
            html += '<div class="alert alert-warning" style="margin-bottom:5px;"><i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(w) + '</div>';
        });
        warningsEl.innerHTML = html;
        warningsEl.style.display = 'block';
    } else {
        warningsEl.style.display = 'none';
    }

    // Actions
    var actionsEl = document.getElementById('plan-actions');
    var html = '';
    plan.actions.forEach(function(action, index) {
        var riskColor = action.risk_level === 'high' ? 'danger' : (action.risk_level === 'medium' ? 'warning' : 'success');
        var riskLabel = action.risk_level === 'high' ? 'Hoy risiko' : (action.risk_level === 'medium' ? 'Middels risiko' : 'Lav risiko');
        var typeIcon = getActionIcon(action.type);
        var typeLabel = getActionLabel(action.type);

        html += '<div class="panel panel-default" style="border-left: 4px solid ' + getRiskBorderColor(action.risk_level) + ';">';
        html += '<div class="panel-body">';
        html += '<div class="row">';
        html += '<div class="col-md-1 text-center" style="padding-top:5px;">';
        html += '<input type="checkbox" class="action-checkbox" data-index="' + index + '" checked style="transform:scale(1.5);">';
        html += '</div>';
        html += '<div class="col-md-8">';
        html += '<h4 style="margin-top:0;"><i class="fa ' + typeIcon + '"></i> ' + escapeHtml(action.description) + '</h4>';
        html += '<p class="text-muted" style="margin-bottom:5px;">' + escapeHtml(action.reasoning || '') + '</p>';
        if (action.details && Object.keys(action.details).length > 0) {
            html += '<code style="font-size:12px;">' + escapeHtml(JSON.stringify(action.details)) + '</code>';
        }
        html += '</div>';
        html += '<div class="col-md-3 text-right">';
        html += '<span class="label label-default" style="margin-right:5px;">' + escapeHtml(typeLabel) + '</span>';
        html += '<span class="label label-' + riskColor + '">' + riskLabel + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    });

    actionsEl.innerHTML = html;
    document.getElementById('strategist-plan').style.display = 'block';
}

function getActionIcon(type) {
    var icons = {
        'scale_budget': 'fa-arrow-up',
        'reduce_budget': 'fa-arrow-down',
        'pause_campaign': 'fa-pause',
        'resume_campaign': 'fa-play',
        'create_campaign': 'fa-plus-circle',
        'reallocate_budget': 'fa-exchange',
        'create_creatives': 'fa-paint-brush',
    };
    return icons[type] || 'fa-cog';
}

function getActionLabel(type) {
    var labels = {
        'scale_budget': 'Opp budsjett',
        'reduce_budget': 'Ned budsjett',
        'pause_campaign': 'Pause',
        'resume_campaign': 'Gjenoppta',
        'create_campaign': 'Ny kampanje',
        'reallocate_budget': 'Omalloker',
        'create_creatives': 'Nye kreativer',
    };
    return labels[type] || type;
}

function getRiskBorderColor(level) {
    return level === 'high' ? '#d9534f' : (level === 'medium' ? '#f0ad4e' : '#5cb85c');
}

function toggleAllActions(checked) {
    document.querySelectorAll('.action-checkbox').forEach(function(cb) { cb.checked = checked; });
}

function executeApproved() {
    var approved = [];
    document.querySelectorAll('.action-checkbox:checked').forEach(function(cb) {
        approved.push(parseInt(cb.getAttribute('data-index')));
    });

    if (approved.length === 0) {
        alert('Velg minst en handling å utføre.');
        return;
    }

    if (!confirm('Er du sikker på at du vil utføre ' + approved.length + ' handling(er)?')) return;

    document.getElementById('btn-execute').disabled = true;
    document.getElementById('btn-execute').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Utfører...';

    fetch('{{ route("admin.ads.strategist.execute") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            conversation_id: currentConversationId,
            approved_actions: approved,
        }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('btn-execute').disabled = false;
        document.getElementById('btn-execute').innerHTML = '<i class="fa fa-play"></i> Utfør godkjente handlinger';

        if (!data.success) {
            alert('Feil: ' + (data.error || 'Ukjent feil'));
            return;
        }

        renderResults(data.results);
    })
    .catch(function(err) {
        document.getElementById('btn-execute').disabled = false;
        document.getElementById('btn-execute').innerHTML = '<i class="fa fa-play"></i> Utfør godkjente handlinger';
        alert('Nettverksfeil: ' + err.message);
    });
}

function renderResults(results) {
    var html = '';
    results.forEach(function(r) {
        var icon = r.success ? 'fa-check-circle text-success' : 'fa-times-circle text-danger';
        var actionDesc = r.action ? (r.action.description || r.action.type) : 'Ukjent';
        html += '<div style="padding:8px 0; border-bottom:1px solid #eee;">';
        html += '<i class="fa ' + icon + '"></i> ';
        html += '<strong>' + escapeHtml(actionDesc) + '</strong>';
        if (r.success && r.result) {
            html += ' <span class="text-muted">(' + escapeHtml(JSON.stringify(r.result)) + ')</span>';
        }
        if (!r.success && r.error) {
            html += ' <span class="text-danger">' + escapeHtml(r.error) + '</span>';
        }
        html += '</div>';
    });
    document.getElementById('results-content').innerHTML = html;
    document.getElementById('strategist-results').style.display = 'block';
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>
@stop
