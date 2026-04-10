@extends('frontend.layouts.course-portal')

@section('page_title', 'Beskjeder &rsaquo; Forfatterskolen')

@section('styles')
<style>
	#topbar { display: none !important; }
</style>
@stop

@section('content')

<div class="bk-redesign">

{{-- ── SCOPED CSS ───────────────────────────────────────────────── --}}
<style>
/* ── BK-REDESIGN SCOPE ──────────────────────────────────────── */
.bk-redesign { font-family: 'Source Sans 3', -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }

.bk-redesign .bk-page { max-width: 880px; margin: 0 auto; padding: 2rem 1rem; padding-top: 3.5rem; }

/* ── PAGE HEADER ──────────────────────────────────── */
.bk-redesign .bk-header { margin-bottom: 1.5rem; }
.bk-redesign .bk-header h1 { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
.bk-redesign .bk-header p { font-size: 0.875rem; color: #5a5550; margin: 0; }

/* ── SIDEBAR TOGGLE — vinrød, stor og tydelig ──────────────────── */
.bk-redesign .bk-sidebar-toggle {
	display: none; position: fixed; top: 16px; left: 16px; z-index: 1050;
	width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
	background: #862736; align-items: center; justify-content: center; cursor: pointer;
	box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
	padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.bk-redesign .bk-sidebar-toggle:hover { background: #9c2e40; transform: scale(1.05); }
.bk-redesign .bk-sidebar-toggle:active { transform: scale(0.96); }
.bk-redesign .bk-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; }
@media (max-width: 1026px) { .bk-redesign .bk-sidebar-toggle { display: flex !important; } }

/* ── TABS ─────────────────────────────────────────── */
.bk-redesign .bk-tabs { display: flex; gap: 0; border-bottom: 2px solid rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
.bk-redesign .bk-tab {
	padding: 0.7rem 1.15rem; border: none; background: transparent;
	font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.835rem; font-weight: 500;
	color: #8a8580; cursor: pointer; white-space: nowrap; position: relative; transition: color 0.15s;
}
.bk-redesign .bk-tab:hover { color: #1a1a1a; }
.bk-redesign .bk-tab.active { color: #862736; font-weight: 600; }
.bk-redesign .bk-tab.active::after {
	content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
	height: 2px; background: #862736; border-radius: 1px 1px 0 0;
}
.bk-redesign .bk-tab__count {
	display: inline-flex; align-items: center; justify-content: center;
	min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px;
	font-size: 0.65rem; font-weight: 600; margin-left: 0.35rem;
}
.bk-redesign .bk-tab.active .bk-tab__count { background: #f4e8ea; color: #862736; }
.bk-redesign .bk-tab:not(.active) .bk-tab__count { background: rgba(0,0,0,0.06); color: #8a8580; }

.bk-redesign .bk-panel { display: none; }
.bk-redesign .bk-panel.active { display: block; }

/* ── SEARCH BAR ───────────────────────────────────── */
.bk-redesign .bk-search { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; }
.bk-redesign .bk-search__input {
	flex: 1; padding: 0.6rem 1rem 0.6rem 2.25rem;
	border: 1px solid rgba(0,0,0,0.12); border-radius: 8px;
	font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.85rem; color: #1a1a1a;
	background: #fff url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238a8580' stroke-width='2' stroke-linecap='round' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 0.75rem center;
	outline: none; transition: border-color 0.15s;
}
.bk-redesign .bk-search__input:focus { border-color: #862736; }
.bk-redesign .bk-search__input::placeholder { color: #8a8580; }
.bk-redesign .bk-search__filter {
	padding: 0.6rem 1rem; border: 1px solid rgba(0,0,0,0.12); border-radius: 8px;
	font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.8rem; color: #5a5550;
	background: #fff; cursor: pointer; appearance: none;
	background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%238a8580' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
	background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2rem;
}

/* ── EMAIL LIST ───────────────────────────────────── */
.bk-redesign .bk-email-list {
	background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; overflow: hidden;
}
.bk-redesign .bk-email-item {
	display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem;
	border-bottom: 1px solid rgba(0,0,0,0.08); cursor: pointer;
	transition: background 0.1s; text-decoration: none; color: inherit;
}
.bk-redesign .bk-email-item:last-child { border-bottom: none; }
.bk-redesign .bk-email-item:hover { background: rgba(0,0,0,0.015); text-decoration: none; color: inherit; }

/* Type icon */
.bk-redesign .bk-icon {
	width: 36px; height: 36px; border-radius: 8px;
	display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.bk-redesign .bk-icon svg { width: 18px; height: 18px; }
.bk-redesign .bk-icon--mentor { background: #f4e8ea; }
.bk-redesign .bk-icon--system { background: rgba(0,0,0,0.04); }
.bk-redesign .bk-icon--course { background: #e3f2fd; }

/* Content */
.bk-redesign .bk-email-content { flex: 1; min-width: 0; }
.bk-redesign .bk-email-subject {
	font-size: 0.875rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem;
	white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.bk-redesign .bk-email-preview {
	font-size: 0.78rem; color: #8a8580;
	white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Date */
.bk-redesign .bk-email-date { font-size: 0.75rem; color: #8a8580; white-space: nowrap; flex-shrink: 0; }

/* Category tag */
.bk-redesign .bk-tag {
	font-size: 0.6rem; font-weight: 600; padding: 0.15rem 0.45rem;
	border-radius: 3px; white-space: nowrap; flex-shrink: 0;
}
.bk-redesign .bk-tag--mentor { background: #f4e8ea; color: #5c1a25; }
.bk-redesign .bk-tag--system { background: rgba(0,0,0,0.05); color: #8a8580; }
.bk-redesign .bk-tag--course { background: #e3f2fd; color: #1565c0; }

/* ── EMAIL DETAIL VIEW ────────────────────────────── */
.bk-redesign .bk-detail {
	background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; overflow: hidden;
}
.bk-redesign .bk-detail__header { padding: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.08); }
.bk-redesign .bk-detail__back {
	display: inline-flex; align-items: center; gap: 0.35rem;
	font-size: 0.8rem; color: #8a8580; text-decoration: none; margin-bottom: 1rem; transition: color 0.15s;
}
.bk-redesign .bk-detail__back:hover { color: #862736; text-decoration: none; }
.bk-redesign .bk-detail__back svg { width: 16px; height: 16px; stroke: currentColor; }
.bk-redesign .bk-detail__subject { font-size: 1.15rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem; }
.bk-redesign .bk-detail__meta {
	display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
	font-size: 0.8rem; color: #8a8580;
}
.bk-redesign .bk-detail__body {
	padding: 1.5rem; font-size: 0.9rem; color: #5a5550; line-height: 1.8;
}
.bk-redesign .bk-detail__body p + p { margin-top: 0.75rem; }
.bk-redesign .bk-detail__body strong { color: #1a1a1a; }
.bk-redesign .bk-detail__body ul { padding-left: 1.25rem; margin: 0.5rem 0; }
.bk-redesign .bk-detail__body li { margin-bottom: 0.35rem; }
.bk-redesign .bk-detail__body img { max-width: 100%; height: auto; }
.bk-redesign .bk-detail__body a { color: #862736; }

/* ── PAGINATION ───────────────────────────────────── */
.bk-redesign .bk-pagination {
	display: flex; align-items: center; justify-content: center;
	gap: 0.35rem; margin-top: 1.5rem; flex-wrap: wrap;
}
.bk-redesign .bk-pagination__info { font-size: 0.78rem; color: #8a8580; margin-right: 1rem; }
.bk-redesign .bk-pagination__btn {
	padding: 0.4rem 0.75rem; border: 1px solid rgba(0,0,0,0.12); border-radius: 6px;
	background: #fff; font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.8rem; color: #5a5550; cursor: pointer; transition: all 0.15s; text-decoration: none;
}
.bk-redesign .bk-pagination__btn:hover { border-color: #862736; color: #862736; text-decoration: none; }
.bk-redesign .bk-pagination__btn.active { background: #862736; border-color: #862736; color: #fff; }
.bk-redesign .bk-pagination__btn.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }

/* ── MESSAGES EMPTY STATE ─────────────────────────── */
.bk-redesign .bk-empty {
	text-align: center; padding: 3rem 2rem; background: #fff;
	border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
}
.bk-redesign .bk-empty__icon {
	width: 56px; height: 56px; margin: 0 auto 1rem;
	background: #faf8f5; border-radius: 14px;
	display: flex; align-items: center; justify-content: center;
}
.bk-redesign .bk-empty__icon svg { width: 28px; height: 28px; }
.bk-redesign .bk-empty__title { font-size: 1rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.25rem; }
.bk-redesign .bk-empty__desc { font-size: 0.85rem; color: #8a8580; }

/* ── PRIVATE MESSAGES LIST ────────────────────────── */
.bk-redesign .bk-msg-list {
	background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; overflow: hidden;
}
.bk-redesign .bk-msg-item {
	padding: 1rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.08);
	font-size: 0.875rem; color: #5a5550; line-height: 1.6;
}
.bk-redesign .bk-msg-item:last-child { border-bottom: none; }
.bk-redesign .bk-msg-item p { margin: 0; }

/* ── RESPONSIVE ───────────────────────────────────── */

/* Tablet — sidebar gone, full width */
@media (max-width: 1026px) {
	.bk-redesign .bk-page { padding: 1.5rem 1rem; padding-top: 3.5rem; }
	.bk-redesign .bk-header h1 { font-size: 1.35rem; }
}

/* Small tablet / large phone */
@media (max-width: 768px) {
	.bk-redesign .bk-page { padding: 1.25rem 0.75rem; padding-top: 3.5rem; }
	.bk-redesign .bk-header h1 { font-size: 1.25rem; }
	.bk-redesign .bk-search { flex-direction: column; }
	.bk-redesign .bk-search__filter { width: 100%; }
	.bk-redesign .bk-email-item { gap: 0.75rem; padding: 0.85rem 1rem; }
	.bk-redesign .bk-email-date { font-size: 0.7rem; }
	.bk-redesign .bk-detail__header { padding: 1.25rem; }
	.bk-redesign .bk-detail__body { padding: 1.25rem; }
	.bk-redesign .bk-pagination { gap: 0.25rem; }
	.bk-redesign .bk-pagination__info { width: 100%; text-align: center; margin-right: 0; margin-bottom: 0.5rem; }
}

/* Phone */
@media (max-width: 576px) {
	.bk-redesign .bk-page { padding: 1rem 0.65rem; padding-top: 3.25rem; }
	.bk-redesign .bk-header { margin-bottom: 1rem; }
	.bk-redesign .bk-header h1 { font-size: 1.15rem; }
	.bk-redesign .bk-header p { font-size: 0.8rem; }
	.bk-redesign .bk-tabs { margin-bottom: 1rem; }
	.bk-redesign .bk-tab { padding: 0.6rem 0.9rem; font-size: 0.8rem; }
	.bk-redesign .bk-email-item {
		flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem 0.85rem;
	}
	.bk-redesign .bk-icon { width: 32px; height: 32px; }
	.bk-redesign .bk-icon svg { width: 15px; height: 15px; }
	.bk-redesign .bk-email-subject { font-size: 0.82rem; white-space: normal; }
	.bk-redesign .bk-email-preview { font-size: 0.73rem; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
	.bk-redesign .bk-tag { order: 0; }
	.bk-redesign .bk-email-date { font-size: 0.68rem; }
	.bk-redesign .bk-detail__subject { font-size: 1rem; }
	.bk-redesign .bk-detail__meta { flex-direction: column; align-items: flex-start; gap: 0.2rem; }
	.bk-redesign .bk-detail__meta span:contains('·') { display: none; }
	.bk-redesign .bk-detail__header { padding: 1rem; }
	.bk-redesign .bk-detail__body { padding: 1rem; font-size: 0.85rem; line-height: 1.7; }
	.bk-redesign .bk-empty { padding: 2rem 1.25rem; }
	.bk-redesign .bk-empty__title { font-size: 0.9rem; }
	.bk-redesign .bk-empty__desc { font-size: 0.8rem; }
	.bk-redesign .bk-pagination__btn { padding: 0.35rem 0.6rem; font-size: 0.75rem; }
	.bk-redesign .bk-email-list { border-radius: 10px; }
	.bk-redesign .bk-detail { border-radius: 10px; }
	.bk-redesign .bk-empty { border-radius: 10px; }
	.bk-redesign .bk-msg-list { border-radius: 10px; }
}
</style>

{{-- ── SIDEBAR TOGGLE (egen knapp, unngår duplikat-ID) ─────── --}}
<button class="bk-sidebar-toggle" id="bkSidebarToggle" type="button" aria-label="Vis/skjul meny">
	<svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<div class="bk-page">

	{{-- ── PAGE HEADER ─────────────────────────────────── --}}
	<div class="bk-header">
		<h1>Beskjeder</h1>
		<p>Meldinger og e-poster fra Forfatterskolen.</p>
	</div>

	{{-- ── TABS ────────────────────────────────────────── --}}
	<div class="bk-tabs">
		<button class="bk-tab {{ $tab === 'meldinger' ? 'active' : '' }}" onclick="bkSwitchTab('meldinger', this)">
			Meldinger <span class="bk-tab__count">{{ $messages->total() }}</span>
		</button>
		<button class="bk-tab {{ $tab === 'epost' ? 'active' : '' }}" onclick="bkSwitchTab('epost', this)">
			E-posthistorikk <span class="bk-tab__count">{{ $totalEmails }}</span>
		</button>
	</div>

	{{-- ═══════════ TAB 1: MELDINGER ═══════════ --}}
	<div class="bk-panel {{ $tab === 'meldinger' ? 'active' : '' }}" id="panel-meldinger">
		@if($messages->count() > 0)
			<div class="bk-msg-list">
				@foreach($messages as $message)
					<div class="bk-msg-item">
						{!! $message->message !!}
					</div>
				@endforeach
			</div>

			@if($messages->lastPage() > 1)
				<div class="bk-pagination">
					<span class="bk-pagination__info">
						Viser {{ $messages->firstItem() }}–{{ $messages->lastItem() }} av {{ $messages->total() }} meldinger
					</span>
					@if($messages->onFirstPage())
						<span class="bk-pagination__btn disabled">‹ Forrige</span>
					@else
						<a href="{{ $messages->appends(['tab' => 'meldinger'])->previousPageUrl() }}" class="bk-pagination__btn">‹ Forrige</a>
					@endif

					@foreach($messages->getUrlRange(1, $messages->lastPage()) as $pageNum => $url)
						<a href="{{ $url }}&tab=meldinger" class="bk-pagination__btn {{ $pageNum == $messages->currentPage() ? 'active' : '' }}">{{ $pageNum }}</a>
					@endforeach

					@if($messages->hasMorePages())
						<a href="{{ $messages->appends(['tab' => 'meldinger'])->nextPageUrl() }}" class="bk-pagination__btn">Neste ›</a>
					@else
						<span class="bk-pagination__btn disabled">Neste ›</span>
					@endif
				</div>
			@endif
		@else
			<div class="bk-empty">
				<div class="bk-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
					</svg>
				</div>
				<div class="bk-empty__title">Ingen nye meldinger</div>
				<div class="bk-empty__desc">Når du mottar beskjeder fra Forfatterskolen vises de her.</div>
			</div>
		@endif
	</div>

	{{-- ═══════════ TAB 2: E-POSTHISTORIKK ═══════════ --}}
	<div class="bk-panel {{ $tab === 'epost' ? 'active' : '' }}" id="panel-epost">

		@if($selectedEmail)
			{{-- ── EMAIL DETAIL VIEW ─────────────────────── --}}
			@php
				$detailSubject = $selectedEmail->subject ?? 'Uten emne';
				$detailFrom = $selectedEmail->from_name ?? 'Forfatterskolen';
				$detailDate = \Carbon\Carbon::parse($selectedEmail->created_at);
				$detailDateStr = $detailDate->format('j') . '. ' . $detailDate->translatedFormat('F') . ' ' . $detailDate->format('Y') . ', kl. ' . $detailDate->format('H:i');

				// Categorize
				$subjectLower = mb_strtolower($detailSubject);
				if (str_contains($subjectLower, 'mentormøte')) {
					$detailType = 'mentor';
					$detailLabel = 'Mentormøte';
				} elseif (str_contains($subjectLower, 'abonnement') || str_contains($subjectLower, 'passord')
					|| str_contains($subjectLower, 'betaling') || str_contains($subjectLower, 'faktura')
					|| str_contains($subjectLower, 'hjelp,')) {
					$detailType = 'system';
					$detailLabel = 'System';
				} else {
					$detailType = 'course';
					$detailLabel = 'Kursinfo';
				}
			@endphp

			<div class="bk-detail">
				<div class="bk-detail__header">
					<a href="{{ route('learner.private-message', ['tab' => 'epost', 'search' => $search, 'type' => $type]) }}" class="bk-detail__back">
						<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
						Tilbake til e-posthistorikk
					</a>
					<h2 class="bk-detail__subject">{{ $detailSubject }}</h2>
					<div class="bk-detail__meta">
						<span>Fra: {{ $detailFrom }}</span>
						<span>·</span>
						<span>{{ $detailDateStr }}</span>
						<span>·</span>
						<span class="bk-tag bk-tag--{{ $detailType }}">{{ $detailLabel }}</span>
					</div>
				</div>
				<div class="bk-detail__body">
					{!! $selectedEmail->message !!}
				</div>
			</div>
		@else
			{{-- ── SEARCH + FILTER ───────────────────────── --}}
			<form method="GET" action="{{ route('learner.private-message') }}" class="bk-search">
				<input type="hidden" name="tab" value="epost">
				<input type="text" name="search" class="bk-search__input" placeholder="Søk i emne..." value="{{ $search }}">
				<select name="type" class="bk-search__filter" onchange="this.form.submit()">
					<option value="" {{ $type === '' ? 'selected' : '' }}>Alle typer</option>
					<option value="mentor" {{ $type === 'mentor' ? 'selected' : '' }}>Mentormøter</option>
					<option value="kurs" {{ $type === 'kurs' ? 'selected' : '' }}>Kursinfo</option>
					<option value="system" {{ $type === 'system' ? 'selected' : '' }}>System</option>
				</select>
			</form>

			@if($emailLogs->count() > 0)
				{{-- ── EMAIL LIST ─────────────────────────── --}}
				<div class="bk-email-list">
					@foreach($emailLogs as $email)
						@php
							$emailSubject = $email->subject ?? 'Uten emne';
							$subjectLower = mb_strtolower($emailSubject);
							$emailDate = \Carbon\Carbon::parse($email->created_at);
							$emailDateStr = $emailDate->format('j') . '. ' . $emailDate->translatedFormat('F') . ' ' . $emailDate->format('Y');

							// Strip HTML, decode entities, and truncate for preview
							$previewText = html_entity_decode(strip_tags($email->message ?? ''), ENT_QUOTES, 'UTF-8');
							$previewText = preg_replace('/\s+/', ' ', trim($previewText));
							$previewText = \Illuminate\Support\Str::limit($previewText, 90);

							// Categorize
							if (str_contains($subjectLower, 'mentormøte')) {
								$emailType = 'mentor';
								$emailLabel = 'Mentormøte';
							} elseif (str_contains($subjectLower, 'abonnement') || str_contains($subjectLower, 'passord')
								|| str_contains($subjectLower, 'betaling') || str_contains($subjectLower, 'faktura')
								|| str_contains($subjectLower, 'hjelp,')) {
								$emailType = 'system';
								$emailLabel = 'System';
							} else {
								$emailType = 'course';
								$emailLabel = 'Kursinfo';
							}
						@endphp

						<a href="{{ route('learner.private-message', ['tab' => 'epost', 'email_id' => $email->id, 'search' => $search, 'type' => $type]) }}" class="bk-email-item">
							{{-- Type icon --}}
							<div class="bk-icon bk-icon--{{ $emailType }}">
								@if($emailType === 'mentor')
									<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
								@elseif($emailType === 'course')
									<svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
								@else
									<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
								@endif
							</div>

							{{-- Content --}}
							<div class="bk-email-content">
								<div class="bk-email-subject">{{ $emailSubject }}</div>
								<div class="bk-email-preview">{{ $previewText }}</div>
							</div>

							{{-- Tag --}}
							<span class="bk-tag bk-tag--{{ $emailType }}">{{ $emailLabel }}</span>

							{{-- Date --}}
							<span class="bk-email-date">{{ $emailDateStr }}</span>
						</a>
					@endforeach
				</div>

				{{-- ── PAGINATION ─────────────────────────── --}}
				@if($emailLogs->lastPage() > 1)
					<div class="bk-pagination">
						<span class="bk-pagination__info">
							Viser {{ $emailLogs->firstItem() }}–{{ $emailLogs->lastItem() }} av {{ $emailLogs->total() }} e-poster
						</span>

						@if($emailLogs->onFirstPage())
							<span class="bk-pagination__btn disabled">‹ Forrige</span>
						@else
							<a href="{{ $emailLogs->previousPageUrl() }}" class="bk-pagination__btn">‹ Forrige</a>
						@endif

						@foreach($emailLogs->getUrlRange(1, $emailLogs->lastPage()) as $pageNum => $url)
							<a href="{{ $url }}" class="bk-pagination__btn {{ $pageNum == $emailLogs->currentPage() ? 'active' : '' }}">{{ $pageNum }}</a>
						@endforeach

						@if($emailLogs->hasMorePages())
							<a href="{{ $emailLogs->nextPageUrl() }}" class="bk-pagination__btn">Neste ›</a>
						@else
							<span class="bk-pagination__btn disabled">Neste ›</span>
						@endif
					</div>
				@endif
			@else
				<div class="bk-empty">
					<div class="bk-empty__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round">
							<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
							<polyline points="22,6 12,13 2,6"/>
						</svg>
					</div>
					@if($search || $type)
						<div class="bk-empty__title">Ingen e-poster funnet</div>
						<div class="bk-empty__desc">Prøv å endre søket eller filteret.</div>
					@else
						<div class="bk-empty__title">Ingen e-poster ennå</div>
						<div class="bk-empty__desc">Når du mottar e-poster fra Forfatterskolen vises de her.</div>
					@endif
				</div>
			@endif
		@endif
	</div>

</div> {{-- end bk-page --}}

</div> {{-- end bk-redesign --}}

{{-- ── JAVASCRIPT ─────────────────────────────────────────────── --}}
<script>
function bkSwitchTab(tabId, btn) {
	document.querySelectorAll('.bk-redesign .bk-tab').forEach(function(t) { t.classList.remove('active'); });
	document.querySelectorAll('.bk-redesign .bk-panel').forEach(function(p) { p.classList.remove('active'); });
	btn.classList.add('active');
	document.getElementById('panel-' + tabId).classList.add('active');
}

// Submit search on Enter
document.addEventListener('DOMContentLoaded', function() {
	var searchInput = document.querySelector('.bk-search__input');
	if (searchInput) {
		searchInput.addEventListener('keydown', function(e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				this.closest('form').submit();
			}
		});
	}
});

/* ── Sidebar toggle + auto-collapse ────────────────── */
(function() {
	var sidebar = document.getElementById('sidebar');
	var mainContainer = document.getElementById('main-container');
	var toggleBtn = document.getElementById('bkSidebarToggle');

	// Egen klikk-handler — stopPropagation hindrer #main-content click fra å lukke med en gang
	if (toggleBtn) {
		toggleBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			if (!sidebar) return;
			sidebar.classList.toggle('sidebar-visible');
			if (mainContainer) mainContainer.classList.toggle('enlarge');
			document.body.classList.toggle('sidebar-open');
		});
	}

	// Auto-collapse: kjør ETTER course-portal sin checkWindowWidth()
	setTimeout(function() {
		if (!sidebar) return;
		var vpWidth = document.documentElement.clientWidth;
		if (vpWidth < 1027 && sidebar.classList.contains('sidebar-visible')) {
			sidebar.classList.remove('sidebar-visible');
			if (mainContainer) mainContainer.classList.remove('enlarge');
			document.body.classList.remove('sidebar-open');
		}
	}, 150);

	// Lytt på resize og kollaps når vinduet er smalt
	window.addEventListener('resize', function() {
		if (!sidebar) return;
		var vpWidth = document.documentElement.clientWidth;
		if (vpWidth < 1027 && sidebar.classList.contains('sidebar-visible')) {
			sidebar.classList.remove('sidebar-visible');
			if (mainContainer) mainContainer.classList.remove('enlarge');
			document.body.classList.remove('sidebar-open');
		}
	});
})();
</script>

@stop