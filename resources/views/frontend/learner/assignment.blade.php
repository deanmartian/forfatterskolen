@extends('frontend.layouts.course-portal')

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
<title>Oppgaver &rsaquo; Forfatterskolen</title>
@stop

@section('content')

<div class="op-redesign">

{{-- ── SCOPED CSS ───────────────────────────────────────────────── --}}
<style>
/* ── OP-REDESIGN SCOPE ──────────────────────────────────────── */
.op-redesign { font-family: 'Source Sans 3', -apple-system, sans-serif; -webkit-font-smoothing: antialiased; }
.op-redesign #topbar { display: none !important; }

.op-redesign .op-page { max-width: 880px; margin: 0 auto; padding: 2rem 1rem; padding-top: 3.5rem; }

/* ── PAGE HEADER ──────────────────────────────────── */
.op-redesign .op-header { margin-bottom: 1.5rem; }
.op-redesign .op-header h1 { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
.op-redesign .op-header p { font-size: 0.875rem; color: #5a5550; margin: 0; }

/* ── SIDEBAR TOGGLE — vinrød, stor og tydelig ──────────────────── */
.op-redesign .op-sidebar-toggle {
	display: none; position: fixed; top: 16px; left: 16px; z-index: 1050;
	width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
	background: #862736; align-items: center; justify-content: center; cursor: pointer;
	box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
	padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.op-redesign .op-sidebar-toggle:hover { background: #9c2e40; transform: scale(1.05); }
.op-redesign .op-sidebar-toggle:active { transform: scale(0.96); }
.op-redesign .op-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
@media (max-width: 1026px) { .op-redesign .op-sidebar-toggle { display: flex !important; } }

/* ── TABS ─────────────────────────────────────────── */
.op-redesign .op-tabs {
	display: flex; gap: 0; border-bottom: 2px solid rgba(0,0,0,0.08);
	margin-bottom: 1.75rem; overflow-x: auto; -webkit-overflow-scrolling: touch;
}
.op-redesign .op-tabs::-webkit-scrollbar { display: none; }

.op-redesign .op-tab {
	padding: 0.7rem 1.15rem; border: none; background: transparent;
	font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.835rem;
	font-weight: 500; color: #8a8580; cursor: pointer; white-space: nowrap;
	position: relative; transition: color 0.15s;
}
.op-redesign .op-tab:hover { color: #1a1a1a; }
.op-redesign .op-tab.active { color: #862736; font-weight: 600; }
.op-redesign .op-tab.active::after {
	content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
	height: 2px; background: #862736; border-radius: 1px 1px 0 0;
}

.op-redesign .op-tab__count {
	display: inline-flex; align-items: center; justify-content: center;
	min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px;
	font-size: 0.65rem; font-weight: 600; margin-left: 0.35rem;
}
.op-redesign .op-tab.active .op-tab__count { background: #f4e8ea; color: #862736; }
.op-redesign .op-tab:not(.active) .op-tab__count { background: rgba(0,0,0,0.06); color: #8a8580; }

/* ── TAB PANELS ───────────────────────────────────── */
.op-redesign .op-panel { display: none; }
.op-redesign .op-panel.active { display: block; }

/* ── TASK CARD (Kommende oppgave) ─────────────────── */
.op-redesign .op-cards { display: flex; flex-direction: column; gap: 0.85rem; }

.op-redesign .op-card {
	background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
	padding: 1.25rem 1.5rem; transition: border-color 0.15s, box-shadow 0.15s;
}
.op-redesign .op-card:hover { border-color: rgba(0,0,0,0.12); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }

.op-redesign .op-card__top { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 0.85rem; }

.op-redesign .op-card__icon {
	width: 40px; height: 40px; border-radius: 10px; display: flex;
	align-items: center; justify-content: center; flex-shrink: 0;
}
.op-redesign .op-card__icon--assignment { background: #f4e8ea; }
.op-redesign .op-card__icon--webinar { background: #e3f2fd; }
.op-redesign .op-card__icon svg { width: 20px; height: 20px; }

.op-redesign .op-card__info { flex: 1; }
.op-redesign .op-card__title { font-size: 1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.15rem; }
.op-redesign .op-card__course { font-size: 0.78rem; color: #8a8580; margin: 0; }

.op-redesign .op-card__badges { display: flex; gap: 0.35rem; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }

.op-redesign .op-badge {
	font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.55rem;
	border-radius: 4px; white-space: nowrap; display: inline-block;
}
.op-redesign .op-badge--words { background: #f4e8ea; color: #5c1a25; }
.op-redesign .op-badge--deadline-soon { background: #fff3e0; color: #e65100; }
.op-redesign .op-badge--deadline-today { background: #fce8e8; color: #c62828; }
.op-redesign .op-badge--deadline-future { background: rgba(0,0,0,0.04); color: #8a8580; }
.op-redesign .op-badge--submitted { background: #e8f5e9; color: #2e7d32; }
.op-redesign .op-badge--waiting { background: #fff3e0; color: #e65100; }
.op-redesign .op-badge--ready { background: #e3f2fd; color: #1565c0; }

.op-redesign .op-card__desc { font-size: 0.85rem; color: #5a5550; line-height: 1.6; margin-bottom: 1rem; }

/* ── Card footer ──────────────────────────── */
.op-redesign .op-card__footer {
	display: flex; align-items: center; justify-content: space-between;
	padding-top: 0.85rem; border-top: 1px solid rgba(0,0,0,0.08); gap: 1rem;
}
.op-redesign .op-card__deadline { display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; color: #8a8580; }
.op-redesign .op-card__deadline svg { width: 14px; height: 14px; stroke: #8a8580; }
.op-redesign .op-card__deadline--urgent { color: #e65100; font-weight: 600; }
.op-redesign .op-card__deadline--urgent svg { stroke: #e65100; }
.op-redesign .op-card__deadline--today { color: #c62828; font-weight: 600; }
.op-redesign .op-card__deadline--today svg { stroke: #c62828; }

.op-redesign .op-card__actions { display: flex; gap: 0.5rem; align-items: center; }

/* ── Manuscript display in card ──────────── */
.op-redesign .op-card__manus {
	display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
	margin-bottom: 0.75rem; padding: 0.6rem 0.85rem;
	background: #faf8f5; border-radius: 8px; font-size: 0.8rem;
}
.op-redesign .op-card__manus a { color: #1565c0; text-decoration: none; font-weight: 500; }
.op-redesign .op-card__manus a:hover { text-decoration: underline; }
.op-redesign .op-card__manus-actions { margin-left: auto; display: flex; gap: 0.35rem; }
.op-redesign .op-card__manus-actions button,
.op-redesign .op-card__manus-actions a {
	width: 28px; height: 28px; border-radius: 6px; border: 1px solid rgba(0,0,0,0.12);
	background: #fff; display: inline-flex; align-items: center; justify-content: center;
	cursor: pointer; color: #5a5550; font-size: 0.7rem; text-decoration: none;
	transition: all 0.15s; padding: 0;
}
.op-redesign .op-card__manus-actions button:hover,
.op-redesign .op-card__manus-actions a:hover { border-color: #862736; color: #862736; }

/* ── Buttons ─────────────────────────────── */
.op-redesign .op-btn {
	display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.5rem 1rem;
	border-radius: 6px; font-family: 'Source Sans 3', -apple-system, sans-serif;
	font-size: 0.8rem; font-weight: 600; text-decoration: none; cursor: pointer;
	border: none; transition: all 0.15s;
}
.op-redesign .op-btn--primary { background: #862736; color: #fff; }
.op-redesign .op-btn--primary:hover { background: #9c2e40; color: #fff; text-decoration: none; }
.op-redesign .op-btn--primary:disabled,
.op-redesign .op-btn--primary.disabled { background: #ccc; color: #888; cursor: not-allowed; pointer-events: none; }
.op-redesign .op-btn--secondary { background: transparent; color: #5a5550; border: 1px solid rgba(0,0,0,0.12); }
.op-redesign .op-btn--secondary:hover { border-color: #862736; color: #862736; text-decoration: none; }

/* ── SUBMITTED LIST (Venter / Innsendt) ───────────── */
.op-redesign .op-sub-list { display: flex; flex-direction: column; gap: 0.6rem; }

.op-redesign .op-sub-item {
	background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;
	padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem;
	transition: border-color 0.15s;
}
.op-redesign .op-sub-item:hover { border-color: rgba(0,0,0,0.12); }

.op-redesign .op-sub-item__dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.op-redesign .op-sub-item__dot--waiting { background: #e65100; }
.op-redesign .op-sub-item__dot--done { background: #2e7d32; }

.op-redesign .op-sub-item__info { flex: 1; }
.op-redesign .op-sub-item__name { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.1rem; }
.op-redesign .op-sub-item__meta { font-size: 0.75rem; color: #8a8580; }

.op-redesign .op-sub-item__file {
	display: flex; align-items: center; gap: 0.35rem; font-size: 0.75rem;
	color: #1565c0; text-decoration: none; font-weight: 500; flex-shrink: 0;
}
.op-redesign .op-sub-item__file svg { width: 14px; height: 14px; stroke: #1565c0; }
.op-redesign .op-sub-item__file:hover { text-decoration: underline; }

/* ── FEEDBACK TABLE ──────────────────────────────── */
.op-redesign .op-fb-table {
	width: 100%; background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; overflow: hidden;
}
.op-redesign .op-fb-table table { width: 100%; border-collapse: collapse; }
.op-redesign .op-fb-table thead th {
	font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;
	color: #8a8580; padding: 0.85rem 1.25rem; text-align: left; background: #faf8f5;
	border-bottom: 1px solid rgba(0,0,0,0.08);
}
.op-redesign .op-fb-table tbody td {
	font-size: 0.85rem; color: #1a1a1a; padding: 0.85rem 1.25rem;
	border-bottom: 1px solid rgba(0,0,0,0.08); vertical-align: middle;
}
.op-redesign .op-fb-table tbody tr:last-child td { border-bottom: none; }
.op-redesign .op-fb-table tbody tr:hover { background: rgba(0,0,0,0.015); }

.op-redesign .op-fb-table__course { font-size: 0.75rem; color: #8a8580; display: block; margin-top: 0.1rem; }

.op-redesign .op-fb-table__file {
	display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.8rem;
	color: #1565c0; text-decoration: none; font-weight: 500;
}
.op-redesign .op-fb-table__file svg { width: 14px; height: 14px; stroke: #1565c0; }
.op-redesign .op-fb-table__file:hover { text-decoration: underline; }

.op-redesign .op-fb-table__dl {
	display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.85rem;
	border: 1px solid #862736; border-radius: 5px; font-size: 0.78rem; font-weight: 600;
	color: #862736; text-decoration: none; transition: all 0.15s;
}
.op-redesign .op-fb-table__dl:hover { background: #862736; color: #fff; text-decoration: none; }
.op-redesign .op-fb-table__dl svg { width: 14px; height: 14px; stroke: currentColor; }

/* ── GROUP CARD ──────────────────────────────────── */
.op-redesign .op-group { background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px; overflow: hidden; margin-bottom: 1.25rem; }
.op-redesign .op-group__header {
	display: flex; align-items: center; justify-content: space-between;
	padding: 1rem 1.5rem; background: #faf8f5; border-bottom: 1px solid rgba(0,0,0,0.08);
}
.op-redesign .op-group__title { font-size: 1rem; font-weight: 700; color: #1a1a1a; }
.op-redesign .op-group__meta { font-size: 0.78rem; color: #8a8580; }

.op-redesign .op-group__members { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1px; background: rgba(0,0,0,0.08); }
.op-redesign .op-group-member { background: #fff; padding: 1.25rem; }
.op-redesign .op-group-member--self { background: rgba(134,39,54,0.03); border-left: 3px solid #862736; }
.op-redesign .op-group-member__name { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.35rem; }
.op-redesign .op-group-member__file { display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; color: #5a5550; margin-bottom: 0.15rem; }
.op-redesign .op-group-member__file svg { width: 14px; height: 14px; stroke: #8a8580; }
.op-redesign .op-group-member__detail { font-size: 0.72rem; color: #8a8580; }
.op-redesign .op-group-member__status { display: inline-block; margin-top: 0.5rem; }
.op-redesign .op-group-member__actions { display: flex; gap: 0.4rem; margin-top: 0.6rem; }
.op-redesign .op-group-member__actions a {
	font-size: 0.75rem; font-weight: 600; color: #862736; text-decoration: none;
	padding: 0.3rem 0.65rem; border: 1px solid #862736; border-radius: 4px; transition: all 0.15s;
}
.op-redesign .op-group-member__actions a:hover { background: #862736; color: #fff; text-decoration: none; }

/* Group feedback section */
.op-redesign .op-group-fb { padding: 1rem 1.5rem; border-top: 1px solid rgba(0,0,0,0.08); }
.op-redesign .op-group-fb__title { font-size: 0.8rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.6rem; }
.op-redesign .op-group-fb__item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; }
.op-redesign .op-group-fb__item + .op-group-fb__item { border-top: 1px solid rgba(0,0,0,0.08); }
.op-redesign .op-group-fb__file { font-size: 0.8rem; color: #1a1a1a; flex: 1; }
.op-redesign .op-group-fb__by { font-size: 0.72rem; color: #8a8580; }

/* ── WEBINAR TAB ─────────────────────────────────── */
.op-redesign .op-webinar-list { display: flex; flex-direction: column; gap: 0.6rem; }

/* ── EMPTY STATE ──────────────────────────────────── */
.op-redesign .op-empty {
	text-align: center; padding: 3rem 2rem; background: #fff;
	border: 1px solid rgba(0,0,0,0.08); border-radius: 14px;
}
.op-redesign .op-empty__icon {
	width: 56px; height: 56px; margin: 0 auto 1rem; background: #faf8f5;
	border-radius: 14px; display: flex; align-items: center; justify-content: center;
}
.op-redesign .op-empty__icon svg { width: 28px; height: 28px; }
.op-redesign .op-empty__title { font-size: 1rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.25rem; }
.op-redesign .op-empty__desc { font-size: 0.85rem; color: #8a8580; }

/* ── Group list (sidebar-style) ──────────────────── */
.op-redesign .op-group-list { display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.5rem; }
.op-redesign .op-group-list__item {
	background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 10px;
	padding: 0.85rem 1.15rem; cursor: pointer; transition: all 0.15s;
}
.op-redesign .op-group-list__item:hover,
.op-redesign .op-group-list__item.active { border-color: #862736; background: rgba(134,39,54,0.03); }
.op-redesign .op-group-list__item h3 { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.15rem; }
.op-redesign .op-group-list__item p { font-size: 0.75rem; color: #8a8580; margin: 0; }

/* ── Loading spinner ──────────────────────────────── */
.op-redesign .op-loading { text-align: center; padding: 2rem; color: #8a8580; }

/* ── SVG icons inline ─────────────────────────────── */
.op-redesign .op-icon-doc { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.5; stroke-linecap: round; }
.op-redesign .op-icon-clock { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.5; stroke-linecap: round; }
.op-redesign .op-icon-dl { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 1.5; stroke-linecap: round; }

/* ── RESPONSIVE ───────────────────────────────────── */

/* Tablet — sidebar gone */
@media (max-width: 1026px) {
	.op-redesign .op-page { padding: 1.5rem 1rem; padding-top: 3.5rem; }
	.op-redesign .op-header h1 { font-size: 1.35rem; }
	/* Tabs: horisontal scroll på trange skjermer */
	.op-redesign .op-tabs { overflow-x: auto; scrollbar-width: none; }
	.op-redesign .op-tab { white-space: nowrap; flex-shrink: 0; padding: 0.55rem 0.85rem; font-size: 0.8rem; }
}

/* Small tablet / large phone */
@media (max-width: 768px) {
	.op-redesign .op-page { padding: 1.25rem 0.75rem; padding-top: 3.5rem; }
	.op-redesign .op-header h1 { font-size: 1.25rem; }
	.op-redesign .op-tab { padding: 0.5rem 0.65rem; font-size: 0.75rem; }
	.op-redesign .op-card__top { flex-direction: column; gap: 0.5rem; }
	.op-redesign .op-card__badges { justify-content: flex-start; }
	.op-redesign .op-card__footer { flex-direction: column; align-items: flex-start; }
	.op-redesign .op-fb-table { overflow-x: auto; }
	.op-redesign .op-group__members { grid-template-columns: 1fr; }
	.op-redesign .op-sub-item { flex-wrap: wrap; }
	.op-redesign .op-card__manus { flex-direction: column; align-items: flex-start; }
	.op-redesign .op-card__manus-actions { margin-left: 0; }
}

/* Phone */
@media (max-width: 576px) {
	.op-redesign .op-page { padding: 1rem 0.65rem; padding-top: 3.25rem; }
	.op-redesign .op-header { margin-bottom: 1rem; }
	.op-redesign .op-header h1 { font-size: 1.15rem; }
	.op-redesign .op-header p { font-size: 0.8rem; }
	.op-redesign .op-tabs { margin-bottom: 1rem; gap: 0; }
	.op-redesign .op-tab { padding: 0.45rem 0.5rem; font-size: 0.7rem; }
	.op-redesign .op-tab__count { font-size: 0.55rem; min-width: 14px; height: 14px; padding: 0 3px; }
	.op-redesign .op-card { padding: 1rem; border-radius: 10px; }
	.op-redesign .op-card__title { font-size: 0.95rem; }
	.op-redesign .op-card__desc { font-size: 0.8rem; }
	.op-redesign .op-badge { font-size: 0.6rem; padding: 0.15rem 0.4rem; }
	.op-redesign .op-card__course { font-size: 0.73rem; }
	.op-redesign .op-card__footer .btn { font-size: 0.78rem; padding: 0.45rem 0.9rem; }
	.op-redesign .op-fb-table th, .op-redesign .op-fb-table td { font-size: 0.78rem; padding: 0.6rem 0.5rem; }
	.op-redesign .op-sub-item { padding: 0.65rem 0.85rem; }
	.op-redesign .op-group-item { padding: 0.85rem; }
	.op-redesign .op-empty { padding: 2rem 1.25rem; }
	.op-redesign .op-card + .op-card { margin-top: 0.75rem; }
}
</style>

{{-- ── SIDEBAR TOGGLE (egen knapp, unngår duplikat-ID) ─────────── --}}
<button class="op-sidebar-toggle" id="opSidebarToggle" type="button" aria-label="Vis/skjul meny">
	<svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<div class="op-page">

	{{-- ── PAGE HEADER ──────────────────────────────────────────── --}}
	<div class="op-header">
		<h1>Oppgaver</h1>
		<p>Innleveringer, tilbakemeldinger og gruppearbeid.</p>
	</div>

	{{-- ── Prep counts ──────────────────────────────────────────── --}}
	@php
		$noGroupWithFeedback = \App\AssignmentFeedbackNoGroup::where('learner_id', Auth::user()->id)
			->orderBy('created_at', 'desc')->get();
		$feedbackCount = 0;
		foreach ($noGroupWithFeedback as $_fb) {
			if ($_fb->is_active && (!$_fb->availability || date('Y-m-d') >= $_fb->availability) && $_fb->manuscript && $_fb->manuscript->status) {
				$feedbackCount++;
			}
		}

		$tabMap = ['feedback-from-editor' => 'tilbakemelding', 'waiting' => 'innsendt', 'groups' => 'grupper'];
		$activeTab = $tabMap[request('tab')] ?? request('tab', 'kommende');
	@endphp

	{{-- ── TABS ──────────────────────────────────────────────────── --}}
	<div class="op-tabs">
		<button class="op-tab {{ $activeTab == 'kommende' ? 'active' : '' }}" onclick="opSwitchTab('kommende', this)">
			Kommende oppgave
			@if(count($assignments) + count($noWordLimitAssignments) > 0)
				<span class="op-tab__count">{{ count($assignments) + count($noWordLimitAssignments) }}</span>
			@endif
		</button>
		<button class="op-tab {{ $activeTab == 'innsendt' ? 'active' : '' }}" onclick="opSwitchTab('innsendt', this)">
			Venter på tilbakemelding
			@if(count($waitingForResponse) > 0)
				<span class="op-tab__count">{{ count($waitingForResponse) }}</span>
			@endif
		</button>
		<button class="op-tab {{ $activeTab == 'tilbakemelding' ? 'active' : '' }}" onclick="opSwitchTab('tilbakemelding', this)">
			Tilbakemelding fra redaktør
			@if($feedbackCount > 0)
				<span class="op-tab__count">{{ $feedbackCount }}</span>
			@endif
		</button>
		<button class="op-tab {{ $activeTab == 'grupper' ? 'active' : '' }}" onclick="opSwitchTab('grupper', this)">
			Grupper
		</button>
		<button class="op-tab {{ $activeTab == 'webinar' ? 'active' : '' }}" onclick="opSwitchTab('webinar', this)">
			Redigeringswebinarer
		</button>
	</div>

	{{-- ═══════════ TAB 1: KOMMENDE OPPGAVE ═══════════ --}}
	<div class="op-panel {{ $activeTab == 'kommende' ? 'active' : '' }}" id="op-panel-kommende">
		@if(count($assignments) == 0 && count($noWordLimitAssignments) == 0 && count($upcomingAssignments) == 0)
			<div class="op-empty">
				<div class="op-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
				</div>
				<div class="op-empty__title">Ingen kommende oppgaver</div>
				<div class="op-empty__desc">Du har ingen oppgaver å levere for øyeblikket.</div>
			</div>
		@else
			<div class="op-cards">
				{{-- ── Regular assignments (with word limit) ──────── --}}
				@foreach($assignments as $assignment)
					@if (is_null($assignment->parent) || $assignment->parent === 'users'
						|| ($assignment->linkedAssignment
						&& !$assignment->linkedAssignment->manuscripts()
						->where('user_id', Auth::user()->id)->first()))

						@php
							$manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
							$extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
							$submission_date_formatted = $assignmentSubmissionDates[$assignment->id] ?? $assignment->submission_date;
							$max_words = $assignmentMaxWords[$assignment->id] ?? $assignment->max_words;
							if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
								$userCoursesTaken = Auth::user()->coursesTaken()->get()->toArray();
								$allowed_packages = $assignment->allowed_package ? json_decode($assignment->allowed_package) : [];
								$courseStarted = '';
								foreach ($userCoursesTaken as $course) {
									if (in_array($course['package_id'], $allowed_packages)) {
										$courseStarted = $course['started_at'];
									}
								}
								if ($assignment['course_taken_end_date'] ?? false) {
									$courseStarted = $assignment['course_taken_end_date'];
									$submission_date_formatted = \Carbon\Carbon::parse($courseStarted)->addDays(0);
								} else {
									$submission_date_formatted = \Carbon\Carbon::parse($courseStarted)->addDays((int) $assignment->submission_date);
								}
							}

							$deadlineCarbon = \Carbon\Carbon::parse($submission_date_formatted);
							$rawDaysLeft = now()->floatDiffInDays($deadlineCarbon, false);
							$isExpired = $rawDaysLeft < 0;
							$daysLeft = max(0, (int) ceil($rawDaysLeft));
							$deadlineClass = '';
							if ($isExpired || $daysLeft == 0) $deadlineClass = 'op-card__deadline--today';
							elseif ($daysLeft <= 3) $deadlineClass = 'op-card__deadline--urgent';

							$deadlineBadgeClass = 'op-badge--deadline-future';
							$deadlineBadgeText = '';
							if ($isExpired) {
								$deadlineBadgeClass = 'op-badge--deadline-today';
								$deadlineBadgeText = 'Utløpt';
							} elseif ($daysLeft == 0) {
								$deadlineBadgeClass = 'op-badge--deadline-today';
								$deadlineBadgeText = 'Frist i dag';
							} elseif ($daysLeft == 1) {
								$deadlineBadgeClass = 'op-badge--deadline-soon';
								$deadlineBadgeText = '1 dag igjen';
							} elseif ($daysLeft <= 3) {
								$deadlineBadgeClass = 'op-badge--deadline-soon';
								$deadlineBadgeText = $daysLeft . ' dager igjen';
							} elseif ($daysLeft <= 30) {
								$deadlineBadgeText = $daysLeft . ' dager igjen';
							} else {
								$months = (int) round($daysLeft / 30);
								$deadlineBadgeText = $months . ' mnd igjen';
							}

							$isForEditor = $assignment->for_editor;
						@endphp

						<div class="op-card">
							<div class="op-card__top">
								<div class="op-card__icon {{ $isForEditor ? 'op-card__icon--webinar' : 'op-card__icon--assignment' }}">
									@if($isForEditor)
										<svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
									@else
										<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
									@endif
								</div>
								<div class="op-card__info">
									<h3 class="op-card__title">{{ $assignment->title }}</h3>
									@if($assignment->course)
										<p class="op-card__course">{{ $assignment->course->title }}</p>
									@endif
								</div>
								<div class="op-card__badges">
									@if($assignment->check_max_words)
										<span class="op-badge op-badge--words">Maks {{ $max_words }} ord</span>
									@endif
									@if($deadlineBadgeText)
										<span class="op-badge {{ $deadlineBadgeClass }}">{{ $deadlineBadgeText }}</span>
									@endif
								</div>
							</div>

							@if($assignment->description)
								<p class="op-card__desc">{{ $assignment->description }}</p>
							@endif

							{{-- Manuscript display --}}
							@if($manuscript)
								<div class="op-card__manus">
									<svg class="op-icon-doc" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
									@if(end($extension) == 'pdf' || end($extension) == 'odt')
										<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
									@elseif(end($extension) == 'docx' || end($extension) == 'doc')
										<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
									@endif
									@if(!$manuscript->locked)
										<span class="op-card__manus-actions">
											<button type="button" class="editManuscriptBtn"
												data-bs-toggle="modal" data-bs-target="#editManuscriptModal"
												data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}"
												title="Erstatt"><i class="fa fa-pencil-alt"></i></button>
											<button type="button" class="deleteManuscriptBtn"
												data-bs-toggle="modal" data-bs-target="#deleteManuscriptModal"
												data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}"
												title="Slett"><i class="fa fa-trash-alt"></i></button>
											<a href="{{ end($extension) == 'pdf' || end($extension) == 'odt' ? '/js/ViewerJS/#../..' . $manuscript->filename : 'https://view.officeapps.live.com/op/embed.aspx?src=' . url('') . $manuscript->filename }}"
												title="Forhåndsvis"><i class="fa fa-eye"></i></a>
											@if(end($extension) == 'docx')
												<a href="{{ route('learner.assignment.manuscript.pdf', $manuscript->id) }}"
													title="Last ned som PDF"><i class="fa fa-file-pdf"></i></a>
											@endif
										</span>
									@endif
								</div>
							@endif

							<div class="op-card__footer">
								<div class="op-card__deadline {{ $deadlineClass }}">
									<svg class="op-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
									Frist: {{ \App\Http\FrontendHelpers::formatDateTimeNor($submission_date_formatted) }}
								</div>
								<div class="op-card__actions">
									@if(!$manuscript && (is_null($assignment->parent) || $assignment->parent === 'users'
										|| ($assignment->linkedAssignment && !$assignment->linkedAssignment->manuscripts()
										->where('user_id', Auth::user()->id)->first())))
										@if($isForEditor)
											<button class="op-btn op-btn--primary submitEditorManuscriptBtn"
												data-bs-toggle="modal" data-bs-target="#submitEditorManuscriptModal"
												data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
												data-show-group-question="{{ $assignment->show_join_group_question }}"
												data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
												disabled style="pointer-events: none;"
												@if($isExpired && $assignment->parent !== 'users') disabled @endif>
												Last opp manus
											</button>
										@else
											<button class="op-btn op-btn--primary submitManuscriptBtn"
												data-bs-toggle="modal" data-bs-target="#submitManuscriptModal"
												data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
												data-show-group-question="{{ $assignment->show_join_group_question }}"
												data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
												@if($isExpired && $assignment->parent !== 'users') disabled @endif>
												Last opp manus
											</button>
										@endif
									@elseif($manuscript && $assignment->parent === 'users')
										<span class="op-badge op-badge--submitted">Innsendt</span>
									@endif
								</div>
							</div>
						</div>
					@endif
				@endforeach

				{{-- ── No word limit assignments (Redigeringswebinar etc) ── --}}
				@foreach($noWordLimitAssignments as $assignment)
					@php
						$manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
						$extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
						$submission_date_formatted = $assignment->submission_date;
						if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
							$userCoursesTaken = Auth::user()->coursesTaken()->get()->toArray();
							$allowed_packages = $assignment->allowed_package ? json_decode($assignment->allowed_package) : [];
							$courseStarted = '';
							foreach ($userCoursesTaken as $course) {
								if (in_array($course['package_id'], $allowed_packages)) {
									$courseStarted = $course['started_at'];
								}
							}
							$submission_date_formatted = \Carbon\Carbon::parse($courseStarted)->addDays((int) $assignment->submission_date);
						}
						$deadlineCarbon = \Carbon\Carbon::parse($submission_date_formatted);
						$rawDaysLeft = now()->floatDiffInDays($deadlineCarbon, false);
						$isExpired = $rawDaysLeft < 0;
						$daysLeft = max(0, (int) ceil($rawDaysLeft));
						$deadlineClass = '';
						if ($isExpired || $daysLeft == 0) $deadlineClass = 'op-card__deadline--today';
						elseif ($daysLeft <= 3) $deadlineClass = 'op-card__deadline--urgent';
						$isForEditor = $assignment->for_editor;
					@endphp

					<div class="op-card">
						<div class="op-card__top">
							<div class="op-card__icon {{ $isForEditor ? 'op-card__icon--webinar' : 'op-card__icon--assignment' }}">
								@if($isForEditor)
									<svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
								@else
									<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
								@endif
							</div>
							<div class="op-card__info">
								<h3 class="op-card__title">{{ $assignment->title }}</h3>
								@if($assignment->course)
									<p class="op-card__course">{{ $assignment->course->title }}</p>
								@endif
							</div>
							<div class="op-card__badges">
								<span class="op-badge op-badge--words">Ingen ordgrense</span>
							</div>
						</div>

						@if($assignment->description)
							<p class="op-card__desc">{{ $assignment->description }}</p>
						@endif

						@if($manuscript)
							<div class="op-card__manus">
								<svg class="op-icon-doc" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
								@if(end($extension) == 'pdf' || end($extension) == 'odt')
									<a href="/js/ViewerJS/#../..{{ $manuscript->filename }}">{{ basename($manuscript->filename) }}</a>
								@elseif(end($extension) == 'docx' || end($extension) == 'doc')
									<a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}{{$manuscript->filename}}">{{ basename($manuscript->filename) }}</a>
								@endif
								@if(!$manuscript->locked)
									<span class="op-card__manus-actions">
										<button type="button" class="editManuscriptBtn"
											data-bs-toggle="modal" data-bs-target="#editManuscriptModal"
											data-action="{{ route('learner.assignment.replace_manuscript', $manuscript->id) }}"
											title="Erstatt"><i class="fa fa-pencil-alt"></i></button>
										<button type="button" class="deleteManuscriptBtn"
											data-bs-toggle="modal" data-bs-target="#deleteManuscriptModal"
											data-action="{{ route('learner.assignment.delete_manuscript', $manuscript->id) }}"
											title="Slett"><i class="fa fa-trash-alt"></i></button>
										<a href="{{ end($extension) == 'pdf' || end($extension) == 'odt' ? '/js/ViewerJS/#../..' . $manuscript->filename : 'https://view.officeapps.live.com/op/embed.aspx?src=' . url('') . $manuscript->filename }}"
											title="Forhåndsvis"><i class="fa fa-eye"></i></a>
										@if(end($extension) == 'docx')
											<a href="{{ route('learner.assignment.manuscript.pdf', $manuscript->id) }}"
												title="Last ned som PDF"><i class="fa fa-file-pdf"></i></a>
										@endif
									</span>
								@endif
							</div>
						@endif

						<div class="op-card__footer">
							<div class="op-card__deadline {{ $deadlineClass }}">
								<svg class="op-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								Frist: {{ \App\Http\FrontendHelpers::formatDateTimeNor($submission_date_formatted) }}
							</div>
							<div class="op-card__actions">
								@if(!$manuscript && (is_null($assignment->parent) || $assignment->parent === 'users'
									|| ($assignment->linkedAssignment && !$assignment->linkedAssignment->manuscripts()
									->where('user_id', Auth::user()->id)->first())))
									@if($isForEditor)
										<button class="op-btn op-btn--primary submitEditorManuscriptBtn"
											data-bs-toggle="modal" data-bs-target="#submitEditorManuscriptModal"
											data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
											data-show-group-question="{{ $assignment->show_join_group_question }}"
											data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
											disabled style="pointer-events: none;"
											@if($isExpired && $assignment->parent !== 'users') disabled @endif>
											Last opp manus
										</button>
									@else
										<button class="op-btn op-btn--primary submitManuscriptBtn"
											data-bs-toggle="modal" data-bs-target="#submitManuscriptModal"
											data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
											data-show-group-question="{{ $assignment->show_join_group_question }}"
											data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
											@if($isExpired && $assignment->parent !== 'users') disabled @endif>
											Last opp manus
										</button>
									@endif
								@elseif($manuscript && $assignment->parent === 'users')
									<span class="op-badge op-badge--submitted">Innsendt</span>
								@endif
							</div>
						</div>
					</div>
				@endforeach

				{{-- ── Upcoming assignments ────────────────────── --}}
				@if(count($upcomingAssignments) > 0)
					@foreach($upcomingAssignments as $assignment)
						<div class="op-card" style="opacity: 0.6;">
							<div class="op-card__top">
								<div class="op-card__icon op-card__icon--assignment">
									<svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
								</div>
								<div class="op-card__info">
									<h3 class="op-card__title">{{ $assignment->title }}</h3>
									@if($assignment->course)
										<p class="op-card__course">{{ $assignment->course->title }}</p>
									@endif
								</div>
								<div class="op-card__badges">
									<span class="op-badge op-badge--deadline-future">Ikke åpen ennå</span>
								</div>
							</div>
							@if($assignment->description)
								<p class="op-card__desc">{{ $assignment->description }}</p>
							@endif
							<div class="op-card__footer">
								<div class="op-card__deadline">
									<svg class="op-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
									Tilgjengelig: {{ \App\Http\FrontendHelpers::formatDate($assignment->available_date) }}
									· Frist: {{ \App\Http\FrontendHelpers::formatDateTimeNor($assignment->submission_date) }}
								</div>
							</div>
						</div>
					@endforeach
				@endif
			</div>
		@endif
	</div>

	{{-- ═══════════ TAB 2: VENTER PÅ TILBAKEMELDING ═══════════ --}}
	<div class="op-panel {{ $activeTab == 'innsendt' ? 'active' : '' }}" id="op-panel-innsendt">
		@if(count($waitingForResponse) == 0 && count($expiredAssignments) == 0)
			<div class="op-empty">
				<div class="op-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				</div>
				<div class="op-empty__title">Ingen innsendte oppgaver</div>
				<div class="op-empty__desc">Du har ikke sendt inn noen oppgaver ennå.</div>
			</div>
		@else
			<div class="op-sub-list">
				{{-- Waiting for response items --}}
				@foreach($waitingForResponse as $assignment)
					@php
						$manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
						$extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
						$expected_finish = $manuscript->expected_finish ?? $assignment->expected_finish ?? null;
					@endphp
					<div class="op-sub-item">
						<span class="op-sub-item__dot op-sub-item__dot--waiting"></span>
						<div class="op-sub-item__info">
							<div class="op-sub-item__name">{{ $assignment->title }}</div>
							<div class="op-sub-item__meta">
								@if($assignment->course) {{ $assignment->course->title }} · @endif
								Innsendt{{ $manuscript && $manuscript->uploaded_at ? ' ' . \App\Http\FrontendHelpers::formatDate($manuscript->uploaded_at) : '' }}
								@if($expected_finish)
									· Forventet ferdig: {{ \App\Http\FrontendHelpers::formatDate($expected_finish) }}
								@endif
							</div>
						</div>
						@if($manuscript)
							<a href="{{ end($extension) == 'pdf' || end($extension) == 'odt' ? '/js/ViewerJS/#../..' . $manuscript->filename : 'https://view.officeapps.live.com/op/embed.aspx?src=' . url('') . $manuscript->filename }}" class="op-sub-item__file">
								<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
								Ditt manus
							</a>
							@if(end($extension) == 'docx')
								<a href="{{ route('learner.assignment.manuscript.pdf', $manuscript->id) }}" class="op-sub-item__file" title="Last ned som PDF">
									<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
									Last ned som PDF
								</a>
							@endif
						@endif
						<span class="op-badge op-badge--waiting">Venter</span>
					</div>
				@endforeach

				{{-- Expired/finished items (have manuscripts) --}}
				@foreach($expiredAssignments as $assignment)
					@php
						$manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
						$extension = $manuscript ? explode('.', basename($manuscript->filename)) : '';
					@endphp
					@if($manuscript)
						<div class="op-sub-item">
							<span class="op-sub-item__dot op-sub-item__dot--done"></span>
							<div class="op-sub-item__info">
								<div class="op-sub-item__name">{{ $assignment->title }}</div>
								<div class="op-sub-item__meta">
									@if($assignment->course) {{ $assignment->course->title }} · @endif
									Innsendt{{ $manuscript->uploaded_at ? ' ' . \App\Http\FrontendHelpers::formatDate($manuscript->uploaded_at) : '' }}
								</div>
							</div>
							<a href="{{ end($extension) == 'pdf' || end($extension) == 'odt' ? '/js/ViewerJS/#../..' . $manuscript->filename : 'https://view.officeapps.live.com/op/embed.aspx?src=' . url('') . $manuscript->filename }}" class="op-sub-item__file">
								<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
								Ditt manus
							</a>
							@if(end($extension) == 'docx')
								<a href="{{ route('learner.assignment.manuscript.pdf', $manuscript->id) }}" class="op-sub-item__file" title="Last ned som PDF">
									<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
									Last ned som PDF
								</a>
							@endif
							<span class="op-badge op-badge--submitted">Levert</span>
						</div>
					@endif
				@endforeach
			</div>
		@endif
	</div>

	{{-- ═══════════ TAB 3: TILBAKEMELDING FRA REDAKTØR ═══════════ --}}
	<div class="op-panel {{ $activeTab == 'tilbakemelding' ? 'active' : '' }}" id="op-panel-tilbakemelding">
		@if($feedbackCount == 0)
			<div class="op-empty">
				<div class="op-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
				</div>
				<div class="op-empty__title">Ingen tilbakemelding ennå</div>
				<div class="op-empty__desc">Tilbakemeldinger fra redaktøren vises her når de er klare.</div>
			</div>
		@else
			<div class="op-fb-table">
				<table>
					<thead>
						<tr>
							<th>Oppgave</th>
							<th>Ditt manus</th>
							<th>Dato</th>
							<th>Tilbakemelding</th>
						</tr>
					</thead>
					<tbody>
						@foreach($noGroupWithFeedback as $feedback)
							@if($feedback->is_active
								&& (!$feedback->availability || date('Y-m-d') >= $feedback->availability)
								&& $feedback->manuscript && $feedback->manuscript->status)
								@php
									$cacheBuster = time();
									$title = $feedback->manuscript->assignment->course
										? $feedback->manuscript->assignment->course->title
										: $feedback->manuscript->assignment->title;

									$fileLinkWithDownload = preg_replace_callback(
										'/href="([^"]+)"/',
										function ($matches) use ($cacheBuster) {
											$url = $matches[1];
											$separator = parse_url($url, PHP_URL_QUERY) ? '&' : '?';
											return 'href="' . $url . $separator . 'v=' . $cacheBuster . '"';
										},
										$feedback->manuscript->file_link_with_download
									);
								@endphp
								<tr>
									<td>
										{{ $feedback->manuscript->assignment->title }}
										<span class="op-fb-table__course">{{ $title }}</span>
									</td>
									<td>
										{!! $fileLinkWithDownload !!}
									</td>
									<td>{{ \App\Http\FrontendHelpers::formatDate($feedback->availability) }}</td>
									<td>
										<a href="{{ route('learner.assignment.no-group-feedback.download', $feedback->id) }}?v={{ $cacheBuster }}" class="op-fb-table__dl">
											<svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
											Last ned
										</a>
										@php $fbExt = pathinfo($feedback->filename ?? '', PATHINFO_EXTENSION); @endphp
										@if($fbExt == 'docx')
											<a href="{{ route('learner.assignment.manuscript.pdf', $feedback->manuscript_id ?? $feedback->id) }}" class="op-fb-table__dl" style="margin-left:8px; color:#862736;" title="Last ned med kommentarer som PDF">
												<i class="fa fa-file-pdf"></i> PDF
											</a>
										@endif
									</td>
								</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	{{-- ═══════════ TAB 4: GRUPPER ═══════════ --}}
	<div class="op-panel {{ $activeTab == 'grupper' ? 'active' : '' }}" id="op-panel-grupper">
		@if($assignmentGroupLearners->count() == 0)
			<div class="op-empty">
				<div class="op-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
				</div>
				<div class="op-empty__title">Ingen grupper</div>
				<div class="op-empty__desc">Du er ikke med i noen oppgavegrupper for øyeblikket.</div>
			</div>
		@else
			{{-- Group list --}}
			<div class="op-group-list">
				@foreach($assignmentGroupLearners as $groupLearner)
					<div class="op-group-list__item {{ $loop->first ? 'active' : '' }} group-container"
						id="group-{{ $groupLearner->group->id }}"
						onclick="showGroupDetails({{ $groupLearner->group->id }})">
						<h3>{{ $groupLearner->group->title }}</h3>
						<p>{{ $groupLearner->group->assignment->course->title }} · {{ $groupLearner->group->assignment->title }}</p>
					</div>
				@endforeach
			</div>

			{{-- Group details loaded via AJAX --}}
			<div class="op-loading d-none" id="loading-wrapper">
				<i class="fa fa-pulse fa-spinner" style="font-size: 1.5rem;"></i>
			</div>
			<div id="group-details-container"></div>
		@endif
	</div>

	{{-- ═══════════ TAB 5: REDIGERINGSWEBINAR (ÅRSKURS) ═══════════ --}}
	<div class="op-panel {{ $activeTab == 'webinar' ? 'active' : '' }}" id="op-panel-webinar">
		@if(count($noWordLimitAssignments) == 0 && count($assignments) == 0)
			<div class="op-empty">
				<div class="op-empty__icon">
					<svg viewBox="0 0 24 24" fill="none" stroke="#8a8580" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
				</div>
				<div class="op-empty__title">Ingen redigeringswebinarer</div>
				<div class="op-empty__desc">Redigeringswebinarene er en del av årskurs-programmet.</div>
			</div>
		@else
			{{-- Show no-word-limit assignments as the webinar-specific content --}}
			<div class="op-sub-list">
				@foreach($noWordLimitAssignments as $assignment)
					@php
						$manuscript = $assignment->manuscripts->where('user_id', Auth::user()->id)->first();
						$submission_date_formatted = $assignment->submission_date;
						if (!\App\Http\AdminHelpers::isDateWithFormat('M d, Y h:i A', $assignment->submission_date)) {
							$userCoursesTaken = Auth::user()->coursesTaken()->get()->toArray();
							$allowed_packages = $assignment->allowed_package ? json_decode($assignment->allowed_package) : [];
							$courseStarted = '';
							foreach ($userCoursesTaken as $course) {
								if (in_array($course['package_id'], $allowed_packages)) {
									$courseStarted = $course['started_at'];
								}
							}
							$submission_date_formatted = \Carbon\Carbon::parse($courseStarted)->addDays((int) $assignment->submission_date);
						}
						$deadlineCarbon = \Carbon\Carbon::parse($submission_date_formatted);
						$isAvailable = $deadlineCarbon->gte(now());
					@endphp
					<div class="op-sub-item" @if(!$isAvailable) style="opacity: 0.6;" @endif>
						<div style="text-align: center; min-width: 42px; flex-shrink: 0;">
							<div style="font-size: 1.25rem; font-weight: 700; color: {{ $isAvailable ? '#862736' : '#8a8580' }}; line-height: 1;">
								{{ $deadlineCarbon->format('j') }}
							</div>
							<div style="font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: {{ $isAvailable ? '#862736' : '#8a8580' }}; margin-top: 2px;">
								{{ $deadlineCarbon->locale('nb')->isoFormat('MMM') }}
							</div>
						</div>
						<div class="op-sub-item__info">
							<div class="op-sub-item__name">{{ $assignment->title }}</div>
							<div class="op-sub-item__meta">
								@if($assignment->course) {{ $assignment->course->title }} · @endif
								Frist: {{ \App\Http\FrontendHelpers::formatDateTimeNor($submission_date_formatted) }}
							</div>
						</div>
						@if($isAvailable && !$manuscript)
							@if($assignment->for_editor)
								<button class="op-btn op-btn--primary submitEditorManuscriptBtn"
									data-bs-toggle="modal" data-bs-target="#submitEditorManuscriptModal"
									data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
									data-show-group-question="{{ $assignment->show_join_group_question }}"
									data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}"
									disabled style="pointer-events: none;">
									Last opp
								</button>
							@else
								<button class="op-btn op-btn--primary submitManuscriptBtn"
									data-bs-toggle="modal" data-bs-target="#submitManuscriptModal"
									data-action="{{ route('learner.assignment.add_manuscript', $assignment->id) }}"
									data-show-group-question="{{ $assignment->show_join_group_question }}"
									data-send-letter-to-editor="{{ $assignment->send_letter_to_editor }}">
									Last opp
								</button>
							@endif
						@elseif($manuscript)
							<span class="op-badge op-badge--submitted">Innsendt</span>
						@elseif(!$isAvailable)
							<span class="op-badge op-badge--deadline-future">Utløpt</span>
						@endif
					</div>
				@endforeach
			</div>
		@endif
	</div>

</div>{{-- end .op-page --}}

</div>{{-- end .op-redesign --}}

{{-- ══════════════════════════════════════════════════════════════════
     MODALS (kept outside .op-redesign scope — render at body level)
     ══════════════════════════════════════════════════════════════════ --}}

<div id="submitSuccessModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
				<div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
				<p>{{ trans('site.learner.submit-success-text') }}</p>
			</div>
		</div>
	</div>
</div>

<div id="errorMaxword" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
				<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
				<p>{{ strtr(trans('site.learner.error-max-word-text'), ['_word_count_' => Session::get('editorMaxWord')]) }}</p>
			</div>
		</div>
	</div>
</div>

<div id="submitEditorManuscriptModal" class="modal fade new-global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					<div class="form-group">
						<div class="file-upload" id="file-upload-area">
							<i class="fa fa-cloud-upload-alt"></i>
							<div class="file-upload-text" id="file-upload-text-editor-manu">
								Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
							</div>
							<input type="file" class="form-control hidden input-file-upload" name="filename"
								id="file-upload" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
								application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
								application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
						</div>
						<div class="alert alert-info manuscript-conversion-message d-none mt-3">Konverterer dokumentet… Vennligst vent.</div>
						<div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
						<label class="file-label">* {{ trans('site.learner.manuscript.doc-format-text') }}</label>
					</div>

					<div class="form-group">
						<label>{{ trans('site.front.genre') }}</label>
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>{{ trans('site.front.select-genre') }}</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type->id }}"> {{ $type->name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label class="d-block">{{ trans('site.learner.manuscript.where-in-manuscript') }}</label>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<div class="custom-radio">
								<input type="radio" name="manu_type" value="{{ $manu['id'] }}" id="{{ $manu['id'] }}" required>
								<label for="{{ $manu['id'] }}">{{ $manu['option'] }}</label>
							</div>
						@endforeach
					</div>

					<div class="join-question-container hide">
						<div class="form-group">
							<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
							<input type="checkbox" data-bs-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
						</div>
					</div>

					<div class="form-group letter-to-editor hide">
						<label>{{ trans('site.letter-to-editor') }}</label>
						<input type="file" class="form-control margin-top" name="letter_to_editor" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
							application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
							application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
					</div>

					<button type="submit" class="btn btn-primary submit-btn float-end">{{ trans('site.front.upload') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitManuscriptModal" class="modal fade new-global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upload-script') }}</h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
					{{ csrf_field() }}
					<div class="form-group">
						<div class="file-upload" id="file-upload-area-submit-manu">
							<i class="fa fa-cloud-upload-alt"></i>
							<div class="file-upload-text">
								Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
							</div>
							<input type="file" class="form-control hidden input-file-upload" name="filename"
								id="file-upload" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
								application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
								application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
						</div>
						<div class="alert alert-info manuscript-conversion-message d-none mt-3">Konverterer dokumentet… Vennligst vent.</div>
						<div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
						<label class="file-label">* {{ trans('site.learner.manuscript.doc-format-text') }}</label>
					</div>

					<div class="form-group">
						<label>{{ trans('site.front.genre') }}</label>
						<select class="form-control" name="type" required>
							<option value="" disabled="disabled" selected>{{ trans('site.front.select-genre') }}</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type->id }}"> {{ $type->name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label class="d-block">{{ trans('site.learner.manuscript.where-in-manuscript') }}</label>
						@foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
							<div class="custom-radio">
								<input type="radio" name="manu_type" value="{{ $manu['id'] }}" id="submit-manu-{{ $manu['id'] }}" required>
								<label for="submit-manu-{{ $manu['id'] }}">{{ $manu['option'] }}</label>
							</div>
						@endforeach
					</div>

					<div class="join-question-container hide">
						<div class="form-group">
							<label>{{ trans('site.learner.join-group-question') }}?</label> <br>
							<input type="checkbox" data-bs-toggle="toggle" data-on="Ja" data-off="Nei" data-size="small" name="join_group">
						</div>
					</div>

					<div class="form-group letter-to-editor hide">
						<label>{{ trans('site.letter-to-editor') }}</label>
						<input type="file" class="form-control margin-top" name="letter_to_editor" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
							application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
							application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
					</div>

					<button type="submit" class="btn btn-primary submit-btn float-end">{{ trans('site.front.upload') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editManuscriptModal" class="modal new-global-modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.manuscript.replace-manuscript') }}</h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<div class="file-upload" id="file-upload-area-edit-manu">
							<i class="fa fa-cloud-upload-alt"></i>
							<div class="file-upload-text">
								Drag and drop files or <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>
							</div>
							<input type="file" class="form-control hidden input-file-upload" name="filename"
								id="file-upload" accept=".doc,.docx,.pdf,.odt,.pages,application/msword,
								application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,
								application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
						</div>
						<div class="alert alert-info manuscript-conversion-message d-none mt-3">Konverterer dokumentet… Vennligst vent.</div>
						<div class="alert alert-danger manuscript-conversion-error d-none mt-3"></div>
						<label class="file-label">* {{ trans('site.learner.manuscript.doc-format-text') }}</label>
					</div>
					<button type="submit" class="btn btn-primary submit-btn float-end">{{ trans('site.front.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteManuscriptModal" class="modal new-global-modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><i class="far fa-flag"></i></h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<h3>{{ trans('site.learner.delete-manuscript.title') }}</h3>
				<p>{{ trans('site.learner.delete-manuscript.question') }}</p>
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<button type="submit" class="btn btn-danger submit-btn float-end margin-top">{{ trans('site.learner.delete') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editLetterModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.manuscript.replace-manuscript') }}</h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<div class="form-group">
						<label>{{ trans('site.letter-to-editor') }}</label>
						<input type="file" class="form-control" required name="filename"
							accept=".doc,.docx,.pdf,.odt,.pages,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
						* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
					</div>
					<button type="submit" class="btn btn-primary float-end">{{ trans('site.front.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="submitFeedbackModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.submit-feedback-to') }} <em></em></h3>
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form method="POST" action="" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="form-group">
						<label>* {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}</label>
						<input type="file" class="form-control margin-top" required multiple name="filename[]"
							accept=".doc,.docx,.pdf,.odt,.pages,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
							application/pdf,application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages">
					</div>
					<button type="submit" class="btn btn-primary float-end">{{ trans('site.front.submit') }}</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

@if(Session::has('manuscript_test_error'))
<div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
				<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
				{!! Session::get('manuscript_test_error') !!}
			</div>
		</div>
	</div>
</div>
@endif

@stop

@section('scripts')
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="{{ asset('/js/app.js?v='.time()) }}"></script>
<script>

	/* ── Tab switching ────────────────────────────────── */
	function opSwitchTab(tabId, btn) {
		document.querySelectorAll('.op-redesign .op-tab').forEach(function(t) { t.classList.remove('active'); });
		document.querySelectorAll('.op-redesign .op-panel').forEach(function(p) { p.classList.remove('active'); });
		if (btn) btn.classList.add('active');
		var panel = document.getElementById('op-panel-' + tabId);
		if (panel) panel.classList.add('active');
	}

	/* ── Sidebar toggle + auto-collapse ────────────────── */
	(function() {
		var sidebar = document.getElementById('sidebar');
		var mainContainer = document.getElementById('main-container');
		var toggleBtn = document.getElementById('opSidebarToggle');

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
		// course-portal-scriptet kjører etter @yield('scripts'), så vi trenger setTimeout
		setTimeout(function() {
			if (!sidebar) return;
			// Sjekk om vinduet faktisk er smalt (bruker documentElement.clientWidth
			// som matcher CSS media queries, i stedet for window.innerWidth)
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

	/* ── Existing functionality ───────────────────────── */
	$(window).on('load', function() {
		const groupLearnerGroupId = '{{ $assignmentGroupLearners->count() ? $assignmentGroupLearners[0]->group->id : "" }}';
		if (groupLearnerGroupId) {
			showGroupDetails(groupLearnerGroupId);
		}
	});

	@if (Session::has('success'))
	$('#submitSuccessModal').modal('show');
	@endif

	@if (Session::has('errorMaxWord'))
	$('#errorMaxword').modal('show');
	@endif

	@if(Session::has('manuscript_test_error'))
	$('#manuscriptTestErrorModal').modal('show');
	@endif

	setupFileUpload('file-upload-area');
	setupFileUpload('file-upload-area-submit-manu');
	setupFileUpload('file-upload-area-edit-manu');

	$('.submitManuscriptBtn').click(function(){
		let form = $('#submitManuscriptModal').find("form");
		let action = $(this).data('action');
		let show_group_question = parseInt($(this).data('show-group-question'));
		let send_letter_to_editor = parseInt($(this).data('send-letter-to-editor'));
		form.attr('action', action);

		if (show_group_question === 1) {
			form.find('.join-question-container').removeClass('hide');
		} else {
			form.find('.join-question-container').addClass('hide');
		}

		if (send_letter_to_editor === 1) {
			form.find('.letter-to-editor').removeClass('hide');
		} else {
			form.find('.letter-to-editor').addClass('hide');
		}
	});

	let submitEditorManuscriptReady = false;
	const submitEditorManuscriptButtons = $(".submitEditorManuscriptBtn");
	submitEditorManuscriptButtons
		.prop('disabled', true)
		.addClass('disabled')
		.css('pointer-events', 'none');

	$(window).on('load', function () {
		submitEditorManuscriptReady = true;
		submitEditorManuscriptButtons
			.prop('disabled', false)
			.removeClass('disabled')
			.css('pointer-events', '');
	});

	$('.submitEditorManuscriptBtn').click(function(){
		if (!submitEditorManuscriptReady) {
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
		let form = $('#submitEditorManuscriptModal').find("form");
		let action = $(this).data('action');
		let show_group_question = parseInt($(this).data('show-group-question'));
		let send_letter_to_editor = parseInt($(this).data('send-letter-to-editor'));
		form.attr('action', action);

		if (show_group_question === 1) {
			form.find('.join-question-container').removeClass('hide');
		} else {
			form.find('.join-question-container').addClass('hide');
		}

		if (send_letter_to_editor === 1) {
			form.find('.letter-to-editor').removeClass('hide');
		} else {
			form.find('.letter-to-editor').addClass('hide');
		}
	});

	$('.editManuscriptBtn').click(function(){
		let form = $('#editManuscriptModal form');
		let action = $(this).data('action');
		form.attr('action', action);
	});

	$('.deleteManuscriptBtn').click(function(){
		let form = $('#deleteManuscriptModal form');
		let action = $(this).data('action');
		form.attr('action', action)
	});

	$(".editLetterBtn").click(function() {
		let form = $('#editLetterModal').find('form');
		let action = $(this).data('action');
		form.attr('action', action)
	});

	function submitFeedbackFromGroup(self) {
		var modal = $('#submitFeedbackModal');
		var name = $(self).data('name');
		var action = $(self).data('action');
		modal.find('em').text(name);
		modal.find('form').attr('action', action);
	}

	function editFeedbackFromGroup(self) {
		let form = $('#editManuscriptModal form');
		let action = $(self).data('action');
		form.attr('action', action);
	}

	function deleteFeedbackFromGroup(self) {
		let form = $('#deleteManuscriptModal form');
		let action = $(self).data('action');
		form.attr('action', action);
	}

	function getCsrfToken() {
		const tokenElement = document.querySelector('meta[name="csrf-token"]');
		return tokenElement ? tokenElement.getAttribute('content') : null;
	}

	async function parseErrorBlob(blob) {
		if (!blob || typeof blob.text !== 'function') { return null; }
		const text = await blob.text();
		if (!text) { return null; }
		try { return JSON.parse(text); } catch (error) { return { message: text }; }
	}

	function createDocxFileName(originalName) {
		if (!originalName || typeof originalName !== 'string') { return 'document.docx'; }
		const dotIndex = originalName.lastIndexOf('.');
		if (dotIndex <= 0) { return originalName.toLowerCase().endsWith('.docx') ? originalName : originalName + '.docx'; }
		const baseName = originalName.substring(0, dotIndex);
		const extension = originalName.substring(dotIndex + 1).toLowerCase();
		if (extension === 'docx') { return originalName; }
		return baseName + '.docx';
	}

	function extractFilenameFromContentDisposition(header) {
		if (!header || typeof header !== 'string') { return null; }
		const utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
		if (utf8Match && utf8Match[1]) { try { return decodeURIComponent(utf8Match[1]); } catch (error) { console.error('Failed to decode UTF-8 filename', error); } }
		const quotedMatch = header.match(/filename="?([^";]+)"?/i);
		if (quotedMatch && quotedMatch[1]) { return quotedMatch[1]; }
		return null;
	}

	async function convertFileToDocx(file) {
		const formData = new FormData();
		formData.append('document', file);
		const csrfToken = getCsrfToken();
		if (csrfToken) { formData.append('_token', csrfToken); }
		const fallbackName = createDocxFileName(file && file.name ? file.name : null);
		const mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

		if (window.axios) {
			try {
				const response = await window.axios.post('/documents/convert-to-docx', formData, {
					responseType: 'blob',
					headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } : { 'X-Requested-With': 'XMLHttpRequest' },
				});
				const headers = response.headers || {};
				const contentDisposition = headers['content-disposition'] || headers['Content-Disposition'] || null;
				const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
				const responseBlob = response.data instanceof Blob ? response.data : new Blob(response.data ? [response.data] : [], { type: mimeType });
				return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
			} catch (error) {
				if (error && error.response && error.response.data instanceof Blob) {
					try { const parsed = await parseErrorBlob(error.response.data); if (parsed) { error.response.data = parsed; } } catch (parseError) { console.error('Failed to parse conversion error response', parseError); }
				}
				if (!error.response || !error.response.data) {
					error.response = error.response || {};
					error.response.data = { errors: { manuscript: ['Kunne ikke konvertere filen. Prøv igjen.'] }, message: 'Kunne ikke konvertere filen. Prøv igjen.' };
				}
				throw error;
			}
		}

		const headers = { 'X-Requested-With': 'XMLHttpRequest' };
		if (csrfToken) { headers['X-CSRF-TOKEN'] = csrfToken; }
		const response = await fetch('/documents/convert-to-docx', { method: 'POST', body: formData, headers });
		const contentDisposition = response.headers ? (response.headers.get('content-disposition') || response.headers.get('Content-Disposition')) : null;
		if (!response.ok) {
			const error = new Error('Kunne ikke konvertere filen. Prøv igjen.');
			let errorData = null;
			try { errorData = await response.clone().json(); } catch (jsonError) { try { errorData = { message: await response.text() }; } catch (textError) { errorData = null; } }
			error.response = { status: response.status, data: errorData || { errors: { manuscript: ['Kunne ikke konvertere filen. Prøv igjen.'] }, message: 'Kunne ikke konvertere filen. Prøv igjen.' } };
			throw error;
		}
		const data = await response.blob();
		const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
		const responseBlob = data instanceof Blob ? data : new Blob([data], { type: mimeType });
		return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
	}

	function getFileExtension(filename) {
		if (!filename || typeof filename !== 'string') { return ''; }
		const parts = filename.split('.');
		return parts.length > 1 ? parts.pop().toLowerCase() : '';
	}

	function getErrorMessageFromConversion(error) {
		if (!error) { return 'Kunne ikke konvertere filen. Prøv igjen.'; }
		if (error.response && error.response.data) {
			const data = error.response.data;
			if (data.errors && data.errors.manuscript && data.errors.manuscript.length) { return data.errors.manuscript[0]; }
			if (typeof data.message === 'string' && data.message.trim() !== '') { return data.message; }
		}
		if (error.message && error.message.trim() !== '') { return error.message; }
		return 'Kunne ikke konvertere filen. Prøv igjen.';
	}

	function assignFilesToInput(input, file) {
		if (!input || !file) { return false; }
		const files = Array.isArray(file) ? file : [file];
		try {
			if (typeof DataTransfer !== 'undefined') {
				const dataTransfer = new DataTransfer();
				files.forEach((item) => dataTransfer.items.add(item));
				input.files = dataTransfer.files;
				return true;
			}
		} catch (error) { console.warn('DataTransfer is not available for file assignment.', error); }
		try {
			if (typeof ClipboardEvent !== 'undefined') {
				const clipboardEvent = new ClipboardEvent('');
				if (clipboardEvent.clipboardData) {
					files.forEach((item) => clipboardEvent.clipboardData.items.add(item));
					input.files = clipboardEvent.clipboardData.files;
					return true;
				}
			}
		} catch (error) { console.warn('ClipboardEvent fallback failed for file assignment.', error); }
		return false;
	}

	function setFormConversionState(form, isConverting) {
		if (!form) { return; }
		const messageElement = form.querySelector('.manuscript-conversion-message');
		if (messageElement) { if (isConverting) { messageElement.classList.remove('d-none'); } else { messageElement.classList.add('d-none'); } }
		const submitButton = form.querySelector('button[type="submit"]');
		if (submitButton) { submitButton.disabled = !!isConverting; }
	}

	function showConversionError(form, message) {
		if (!form) { window.alert(message); return; }
		const errorElement = form.querySelector('.manuscript-conversion-error');
		if (errorElement) { errorElement.textContent = message; errorElement.classList.remove('d-none'); } else { window.alert(message); }
	}

	function clearConversionError(form) {
		if (!form) { return; }
		const errorElement = form.querySelector('.manuscript-conversion-error');
		if (errorElement) { errorElement.textContent = ''; errorElement.classList.add('d-none'); }
	}

	function resetFileInput(input) {
		if (!input) { return; }
		try { input.value = ''; } catch (error) { input.value = null; }
	}

	function setupFileUpload(area) {
		const fileUploadArea = document.getElementById(area);
		if (!fileUploadArea) { return; }
		const fileInput = fileUploadArea.querySelector('.input-file-upload');
		const fileUploadText = fileUploadArea.querySelector('.file-upload-text');
		const form = fileUploadArea.closest('form');
		const textWithBrowseButton = 'Dra og slipp filer eller <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a>';

		const openFileInput = () => { if (fileInput) { fileInput.click(); } };

		const attachBrowseButtonHandlers = () => {
			if (!fileUploadArea) { return; }
			const browseButtons = fileUploadArea.querySelectorAll('.file-upload-btn');
			browseButtons.forEach((button) => {
				if (button.dataset.handlerAttached === 'true') { return; }
				const handleBrowseInteraction = (event) => { if (event) { event.preventDefault(); } openFileInput(); };
				button.addEventListener('click', handleBrowseInteraction);
				button.addEventListener('mousedown', handleBrowseInteraction);
				button.dataset.handlerAttached = 'true';
			});
		};

		const updateText = (text) => { if (fileUploadText) { fileUploadText.innerHTML = text; attachBrowseButtonHandlers(); } };

		const handleFiles = async (files) => {
			if (!fileInput) { return; }
			if (!files || !files.length) { clearConversionError(form); setFormConversionState(form, false); updateText(textWithBrowseButton); return; }
			let selectedFile = files[0];
			if (!selectedFile) { clearConversionError(form); setFormConversionState(form, false); updateText(textWithBrowseButton); return; }
			const extension = getFileExtension(selectedFile.name || '');
			clearConversionError(form);
			if (extension !== 'docx') {
				setFormConversionState(form, true);
				try { selectedFile = await convertFileToDocx(selectedFile); } catch (error) {
					showConversionError(form, getErrorMessageFromConversion(error));
					resetFileInput(fileInput); updateText(textWithBrowseButton); setFormConversionState(form, false); return;
				}
				setFormConversionState(form, false);
			}
			const assigned = assignFilesToInput(fileInput, selectedFile);
			if (!assigned) { showConversionError(form, 'Kunne ikke oppdatere filen etter konvertering. Prøv en annen nettleser.'); resetFileInput(fileInput); updateText(textWithBrowseButton); setFormConversionState(form, false); return; }
			updateText(selectedFile.name || textWithBrowseButton);
			clearConversionError(form); setFormConversionState(form, false);
		};

		fileUploadArea.addEventListener('dragover', (e) => { e.preventDefault(); fileUploadArea.classList.add('dragover'); updateText('Release to upload'); });
		fileUploadArea.addEventListener('dragleave', () => { fileUploadArea.classList.remove('dragover'); updateText(textWithBrowseButton); });
		fileUploadArea.addEventListener('drop', (e) => { e.preventDefault(); fileUploadArea.classList.remove('dragover'); const files = e.dataTransfer ? e.dataTransfer.files : null; handleFiles(files); });
		fileUploadArea.addEventListener('click', (event) => { openFileInput(event); });
		attachBrowseButtonHandlers();
		if (fileInput) { fileInput.addEventListener('change', (event) => { handleFiles(event.target.files); }); }
		const modal = fileUploadArea.closest('.modal');
		if (modal) {
			const submitButton = modal.querySelector('[type=submit]');
			if (submitButton) { submitButton.addEventListener('click', function (e) { if (!fileInput || !fileInput.files || !fileInput.files.length) { alert('Please select a document file.'); e.preventDefault(); } }); }
		}
		updateText(textWithBrowseButton);
	}

	function showGroupDetails(group_id) {
		$(".group-container").removeClass('active');
		$("#group-"+group_id).addClass('active');

		$.ajax({
			type: "GET",
			url: "/account/assignment/group/" + group_id + "/show-details",
			beforeSend: function() {
				$("#loading-wrapper").removeClass('d-none');
			},
			success:function(data) {
				$("#loading-wrapper").addClass('d-none');
				$("#group-details-container").html(data);
			}
		});
	}
</script>
@stop
