{{-- Profil & innstillinger — redesignet --}}
@extends('frontend.layouts.course-portal')

@section('title')
<title>Profil &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Profil @stop

@section('styles')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
	<link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
<style>
/* ── RESET / LAYOUT ──────────────────────────────── */
#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; overflow-x: hidden !important; max-width: 100vw; }
#main-container { overflow-x: hidden !important; }

.pf-redesign {
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	max-width: 880px;
	margin: 0 auto;
	padding: 2rem;
	-webkit-font-smoothing: antialiased;
}

/* ── SIDEBAR TOGGLE ─────────────────────────────── */
.pf-redesign .pf-sidebar-toggle {
	display: none; position: fixed; top: 16px; left: 16px; z-index: 1050;
	width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
	background: #862736; align-items: center; justify-content: center; cursor: pointer;
	box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
	padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.pf-redesign .pf-sidebar-toggle:hover { background: #9c2e40; transform: scale(1.05); }
.pf-redesign .pf-sidebar-toggle:active { transform: scale(0.96); }
.pf-redesign .pf-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
@media (max-width: 1026px) { .pf-redesign .pf-sidebar-toggle { display: flex !important; } }

/* ── PAGE HEADER ─────────────────────────────────── */
.pf-header { margin-bottom: 1.5rem; }
.pf-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: #1a1a1a; }
.pf-header p { font-size: 0.875rem; color: #5a5550; }

/* ── TABS ────────────────────────────────────────── */
.pf-tabs {
	display: flex; gap: 0; border-bottom: 2px solid rgba(0,0,0,0.08);
	margin-bottom: 1.5rem; overflow-x: auto;
	-webkit-overflow-scrolling: touch;
	scrollbar-width: none; /* Firefox */
	-ms-overflow-style: none; /* IE/Edge */
}
.pf-tabs::-webkit-scrollbar { display: none; } /* Chrome/Safari */
.pf-tab {
	padding: 0.7rem 1.15rem; border: none; background: transparent;
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.835rem; font-weight: 500; color: #8a8580;
	cursor: pointer; white-space: nowrap; position: relative; transition: color 0.15s;
}
.pf-tab:hover { color: #1a1a1a; }
.pf-tab.active { color: #862736; font-weight: 600; }
.pf-tab.active::after {
	content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
	height: 2px; background: #862736;
}
.pf-tab-panel { display: none; }
.pf-tab-panel.active { display: block; }

/* ── SETTINGS CARD ───────────────────────────────── */
.pf-card {
	background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; margin-bottom: 1.25rem; overflow: hidden;
}
.pf-card__header {
	display: flex; align-items: center; justify-content: space-between;
	padding: 1.25rem 1.5rem 0.75rem;
}
.pf-card__title { font-size: 1rem; font-weight: 700; color: #1a1a1a; }
.pf-card__desc { font-size: 0.8rem; color: #8a8580; }
.pf-card__body { padding: 0 1.5rem 1.5rem; }

/* ── AVATAR ──────────────────────────────────────── */
.pf-avatar-section {
	display: flex; align-items: center; gap: 1.25rem;
	padding: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.08);
}
.pf-avatar-circle {
	width: 72px; height: 72px; border-radius: 50%;
	background: #f4e8ea; display: flex; align-items: center; justify-content: center;
	font-size: 1.5rem; font-weight: 700; color: #862736; flex-shrink: 0;
	position: relative; overflow: hidden;
}
.pf-avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
.pf-avatar-info__name { font-size: 1.15rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.1rem; }
.pf-avatar-info__meta { font-size: 0.8rem; color: #8a8580; }
.pf-avatar-info__change {
	display: inline-block; margin-top: 0.5rem; font-size: 0.78rem;
	font-weight: 600; color: #862736; cursor: pointer; text-decoration: none; transition: color 0.15s;
}
.pf-avatar-info__change:hover { color: #9c2e40; }

/* ── FORM ────────────────────────────────────────── */
.pf-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.pf-form-grid--full { grid-template-columns: 1fr; }
.pf-form-group { margin-bottom: 0; }
.pf-form-group--full { grid-column: 1 / -1; }
.pf-form-group label {
	display: block; font-size: 0.78rem; font-weight: 600;
	color: #1a1a1a; margin-bottom: 0.3rem;
}
.pf-form-group input,
.pf-form-group select {
	width: 100%; padding: 0.6rem 0.85rem;
	border: 1px solid rgba(0,0,0,0.12); border-radius: 6px;
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.85rem; color: #1a1a1a; background: #fff;
	outline: none; transition: border-color 0.15s;
}
.pf-form-group input:focus,
.pf-form-group select:focus { border-color: #862736; }
.pf-form-group input::placeholder { color: #8a8580; }
.pf-form-group input:disabled { background: #faf8f5; color: #8a8580; cursor: not-allowed; }
.pf-form-hint { font-size: 0.72rem; color: #8a8580; margin-top: 0.25rem; }

.pf-form-actions {
	display: flex; justify-content: flex-end; gap: 0.5rem;
	margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.08);
}

/* ── BUTTONS ─────────────────────────────────────── */
.pf-btn {
	display: inline-flex; align-items: center; gap: 0.3rem;
	padding: 0.55rem 1.15rem; border-radius: 6px;
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.825rem; font-weight: 600; text-decoration: none;
	cursor: pointer; border: none; transition: all 0.15s;
}
.pf-btn--primary { background: #862736; color: #fff; }
.pf-btn--primary:hover { background: #9c2e40; color: #fff; }
.pf-btn--secondary {
	background: transparent; color: #5a5550;
	border: 1px solid rgba(0,0,0,0.12);
}
.pf-btn--secondary:hover { border-color: #862736; color: #862736; }

/* ── TOGGLE SWITCHES ─────────────────────────────── */
.pf-toggle-list { display: flex; flex-direction: column; }
.pf-toggle-item {
	display: flex; align-items: center; justify-content: space-between;
	padding: 1rem 0; border-bottom: 1px solid rgba(0,0,0,0.08); gap: 1rem;
}
.pf-toggle-item:last-child { border-bottom: none; }
.pf-toggle-item__info { flex: 1; }
.pf-toggle-item__title { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem; }
.pf-toggle-item__desc { font-size: 0.75rem; color: #8a8580; line-height: 1.5; }
.pf-toggle-switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.pf-toggle-switch input { opacity: 0; width: 0; height: 0; }
.pf-toggle-slider {
	position: absolute; cursor: pointer;
	top: 0; left: 0; right: 0; bottom: 0;
	background: rgba(0,0,0,0.12); border-radius: 12px; transition: background 0.2s;
}
.pf-toggle-slider::before {
	content: ''; position: absolute; height: 18px; width: 18px;
	left: 3px; bottom: 3px; background: #fff; border-radius: 50%;
	transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
.pf-toggle-switch input:checked + .pf-toggle-slider { background: #862736; }
.pf-toggle-switch input:checked + .pf-toggle-slider::before { transform: translateX(20px); }

/* ── EMAIL LIST ──────────────────────────────────── */
.pf-email-list { display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.25rem; }
.pf-email-item {
	display: flex; align-items: center; gap: 0.75rem;
	padding: 0.75rem 1rem; border: 1px solid rgba(0,0,0,0.08); border-radius: 8px;
}
.pf-email-item__address { font-size: 0.875rem; font-weight: 500; color: #1a1a1a; flex: 1; }
.pf-email-item__badge {
	font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.55rem;
	border-radius: 4px; background: #e8f5e9; color: #2e7d32;
}
.pf-email-item__badge--secondary { background: #e3f2fd; color: #1565c0; }
.pf-email-item__actions { display: flex; gap: 0.35rem; }
.pf-email-item__actions .pf-btn { padding: 0.25rem 0.6rem; font-size: 0.72rem; }

.pf-email-add { display: flex; gap: 0.5rem; }
.pf-email-add input {
	flex: 1; padding: 0.6rem 0.85rem;
	border: 1px solid rgba(0,0,0,0.12); border-radius: 6px;
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.85rem; outline: none;
}
.pf-email-add input:focus { border-color: #862736; }

/* ── KURSBEVIS ───────────────────────────────────── */
.pf-kursbevis-list { display: flex; flex-direction: column; gap: 0.6rem; }
.pf-kursbevis-item {
	display: flex; align-items: center; gap: 1rem;
	padding: 1rem 1.25rem; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 10px; transition: border-color 0.15s;
}
.pf-kursbevis-item:hover { border-color: rgba(0,0,0,0.12); }
.pf-kursbevis-item__icon {
	width: 40px; height: 40px; border-radius: 10px;
	background: #f4e8ea; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.pf-kursbevis-item__icon svg { width: 20px; height: 20px; }
.pf-kursbevis-item__info { flex: 1; }
.pf-kursbevis-item__name { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; }
.pf-kursbevis-item__date { font-size: 0.75rem; color: #8a8580; }
.pf-kursbevis-empty {
	text-align: center; padding: 2.5rem; color: #8a8580; font-size: 0.875rem;
}

/* ── ALERTS ──────────────────────────────────────── */
.pf-alert {
	padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.85rem;
	margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
}
.pf-alert--success { background: #e8f5e9; color: #2e7d32; }
.pf-alert--danger { background: #fce4ec; color: #c62828; }
.pf-alert__close {
	margin-left: auto; background: none; border: none; cursor: pointer;
	font-size: 1.1rem; color: inherit; opacity: 0.6; line-height: 1;
}
.pf-alert__close:hover { opacity: 1; }

/* ── RESPONSIVE ──────────────────────────────────── */
@media (max-width: 1026px) {
	.pf-redesign { padding-top: 4.5rem; }
}
@media (max-width: 600px) {
	.pf-redesign { padding: 1rem; padding-top: 4.5rem; }
	.pf-form-grid { grid-template-columns: 1fr; }
	.pf-avatar-section { flex-direction: column; text-align: center; }
	.pf-tab { padding: 0.55rem 0.7rem; font-size: 0.78rem; }
	.pf-header h1 { font-size: 1.25rem; }
	.pf-card__body { padding: 0 1rem 1rem; }
	.pf-card__header { padding: 1rem 1rem 0.5rem; }
	.pf-toggle-item { padding: 0.75rem 0; }
	.pf-email-item { flex-wrap: wrap; }
	.pf-email-item__actions { width: 100%; margin-top: 0.35rem; }
}
</style>
@stop

@section('content')
<div class="pf-redesign">

	{{-- Sidebar toggle --}}
	<button class="pf-sidebar-toggle" data-sidebar-toggle aria-label="Meny">
		<svg viewBox="0 0 24 24" fill="none"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
	</button>

	{{-- Alerts --}}
	@if($errors->any())
		<div class="pf-alert pf-alert--danger">
			<span>
				@foreach($errors->all() as $error)
					{{ $error }}@if(!$loop->last)<br>@endif
				@endforeach
			</span>
			<button class="pf-alert__close" onclick="this.parentElement.remove()">&times;</button>
		</div>
	@endif
	@if(session()->has('profile_success'))
		<div class="pf-alert pf-alert--success">
			<span>{{ session()->get('profile_success') }}</span>
			<button class="pf-alert__close" onclick="this.parentElement.remove()">&times;</button>
		</div>
	@endif

	{{-- Page header --}}
	<div class="pf-header">
		<h1>Profil & innstillinger</h1>
		<p>Administrer kontoen din, varsler og personlige opplysninger.</p>
	</div>

	{{-- Tabs --}}
	<div class="pf-tabs" id="profileTabs">
		<button class="pf-tab active" data-tab="profil">Profil</button>
		<button class="pf-tab" data-tab="varsler">Varsler & e-post</button>
		<button class="pf-tab" data-tab="epost">E-postadresser</button>
		<button class="pf-tab" data-tab="passord">Passord</button>
		<button class="pf-tab" data-tab="kursbevis">Kursbevis</button>
	</div>

	{{-- ═══════════ TAB 1: PROFIL ═══════════ --}}
	<div class="pf-tab-panel active" id="panel-profil">
		<div class="pf-card">
			{{-- Avatar --}}
			<div class="pf-avatar-section">
				<div class="pf-avatar-circle">
					@if(Auth::user()->hasProfileImage)
						<img src="{{ Auth::user()->profile_image }}" alt="{{ Auth::user()->full_name }}">
					@else
						{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
					@endif
				</div>
				<div>
					<div class="pf-avatar-info__name">{{ Auth::user()->full_name }}</div>
					<div class="pf-avatar-info__meta">{{ Auth::user()->email }}</div>
					<a href="#" class="pf-avatar-info__change" id="changePhotoLink">Endre profilbilde</a>
					<form method="POST" action="{{ route('learner.profile.update-photo') }}" enctype="multipart/form-data" id="photo-form" style="display:none;">
						{{ csrf_field() }}
						<input type="file" accept="image/*" name="image" id="photoInput">
					</form>
				</div>
			</div>

			{{-- Profile form --}}
			<div class="pf-card__body" style="padding-top: 1.25rem;">
				<form id="profileForm" method="POST" action="{{ route('learner.profile.update') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="pf-form-grid">
						<div class="pf-form-group">
							<label for="first_name">Fornavn</label>
							<input type="text" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" required autocomplete="off">
						</div>
						<div class="pf-form-group">
							<label for="last_name">Etternavn</label>
							<input type="text" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" required autocomplete="off">
						</div>
						<div class="pf-form-group">
							<label for="profile_email">E-post</label>
							<input type="email" id="profile_email" value="{{ Auth::user()->email }}" disabled>
							<span class="pf-form-hint">Endre e-post under &laquo;E-postadresser&raquo;-fanen.</span>
						</div>
						<div class="pf-form-group">
							<label for="phone">Telefon</label>
							<input type="tel" id="phone" name="phone" value="{{ Auth::user()->address->phone }}" autocomplete="off">
						</div>
					</div>

					<div style="margin-top: 1.25rem;">
						<div class="pf-card__title" style="margin-bottom: 0.75rem;">Adresse</div>
						<div class="pf-form-grid">
							<div class="pf-form-group">
								<label for="street">Gate</label>
								<input type="text" id="street" name="street" value="{{ Auth::user()->address->street }}" autocomplete="off">
							</div>
							<div class="pf-form-group">
								<label for="zip">Postnummer</label>
								<input type="text" id="zip" name="zip" value="{{ Auth::user()->address->zip }}" autocomplete="off">
							</div>
							<div class="pf-form-group">
								<label for="city">Sted</label>
								<input type="text" id="city" name="city" value="{{ Auth::user()->address->city }}" autocomplete="off">
							</div>
							<div class="pf-form-group">
								<label for="country">Land</label>
								<select id="country" name="country">
									<option value="Norge" {{ (Auth::user()->address->country ?? 'Norge') == 'Norge' ? 'selected' : '' }}>Norge</option>
									<option value="Sverige" {{ (Auth::user()->address->country ?? '') == 'Sverige' ? 'selected' : '' }}>Sverige</option>
									<option value="Danmark" {{ (Auth::user()->address->country ?? '') == 'Danmark' ? 'selected' : '' }}>Danmark</option>
								</select>
							</div>
						</div>
					</div>

					<div class="pf-form-actions">
						<button type="submit" class="pf-btn pf-btn--primary">Lagre endringer</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- ═══════════ TAB 2: VARSLER & E-POST ═══════════ --}}
	<div class="pf-tab-panel" id="panel-varsler">
		<form method="POST" action="{{ route('learner.profile.update-notifications') }}">
			{{ csrf_field() }}

			{{-- E-postvarsler --}}
			<div class="pf-card">
				<div class="pf-card__header">
					<div>
						<div class="pf-card__title">E-postvarsler</div>
						<div class="pf-card__desc">Velg hvilke e-poster du ønsker å motta fra oss.</div>
					</div>
				</div>
				<div class="pf-card__body">
					<div class="pf-toggle-list">
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Ukentlig kursoppdatering</div>
								<div class="pf-toggle-item__desc">Informasjon om nye moduler, ukens program og oppgaver.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="weekly_course_update" value="1" {{ Auth::user()->wantsNotification('weekly_course_update') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Mentormøte-påminnelse</div>
								<div class="pf-toggle-item__desc">Påminnelse dagen før mentormøtet starter.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="mentor_reminder" value="1" {{ Auth::user()->wantsNotification('mentor_reminder') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Tilbakemelding klar</div>
								<div class="pf-toggle-item__desc">Varsling når redaktøren har levert tilbakemelding på manuset ditt.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="feedback_ready" value="1" {{ Auth::user()->wantsNotification('feedback_ready') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Oppgavepåminnelse</div>
								<div class="pf-toggle-item__desc">Påminnelse når fristen for en innlevering nærmer seg.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="task_reminder" value="1" {{ Auth::user()->wantsNotification('task_reminder') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			{{-- Fakturavarsler --}}
			<div class="pf-card">
				<div class="pf-card__header">
					<div>
						<div class="pf-card__title">Fakturavarsler</div>
						<div class="pf-card__desc">Styr varsler om fakturaer og betalinger.</div>
					</div>
				</div>
				<div class="pf-card__body">
					<div class="pf-toggle-list">
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Påminnelse før forfall</div>
								<div class="pf-toggle-item__desc">E-post dagen før fakturaen forfaller. Skru av om du har kontroll selv.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="invoice_due_reminder" value="1" {{ Auth::user()->wantsNotification('invoice_due_reminder') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Purring ved forfall</div>
								<div class="pf-toggle-item__desc">E-post på forfallsdato dersom fakturaen ikke er betalt. Kan ikke skrus av.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" checked disabled>
								<span class="pf-toggle-slider" style="cursor: not-allowed;"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Kvittering ved betaling</div>
								<div class="pf-toggle-item__desc">E-postbekreftelse når betalingen er registrert.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="payment_receipt" value="1" {{ Auth::user()->wantsNotification('payment_receipt') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			{{-- Nyhetsbrev --}}
			<div class="pf-card">
				<div class="pf-card__header">
					<div>
						<div class="pf-card__title">Nyhetsbrev & markedsføring</div>
						<div class="pf-card__desc">Tilbud, nye kurs og inspirasjon fra Forfatterskolen.</div>
					</div>
				</div>
				<div class="pf-card__body">
					<div class="pf-toggle-list">
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Nyhetsbrev</div>
								<div class="pf-toggle-item__desc">Månedlig inspirasjon, skrivetips og nyheter.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="newsletter" value="1" {{ Auth::user()->wantsNotification('newsletter') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
						<div class="pf-toggle-item">
							<div class="pf-toggle-item__info">
								<div class="pf-toggle-item__title">Kurstilbud</div>
								<div class="pf-toggle-item__desc">Varsler om nye kurs, kampanjer og rabatter.</div>
							</div>
							<label class="pf-toggle-switch">
								<input type="checkbox" name="course_offers" value="1" {{ Auth::user()->wantsNotification('course_offers') ? 'checked' : '' }}>
								<span class="pf-toggle-slider"></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="pf-form-actions" style="border: none; padding: 0; margin-top: 0.5rem;">
				<button type="submit" class="pf-btn pf-btn--primary">Lagre innstillinger</button>
			</div>
		</form>
	</div>

	{{-- ═══════════ TAB 3: E-POSTADRESSER ═══════════ --}}
	<div class="pf-tab-panel" id="panel-epost">
		<div class="pf-card">
			<div class="pf-card__header">
				<div>
					<div class="pf-card__title">Dine e-postadresser</div>
					<div class="pf-card__desc">Hovedadressen brukes for innlogging og all e-post fra oss.</div>
				</div>
			</div>
			<div class="pf-card__body email-container">
				<div class="pf-email-list" id="email-list">
					{{-- Fylles via AJAX fra profile.js --}}
				</div>

				<div style="margin-top: 1rem;">
					<label style="font-size: 0.78rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.3rem; display: block;">Legg til ny e-postadresse</label>
					<div class="pf-email-add">
						<input type="email" name="email" placeholder="Din nye e-postadresse" autocomplete="off" onkeyup="methods.sendConfirmation(event)">
						<button class="pf-btn pf-btn--primary email-btn" type="button" onclick="methods.sendConfirmation()">
							<i class="plus" style="display:none"></i>Legg til
						</button>
					</div>
					<span class="pf-form-hint" style="margin-top: 0.5rem; display: block;">Du mottar en e-post for å bekrefte adressen. Kun bekreftede adresser kan settes som hoved.</span>
				</div>
			</div>
		</div>
	</div>

	{{-- ═══════════ TAB 4: PASSORD ═══════════ --}}
	<div class="pf-tab-panel" id="panel-passord">
		<div class="pf-card">
			<div class="pf-card__header">
				<div>
					<div class="pf-card__title">Endre passord</div>
					<div class="pf-card__desc">Bruk et sterkt passord med minst 8 tegn.</div>
				</div>
			</div>
			<div class="pf-card__body">
				<form method="POST" action="{{ route('learner.profile.update') }}">
					{{ csrf_field() }}
					{{-- Skjulte felt for å ikke overskrive profil --}}
					<input type="hidden" name="first_name" value="{{ Auth::user()->first_name }}">
					<input type="hidden" name="last_name" value="{{ Auth::user()->last_name }}">

					<div class="pf-form-grid pf-form-grid--full" style="max-width: 400px;">
						<div class="pf-form-group">
							<label for="old_password">Nåværende passord</label>
							<input type="password" id="old_password" name="old_password" placeholder="Ditt nåværende passord" autocomplete="off">
						</div>
						<div class="pf-form-group">
							<label for="new_password">Nytt passord</label>
							<input type="password" id="new_password" name="new_password" placeholder="Minst 8 tegn" autocomplete="off">
						</div>
						<div class="pf-form-group">
							<label for="confirm_password">Bekreft nytt passord</label>
							<input type="password" id="confirm_password" placeholder="Gjenta nytt passord" autocomplete="off">
						</div>
					</div>
					<div class="pf-form-actions" style="justify-content: flex-start;">
						<button type="submit" class="pf-btn pf-btn--primary">Oppdater passord</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- ═══════════ TAB 5: KURSBEVIS ═══════════ --}}
	<div class="pf-tab-panel" id="panel-kursbevis">
		<div class="pf-card">
			<div class="pf-card__header">
				<div>
					<div class="pf-card__title">Kursbevis</div>
					<div class="pf-card__desc">Last ned bevis for fullførte kurs.</div>
				</div>
			</div>
			<div class="pf-card__body">
				@php
					$diplomas = Auth::user()->diplomas()->orderBy('created_at', 'DESC')->get();
					$hasCerts = $diplomas->count() > 0 || (isset($certificates) && count($certificates) > 0);
				@endphp

				@if($hasCerts)
					<div class="pf-kursbevis-list">
						@foreach($diplomas as $diploma)
							<div class="pf-kursbevis-item">
								<div class="pf-kursbevis-item__icon">
									<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="2">
										<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
										<polyline points="14 2 14 8 20 8"/>
										<line x1="16" y1="13" x2="8" y2="13"/>
										<line x1="16" y1="17" x2="8" y2="17"/>
									</svg>
								</div>
								<div class="pf-kursbevis-item__info">
									<div class="pf-kursbevis-item__name">{{ $diploma->course->title }}</div>
									<div class="pf-kursbevis-item__date">Diplom</div>
								</div>
								<a href="{{ route('learner.download-diploma', $diploma->id) }}" class="pf-btn pf-btn--secondary">Last ned</a>
							</div>
						@endforeach

						@if(isset($certificates))
							@foreach($certificates as $certificate)
								<div class="pf-kursbevis-item">
									<div class="pf-kursbevis-item__icon">
										<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="2">
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
											<polyline points="14 2 14 8 20 8"/>
											<line x1="16" y1="13" x2="8" y2="13"/>
											<line x1="16" y1="17" x2="8" y2="17"/>
										</svg>
									</div>
									<div class="pf-kursbevis-item__info">
										<div class="pf-kursbevis-item__name">{{ $certificate->course_title }}</div>
										<div class="pf-kursbevis-item__date">Kursbevis</div>
									</div>
									<a href="{{ route('learner.download-course-certificate', $certificate->id) }}" class="pf-btn pf-btn--secondary">Last ned</a>
								</div>
							@endforeach
						@endif
					</div>
				@else
					<div class="pf-kursbevis-empty">
						Kursbevis genereres automatisk når du fullfører et kurs. Du har ingen fullførte kurs ennå.
					</div>
				@endif
			</div>
		</div>
	</div>

</div>

{{-- Unsaved changes modal --}}
<button id="hiddenTrigger" type="button" data-bs-toggle="modal" data-bs-target="#unsavedAddressModal" style="display:none;"></button>
<div id="unsavedAddressModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.save-changes') }}</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p>{{ trans('site.save-new-information-question') }}</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary save-changes">{{ trans('site.front.yes') }}</button>
				<button type="button" class="btn btn-secondary discard-changes" data-bs-dismiss="modal">{{ trans('site.front.no') }}</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('js/profile.js') }}"></script>
<script>
// ── Tab switching ────────────────────────────────────
$('#profileTabs').on('click', '.pf-tab', function(e) {
	e.preventDefault();
	e.stopPropagation();
	var tabId = $(this).data('tab');
	$('.pf-tab').removeClass('active');
	$(this).addClass('active');
	$('.pf-tab-panel').removeClass('active');
	$('#panel-' + tabId).addClass('active');
});

// ── Photo upload ─────────────────────────────────────
$('#changePhotoLink').on('click', function(e) {
	e.preventDefault();
	$('#photoInput').click();
});
$('#photoInput').on('change', function() {
	if (this.files && this.files[0]) {
		$('#photo-form').submit();
	}
});

// ── Unsaved changes detection (profil-tab) ───────────
(function(){
	var form = document.getElementById('profileForm');
	if(!form) return;
	var inputs = form.querySelectorAll("input[name='street'], input[name='zip'], input[name='city'], input[name='phone']");
	var original = {};
	inputs.forEach(function(input){ original[input.name] = input.value; });
	var isDirty = false;
	function checkDirty(){
		isDirty = Array.prototype.some.call(inputs, function(input){
			return input.value !== original[input.name];
		});
	}
	inputs.forEach(function(input){ input.addEventListener('input', checkDirty); });
	var targetHref = null;
	window.addEventListener('beforeunload', function(e){
		if(isDirty){ e.preventDefault(); e.returnValue = ''; }
	});
	document.querySelectorAll('a').forEach(function(a){
		a.addEventListener('click', function(e){
			if(isDirty){
				e.preventDefault();
				targetHref = this.href;
				document.getElementById('hiddenTrigger').click();
			}
		});
	});
	$(window).on('keydown', function(e){
		if(isDirty && (e.which === 116 || (e.which === 82 && e.ctrlKey))){
			e.preventDefault();
			targetHref = window.location.href;
			document.getElementById('hiddenTrigger').click();
		}
	});
	$('#unsavedAddressModal .save-changes').on('click', function(){
		isDirty = false;
		form.submit();
	});
	$('#unsavedAddressModal .discard-changes').on('click', function(){
		isDirty = false;
		$('#unsavedAddressModal').modal('hide');
		if(targetHref){ window.location.href = targetHref; }
		else { window.location.reload(); }
	});
})();

// ── Sidebar toggle (mobil) ───────────────────────────
$(function(){
	if (window.innerWidth <= 1026) {
		var sb = document.getElementById('learner-sidebar');
		if (sb) {
			sb.classList.remove('enlarge');
			var mc = document.getElementById('main-container');
			if (mc) mc.classList.remove('enlarge');
		}
	}
	$('.pf-sidebar-toggle').on('click', function(e){
		e.stopPropagation();
		var sb = $('#learner-sidebar');
		sb.toggleClass('enlarge');
		$('#main-container').toggleClass('enlarge');
	});
});
</script>
@stop
