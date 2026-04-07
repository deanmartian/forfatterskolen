@extends('editor.layout')

@section('title')
<title>Dashboard &rsaquo; Forfatterskolen Redaktørportal</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
		/* ══════════════════════════════════════════
		   EDITOR DASHBOARD — REDESIGN
		══════════════════════════════════════════ */
		:root {
			--brand-primary: #862736;
			--brand-dark: #5e1a26;
			--brand-light: #a8344a;
			--brand-accent: #d4a853;
			--bg: #f6f5f3;
			--surface: #ffffff;
			--text: #2c2c2c;
			--text-secondary: #6b6b6b;
			--muted: #999;
			--border: #e4e1dc;
			--border-light: #f0ede8;
			--success: #2d8a56;
			--warning: #d4a020;
			--info: #2a7ab5;
			--danger: #c0392b;
			--radius: 10px;
			--radius-sm: 6px;
			--shadow-sm: 0 1px 3px rgba(0,0,0,.06);
			--shadow-md: 0 4px 16px rgba(0,0,0,.08);
			--font-display: 'Playfair Display', Georgia, serif;
			--font-body: 'Source Sans 3', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
		}

		.editor-dashboard {
			padding: 0;
			font-family: var(--font-body);
			color: var(--text);
			background: var(--bg);
		}

		/* ── Welcome Banner ── */
		.welcome-banner {
			background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-dark) 100%);
			color: #fff;
			padding: 28px 32px;
			border-radius: var(--radius);
			margin-bottom: 24px;
			position: relative;
			overflow: hidden;
		}
		.welcome-banner::after {
			content: '';
			position: absolute;
			top: -40px; right: -40px;
			width: 180px; height: 180px;
			background: rgba(255,255,255,.06);
			border-radius: 50%;
		}
		.welcome-banner h2 {
			font-family: var(--font-display);
			font-size: 1.6rem;
			font-weight: 600;
			margin: 0 0 4px;
		}
		.welcome-banner p {
			opacity: .85;
			margin: 0;
			font-size: .95rem;
		}

		/* ── Stat Cards ── */
		.stat-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 16px;
			margin-bottom: 28px;
		}
		.stat-card {
			background: var(--surface);
			border-radius: var(--radius);
			padding: 20px 22px;
			box-shadow: var(--shadow-sm);
			border: 1px solid var(--border-light);
			display: flex;
			align-items: center;
			gap: 16px;
			transition: box-shadow .2s, transform .15s;
		}
		.stat-card:hover {
			box-shadow: var(--shadow-md);
			transform: translateY(-2px);
		}
		.stat-icon {
			width: 46px; height: 46px;
			border-radius: var(--radius-sm);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.2rem;
			flex-shrink: 0;
		}
		.stat-icon.personal   { background: #fef3f4; color: var(--brand-primary); }
		.stat-icon.shop       { background: #fef8ec; color: var(--warning); }
		.stat-icon.assignment  { background: #eef6f1; color: var(--success); }
		.stat-icon.coaching    { background: #eef3fa; color: var(--info); }
		.stat-icon.correction  { background: #fef3f4; color: var(--danger); }
		.stat-icon.copyedit    { background: #f3eefa; color: #7c3aed; }
		.stat-icon.publishing  { background: #fef8ec; color: #b45309; }
		.stat-icon.editing     { background: #eef3fa; color: #1d4ed8; }
		.stat-icon.free        { background: #eef6f1; color: #047857; }
		.stat-icon.project     { background: #f0f0f0; color: #555; }
		.stat-value {
			font-size: 1.6rem;
			font-weight: 700;
			line-height: 1;
			color: var(--text);
		}
		.stat-label {
			font-size: .78rem;
			text-transform: uppercase;
			letter-spacing: .04em;
			color: var(--text-secondary);
			margin-top: 2px;
		}

		/* ── Section Panel ── */
		.dashboard-section {
			background: var(--surface);
			border-radius: var(--radius);
			box-shadow: var(--shadow-sm);
			border: 1px solid var(--border-light);
			margin-bottom: 20px;
			overflow: hidden;
		}
		.section-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 18px 24px;
			border-bottom: 1px solid var(--border-light);
		}
		.section-header h4 {
			font-family: var(--font-display);
			font-size: 1.15rem;
			font-weight: 600;
			margin: 0;
			color: var(--text);
		}
		.section-badge {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-width: 26px;
			height: 26px;
			padding: 0 8px;
			border-radius: 13px;
			font-size: .8rem;
			font-weight: 600;
			color: #fff;
			margin-left: 10px;
		}
		.section-badge.brand   { background: var(--brand-primary); }
		.section-badge.warning { background: var(--warning); }
		.section-badge.success { background: var(--success); }
		.section-badge.info    { background: var(--info); }
		.section-badge.danger  { background: var(--danger); }
		.section-badge.purple  { background: #7c3aed; }
		.section-badge.orange  { background: #b45309; }
		.section-badge.blue    { background: #1d4ed8; }
		.section-badge.neutral { background: #888; }
		.section-body {
			padding: 12px 24px 20px;
			overflow-x: auto;
		}

		/* ── Tables ── */
		.dashboard-section .table {
			margin-bottom: 0;
			font-size: .9rem;
		}
		.dashboard-section .table thead th {
			background: var(--bg);
			border-bottom: 2px solid var(--border);
			font-size: .78rem;
			text-transform: uppercase;
			letter-spacing: .04em;
			color: var(--text-secondary);
			font-weight: 600;
			padding: 10px 12px;
			white-space: nowrap;
		}
		.dashboard-section .table tbody td {
			padding: 10px 12px;
			vertical-align: middle;
			border-bottom: 1px solid var(--border-light);
			color: var(--text);
		}
		.dashboard-section .table tbody tr:hover {
			background: #fafaf8;
		}
		.dashboard-section .table tbody tr:last-child td {
			border-bottom: none;
		}
		.dashboard-section .dataTables_wrapper .dataTables_length,
		.dashboard-section .dataTables_wrapper .dataTables_filter,
		.dashboard-section .dataTables_wrapper .dataTables_info,
		.dashboard-section .dataTables_wrapper .dataTables_paginate {
			font-size: .85rem;
			padding: 8px 0;
		}

		/* Request rows (beige highlight) */
		.dashboard-section .table tbody tr.request-row {
			background-color: #fdf8ef;
		}
		.dashboard-section .table tbody tr.request-row:hover {
			background-color: #faf2e4;
		}

		/* ── Buttons overrides ── */
		.btn-brand {
			background: var(--brand-primary);
			border-color: var(--brand-primary);
			color: #fff;
		}
		.btn-brand:hover {
			background: var(--brand-dark);
			border-color: var(--brand-dark);
			color: #fff;
		}

		/* ── Empty state ── */
		.empty-state {
			text-align: center;
			padding: 32px 16px;
			color: var(--muted);
		}
		.empty-state i {
			font-size: 2rem;
			margin-bottom: 8px;
			display: block;
			opacity: .4;
		}
		.lock-label { display: inline-flex; align-items: center; gap: 4px; }
		.lock-label input[type="checkbox"] { display: none; }
		.lock-label i { font-size: 16px; cursor: pointer; transition: color 0.15s; }
		.lock-label:hover i { opacity: 0.7; }

		/* ── Labels ── */
		.label-pending {
			background: var(--warning);
			color: #fff;
		}

		/* ── Responsive ── */
		@media (max-width: 768px) {
			.stat-grid {
				grid-template-columns: repeat(2, 1fr);
			}
			.welcome-banner {
				padding: 20px;
			}
			.section-header {
				padding: 14px 16px;
			}
			.section-body {
				padding: 8px 12px 16px;
			}
		}
	</style>
@stop

@section('content')
@php $cacheBuster = now()->timestamp; @endphp
<div class="col-sm-12 editor-dashboard">

	{{-- ═══════ WELCOME BANNER ═══════ --}}
	<div class="welcome-banner">
		<h2>God morgen, {{ auth()->user()->first_name ?? 'Redaktør' }}</h2>
		<p>
			Du har
			<strong>{{ $assignedAssignmentManuscripts->count()
				+ $assigned_shop_manuscripts->filter(fn($m) => in_array($m->status, ['Started','Pending']))->count()
				+ $assignedAssignments->count()
				+ $freeManuscripts->count()
				+ $coachingTimers->count()
				+ $selfPublishingList->count()
				+ $corrections->count()
				+ $copyEditings->count()
				+ $editingAssignments->count()
			}}</strong>
			aktive oppgaver som venter på tilbakemelding.
		</p>
	</div>

	{{-- ═══════ KAPASITET-VARSLING ═══════ --}}
	@php
		$hasActiveCapacity = \App\ManuscriptEditorCanTake::where('editor_id', auth()->id())
			->where(function ($q) { $q->where('date_to', '>=', now()->toDateString())->orWhereNull('date_to'); })
			->exists();
	@endphp
	@if(!$hasActiveCapacity)
		<div style="background:#fff3e0;border:1px solid #ffe0b2;border-left:4px solid #e65100;border-radius:8px;padding:16px 20px;margin-bottom:1rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
			<div>
				<strong style="color:#e65100;"><i class="fa fa-exclamation-triangle"></i> Kapasitet ikke satt</strong>
				<p style="margin:4px 0 0;font-size:14px;color:#5a5550;">Du har ikke registrert din kapasitet. Oppdater innstillingene slik at vi vet hvor mange manus du kan ta.</p>
			</div>
			<a href="{{ route('editor.settings') }}" class="btn btn-warning btn-sm" style="white-space:nowrap;">
				<i class="fa fa-cog"></i> Sett kapasitet
			</a>
		</div>
	@endif

	{{-- ═══════ PUSH NOTIFICATION SETTINGS ═══════ --}}
	<div style="background:#fff;border:1px solid #e8e4de;border-radius:10px;padding:16px 20px;margin-bottom:1rem;">
		<div style="display:flex;align-items:center;justify-content:space-between;">
			<div>
				<strong style="font-size:14px;">📱 Push-varsler</strong>
				<span id="edPushStatus" style="font-size:12px;margin-left:8px;"></span>
			</div>
			<button id="edPushBtn" onclick="toggleEditorPush()" style="padding:6px 14px;border-radius:6px;border:1px solid #ddd;background:#fff;font-size:12px;cursor:pointer;">
				Sjekker...
			</button>
		</div>
	</div>
	<script>
	(function() {
		var btn = document.getElementById('edPushBtn');
		var status = document.getElementById('edPushStatus');
		if (!('PushManager' in window)) { btn.textContent = 'Ikke støttet'; status.textContent = '(nettleseren støtter ikke push)'; return; }
		navigator.serviceWorker && navigator.serviceWorker.ready.then(function(reg) {
			reg.pushManager.getSubscription().then(function(sub) {
				if (sub) { btn.textContent = 'Slå av'; btn.style.background='#e8f5e9'; btn.style.color='#2e7d32'; btn.style.borderColor='#2e7d32'; status.innerHTML = '<span style="color:#2e7d32;">✓ Aktivert</span>'; }
				else { btn.textContent = 'Aktiver'; btn.style.background='#862736'; btn.style.color='#fff'; btn.style.borderColor='#862736'; status.innerHTML = '<span style="color:#e65100;">Ikke aktivert</span>'; }
			});
		});
	})();
	function toggleEditorPush() {
		navigator.serviceWorker.ready.then(function(reg) {
			reg.pushManager.getSubscription().then(function(sub) {
				if (sub) { sub.unsubscribe().then(function() { location.reload(); }); }
				else { Notification.requestPermission().then(function(p) { if (p==='granted' && typeof subscribePush==='function') { subscribePush(reg); location.reload(); } }); }
			});
		});
	}
	</script>

	{{-- ═══════ INSTALL APP BANNER ═══════ --}}
	<div id="editorInstallBanner" style="display:none;background:linear-gradient(135deg,#862736,#a83347);border-radius:12px;padding:16px 20px;margin-bottom:1.25rem;color:#fff;align-items:center;gap:14px;">
		<div style="flex:1;">
			<div style="font-weight:700;font-size:15px;margin-bottom:2px;">📱 Få Redaktørportalen som app</div>
			<div style="font-size:13px;opacity:0.85;">Raskere tilgang til manus, oppgaver og tilbakemeldinger</div>
		</div>
		<button id="editorInstallBtn" onclick="installEditorPWA()" style="background:#fff;color:#862736;border:none;border-radius:8px;padding:10px 20px;font-weight:700;font-size:13px;cursor:pointer;white-space:nowrap;">
			Installer
		</button>
		<button onclick="dismissEditorInstall()" style="background:none;border:none;color:rgba(255,255,255,0.7);font-size:18px;cursor:pointer;padding:0 4px;">✕</button>
	</div>

	{{-- ═══════ DAGENS ORDTAK ═══════ --}}
	@php
		$ordtak = [
			'Bak hver utgitt bok står en redaktør som trodde på den.',
			'Å redigere er ikke å fjerne — det er å finne gullet.',
			'En forfatter planter frø. En redaktør får dem til å blomstre.',
			'En god redaktør ser ikke feil — de ser muligheter.',
			'Litteraturen lever fordi noen tør å lese mellom linjene.',
			'Den beste redaktøren er usynlig — men uunnværlig.',
			'Et manus er et løfte. En redaktør hjelper forfatteren å holde det.',
			'Hemingway sa: «Den første versjonen av alt er dritt.» Derfor finnes redaktører.',
			'Raymond Carver ble Raymond Carver takket være redaktøren Gordon Lish.',
			'Toni Morrison var redaktør før hun ble forfatter. Det preget alt hun skrev.',
			'Max Perkins formet Fitzgerald, Hemingway og Wolfe. Gode redaktører former litteraturhistorien.',
			'Å gi tilbakemelding er en kunst — like viktig som å skrive.',
			'En tekst blir ikke ferdig når forfatteren legger fra seg pennen, men når redaktøren nikker.',
			'Det vakreste med litteratur er at den gjør oss mindre alene.',
			'Ordene tilhører forfatteren. Historien tilhører leseren. Broen mellom dem er redaktøren.',
			'Astrid Lindgren sa: «Gi barna kjærlighet, mer kjærlighet og enda mer kjærlighet.» Det gjelder også for manus.',
			'En god tilbakemelding er som en lykt i mørket — den viser veien uten å blende.',
			'Litteratur er empati i trykt form.',
			'Det finnes ingen dårlige førsteutkast — bare uferdig magi.',
			'Hver tekst du leser for en elev, kan være starten på en karriere.',
			'Norsk litteratur er sterk fordi vi har redaktører som bryr seg.',
		];
		$dagensOrdtak = $ordtak[date('z') % count($ordtak)];
	@endphp
	<div style="background: linear-gradient(135deg, #fdf8f4 0%, #f5ede4 100%); border-radius: var(--radius); padding: 1rem 1.5rem; margin-bottom: 1.5rem; text-align: center;">
		<p style="font-family: var(--font-display); font-size: 1.05rem; color: var(--text); font-style: italic; margin-bottom: 0.3rem;">«{{ $dagensOrdtak }}»</p>
		<p style="font-size: 0.78rem; color: var(--text-secondary); margin-bottom: 0;">— Sven Inge</p>
	</div>

	{{-- ═══════ MELDING FRA SVEN INGE ═══════ --}}
	{{-- Melding fra Sven Inge (fjernet 25.03.2026) --}}


	{{-- ═══════ STAT CARDS ═══════ --}}
	<div class="stat-grid">
		@if($availableManuscripts->count() > 0)
		<div class="stat-card" style="border-left: 3px solid var(--brand-accent);">
			<div class="stat-icon" style="background:rgba(212,168,83,0.12);color:var(--brand-accent);"><i class="fa fa-book"></i></div>
			<div>
				<div class="stat-value">{{ $availableManuscripts->count() }}</div>
				<div class="stat-label">Ledige manus</div>
			</div>
		</div>
		@endif
		@if($assignedAssignmentManuscripts->count() > 0)
		<div class="stat-card">
			<div class="stat-icon personal"><i class="fa fa-user-edit"></i></div>
			<div>
				<div class="stat-value">{{ $assignedAssignmentManuscripts->count() }}</div>
				<div class="stat-label">{{ trans('site.personal-assignment') }}</div>
			</div>
		</div>
		@endif
		@if($assigned_shop_manuscripts->filter(fn($m) => in_array($m->status, ['Started','Pending']))->count() + $shopManuscriptRequests->count() > 0)
		<div class="stat-card">
			<div class="stat-icon shop"><i class="fa fa-book"></i></div>
			<div>
				<div class="stat-value">{{ $assigned_shop_manuscripts->filter(fn($m) => in_array($m->status, ['Started','Pending']))->count() + $shopManuscriptRequests->count() }}</div>
				<div class="stat-label">{{ trans_choice('site.shop-manuscripts', 2) }}</div>
			</div>
		</div>
		@endif
		@if($assignedAssignments->count() > 0)
		<div class="stat-card">
			<div class="stat-icon assignment"><i class="fa fa-tasks"></i></div>
			<div>
				<div class="stat-value">{{ $assignedAssignments->count() }}</div>
				<div class="stat-label">{{ trans('site.my-assignments') }}</div>
			</div>
		</div>
		@endif
		@if($freeManuscripts->count() > 0)
		<div class="stat-card">
			<div class="stat-icon free"><i class="fa fa-file-text"></i></div>
			<div>
				<div class="stat-value">{{ $freeManuscripts->count() }}</div>
				<div class="stat-label">Free Manuscript</div>
			</div>
		</div>
		@endif
		@if($coachingTimers->count() > 0)
		<div class="stat-card">
			<div class="stat-icon coaching"><i class="fa fa-headphones"></i></div>
			<div>
				<div class="stat-value">{{ $coachingTimers->count() }}</div>
				<div class="stat-label">{{ trans('site.my-coaching-timer') }}</div>
			</div>
		</div>
		@endif
		@if($selfPublishingList->count() > 0)
		<div class="stat-card">
			<div class="stat-icon publishing"><i class="fa fa-print"></i></div>
			<div>
				<div class="stat-value">{{ $selfPublishingList->count() }}</div>
				<div class="stat-label">Self Publishing</div>
			</div>
		</div>
		@endif
		@if($corrections->count() > 0)
		<div class="stat-card">
			<div class="stat-icon correction"><i class="fa fa-check-circle"></i></div>
			<div>
				<div class="stat-value">{{ $corrections->count() }}</div>
				<div class="stat-label">{{ trans('site.my-correction') }}</div>
			</div>
		</div>
		@endif
		@if($copyEditings->count() > 0)
		<div class="stat-card">
			<div class="stat-icon copyedit"><i class="fa fa-spell-check"></i></div>
			<div>
				<div class="stat-value">{{ $copyEditings->count() }}</div>
				<div class="stat-label">{{ trans('site.my-copy-editing') }}</div>
			</div>
		</div>
		@endif
		@if($editingAssignments->count() > 0)
		<div class="stat-card">
			<div class="stat-icon editing"><i class="fa fa-pencil"></i></div>
			<div>
				<div class="stat-value">{{ $editingAssignments->count() }}</div>
				<div class="stat-label">Redigering</div>
			</div>
		</div>
		@endif
		@if($projects->count() > 0)
		<div class="stat-card">
			<div class="stat-icon project"><i class="fa fa-folder-open"></i></div>
			<div>
				<div class="stat-value">{{ $projects->count() }}</div>
				<div class="stat-label">Prosjekter</div>
			</div>
		</div>
		@endif
	</div>

	{{-- ══════════════════════════════════════════
		0. LEDIGE MANUS (Available for pickup)
	══════════════════════════════════════════ --}}
	@if($availableManuscripts->count() > 0)
	<div class="dashboard-section" style="border-left: 3px solid var(--brand-accent);">
		<div class="section-header">
			<h4>
				<i class="fa fa-book" style="color:var(--brand-accent);margin-right:8px;"></i>
				Ledige manus — ta et oppdrag!
				<span class="section-badge" style="background:var(--brand-accent);color:#fff;">{{ $availableManuscripts->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table">
					<thead>
					<tr>
						<th>Manus</th>
						<th>Sjanger</th>
						<th>Elev</th>
						<th>Antall ord</th>
						<th>Frist</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($availableManuscripts as $am)
						<tr>
							<td><a href="#" data-toggle="modal" data-target="#manuscriptModal{{ $am->id }}" style="color:var(--brand-primary);font-weight:600;text-decoration:underline;cursor:pointer;">{{ $am->shopManuscript->title ?? 'Manusutvikling' }}</a></td>
							<td>
								@if($am->genre > 0)
									{{ \App\Http\FrontendHelpers::assignmentType($am->genre) }}
								@endif
							</td>
							<td>{{ $am->user->first_name ?? '' }} {{ substr($am->user->last_name ?? '', 0, 1) }}.</td>
							<td>{{ $am->words ? number_format($am->words, 0, ',', ' ') : '—' }}</td>
							<td>{{ $am->expected_finish ? \Carbon\Carbon::parse($am->expected_finish)->format('d.m.Y') : '—' }}</td>
							<td>
								<form method="POST" action="{{ route('editor.claim-manuscript', $am->id) }}" id="claimForm{{ $am->id }}">
									@csrf
									<button type="submit" class="btn btn-sm" style="background:var(--brand-primary);color:#fff;" onclick="return confirm('Er du sikker på at du vil ta dette manuset?')">
										<i class="fa fa-hand-paper-o"></i> Ta dette manuset
									</button>
								</form>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
		@foreach($availableManuscripts as $am)
		<div class="modal fade" id="manuscriptModal{{ $am->id }}" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content" style="border-radius:var(--radius);border:1px solid var(--border);">
					<div class="modal-header" style="background:var(--bg);border-bottom:1px solid var(--border);">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" style="font-family:var(--font-display);font-weight:600;">{{ $am->shopManuscript->title ?? 'Manusutvikling' }}</h4>
					</div>
					<div class="modal-body">
						<table class="table table-condensed" style="margin-bottom:0;">
							<tr><td style="font-weight:600;width:140px;">Pakke</td><td>{{ $am->shop_manuscript->title ?? '—' }}</td></tr>
							<tr><td style="font-weight:600;">Maks ord</td><td>{{ $am->shop_manuscript->max_words ?? '—' }}</td></tr>
							<tr><td style="font-weight:600;">Elev</td><td>{{ $am->user->full_name ?? '—' }}</td></tr>
							<tr><td style="font-weight:600;">Sjanger</td><td>@if($am->genre > 0){{ \App\Http\FrontendHelpers::assignmentType($am->genre) }}@else — @endif</td></tr>
							<tr><td style="font-weight:600;">Antall ord</td><td>{{ $am->words ? number_format($am->words, 0, ',', ' ') : '—' }}</td></tr>
							<tr><td style="font-weight:600;">Opplastet</td><td>{{ $am->manuscript_uploaded_date ? \Carbon\Carbon::parse($am->manuscript_uploaded_date)->format('d.m.Y') : '—' }}</td></tr>
							<tr><td style="font-weight:600;">Frist</td><td>{{ $am->expected_finish ?? '—' }}</td></tr>
							@if($am->description)
							<tr><td style="font-weight:600;">Beskrivelse</td><td>{{ $am->description }}</td></tr>
							@endif
							@if($am->synopsis)
							<tr><td style="font-weight:600;">Synopsis</td><td>{{ $am->synopsis }}</td></tr>
							@endif
						</table>
					</div>
					<div class="modal-footer">
						<form method="POST" action="{{ route('editor.claim-manuscript', $am->id) }}" style="display:inline;">
							@csrf
							<button type="submit" class="btn" style="background:var(--brand-primary);color:#fff;font-weight:600;" onclick="return confirm('Er du sikker på at du vil ta dette manuset?')">
								<i class="fa fa-hand-paper-o"></i> Ta dette manuset
							</button>
						</form>
						<button type="button" class="btn btn-default" data-dismiss="modal">Lukk</button>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		1. PERSONLIG OPPGAVER
	══════════════════════════════════════════ --}}
	@if($assignedAssignmentManuscripts->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-user-edit" style="color:var(--brand-primary);margin-right:8px;"></i>
				{{ trans('site.personal-assignment') }}
				<span class="section-badge brand">{{ $assignedAssignmentManuscripts->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="myAssignedShopManuTable">
					<thead>
					<tr>
						<th>{{ trans_choice('site.manuscripts', 1) }}</th>
						<th>Brev</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans_choice('site.courses', 1) }}</th>
						<th>{{ trans('site.type') }}</th>
						<th>{{ trans('site.where') }}</th>
						<th>{{ trans('site.expected-finish') }}</th>
						<th>Opplastet</th>
						<th>{{ trans('site.feedback-status') }}</th>
							<th style="width:60px;">Lås</th>
					</tr>
					</thead>
					<tbody>
					@foreach($assignedAssignmentManuscripts as $assignedManuscript)
						<?php $extension = explode('.', basename($assignedManuscript->filename));
							$course = $assignedManuscript->assignment->course;
						?>
						<tr>
							<td>
								<a href="{{ $assignedManuscript->filename }}?v={{ $cacheBuster }}" download>
									<i class="fa fa-download" aria-hidden="true"></i>
								</a> &nbsp;
								@if( end($extension) == 'pdf' || end($extension) == 'odt' )
									<a href="/js/ViewerJS/#../..{{ $assignedManuscript->filename }}">
										{{ basename($assignedManuscript->filename) }}
									</a>
								@elseif( end($extension) == 'docx' || end($extension) == 'doc' )
									<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$assignedManuscript->filename}}">
										{{ basename($assignedManuscript->filename) }}
									</a>
								@endif
							</td>
							<td>
								@if ($assignedManuscript->letter_to_editor)
									<a href="{{ route('editor.assignment.manuscript.download_letter', ['id' => $assignedManuscript->id, 'v' => $cacheBuster]) }}">
										<i class="fa fa-download" aria-hidden="true"></i>
									</a>&nbsp;
									{{ basename($assignedManuscript->letter_to_editor) }}
								@endif
							</td>
							<td>{{ $assignedManuscript->user->id }}</td>
							<td>
								@if($course)
									<a href="{{ route('admin.course.show', $course->id) }}">
										{{ $course->title }}
									</a>
								@endif
							</td>
							<td>{{ \App\Http\AdminHelpers::assignmentType($assignedManuscript->type) }}</td>
							<td>{{ \App\Http\AdminHelpers::manuscriptType($assignedManuscript->manu_type) }}</td>
							<td>
								{{ $assignedManuscript->expected_finish }}
								@if(!$assignedManuscript->expected_finish)
									<button class="btn btn-brand btn-xs" data-toggle="modal"
											data-target="#editExpectedFinishModal"
											data-action="{{ route('editor.personal-assignment.update-expected-finish', ['assignment', $assignedManuscript->id]) }}"
											data-expected_finish="{{ $assignedManuscript->expected_finish
										? strftime('%Y-%m-%d', strtotime($assignedManuscript->expected_finish)) : NULL }}" onclick="editExpectedFinish(this)">
										<i class="fa fa-edit"></i> Edit
									</button>
								@endif
							</td>
							<td>{{ $assignedManuscript->uploaded_date }}</td>
							<td>
								<div>
									@if($assignedManuscript->has_feedback && $assignedManuscript->noGroupFeedbacks->first())
										<span class="label label-default">{{ trans('site.pending') }}</span>
										<button class="btn btn-xs btn-success submitPersonalAssignmentFeedbackBtn"
												data-target="#submitPersonalAssignmentFeedbackModal"
												data-toggle="modal"
												data-manuscript="{{$assignedManuscript->noGroupFeedbacks->first()->filename}}"
												data-created_at="{{$assignedManuscript->noGroupFeedbacks->first()->created_at}}"
												data-updated_at="{{$assignedManuscript->noGroupFeedbacks->first()->updated_at}}"
												data-feedback_id="{{$assignedManuscript->noGroupFeedbacks->first()->id}}"
												data-grade="{{$assignedManuscript->grade}}"
												data-notes_to_head_editor="{{$assignedManuscript->noGroupFeedbacks->first()->notes_to_head_editor}}"
												data-edit="1"
												data-name="{{ $assignedManuscript->user->id }}"
												data-action="{{ route('editor.assignment.group.manuscript-feedback-no-group',
															['id' => $assignedManuscript->id,
															'learner_id' => $assignedManuscript->user->id]) }}">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button>
									@else
										<button class="btn btn-warning btn-xs d-block submitPersonalAssignmentFeedbackBtn"
												data-target="#submitPersonalAssignmentFeedbackModal"
												data-toggle="modal"
												data-name="{{ $assignedManuscript->user->id }}"
												data-action="{{ route('editor.assignment.group.manuscript-feedback-no-group',
															['id' => $assignedManuscript->id,
															'learner_id' => $assignedManuscript->user->id]) }}">
											+ {{ trans('site.add-feedback') }}
										</button>
									@endif
								</div>
							</td>
							<td>
								<label class="lock-label" style="cursor:pointer;margin:0;">
									<input type="checkbox" class="manuscript-lock-toggle" data-type="assignment" data-id="{{ $assignedManuscript->id }}"
										{{ $assignedManuscript->locked ? 'checked' : '' }}>
									<i class="fa {{ $assignedManuscript->locked ? 'fa-lock text-danger' : 'fa-unlock-alt text-muted' }}"></i>
								</label>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		2. MANUSUTVIKLING (Shop Manuscripts)
	══════════════════════════════════════════ --}}
	@if($assigned_shop_manuscripts->filter(fn($m) => in_array($m->status, ['Started','Pending']))->count() + $shopManuscriptRequests->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-book" style="color:var(--warning);margin-right:8px;"></i>
				{{ trans_choice('site.shop-manuscripts', 2) }}
				<span class="section-badge warning">{{ $assigned_shop_manuscripts->filter(fn($m) => in_array($m->status, ['Started','Pending']))->count() + $shopManuscriptRequests->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="shopManuTable">
					<thead>
					<tr>
						<th>{{ trans_choice('site.manuscripts', 1) }}</th>
						<th>{{ trans('site.genre') }}</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.deadline') }}</th>
						<th>{{ trans('site.feedback-status') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach($assigned_shop_manuscripts as $shopManuscript)
						@if( $shopManuscript->status == 'Started' || $shopManuscript->status == 'Pending' )
							<tr>
								<td>
									<a href="{{ route('editor.backend.download_shop_manuscript', ['id' => $shopManuscript->id, 'v' => $cacheBuster]) }}"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
									@if($shopManuscript->is_active)
										<a href="{{ route('editor.shop_manuscript_taken', ['id' => $shopManuscript->user->id, 'shop_manuscript_taken_id' => $shopManuscript->id]) }}">{{$shopManuscript->shop_manuscript->title}}</a>
									@else
										{{$shopManuscript->shop_manuscript->title}}
									@endif
								</td>
								<td>
									@if($shopManuscript->genre > 0)
										{{ \App\Http\FrontendHelpers::assignmentType($shopManuscript->genre) }}
									@endif
								</td>
								<td>{{ $shopManuscript->user->id }}</td>
								<td>{{ $shopManuscript->editor_expected_finish }}</td>
								<td>
									@if($shopManuscript->status == 'Started')
										<button type="button" class="btn btn-warning btn-xs addShopManuscriptFeedback" data-toggle="modal"
											data-target="#addFeedbackModal"
											data-action="{{ route('editor.admin.shop-manuscript-taken-feedback.store', $shopManuscript->id) }}">+ {{ trans('site.add-feedback') }}</button>
									@elseif($shopManuscript->status == 'Pending')
										<?php $feedbackFile = implode(",",$shopManuscript->feedbacks->first()->filename); ?>
										<span class="label label-default">Venter</span>
										<button type="button" class="btn btn-success btn-xs addShopManuscriptFeedback" data-toggle="modal"
											data-target="#addFeedbackModal"
											data-f_id="{{$shopManuscript->feedbacks->first()->id}}"
											data-edit="1"
											data-f_created_at="{{$shopManuscript->feedbacks->first()->created_at}}"
											data-f_updated_at="{{$shopManuscript->feedbacks->first()->updated_at}}"
											data-f_file="{{$feedbackFile}}"
											data-f_notes="{{$shopManuscript->feedbacks->first()->notes}}"
											data-hours="{{$shopManuscript->feedbacks->first()->hours_worked}}"
											data-notes_to_head_editor="{{$shopManuscript->feedbacks->first()->notes_to_head_editor}}"
											data-action="{{ route('editor.admin.shop-manuscript-taken-feedback.store', $shopManuscript->id) }}">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button>
									@endif
								</td>
							</tr>
						@endif
					@endforeach
					@foreach($shopManuscriptRequests as $request)
						@if($request->manuscript)
							<tr class="request-row">
								<td>
									<a href="{{ route('editor.backend.download_shop_manuscript', ['id' => $request->manuscript_id, 'v' => $cacheBuster]) }}"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
									@if($request->manuscript->is_active)
										<a href="{{ route('editor.shop_manuscript_taken', ['id' => $request->manuscript->user->id, 'shop_manuscript_taken_id' => $request->manuscript_id]) }}">{{$request->manuscript->shop_manuscript->title}}</a>
									@else
										{{$request->manuscript->shop_manuscript->title}}
									@endif
								</td>
								<td>
									@if($request->manuscript->genre > 0)
										{{ \App\Http\FrontendHelpers::assignmentType($request->manuscript->genre) }}
									@endif
								</td>
								<td>{{ $request->manuscript->user->id }}</td>
								<td>{{ $request->manuscript->editor_expected_finish }}</td>
								<td>
									<button class="btn btn-success btn-xs acceptRequestBtn"
											data-toggle="modal"
											data-target="#acceptRequest"
											data-title="{{ trans('site.are-you-sure-you-want-to-accept') }}"
											data-sub_title="{{ trans('site.are-you-sure-you-want-to-accept-sub') }}"
											data-action="{{ route('editor.acceptShopManuscriptRequest', ['shop_manuscript_taken_id' => $request->manuscript_id, 'accept' => '1', 'request_id' => $request->id]) }}">
										<i class="fa fa-check" aria-hidden="true"></i>&nbsp;{{ trans('site.accept') }}
									</button>&nbsp;
									<button class="btn btn-danger btn-xs acceptRequestBtn"
											data-toggle="modal"
											data-target="#acceptRequest"
											data-title="{{ trans('site.are-you-sure-you-want-to-reject') }}"
											data-sub_title="{{ trans('site.are-you-sure-you-want-to-reject-sub') }}"
											data-action="{{ route('editor.acceptShopManuscriptRequest', ['shop_manuscript_taken_id' => $request->manuscript_id, 'accept' => '0', 'request_id' => $request->id]) }}">
										<i class="fa fa-times" aria-hidden="true"></i>&nbsp;{{ trans('site.reject') }}
									</button>&nbsp;
									<span class="label label-info" style="font-size: 1.2rem; font-weight: 100;">
										<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;{{ trans('site.answer-until') }}&nbsp;{{ $request->answer_until }}
									</span>
								</td>
							</tr>
						@endif
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		3. MINE OPPGAVER (My Assignments)
	══════════════════════════════════════════ --}}
	@if($assignedAssignments->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-tasks" style="color:var(--success);margin-right:8px;"></i>
				{{ trans('site.my-assignments') }}
				<span class="section-badge success">{{ $assignedAssignments->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="myAssignmentTable">
					<thead>
					<tr>
						<th>{{ trans_choice('site.courses', 1) }}</th>
						<th>Brev</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.type') }}</th>
						<th>{{ trans('site.where') }}</th>
						<th>{{ trans('site.deadline') }}</th>
						<th>Opplastet</th>
						<th>{{ trans('site.feedback-status') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach ($assignedAssignments as $assignedAssignment)
						<tr>
							<td>
								<a href="{{ route('editor.backend.download_assigned_manuscript', ['id' => $assignedAssignment->id, 'v' => $cacheBuster]) }}"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
								@if($assignedAssignment->assignment->course)
									{{ $assignedAssignment->assignment->course->title }}
								@else
									{{ $assignedAssignment->assignment->title }}
								@endif
							</td>
							<td>
								@if ($assignedAssignment->letter_to_editor)
									<a href="{{ route('assignment.manuscript.download_letter', ['id' => $assignedAssignment->id, 'v' => $cacheBuster]) }}">
										<i class="fa fa-download" aria-hidden="true"></i>
									</a>&nbsp;
									{{ basename($assignedAssignment->letter_to_editor) }}
								@endif
							</td>
							<td>{{ $assignedAssignment->user_id }}</td>
							<td>{{ \App\Http\AdminHelpers::assignmentType($assignedAssignment->type) }}</td>
							<td>{{ \App\Http\AdminHelpers::manuscriptType($assignedAssignment->manu_type) }}</td>
							<td>{{ $assignedAssignment->editor_expected_finish?$assignedAssignment->editor_expected_finish:$assignedAssignment->assignment->editor_expected_finish }}</td>
							<td>{{ $assignedAssignment->uploaded_date }}</td>
							<td>
							<?php
							$groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
							if($groupDetails){
								$feedback = DB::SELECT("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = $assignedAssignment->user_id AND A.assignment_group_learner_id = ".$groupDetails[0]->assignment_group_learner_id);
							}
							
							if($assignedAssignment->has_feedback){
								echo '<span class="label label-default">Venter</span> ';
								if($groupDetails){
								}else{
									echo '<button type="button" class="btn btn-success btn-xs submitFeedbackBtn"
											data-toggle="modal" data-target="#submitFeedbackModal"
											data-manuscript = "'.$assignedAssignment->noGroupFeedbacks->first()->filename.'"
											data-created_at = "'.$assignedAssignment->noGroupFeedbacks->first()->created_at.'"
											data-updated_at = "'.$assignedAssignment->noGroupFeedbacks->first()->updated_at.'"
											data-feedback_id = "'.$assignedAssignment->noGroupFeedbacks->first()->id.'"
											data-grade = "'.$assignedAssignment->grade.'"
											data-edit = "1"
											data-notes_to_head_editor = "'.$assignedAssignment->noGroupFeedbacks->first()->notes_to_head_editor.'"
											data-name="'.$assignedAssignment->user->id.'"
											data-action="'.route('editor.assignment.group.manuscript-feedback-no-group',
											['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
											data-manuscript_id="'.$assignedAssignment->id.'">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
											</button>';
								}
							}else{
								if($groupDetails){
									echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="'.$assignedAssignment->user->id.'"
												data-action="'.route('editor.assignment.group.submit_feedback',
												['group_id' => $groupDetails[0]->assignment_group_id, 'id' => $groupDetails[0]->assignment_group_learner_id]).'"
												data-manuscript_id="'.$assignedAssignment->id.'">'.
											'+ '.trans('site.add-feedback').'</button>';
								}else{
									echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="'.$assignedAssignment->user->id.'"
												data-action="'.route('editor.assignment.group.manuscript-feedback-no-group',
												['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
												data-manuscript_id="'.$assignedAssignment->id.'">'.
												'+ '.trans('site.add-feedback').'</button>';
								}
							}
							?>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		4. FREE MANUSCRIPT
	══════════════════════════════════════════ --}}
	@if($freeManuscripts->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-file-text" style="color:#047857;margin-right:8px;"></i>
				Free Manuscript
				<span class="section-badge success">{{ $freeManuscripts->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table">
					<thead>
						<tr>
							<th>{{ trans('site.name') }}</th>
							<th>{{ trans('site.genre') }}</th>
							<th width="500">{{ trans('site.content') }}</th>
							<th width="200">{{ trans('site.feedback-status') }}</th>
							<th width="200">{{ trans('site.deadline') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach($freeManuscripts as $freeManuscript)
							<tr>
								<td>{{ $freeManuscript->name }}</td>
								<td>{{ \App\Http\AdminHelpers::assignmentType($freeManuscript->genre) }}</td>
								<td>
									{!! \Illuminate\Support\Str::limit(strip_tags($freeManuscript->content), 120) !!}<br>
									<a href="#editContentModal" data-toggle="modal"
									   data-content="{{ $freeManuscript->content }}"
									   data-action="{{ route('editor.free-manuscript.edit-content', $freeManuscript->id) }}"
									   onclick="editFMContent(this)">
										Her kan du også nå putte in ekstra tekst
									</a>
								</td>
								<td>
									@if($freeManuscript->feedback_content)
										<span class="label label-default">{{ trans('site.pending') }}</span>
										<button class="btn btn-xs btn-success"
												data-toggle="modal" data-target="#freeManuscriptFeedbackModal"
												onclick="sendFMFeedback(this)"
												data-fields="{{ json_encode($freeManuscript) }}"
												data-action="{{ route('editor.free-manuscript.send_feedback', $freeManuscript->id) }}"
												data-email_template="{{ $freeManuscript->from === 'Giutbok'
												? $freeManuscriptEmailTemplate2->email_content
												: $freeManuscriptEmailTemplate->email_content }}">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
										</button>
									@else
										<button class="btn btn-xs btn-warning"
												data-toggle="modal" data-target="#freeManuscriptFeedbackModal"
												onclick="sendFMFeedback(this)"
												data-fields="{{ json_encode($freeManuscript) }}"
												data-action="{{ route('editor.free-manuscript.send_feedback', $freeManuscript->id) }}"
												data-email_template="{{ $freeManuscript->from === 'Giutbok'
												? $freeManuscriptEmailTemplate2->email_content
												: $freeManuscriptEmailTemplate->email_content }}">
											+ {{ trans('site.add-feedback') }}
										</button>
									@endif
								</td>
								<td>{{ $freeManuscript->deadline_date }}</td>
								<td>
									<a href="{{ route('editor.free-manuscript.download', ['id' => $freeManuscript->id, 'v' => $cacheBuster]) }}"
									   class="btn btn-brand btn-xs">
										<i class="fa fa-download"></i>
										{{ trans('site.download') }}
									</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		5. COACHING TIMER
	══════════════════════════════════════════ --}}
	@if($coachingTimers->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-headphones" style="color:var(--info);margin-right:8px;"></i>
				{{ trans('site.my-coaching-timer') }}
				<span class="section-badge info">{{ $coachingTimers->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="coachingTable">
					<thead>
					<tr>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.approved-date') }}</th>
						<th>{{ trans('site.session-length') }}</th>
						<th>{{ trans('site.set-replay') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach($coachingTimers as $coachingTimer)
						<?php $extension = explode('.', basename($coachingTimer->file)); ?>
						<tr>
							<td>
								@if ($coachingTimer->file)
									<a href="{{ $coachingTimer->file }}?v={{ $cacheBuster }}" download><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
								@endif
								{{ $coachingTimer->user->id }}
								@if ($coachingTimer->help_with)
									<br>
									<a href="#viewHelpWithModal" style="color:#eea236" class="viewHelpWithBtn"
									   data-toggle="modal" data-details="{{ $coachingTimer->help_with }}">
										{{ trans('site.view-help-with') }}
									</a>
								@endif
							</td>
							<td>
								{{ $coachingTimer->approved_date ?
								\App\Http\FrontendHelpers::formatToYMDtoPrettyDate($coachingTimer->approved_date)
								: ''}}
							</td>
							<td>{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($coachingTimer->plan_type) }}</td>
							<td>
								<button class="btn btn-xs btn-brand setReplayBtn" data-toggle="modal"
										data-target="#setReplayModal" data-action="{{ route('editor.other-service.coaching-timer.set_replay', $coachingTimer->id) }}">{{ trans('site.set-replay') }}</button>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		6. SELF PUBLISHING
	══════════════════════════════════════════ --}}
	@if($selfPublishingList->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-print" style="color:#b45309;margin-right:8px;"></i>
				Self Publishing
				<span class="section-badge orange">{{ $selfPublishingList->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="selfPublishingTable">
					<thead>
					<tr>
						<th>{{ trans('site.title') }}</th>
						<th>{{ trans('site.learner.manuscript-text') }}</th>
						<th>{{ trans('site.expected-finish') }}</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($selfPublishingList as $publishing)
						<tr>
							<td>{{ $publishing->title }}</td>
							<td>
								<a href="{{ route('editor.self-publishing.download-manuscript', ['id' => $publishing->id, 'v' => $cacheBuster]) }}">
									<i class="fa fa-download" aria-hidden="true"></i>
								</a> &nbsp; {!! $publishing->file_link !!}
							</td>
							<td>{{ $publishing->expected_finish }}</td>
							<td>
								<button class="btn btn-warning btn-xs d-block selfPublishingFeedbackBtn"
										data-target="#selfPublishingFeedbackModal"
										data-toggle="modal"
										data-action="{{ route('editor.self-publishing.feedback', $publishing->id) }}">
									+ {{ trans('site.add-feedback') }}
								</button>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		7. KORREKTUR (My Corrections)
	══════════════════════════════════════════ --}}
	@if($corrections->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-check-circle" style="color:var(--danger);margin-right:8px;"></i>
				{{ trans('site.my-correction') }}
				<span class="section-badge danger">{{ $corrections->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="correctionTable">
					<thead>
					<tr>
						<th>{{ trans_choice('site.manus', 2) }}</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.expected-finish') }}</th>
						<th>{{ trans('site.feedback-status') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach($corrections as $correction)
						<?php $extension = explode('.', basename($correction->file)); ?>
						<tr>
							<td>
								@if (strpos($correction->file, 'project-'))
									<a href="{{ route('dropbox.download_file', trim($correction->file)) }}?v={{ $cacheBuster }}">
										<i class="fa fa-download" aria-hidden="true"></i>
									</a>&nbsp;
									<a href="{{ route('dropbox.shared_link', trim($correction->file)) }}" target="_blank">
										{{ basename($correction->file) }}
									</a>
								@else
									@if ($correction->file)
										<a href="{{ route('editor.other-service.download-doc', ['id' => $correction->id, 'type' => 2, 'v' => $cacheBuster]) }}" download>
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>&nbsp;
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
										@endif
									@endif
								@endif
							</td>
							<td>{{ $correction->user->id }}</td>
							<td>
								@if ($correction->expected_finish)
									{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($correction->expected_finish) }}
								@endif
							</td>
							<td>
								@if (!$correction->feedback)
									<a href="#addOtherServiceFeedbackModal" data-toggle="modal"
									   class="btn btn-warning btn-xs addOtherServiceFeedbackBtn" data-service="2"
									   data-action="{{ route('editor.other-service.add-feedback', ['id' => $correction->id, 'type' => 2]) }}">+ {{ trans('site.add-feedback') }}</a>
								@else
									@if( $correction->status == 2 )
										<span class="label label-success">{{ trans('site.finished') }}</span>
									@elseif( $correction->status == 1 )
										<span class="label label-primary">{{ trans('site.started') }}</span>
									@elseif( $correction->status == 0 )
										<span class="label label-warning">{{ trans('site.not-started') }}</span>
									@elseif( $correction->status == 3 )
										<span class="label label-default">{{ trans('site.pending') }}</span>
									@endif
									<a href="#addOtherServiceFeedbackModal" data-toggle="modal"
									   class="btn btn-success btn-xs addOtherServiceFeedbackBtn"
									   data-service="2"
									   data-f_id="{{$correction->feedback->id}}"
									   data-f_created_at="{{$correction->feedback->created_at}}"
									   data-f_updated_at="{{$correction->feedback->updated_at}}"
									   data-f_file="{{$correction->feedback->manuscript}}"
									   data-hours="{{$correction->feedback->hours_worked}}"
									   data-notes_to_head_editor="{{ $correction->feedback->notes_to_head_editor }}"
									   data-edit="1"
									   data-action="{{ route('editor.other-service.add-feedback', ['id' => $correction->id, 'type' => 2]) }}">
										<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
									</a>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		8. SPRÅKVASK (My Copy Editing)
	══════════════════════════════════════════ --}}
	@if($copyEditings->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-spell-check" style="color:#7c3aed;margin-right:8px;"></i>
				{{ trans('site.my-copy-editing') }}
				<span class="section-badge purple">{{ $copyEditings->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table" id="copyEditingTable">
					<thead>
					<tr>
						<th>{{ trans_choice('site.manus', 2) }}</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.expected-finish') }}</th>
						<th>{{ trans('site.feedback-status') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach($copyEditings as $copyEditing)
						<?php $extension = explode('.', basename($copyEditing->file)); ?>
						<tr>
							<td>
								@if ($copyEditing->file)
									@if (strpos($copyEditing->file, 'project-'))
										<a href="{{ route('editor.dropbox.download_file', trim($copyEditing->file)) }}?v={{ $cacheBuster }}">
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>&nbsp;
										<a href="{{ route('editor.dropbox.shared_link', trim($copyEditing->file)) }}" target="_blank">
											{{ basename($copyEditing->file) }}
										</a>
									@else
										<a href="{{ route('editor.other-service.download-doc', ['id' => $copyEditing->id, 'type' => 1, 'v' => $cacheBuster]) }}" download>
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>&nbsp;
										@if( end($extension) == 'pdf' || end($extension) == 'odt' )
											<a href="/js/ViewerJS/#../../{{ $copyEditing->file }}">{{ basename($copyEditing->file) }}</a>
										@elseif( end($extension) == 'docx' )
											<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$copyEditing->file}}">{{ basename($copyEditing->file) }}</a>
										@endif
									@endif
								@endif
							</td>
							<td>{{ $copyEditing->user->id }}</td>
							<td>
								@if ($copyEditing->expected_finish)
									{{ \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($copyEditing->expected_finish) }}
								@endif
							</td>
							<td>
								@if (!$copyEditing->feedback)
									<a href="#addOtherServiceFeedbackModal" data-toggle="modal"
									   class="btn btn-warning btn-xs addOtherServiceFeedbackBtn" data-service="1"
									   data-action="{{ route('editor.other-service.add-feedback', ['id' => $copyEditing->id, 'type' => 1]) }}">+ {{ trans('site.add-feedback') }}</a>
								@else
									@if( $copyEditing->status == 2 )
										<span class="label label-success">{{ trans('site.finished') }}</span>
									@elseif( $copyEditing->status == 1 )
										<span class="label label-primary">{{ trans('site.started') }}</span>
									@elseif( $copyEditing->status == 0 )
										<span class="label label-warning">{{ trans('site.not-started') }}</span>
									@elseif( $copyEditing->status == 3 )
										<span class="label label-default">{{ trans('site.pending') }}</span>
									@endif
									<a href="#addOtherServiceFeedbackModal" data-toggle="modal"
									   class="btn btn-success btn-xs addOtherServiceFeedbackBtn"
									   data-f_id="{{$copyEditing->feedback->id}}"
									   data-f_created_at="{{$copyEditing->feedback->created_at}}"
									   data-f_updated_at="{{$copyEditing->feedback->updated_at}}"
									   data-f_file="{{$copyEditing->feedback->manuscript}}"
									   data-hours="{{$copyEditing->feedback->hours_worked}}"
									   data-notes_to_head_editor="{{ $copyEditing->feedback->notes_to_head_editor }}"
									   data-service="1"
									   data-edit="1"
									   data-action="{{ route('editor.other-service.add-feedback', ['id' => $copyEditing->id, 'type' => 1]) }}">
										<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
									</a>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		9. REDIGERING (Assignment Editing)
	══════════════════════════════════════════ --}}
	@if($editingAssignments->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-pencil" style="color:#1d4ed8;margin-right:8px;"></i>
				Redigering
				<span class="section-badge blue">{{ $editingAssignments->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table assignment-table">
					<thead>
					<tr>
						<th>{{ trans_choice('site.courses', 1) }}</th>
						<th>Brev</th>
						<th>{{ trans('site.learner-id') }}</th>
						<th>{{ trans('site.type') }}</th>
						<th>{{ trans('site.where') }}</th>
						<th>{{ trans('site.deadline') }}</th>
						<th>{{ trans('site.feedback-status') }}</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach ($editingAssignments as $assignedAssignment)
						<tr>
							<td>
								<a href="{{ route('editor.backend.download_assigned_manuscript', ['id' => $assignedAssignment->id, 'v' => $cacheBuster]) }}"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
								@if($assignedAssignment->assignment->course)
									{{ $assignedAssignment->assignment->course->title }}
								@else
									{{ $assignedAssignment->assignment->title }}
								@endif
							</td>
							<td>
								@if ($assignedAssignment->letter_to_editor)
									<a href="{{ route('assignment.manuscript.download_letter', ['id' => $assignedAssignment->id, 'v' => $cacheBuster]) }}">
										<i class="fa fa-download" aria-hidden="true"></i>
									</a>&nbsp;
									{{ basename($assignedAssignment->letter_to_editor) }}
								@endif
							</td>
							<td>{{ $assignedAssignment->user_id }}</td>
							<td>{{ \App\Http\AdminHelpers::assignmentType($assignedAssignment->type) }}</td>
							<td>{{ \App\Http\AdminHelpers::manuscriptType($assignedAssignment->manu_type) }}</td>
							<td>{{ $assignedAssignment->editor_expected_finish?$assignedAssignment->editor_expected_finish:$assignedAssignment->assignment->editor_expected_finish }}</td>
							<td>
							<?php
							$groupDetails = DB::SELECT("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = $assignedAssignment->user_id WHERE A.assignment_id = $assignedAssignment->assignment_id");
							if($groupDetails){
								$feedback = DB::SELECT("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = $assignedAssignment->user_id AND A.assignment_group_learner_id = ".$groupDetails[0]->assignment_group_learner_id);
							}
							
							if($assignedAssignment->has_feedback){
								echo '<span class="label label-default">Venter</span> ';
								if($groupDetails){
								}else{
									echo '<button type="button" class="btn btn-success btn-xs submitFeedbackBtn"
											data-toggle="modal" data-target="#submitFeedbackModal"
											data-manuscript = "'.$assignedAssignment->noGroupFeedbacks->first()->filename.'"
											data-created_at = "'.$assignedAssignment->noGroupFeedbacks->first()->created_at.'"
											data-updated_at = "'.$assignedAssignment->noGroupFeedbacks->first()->updated_at.'"
											data-feedback_id = "'.$assignedAssignment->noGroupFeedbacks->first()->id.'"
											data-grade = "'.$assignedAssignment->grade.'"
											data-edit = "1"
											data-notes_to_head_editor = "'.$assignedAssignment->noGroupFeedbacks->first()->notes_to_head_editor.'"
											data-name="'.$assignedAssignment->user->id.'"
											data-action="'.route('editor.assignment.group.manuscript-feedback-no-group',
											['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
											data-manuscript_id="'.$assignedAssignment->id.'">
											<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
											</button>';
								}
							}else{
								if($groupDetails){
									echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="'.$assignedAssignment->user->id.'"
												data-action="'.route('editor.assignment.group.submit_feedback',
												['group_id' => $groupDetails[0]->assignment_group_id, 'id' => $groupDetails[0]->assignment_group_learner_id]).'"
												data-manuscript_id="'.$assignedAssignment->id.'">'.
											'+ '.trans('site.add-feedback').'</button>';
								}else{
									echo '<button type="button" class="btn btn-warning btn-xs submitFeedbackBtn"
												data-toggle="modal" data-target="#submitFeedbackModal"
												data-name="'.$assignedAssignment->user->id.'"
												data-action="'.route('editor.assignment.group.manuscript-feedback-no-group',
												['id' => $assignedAssignment->id, 'learner_id' => $assignedAssignment->user_id]).'"
												data-manuscript_id="'.$assignedAssignment->id.'">'.
												'+ '.trans('site.add-feedback').'</button>';
								}
							}
							?>
							</td>
							<td>
								<button class="btn btn-success btn-xs finishAssignmentManuscriptBtn" data-toggle="modal"
										data-target="#finishAssignmentManuscriptModal"
										data-action="{{ route('editor.assignment-manuscript.mark-finished', $assignedAssignment->id) }}">
									Merk som ferdig
								</button>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		GODKJENTE UTSETTELSER
	══════════════════════════════════════════ --}}
	@if($approvedExtensions->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-clock-o"></i>
				Godkjente utsettelser
				<span class="section-badge" style="background:#2e7d32;">{{ $approvedExtensions->count() }}</span>
			</h4>
		</div>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Elev</th>
						<th>Oppgave</th>
						<th>Opprinnelig frist</th>
						<th>Ny frist</th>
						<th>Godkjent</th>
					</tr>
				</thead>
				<tbody>
					@foreach($approvedExtensions as $ext)
					<tr>
						<td>{{ $ext->user->full_name ?? '—' }}</td>
						<td>{{ $ext->assignment->title ?? '—' }}</td>
						<td>{{ \Carbon\Carbon::parse($ext->original_deadline)->format('d.m.Y') }}</td>
						<td><strong>{{ \Carbon\Carbon::parse($ext->requested_deadline)->format('d.m.Y') }}</strong></td>
						<td>{{ $ext->decided_at ? \Carbon\Carbon::parse($ext->decided_at)->format('d.m.Y') : '—' }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		PROSJEKTFORESPØRSLER (Project Requests)
	══════════════════════════════════════════ --}}
	@if($projectRequests->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-envelope" style="color:var(--info);margin-right:8px;"></i>
				Prosjektforespørsler
				<span class="section-badge info">{{ $projectRequests->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table">
					<thead>
					<tr>
						<th>Type</th>
						<th>Svarfrist</th>
						<th>Dato sendt</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($projectRequests as $pRequest)
						<tr>
							<td>
								@if($pRequest->project_item_type == 'copy-editing')
									Språkvask
								@elseif($pRequest->project_item_type == 'correction')
									Korrektur
								@elseif($pRequest->project_item_type == 'self-publishing')
									Selvpublisering
								@endif
							</td>
							<td>{{ $pRequest->answer_until }}</td>
							<td>{{ $pRequest->created_at->format('d.m.Y') }}</td>
							<td>
								<button class="btn btn-success btn-xs acceptRequestBtn"
										data-toggle="modal"
										data-target="#acceptRequest"
										data-title="{{ trans('site.are-you-sure-you-want-to-accept') }}"
										data-sub_title="{{ trans('site.are-you-sure-you-want-to-accept-sub') }}"
										data-action="{{ route('editor.acceptProjectRequest', ['itemId' => $pRequest->project_item_id, 'type' => $pRequest->project_item_type, 'accept' => '1', 'requestId' => $pRequest->id]) }}">
									<i class="fa fa-check" aria-hidden="true"></i>&nbsp;{{ trans('site.accept') }}
								</button>&nbsp;
								<button class="btn btn-danger btn-xs acceptRequestBtn"
										data-toggle="modal"
										data-target="#acceptRequest"
										data-title="{{ trans('site.are-you-sure-you-want-to-reject') }}"
										data-sub_title="{{ trans('site.are-you-sure-you-want-to-reject-sub') }}"
										data-action="{{ route('editor.acceptProjectRequest', ['itemId' => $pRequest->project_item_id, 'type' => $pRequest->project_item_type, 'accept' => '0', 'requestId' => $pRequest->id]) }}">
									<i class="fa fa-times" aria-hidden="true"></i>&nbsp;{{ trans('site.reject') }}
								</button>&nbsp;
								<span class="label label-info" style="font-size: 1.2rem; font-weight: 100;">
									<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;{{ trans('site.answer-until') }}&nbsp;{{ $pRequest->answer_until }}
								</span>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	{{-- ══════════════════════════════════════════
		10. PROSJEKTER (Projects)
	══════════════════════════════════════════ --}}
	@if($projects->count() > 0)
	<div class="dashboard-section">
		<div class="section-header">
			<h4>
				<i class="fa fa-folder-open" style="color:#555;margin-right:8px;"></i>
				Prosjekter
				<span class="section-badge neutral">{{ $projects->count() }}</span>
			</h4>
		</div>
		<div class="section-body">
			<div class="table-responsive">
				<table class="table dt-table assignment-table">
					<thead>
						<tr>
							<th>Prosjektnummer</th>
							<th>Navn</th>
							<th>{{ trans_choice('site.learners', 1) }}</th>
							<th>Beskrivelse</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($projects as $project)
							<tr>
								<td>
									<a href="{{ route('editor.project.show', $project->id) }}">
										{{ $project->identifier }}
									</a>
								</td>
								<td>{{ $project->name }}</td>
								<td>{{ $project->user_id }}</td>
								<td>{{ $project->description }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

</div>

{{-- ══════════════════════════════════════════════════════════════
     MODALS — All kept intact
══════════════════════════════════════════════════════════════ --}}

<div id="approveFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.approve-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.approve-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-warning">{{ trans('site.approve') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="removeFeedbackAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">{{ trans('site.delete-feedback') }}</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="" onsubmit="disableSubmit(this)">
		      {{ csrf_field() }}
				{{ trans('site.delete-feedback-question') }}
		      <div class="text-right margin-top">
		      	<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
		      </div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="viewManuscriptModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-body">
		  	<p>
		  		<strong>{{ trans('site.name') }}:</strong><br />
		  		<span id="name"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.emails', 1) }}:</strong><br />
		  		<span id="email"></span><br />
		  		<br />
		  		<strong>{{ trans_choice('site.manuscripts', 1) }}:</strong><br />
		  		<span id="content"></span>
		  	</p>
		  </div>
		</div>
	</div>
</div>

<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
			</div>
			<div class="modal-body">
				<form id="submitFeedbackForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
					<div class="form-group">
						<label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" class="form-control" required multiple name="filename[]" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
						{{ trans('site.docx-pdf-odt-text') }}
					</div>
					<div class="form-group">
						<label>{{ trans('site.grade') }}</label>
						<input type="number" class="form-control" step="0.01" name="grade">
					</div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
					<input type="hidden" name="manuscript_id">
					<button type="submit" class="btn btn-brand pull-right margin-top">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.add-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form id="addFeedbackModalForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					<?php $emailTemplate = \App\Http\AdminHelpers::emailTemplate('Shop Manuscript Feedback'); ?>
					{{csrf_field()}}
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
					<div class="form-group">
						<label name="manuscriptLabel">{{ trans_choice('site.files', 2) }}</label>
						<input type="file" class="form-control" name="files[]" multiple
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							   application/pdf, application/vnd.oasis.opendocument.text" required>
							   {{ trans('site.docx-pdf-odt-text') }}
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.notes', 2) }}</label>
						<textarea class="form-control" name="notes" rows="6"></textarea>
					</div>
					<div class="form-group">
                        <label>{{ trans('site.hours-worked') }}</label>
                        <input type="number" class="form-control" step="0.01" name="hours">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
					<button type="submit" class="btn btn-brand pull-right">{{ trans('site.add-feedback') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="finishAssignmentManuscriptModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.finish-assignment') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{csrf_field()}}
					{{ trans('site.finish-assignment-question') }}
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-success">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="assignEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Velg redaktør --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="pendingAssignmentEditorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.assign-editor') }}</label>
						<select name="editor_id" class="form-control select2" required>
							<option value="" disabled="" selected>-- Velg redaktør --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
						<div class="hidden-container">
							<label></label>
							<a href="javascript:void(0)" onclick="enableSelect('pendingAssignmentEditorModal')">Rediger</a>
						</div>
					</div>
					<div class="form-group">
						<label>Forventet sluttdato</label>
						<input type="date" class="form-control" name="expected_finish">
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="updateOtherServiceStatusModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{!! str_replace('_SERVICE_','<span></span>',trans('site.update-service-status')) !!}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.update-service-status-question') }}</p>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="setOtherServiceFinishDateModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><span></span> {{ trans('site.expected-finish') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish-date') }}</label>
						<input type="datetime-local" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.submit') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addOtherServiceFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span></span> {{ trans('site.add-feedback') }}</h4>
            </div>
            <div class="modal-body">
                <form id="addOtherServiceFeedbackForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                    {{csrf_field()}}
					<?php $emailTemplate = \App\Http\AdminHelpers::emailTemplate('Other Services Feedback'); ?>
					<input type="hidden" class="form-control" name="feedback_id">
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
                    <div class="form-group">
                        <label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" name="manuscript[]" multiple accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf" required>
						{{ trans('site.docx-pdf-odt-text') }}
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.hours-worked') }}</label>
                        <input type="number" class="form-control" step="0.01" name="hours_worked">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand pull-right">{{ trans('site.add-feedback') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="approveCoachingSessionModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.approve-coaching-timer') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.approve-coaching-timer-question') }}</p>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.approve') }}</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="viewHelpWithModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Hjelp med</h4>
			</div>
			<div class="modal-body">
				<pre></pre>
			</div>
		</div>
	</div>
</div>

<div id="finishTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Fullfør oppgave</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>Er du sikker på at du vil fullføre denne oppgaven?</p>
					<button type="submit" class="btn btn-success pull-right">Fullfør</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Rediger oppgave</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<input type="hidden" name="user_id" value="">
					<div class="form-group">
						<label>Oppgave</label>
						<textarea name="task" cols="30" rows="10" class="form-control" required></textarea>
					</div>
					<div class="form-group">
						<label>{{ trans('site.assign-to') }}</label>
						<select name="assigned_to" class="form-control select2" required>
							<option value="" disabled="" selected>-- Velg tilordnet --</option>
							@foreach( App\User::whereIn('role', array(1,3))->orderBy('created_at', 'desc')->get() as $editor )
								<option value="{{ $editor->id }}">{{ $editor->full_name }}</option>
							@endforeach
						</select>
					</div>
					<button type="submit" class="btn btn-brand pull-right">Oppdater oppgave</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteTaskModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Slett oppgave</h4>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
					<p>Er du sikker på at du vil slette denne oppgaven?</p>
					<button type="submit" class="btn btn-danger pull-right">Slett</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editExpectedFinishModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Rediger forventet sluttdato</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.expected-finish') }}</label>
						<input type="date" name="expected_finish" class="form-control" required>
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitPersonalAssignmentFeedbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ trans('site.submit-feedback-to') }} <em></em></h4>
            </div>
            <div class="modal-body">
                <form id="submitPersonalAssignmentFeedbackForm" method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					<input type="hidden" class="form-control" name="feedback_id">
                    <?php $emailTemplate = \App\Http\AdminHelpers::emailTemplate('Assignment Manuscript Feedback'); ?>
                    {{ csrf_field() }}
					<div id="dates"></div>
					<div id="feedbackFileAppend">-</div>
					<div class="form-check" id="replaceAdd">
						<input class="form-check-input" type="checkbox" id="flexCheckDefault" name="replaceFiles">
						<label id="replace" class="form-check-label" for="flexCheckDefault">{{ trans('site.replace-feedback-file') }}</label>
					</div>
                    <div class="form-group">
                        <label name="manuscriptLabel">{{ trans_choice('site.manuscripts', 1) }}</label>
                        <input type="file" class="form-control" required multiple name="filename[]"
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/pdf, application/vnd.oasis.opendocument.text">
                        {{ trans('site.docx-pdf-odt-text') }} <br>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('site.grade') }}</label>
                        <input type="number" class="form-control" step="0.01" name="grade">
                    </div>
					<div class="form-group">
                        <label>{{ trans('site.notes_to_head_editor') }}</label>
                        <textarea name="notes_to_head_editor" class="form-control" cols="30" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand pull-right margin-top">{{ trans('site.submit') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="setReplayModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.replay-link') }}</label>
						<input type="url" name="replay_link" class="form-control">
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.comments', 1) }}</label>
						<textarea name="comment" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label>{{ trans('site.document') }}</label>
						<input type="file" name="document" class="form-control"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                   application/msword,
                               application/pdf,">
					</div>
					<div class="form-group">
						<small>{{ trans('site.coaching-timer.form.note') }}</small>
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="selfPublishingFeedbackModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Feedback</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans_choice('site.manuscripts', 1) }}</label>
						<input type="file" name="manuscript[]" class="form-control"
							   accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf,
					    application/vnd.oasis.opendocument.text" multiple>
					</div>
					<div class="form-group">
						<label>{{ trans_choice('site.notes', 1) }}</label>
						<textarea name="notes" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<div class="text-right">
						<button class="btn btn-brand" type="submit">{{ trans('site.save') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="acceptRequest" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title main-title"><em></em></h4>
				<h5 class="modal-title sub-title"></h5>
			</div>
			<div class="modal-body">
				<a href="#" style="width: 100px;" class="btn btn-success yesBtn">{{ trans('site.front.yes') }}</a>
				<a href="#" style="width: 100px;" class="btn btn-danger" data-dismiss="modal">{{ trans('site.front.no') }}</a>
			</div>
		</div>
	</div>
</div>

<div id="editContentModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.edit-content') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.content') }}</label>
						<textarea name="manu_content" cols="30" rows="10" class="form-control tinymce" id="editContentEditor" required></textarea>
					</div>
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-success pull-right margin-top">{{ trans('site.save') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="freeManuscriptFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">{{ trans('site.send-feedback') }}</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" id="sendFeedbackForm" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.body') }}</label>
						<textarea name="email_content" cols="30" rows="10" class="form-control tinymce" id="FMEmailContentEditor" required></textarea>
					</div>
					<div class="clearfix"></div>
					<button type="submit" class="btn btn-brand pull-right margin-top" id="sendFeedbackEmail">{{ trans('site.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="projectHoursModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
		    	<button type="button" class="close" data-dismiss="modal">&times;</button>
		    	<h4 class="modal-title">Rediger prosjekt</h4>
		  	</div>
		  	<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
		    		{{ csrf_field() }}
					<div class="form-group">
						<label>Project Number</label>
						<input type="text" class="form-control" name="project_number" readonly>
					</div>
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" name="name" readonly>
					</div>
					<div class="form-group">
						<label>Number of hours</label>
						<input type="text" name="editor_total_hours" class="form-control" id="timeInput" required>
						<button type="button" class="btn btn-xs" onclick="adjustTime(1)">+1</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(0.5)">+1/2</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(-0.5)">-1/2</button>
						<button type="button" class="btn btn-xs" onclick="adjustTime(-1)">-1</button>
					</div>
					<div class="text-right margin-top">
		      			<button type="submit" class="btn btn-brand">{{ trans('site.submit') }}</button>
		      		</div>
		    	</form>
		  	</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
	var cacheBuster = '{{ $cacheBuster }}';

	// PWA Install
	var editorDeferredPrompt = null;
	var editorIsStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
	window.addEventListener('beforeinstallprompt', function(e) { e.preventDefault(); editorDeferredPrompt = e; showEditorInstall(); });
	function showEditorInstall() {
		if (editorIsStandalone || localStorage.getItem('editor-pwa-dismissed')) return;
		document.getElementById('editorInstallBanner').style.display = 'flex';
	}
	function installEditorPWA() {
		if (editorDeferredPrompt) {
			editorDeferredPrompt.prompt();
			editorDeferredPrompt.userChoice.then(function(r) { if (r.outcome==='accepted') document.getElementById('editorInstallBanner').style.display='none'; editorDeferredPrompt=null; });
		} else {
			alert('For å installere appen:\n\n📱 iPhone/iPad: Trykk Del-knappen (⎋) → «Legg til på Hjem-skjerm»\n\n📱 Android: Trykk ⋮ → «Installer app»');
		}
	}
	function dismissEditorInstall() { document.getElementById('editorInstallBanner').style.display='none'; localStorage.setItem('editor-pwa-dismissed','1'); }
	if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent) && !editorIsStandalone && !localStorage.getItem('editor-pwa-dismissed')) {
		document.getElementById('editorInstallBanner').style.display = 'flex';
	}

	// Push notification settings
	(function() {
		if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
		navigator.serviceWorker.ready.then(function(reg) {
			reg.pushManager.getSubscription().then(function(sub) {
				if (!sub && !localStorage.getItem('editor-push-asked')) {
					localStorage.setItem('editor-push-asked', '1');
					Notification.requestPermission().then(function(p) {
						if (p === 'granted' && typeof subscribePush === 'function') subscribePush(reg);
					});
				}
			});
		});
	})();

	// Vis melding fra Sven Inge (med mulighet for å lukke)
	(function() {
		var el = document.getElementById('svenIngeMelding');
		if (el) {
			var key = '{{ md5(now()->format('Y-m-d')) }}';
			if (localStorage.getItem('hideSvenMelding') !== key) {
				el.style.display = 'block';
			}
		}
	})();


				plLeader = entries[0].team.displayName;

				// Velg 4 tilfeldige lag fra topp 10, inkludert lederen
				var teams = [plLeader];
	$('.viewManuscriptBtn').click(function(){
		var fields = $(this).data('fields');
		var modal = $('#viewManuscriptModal');
		modal.find('#name').text(fields.name);
		modal.find('#email').text(fields.email);
		modal.find('#content').text(fields.content);
	});
	$('.approveFeedbackAdminBtn').click(function(){
		var modal = $('#approveFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});
	$('.removeFeedbackAdminBtn').click(function(){
		var modal = $('#removeFeedbackAdminModal');
		var action = $(this).data('action');
		modal.find('form').attr('action', action);
	});

	$(".editContentBtn").click(function() {
		let action = $(this).data('action');
		let content = $(this).data('content');
		let modal = $('#editContentModal');
		modal.find('form').attr('action', action);
		tinymce.get('editContentEditor').setContent(content);
	});

	$('#myAssignmentTable, .assignment-table').on('click','.submitFeedbackBtn',function (){
		var modal = $('#submitFeedbackModal');
		var name = $(this).data('name');
		var action = $(this).data('action');
		var manuscript_id = $(this).data('manuscript_id');
		var is_edit = $(this).data('edit');
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
		modal.find('form').find('input[name=manuscript_id]').val(manuscript_id);

		$('#submitFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

		if (is_edit) {
			let feedbackFileName = $(this).data('manuscript');
			let createdAt = $(this).data('created_at');
			let updatedAt = $(this).data('updated_at');
			let feedbackId = $(this).data('feedback_id');
			let grade = $(this).data('grade');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

			modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Rediger tilbakemelding");
			modal.find('[name=grade]').val(grade)
			modal.find('[name=manuscriptLabel]').text("Replace Manuscript")
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
			feedbackArray.forEach(function (item, index){
				modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
			})
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
		}
	});

	$('#shopManuTable').on('click','.addShopManuscriptFeedback',function (){
		var modal = $('#addFeedbackModal');
		var action = $(this).data('action');
		var is_edit = $(this).data('edit');
		modal.find('form').attr('action', action);

		$('#addFeedbackModalForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

		if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let notes = $(this).data('f_notes');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

			modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Rediger tilbakemelding");
			modal.find('[name=notes]').val(notes)
			modal.find('[name=manuscriptLabel]').text("Replace Manuscript")
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
			feedbackArray.forEach(function (item, index){
				modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
			})
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
		}
	});

	$(".finishAssignmentManuscriptBtn").click(function(){
		let modal = $('#finishAssignmentManuscriptModal');
		let action = $(this).data('action');
		modal.find('form').attr('action', action);
	});

	$('.assignEditorBtn').click(function(){
		let action = $(this).data('action');
		let editor = $(this).data('editor');
		let modal = $('#assignEditorModal');
		modal.find('select').val(editor);
		modal.find('form').attr('action', action);
	});

	$(".pendingAssignmentEditorBtn").click(function(){
		let action = $(this).data('action');
		let editor = $(this).data('editor');
		let preferred_editor = $(this).data('preferred-editor');
		let preferred_editor_name = $(this).data('preferred-editor-name');
		let modal = $('#pendingAssignmentEditorModal');
		modal.find('select').val(preferred_editor).trigger('change');
		modal.find('form').attr('action', action);

		if (preferred_editor) {
			modal.find('.select2').hide();
			modal.find('.hidden-container').show();
			modal.find('.hidden-container').find('label').empty().text(preferred_editor_name);
		} else {
			modal.find('.select2').show();
			modal.find('.hidden-container').hide();
		}
	});

	$(".updateOtherServiceStatusBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#updateOtherServiceStatusModal');
		let service = $(this).data('service');
		let title = 'Korrektur';
		if (service === 1) { title = 'Språkvask'; }
		modal.find('form').attr('action', action);
		modal.find('.modal-title').find('span').text(title);
	});

	$(".setOtherServiceFinishDateBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#setOtherServiceFinishDateModal');
		let finish = $(this).data('finish');
		modal.find('form').attr('action', action);
		modal.find('form').find('[name=expected_finish]').val(finish);
	});

	$('#correctionTable').on('click','.addOtherServiceFeedbackBtn',function (){
		let action = $(this).data('action');
		let modal = $('#addOtherServiceFeedbackModal');
		let service = $(this).data('service');
		let title = 'Korrektur';
		let is_edit = $(this).data('edit');
		if (service === 1) { title = 'Språkvask'; }
		modal.find('form').attr('action', action);
		modal.find('.modal-title').find('span').text(title);

		$('#addOtherServiceFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

		if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

			modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Rediger tilbakemelding");
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours_worked]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
			feedbackArray.forEach(function (item, index){
				modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
			})
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
		}
	});
	
	$('#copyEditingTable').on('click','.addOtherServiceFeedbackBtn',function (){
		let action = $(this).data('action');
		let modal = $('#addOtherServiceFeedbackModal');
		let service = $(this).data('service');
		let title = 'Korrektur';
		let is_edit = $(this).data('edit');
		if (service === 1) { title = 'Språkvask'; }
		modal.find('form').attr('action', action);
		modal.find('.modal-title').find('span').text(title);

		$('#addOtherServiceFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

		if (is_edit) {
			let feedbackFileName = $(this).data('f_file');
			let createdAt = $(this).data('f_created_at');
			let updatedAt = $(this).data('f_updated_at');
			let feedbackId = $(this).data('f_id');
			let hours = $(this).data('hours');
			let notes_to_head_editor = $(this).data('notes_to_head_editor');

			modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Rediger tilbakemelding");
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=hours_worked]').val(hours)
			modal.find('[name=notes_to_head_editor]').val(notes_to_head_editor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
			feedbackArray.forEach(function (item, index){
				modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
			})
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
		}
	});

	$(".approveCoachingSessionBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#approveCoachingSessionModal');
		modal.find('form').attr('action', action);
	});

	$('#coachingTable').on('click','.viewHelpWithBtn',function (){
		let details = $(this).data('details');
		let modal = $("#viewHelpWithModal");
		modal.find('.modal-body').find('pre').text(details);
	});

	// Unified manuscript lock toggle for editor portal
	$(document).on('change', '.manuscript-lock-toggle', function(){
		var $cb = $(this);
		var type = $cb.data('type');
		var id = $cb.data('id');
		var locked = $cb.prop('checked') ? 1 : 0;
		var $icon = $cb.siblings('i');
		$icon.attr('class', locked ? 'fa fa-lock text-danger' : 'fa fa-unlock-alt text-muted');
		$.ajax({
			type: 'POST',
			url: '{{ route("editor.manuscript.lock") }}',
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
			data: { type: type, id: id, locked: locked },
			error: function() { $cb.prop('checked', !locked); $icon.attr('class', !locked ? 'fa fa-lock text-danger' : 'fa fa-unlock-alt text-muted'); }
		});
	});

	$(".finishTaskBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#finishTaskModal');
		modal.find('form').attr('action', action);
	});

	$(".editTaskBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#editTaskModal');
		let fields = $(this).data('fields');
		modal.find('form').attr('action', action);
		modal.find('[name=task]').text(fields.task);
		modal.find('[name=user_id]').val(fields.user_id);
		modal.find('form').find('[name=assigned_to]').val(fields.assigned_to).trigger('change');
	});

	// Legacy lock-toggle (replaced by manuscript-lock-toggle above)
	$(".lock-toggle").change(function(){
		var $cb = $(this);
		$cb.closest('label').find('.manuscript-lock-toggle').trigger('change');
	});
	/* Old code kept for reference:
	$(".lock-toggle-old").change(function(){
		let course_id = $(this).attr('data-id');
		let is_checked = $(this).prop('checked');
		let check_val = is_checked ? 1 : 0;
		$.ajax({
			type:'POST',
			url:'/assignment_manuscript/lock-status',
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			data: { "manuscript_id" : course_id, 'locked' : check_val },
			success: function(data){}
		});
	});
	*/

	$(".deleteTaskBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#deleteTaskModal');
		modal.find('form').attr('action', action);
	});

	$(".editExpectedFinishBtn").click(function() {
		let expected_finish = $(this).data('expected_finish');
		let modal = $('#editExpectedFinishModal');
		let action = $(this).data('action');
		modal.find('form').attr('action', action);
		modal.find('[name=expected_finish]').val(expected_finish);
	});

	$('#myAssignedShopManuTable').on('click','.submitPersonalAssignmentFeedbackBtn',function (){
		let modal = $('#submitPersonalAssignmentFeedbackModal');
		let name = $(this).data('name');
		let action = $(this).data('action');
		let is_edit = $(this).data('edit');
		
		modal.find('em').text(name);
		modal.find('form').attr('action', action);

		$('#submitPersonalAssignmentFeedbackForm').trigger('reset');
		modal.find('#feedbackFileAppend').html('');
		modal.find('.modal-title').text("Feedback");
		modal.find('#dates').html('');
		modal.find('form').find('input[type=file]').attr('required');
		modal.find('[name=feedback_id]').val('')
		modal.find('[name=manuscriptLabel]').show();
		modal.find('#replaceAdd').hide();

		if (is_edit) {
			let feedbackFileName = $(this).data('manuscript');
			let grade = $(this).data('grade');
			let createdAt = $(this).data('created_at');
			let updatedAt = $(this).data('updated_at');
			let feedbackId = $(this).data('feedback_id');
			let notesToHeadEditor = $(this).data('notes_to_head_editor');

			modal.find('form').find('input[type=file]').removeAttr('required');
			modal.find('.modal-title').text("Rediger tilbakemelding");
			modal.find('[name=grade]').val(grade)
			modal.find('[name=feedback_id]').val(feedbackId)
			modal.find('[name=notes_to_head_editor]').val(notesToHeadEditor)
			
			modal.find('#dates').append('<label>Created At</label>&nbsp;'+createdAt);
			modal.find('#dates').append('<br><label>Last Updated At</label>&nbsp;'+updatedAt+'<br><br>');

			var feedbackArray = feedbackFileName.split(",");
			modal.find('#feedbackFileAppend').append('<label>Manuscript</label><br>')
			feedbackArray.forEach(function (item, index){
				modal.find('#feedbackFileAppend').append('<a href="'+ item + '?v=' + cacheBuster +'" name="feedback_filename" class="" download>'+ item +'</a><br>')
			})
			modal.find('#feedbackFileAppend').append('<br>');
			modal.find('[name=manuscriptLabel]').hide();
			modal.find('#replaceAdd').show();
		}
	});

	$('#coachingTable').on('click','.setReplayBtn',function (){
		let action = $(this).data('action');
		let modal = $('#setReplayModal');
		modal.find('form').attr('action', action);
	});

	function disableSubmit(t) {
		let submit_btn = $(t).find('[type=submit]');
		submit_btn.text('');
		submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
		submit_btn.attr('disabled', 'disabled');
	};

	$('.acceptRequestBtn').click(function(){
		let action = $(this).data('action');
		let title = $(this).data('title');
		let sub_title = $(this).data('sub_title');
		let modal = $('#acceptRequest');
		modal.find('.main-title').text(title);
		modal.find('.sub-title').text(title);
		modal.find('.yesBtn').attr('href', action);
	});

	$(".sendFMFeedbackBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#freeManuscriptFeedbackModal');
		modal.find('form').attr('action', action);
		let fields = $(this).data('fields');
		let email_template = $(this).data('email_template');
		let content = fields.feedback_content ? fields.feedback_content : email_template;
		tinymce.get('FMEmailContentEditor').setContent(content);
	});

	$(".selfPublishingFeedbackBtn").click(function(){
		let action = $(this).data('action');
		let modal = $('#selfPublishingFeedbackModal');
		modal.find('form').attr('action', action);
	});

	$(".projectHoursBtn").click(function() {
		let action = $(this).data('action');
		let modal = $('#projectHoursModal');
		let record = $(this).data('record');
		modal.find('form').attr('action', action);
		modal.find("[name=project_number]").val(record.identifier);
		modal.find("[name=name]").val(record.name);
		modal.find("[name=editor_total_hours]").val(record.editor_total_hours);
	})

	function editExpectedFinish(self) {
		let expected_finish = $(self).data('expected_finish');
		let modal = $('#editExpectedFinishModal');
		let action = $(self).data('action');
		modal.find('form').attr('action', action);
		modal.find('[name=expected_finish]').val(expected_finish);
	}

	function editFMContent(self) {
		let action = $(self).data('action');
		let content = $(self).data('content');
		let modal = $('#editContentModal');
		modal.find('form').attr('action', action);
		tinymce.get('editContentEditor').setContent(content);
	}

	function sendFMFeedback(self) {
		let action = $(self).data('action');
		let modal = $('#freeManuscriptFeedbackModal');
		modal.find('form').attr('action', action);
		let fields = $(self).data('fields');
		let email_template = $(self).data('email_template');
		let content = fields.feedback_content ? fields.feedback_content : email_template;
		tinymce.get('FMEmailContentEditor').setContent(content);
	}

	function adjustTime(amount) {
		let timeInput = document.getElementById('timeInput');
		let currentTime = parseFloat(timeInput.value);
		if (isNaN(currentTime)) { currentTime = 0; }
		timeInput.value = currentTime + amount;
	}
</script>
@stop