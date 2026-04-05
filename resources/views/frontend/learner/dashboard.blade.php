@extends('frontend.layouts.course-portal')

@section('title')
<title>Kontrollpanel &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style>
/* ── DASHBOARD REDESIGN — scoped under .db-redesign ── */
.db-redesign {
    --db-wine: #862736;
    --db-wine-hover: #9c2e40;
    --db-wine-dark: #5c1a25;
    --db-wine-light: rgba(134, 39, 54, 0.08);
    --db-wine-light-solid: #f4e8ea;
    --db-cream: #faf8f5;
    --db-green: #2e7d32;
    --db-green-bg: #e8f5e9;
    --db-amber: #e65100;
    --db-amber-bg: #fff3e0;
    --db-red: #c62828;
    --db-red-bg: #fce8e8;
    --db-blue: #1565c0;
    --db-blue-bg: #e3f2fd;
    --db-text: #1a1a1a;
    --db-text-sec: #5a5550;
    --db-text-muted: #8a8580;
    --db-border: rgba(0, 0, 0, 0.08);
    --db-border-strong: rgba(0, 0, 0, 0.12);
    --db-font: 'Source Sans 3', -apple-system, sans-serif;
    --db-radius: 10px;
    --db-radius-lg: 14px;
    font-family: var(--db-font);
    color: var(--db-text);
    -webkit-font-smoothing: antialiased;
    padding: 2rem 2.5rem;
    background: #f5f3f0;
    min-height: 100vh;
    overflow-x: hidden;
    max-width: 100%;
    box-sizing: border-box;
}

/* Hide topbar on dashboard — its content is integrated into the dashboard */
#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; overflow-x: hidden !important; max-width: 100vw; }
#main-container { overflow-x: hidden !important; }

/* Sidebar toggle — kun synlig på mobil/tablet */
.db-redesign .db-sidebar-toggle {
    display: none !important; position: fixed; top: 16px; left: 16px; z-index: 1050;
    width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
    background: var(--db-wine); align-items: center; justify-content: center; cursor: pointer;
    box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
    padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.db-redesign .db-sidebar-toggle:hover {
    background: var(--db-wine-hover);
    box-shadow: 0 6px 24px rgba(134, 39, 54, 0.5), 0 0 0 4px rgba(134, 39, 54, 0.2);
    transform: scale(1.08);
}
.db-redesign .db-sidebar-toggle:active { transform: scale(0.95); }
.db-redesign .db-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
@media (max-width: 991px) {
    .db-redesign .db-sidebar-toggle { display: flex !important; }
}

/* Welcome */
.db-welcome { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem; }
.db-welcome h1 { font-size: 1.5rem; font-weight: 700; color: var(--db-text); margin: 0 0 0.15rem; }
.db-welcome p { font-size: 0.875rem; color: var(--db-text-sec); margin: 0; }
.db-welcome__date { font-size: 0.825rem; color: var(--db-text-muted); }

/* Author quote */
.db-quote {
    background: var(--db-cream); border: 1px solid var(--db-border);
    border-left: 3px solid var(--db-wine);
    border-radius: 0 var(--db-radius) var(--db-radius) 0;
    padding: 1rem 1.5rem; margin-bottom: 1.5rem;
    display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
}
.db-quote__text { font-size: 0.9rem; font-style: italic; color: var(--db-text-sec); line-height: 1.6; }
.db-quote__author { font-size: 0.78rem; font-weight: 600; color: var(--db-text-muted); font-style: normal; white-space: nowrap; }

/* Alert cards */
.db-alert { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border-radius: var(--db-radius); margin-bottom: 1.5rem; }
.db-alert--warning { background: var(--db-amber-bg); border: 1px solid rgba(230, 81, 0, 0.15); }
.db-alert--danger { background: var(--db-red-bg); border: 1px solid rgba(198, 40, 40, 0.15); }
.db-alert__icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.db-alert--warning .db-alert__icon { background: rgba(230, 81, 0, 0.12); }
.db-alert--danger .db-alert__icon { background: rgba(198, 40, 40, 0.12); }
.db-alert__text { flex: 1; font-size: 0.85rem; color: var(--db-text); min-width: 0; word-wrap: break-word; overflow-wrap: break-word; }
.db-alert__text strong { font-weight: 600; }
.db-alert__action { font-size: 0.8rem; font-weight: 600; color: var(--db-wine); text-decoration: none; white-space: nowrap; padding: 0.4rem 1rem; border: 1px solid var(--db-wine); border-radius: 6px; transition: all 0.15s; }
.db-alert__action:hover { background: var(--db-wine); color: #fff; text-decoration: none; }

/* Next-up cards */
.db-next-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
.db-next-card { background: #fff; border: 1px solid var(--db-border); border-radius: var(--db-radius-lg); padding: 1.25rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; min-width: 0; }
.db-next-card__icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.db-next-card__icon--task { background: var(--db-wine-light-solid); }
.db-next-card__icon--mentor { background: var(--db-blue-bg); }
.db-next-card__icon svg { width: 22px; height: 22px; }
.db-next-card__label { font-size: 0.7rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--db-text-muted); margin-bottom: 0.3rem; }
.db-next-card__title { font-size: 0.95rem; font-weight: 600; color: var(--db-text); margin-bottom: 0.2rem; }
.db-next-card__meta { font-size: 0.78rem; color: var(--db-text-muted); }
.db-next-card__action { display: inline-block; margin-top: 0.75rem; font-size: 0.78rem; font-weight: 600; color: var(--db-wine); text-decoration: none; padding: 0.35rem 0.85rem; border: 1px solid var(--db-wine); border-radius: 5px; transition: all 0.15s; }
.db-next-card__action:hover { background: var(--db-wine); color: #fff; text-decoration: none; }

/* Quick stats */
.db-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
.db-stat { background: #fff; border: 1px solid var(--db-border); border-radius: var(--db-radius); padding: 1rem; text-align: center; }
.db-stat__number { font-size: 1.5rem; font-weight: 700; color: var(--db-text); line-height: 1; margin-bottom: 0.25rem; }
.db-stat__label { font-size: 0.72rem; color: var(--db-text-muted); }

/* Cards */
.db-card { background: #fff; border: 1px solid var(--db-border); border-radius: var(--db-radius-lg); overflow: hidden; min-width: 0; }
.db-card__header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem 1rem; }
.db-card__title { font-size: 1rem; font-weight: 700; color: var(--db-text); margin: 0; }
.db-card__link { font-size: 0.78rem; font-weight: 600; color: var(--db-wine); text-decoration: none; }
.db-card__link:hover { color: var(--db-wine-hover); text-decoration: none; }
.db-card__body { padding: 0 1.5rem 1.5rem; min-width: 0; overflow: hidden; }

/* Grid layout */
.db-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; min-width: 0; }

/* Course list */
.db-course-list { display: flex; flex-direction: column; gap: 0.6rem; }
.db-course-item { display: flex; align-items: center; gap: 1rem; padding: 0.85rem; border: 1px solid var(--db-border); border-radius: var(--db-radius); transition: border-color 0.15s; text-decoration: none; color: inherit; }
.db-course-item:hover { border-color: var(--db-border-strong); text-decoration: none; color: inherit; }
.db-course-item__thumb { width: 56px; height: 56px; border-radius: 8px; background: linear-gradient(135deg, #e8e2da, #d4cec6); flex-shrink: 0; overflow: hidden; }
.db-course-item__thumb img { width: 100%; height: 100%; object-fit: cover; }
.db-course-item__info { flex: 1; min-width: 0; overflow: hidden; }
.db-course-item__name { font-size: 0.875rem; font-weight: 600; color: var(--db-text); margin-bottom: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.db-course-item__instructor { font-size: 0.75rem; color: var(--db-text-muted); }
.db-course-item__badge { font-size: 0.65rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 4px; white-space: nowrap; }
.db-course-item__badge--active { background: var(--db-green-bg); color: var(--db-green); }
.db-course-item__badge--renewal { background: var(--db-amber-bg); color: var(--db-amber); }
.db-course-item__badge--hold { background: var(--db-red-bg); color: var(--db-red); }
.db-course-item__arrow { color: var(--db-text-muted); font-size: 0.85rem; }

/* Mentor timeline */
.db-mentor-tl { display: flex; flex-direction: column; }
.db-mentor-item { display: flex; align-items: flex-start; gap: 0.85rem; padding: 0.85rem 0; border-bottom: 1px solid var(--db-border); min-width: 0; }
.db-mentor-item:last-child { border-bottom: none; }
.db-mentor-item__date { text-align: center; min-width: 42px; flex-shrink: 0; }
.db-mentor-item__day { font-size: 1.25rem; font-weight: 700; color: var(--db-wine); line-height: 1; }
.db-mentor-item__month { font-size: 0.6rem; font-weight: 600; text-transform: uppercase; color: var(--db-wine); margin-top: 2px; }
.db-mentor-item__info { flex: 1; min-width: 0; }
.db-mentor-item__name { font-size: 0.85rem; font-weight: 600; color: var(--db-text); margin-bottom: 0.1rem; word-wrap: break-word; overflow-wrap: break-word; }
.db-mentor-item__topic { font-size: 0.75rem; color: var(--db-text-muted); word-wrap: break-word; overflow-wrap: break-word; }
.db-mentor-item__time { font-size: 0.7rem; color: var(--db-text-muted); white-space: nowrap; }
.db-mentor-item__badge-live { font-size: 0.6rem; font-weight: 600; padding: 0.15rem 0.4rem; border-radius: 3px; background: var(--db-green-bg); color: var(--db-green); display: inline-block; margin-top: 0.15rem; }

/* Calendar items */
.db-cal-item { display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--db-border); min-width: 0; }
.db-cal-item:last-child { border-bottom: none; }
.db-cal-item__dot { width: 8px; height: 8px; border-radius: 50%; background: var(--db-wine); margin-top: 5px; flex-shrink: 0; }
.db-cal-item__info { min-width: 0; flex: 1; }
.db-cal-item__text { font-size: 0.82rem; color: var(--db-text); line-height: 1.5; word-wrap: break-word; overflow-wrap: break-word; }
.db-cal-item__date { font-size: 0.7rem; color: var(--db-text-muted); margin-top: 0.1rem; }

/* Community card */
.db-community-box { background: var(--db-wine-light-solid); border-radius: 10px; padding: 1.25rem; text-align: center; word-wrap: break-word; overflow-wrap: break-word; }
.db-community-box svg { margin-bottom: 0.5rem; }
.db-community-box__title { font-size: 0.9rem; font-weight: 600; color: var(--db-text); margin-bottom: 0.2rem; }
.db-community-box__desc { font-size: 0.78rem; color: var(--db-text-sec); line-height: 1.5; }

/* Quick links */
.db-quick-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0.85rem; border: 1px solid var(--db-border); border-radius: 8px; text-decoration: none; color: var(--db-text); font-size: 0.85rem; font-weight: 500; transition: border-color 0.15s; margin-bottom: 0.5rem; }
.db-quick-link:hover { border-color: var(--db-border-strong); text-decoration: none; color: var(--db-text); }
.db-quick-link svg { width: 18px; height: 18px; flex-shrink: 0; }

/* Auto-renew toggle */
.db-auto-renew { display: flex; align-items: center; gap: 0.5rem; font-size: 0.78rem; color: var(--db-text-muted); margin-bottom: 1.5rem; }
.db-auto-renew label { margin: 0; cursor: pointer; }

/* ── UPLOAD MODAL REDESIGN ── */
/* Note: modals render outside .db-redesign, so we use literal colors instead of var(--db-*) */
.um-modal .modal-dialog { max-width: 480px; }
.um-modal .modal-content { border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 24px 64px rgba(0,0,0,0.2); }

.um-modal .um-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.08); }
.um-modal .um-header h3 { font-size: 1.1rem; font-weight: 700; color: #1a1a1a; margin: 0; }
.um-modal .um-close { width: 32px; height: 32px; border-radius: 8px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
.um-modal .um-close:hover { background: rgba(0,0,0,0.05); }
.um-modal .um-close svg { width: 18px; height: 18px; stroke: #8a8580; stroke-width: 2; }

.um-modal .um-body { padding: 1.5rem; }

/* Context info */
.um-context { display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; background: #faf8f5; border-radius: 10px; margin-bottom: 1.5rem; }
.um-context-icon { width: 36px; height: 36px; border-radius: 8px; background: #f4e8ea; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.um-context-icon svg { width: 18px; height: 18px; }
.um-context-text { font-size: 0.825rem; color: #5a5550; line-height: 1.5; }
.um-context-text strong { color: #1a1a1a; }

/* Upload zone */
.um-upload-zone { border: 2px dashed rgba(0,0,0,0.12); border-radius: 10px; padding: 2rem 1.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; margin-bottom: 1.25rem; position: relative; }
.um-upload-zone:hover, .um-upload-zone.dragover { border-color: #862736; background: rgba(134,39,54,0.03); }
.um-upload-zone__icon { width: 48px; height: 48px; margin: 0 auto 0.75rem; background: #f4e8ea; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.um-upload-zone__icon svg { width: 24px; height: 24px; }
.um-upload-zone__title { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.2rem; }
.um-upload-zone__sub { font-size: 0.78rem; color: #8a8580; margin-bottom: 0.75rem; }
.um-upload-zone__formats { display: flex; justify-content: center; gap: 0.35rem; flex-wrap: wrap; }
.um-format-tag { font-size: 0.65rem; font-weight: 600; color: #8a8580; background: rgba(0,0,0,0.04); padding: 0.2rem 0.55rem; border-radius: 4px; }
.um-upload-zone input[type="file"] { display: none; }

/* File selected */
.um-upload-zone--selected { border-style: solid; border-color: #2e7d32; background: #e8f5e9; padding: 1rem 1.25rem; text-align: left; }
.um-upload-file { display: flex; align-items: center; gap: 0.75rem; }
.um-upload-file__icon { width: 40px; height: 40px; border-radius: 8px; background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.um-upload-file__icon svg { width: 20px; height: 20px; }
.um-upload-file__info { flex: 1; }
.um-upload-file__name { font-size: 0.85rem; font-weight: 600; color: #1a1a1a; }
.um-upload-file__size { font-size: 0.72rem; color: #2e7d32; }
.um-upload-file__remove { width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(0,0,0,0.06); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s; }
.um-upload-file__remove:hover { background: rgba(0,0,0,0.1); }
.um-upload-file__remove svg { width: 14px; height: 14px; stroke: #8a8580; stroke-width: 2; }

/* Form fields */
.um-form-row { display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1.25rem; }
.um-form-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.35rem; }
.um-modal .um-form-group select { width: 100%; padding: 0.6rem 0.85rem; border: 1px solid rgba(0,0,0,0.12) !important; border-radius: 6px; font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.85rem; color: #1a1a1a !important; background: #fff !important; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%238a8580' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.85rem center; padding-right: 2.25rem; transition: border-color 0.15s; }
.um-modal .um-form-group select:focus { outline: none; border-color: #862736 !important; }

/* Segment buttons */
.um-segment-label { font-size: 0.78rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.5rem; }
.um-segment-buttons { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem; margin-bottom: 1.5rem; }
.um-modal .um-segment-btn,
.um-modal .um-segment-btn:focus,
.um-modal .um-segment-btn:active { padding: 0.55rem 0.25rem; border: 1px solid rgba(0,0,0,0.12) !important; border-radius: 6px; background: #fff !important; font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.75rem; font-weight: 500; color: #5a5550 !important; cursor: pointer; text-align: center; transition: all 0.15s; box-shadow: none !important; outline: none !important; }
.um-modal .um-segment-btn:hover { border-color: #862736 !important; color: #862736 !important; }
.um-modal .um-segment-btn.active,
.um-modal .um-segment-btn.active:focus,
.um-modal .um-segment-btn.active:active,
.um-modal .um-segment-btn.active:hover { background: #862736 !important; border-color: #862736 !important; color: #fff !important; }

/* Word note */
.um-word-note { display: flex; align-items: center; gap: 0.5rem; padding: 0.7rem 0.85rem; background: rgba(134,39,54,0.05); border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.78rem; color: #5a5550; }
.um-word-note svg { width: 16px; height: 16px; flex-shrink: 0; }

/* Footer */
.um-footer { padding: 0 1.5rem 1.5rem; display: flex; gap: 0.75rem; }
.um-modal .um-btn { flex: 1; padding: 0.75rem; border-radius: 8px; font-family: 'Source Sans 3', -apple-system, sans-serif; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.15s; text-align: center; border: none !important; }
.um-modal .um-btn--primary { background: #862736 !important; color: #fff !important; }
.um-modal .um-btn--primary:hover { background: #9c2e40 !important; }
.um-modal .um-btn--secondary { background: transparent !important; color: #5a5550 !important; border: 1px solid rgba(0,0,0,0.12) !important; }
.um-modal .um-btn--secondary:hover { background: rgba(0,0,0,0.02) !important; }

/* Responsive */
@media (max-width: 1100px) { .db-grid { grid-template-columns: 1fr; } }
@media (max-width: 1026px) {
    .db-redesign { padding: 1.5rem 1rem; padding-top: 3.5rem; }
    .db-next-grid { grid-template-columns: 1fr; }
    .db-stats { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .db-redesign { padding: 1.25rem 0.75rem; padding-top: 3.5rem; }
    .db-welcome h1 { font-size: 1.25rem; }
    .db-quote { flex-direction: column; gap: 0.5rem; padding: 0.85rem 1rem; }
    .db-quote__author { white-space: normal; }
    .db-next-card { padding: 1rem; }
    .db-next-card__icon { width: 36px; height: 36px; }
    .db-alert { flex-wrap: wrap; gap: 0.75rem; }
    .db-alert__action { margin-left: auto; }
}
@media (max-width: 576px) {
    .db-redesign { padding: 1rem 0.65rem; padding-top: 3.25rem; }
    .db-welcome h1 { font-size: 1.1rem; }
    .db-welcome__date { font-size: 0.75rem; }
    .db-stats { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .db-stat { padding: 0.75rem 0.5rem; }
    .db-stat__number { font-size: 1.25rem; }
    .db-stat__label { font-size: 0.65rem; }
    .db-next-card__title { font-size: 0.875rem; }
    .db-card { border-radius: 10px; }
    .db-card__header { padding: 0.85rem 1rem; }
    .db-card__body { padding: 0 1rem 1rem; }
}
</style>
@stop

@section('content')
@php
    $months_no = ['Jan','Feb','Mar','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Des'];
    $days_no = ['søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag'];
    $today = \Carbon\Carbon::now();
    $todayFormatted = $days_no[$today->dayOfWeek] . ' ' . $today->format('d') . '. ' .
        strtolower($months_no[$today->month - 1]) . ' ' . $today->format('Y');
    $todayFormatted = ucfirst($todayFormatted);

    // Wrap arrays into collections for chaining
    $assignmentsCol = collect($assignments);
    $coursesTakenCol = collect($coursesTaken);

    // Get next assignment
    $nextAssignment = $assignmentsCol->first(function($a) {
        return !$a->manuscripts->where('user_id', Auth::user()->id)->first();
    });

    // Count active courses
    $activeCourseCount = $coursesTakenCol->filter(function($ct) {
        return $ct->is_active && $ct->hasStarted && !$ct->hasEnded;
    })->count();

    $pendingAssignmentCount = $assignmentsCol->filter(function($a) {
        return !$a->manuscripts->where('user_id', Auth::user()->id)->first();
    })->count();

    // Get webinars for timeline
    $webinarTimeline = DB::table('courses_taken')
        ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
        ->join('courses', 'packages.course_id', '=', 'courses.id')
        ->join('webinars', 'courses.id', '=', 'webinars.course_id')
        ->select('webinars.*','courses.title as course_title')
        ->where('user_id', Auth::user()->id)
        ->where('courses.id', 17)
        ->whereNotIn('webinars.id', [24, 25, 31])
        ->where('set_as_replay', 0)
        ->where('webinars.start_date', '>=', now()->toDateString())
        ->orderBy('webinars.start_date', 'ASC')
        ->groupBy('webinars.id')
        ->limit(4)
        ->get();

    // Quotes
    $quotes = [
        ['text' => 'Start writing, no matter what. The water does not flow until the faucet is turned on.', 'author' => 'Louis L\'Amour'],
        ['text' => 'You can always edit a bad page. You can\'t edit a blank page.', 'author' => 'Jodi Picoult'],
        ['text' => 'The first draft is just you telling yourself the story.', 'author' => 'Terry Pratchett'],
        ['text' => 'A writer is someone for whom writing is more difficult than it is for other people.', 'author' => 'Thomas Mann'],
    ];
    $dailyQuote = $quotes[array_rand($quotes)];

    // Invoice alert logic
    $unpaidInvoice = Auth::user()->invoices()->where('fiken_is_paid', 0)->first();
    $invoiceAlert = null;
    if ($unpaidInvoice && $unpaidInvoice->fiken_dueDate) {
        $daysUntilDue = (int) round(now()->diffInDays(\Carbon\Carbon::parse($unpaidInvoice->fiken_dueDate), false));
        if ($daysUntilDue < 0) {
            $invoiceAlert = ['type' => 'danger', 'text' => 'Forfalt faktura: #' . $unpaidInvoice->invoice_number . ' — ' . abs($daysUntilDue) . ' dager over fristen'];
        } elseif ($daysUntilDue <= 7) {
            $invoiceAlert = ['type' => 'warning', 'text' => 'Faktura #' . $unpaidInvoice->invoice_number . ' forfaller om ' . $daysUntilDue . ' dager'];
        }
    }

    // Pay later reminder — vis kun hvis det finnes ordrer som mangler betalingsløsning
    $hasPayLaterWithoutInvoice = Auth::user()->orders()
        ->where('is_pay_later', 1)
        ->where('is_processed', 1)
        ->where('is_invoice_sent', 0)
        ->where('is_order_withdrawn', 0)
        ->exists();
    $payLaterAlert = $hasPayLaterWithoutInvoice && !$invoiceAlert;

    // Calendar entries
    $uniqueStart = array_unique(array_map(function ($i) {
        if (\Carbon\Carbon::parse($i['start'])->gte(\Carbon\Carbon::today())) {
            return $i['start'];
        }
    }, $dashboardCalendar));
    $filteredUniqueStart = array_filter($uniqueStart);
    sort($filteredUniqueStart);
@endphp

<div class="db-redesign">

    {{-- Sidebar toggle (egen knapp, unngår duplikat-ID) --}}
    <button type="button" id="dbSidebarToggle" class="db-sidebar-toggle" data-sidebar-toggle aria-label="Vis/skjul meny">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    {{-- ═══ WELCOME ═══ --}}
    <div class="db-welcome">
        <div>
            <h1>Hei, {{ Auth::user()->first_name ?? Auth::user()->name }}!</h1>
            <p>Her er oversikten din for denne uken.</p>
        </div>
        <span class="db-welcome__date">{{ $todayFormatted }}</span>
    </div>

    {{-- Daily quote --}}
    <div class="db-quote">
        <p class="db-quote__text">"{{ $dailyQuote['text'] }}"</p>
        <span class="db-quote__author">— {{ $dailyQuote['author'] }}</span>
    </div>

    {{-- Invoice alert --}}
    @if($invoiceAlert)
        <div class="db-alert db-alert--{{ $invoiceAlert['type'] }}">
            <div class="db-alert__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $invoiceAlert['type'] === 'danger' ? '#c62828' : '#e65100' }}" stroke-width="1.5" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="db-alert__text"><strong>{{ $invoiceAlert['text'] }}</strong></div>
            <a href="{{ route('learner.invoice') }}" class="db-alert__action">Se faktura</a>
        </div>
    @endif

    @if($payLaterAlert)
        <div class="db-alert db-alert--warning" style="margin-bottom: 1rem;">
            <div class="db-alert__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e65100" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="db-alert__text">Husk å velge betalingsløsning (for deg som har valgt «bestill nå, betal senere»).</div>
            <a href="{{ route('learner.invoice', ['tab' => 'pay-later']) }}" class="db-alert__action">Opprett betalingsløsning</a>
        </div>
    @endif

    @php
        $isPabyggElev = Auth::user()->coursesTaken()->whereHas('package', fn($q) => $q->where('course_id', 120))->where('is_active', 1)->exists();
        $harMeldtPabygg = Auth::user()->coursesTaken()->whereHas('package', fn($q) => $q->where('course_id', 120))->where('is_active', 1)->whereNotNull('pabygg_treff_day')->exists();
    @endphp
    @if($isPabyggElev && !$harMeldtPabygg)
        <div class="db-alert db-alert--warning" style="margin-bottom: 1rem;">
            <div class="db-alert__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="db-alert__text"><strong>Påbyggingstreff 8.–9. mai:</strong> Du har ikke meldt deg på ennå — velg fredag eller lørdag.</div>
            <a href="{{ route('learner.pabygg-treff') }}" class="db-alert__action">Meld deg på</a>
        </div>
    @endif

    {{-- ═══ NEXT UP ═══ --}}
    <div class="db-next-grid">
        <div class="db-next-card">
            <div class="db-next-card__icon db-next-card__icon--task">
                <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="db-next-card__label">Neste oppgave</div>
                @if($nextAssignment)
                    <div class="db-next-card__title">{{ $nextAssignment->title }}</div>
                    @if($nextAssignment->deadline)
                        <div class="db-next-card__meta">Frist: {{ \Carbon\Carbon::parse($nextAssignment->deadline)->format('d.m.Y') }}</div>
                    @endif
                    <button class="db-next-card__action submitManuscriptBtn"
                            data-bs-toggle="modal" data-bs-target="#submitManuscriptModal"
                            data-action="{{ route('learner.assignment.add_manuscript', $nextAssignment->id) }}">
                        Last opp manus
                    </button>
                @else
                    <div class="db-next-card__title" style="color: var(--db-text-muted);">Ingen ventende oppgaver</div>
                    <div class="db-next-card__meta">Du er à jour!</div>
                @endif
            </div>
        </div>
        <div class="db-next-card">
            <div class="db-next-card__icon db-next-card__icon--mentor">
                <svg viewBox="0 0 24 24" fill="none" stroke="#1565c0" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
            </div>
            <div>
                <div class="db-next-card__label">Neste mentormøte</div>
                @if($webinarTimeline->count())
                    @php $nextWebinar = $webinarTimeline->first(); @endphp
                    <div class="db-next-card__title">{{ $nextWebinar->title }}</div>
                    <div class="db-next-card__meta">
                        {{ \Carbon\Carbon::parse($nextWebinar->start_date)->format('d.m.Y') }}
                        kl. {{ \Carbon\Carbon::parse($nextWebinar->start_date)->format('H:i') }}
                    </div>
                    <a href="{{ route('learner.webinar') }}" class="db-next-card__action">Se mentormøter</a>
                @else
                    <div class="db-next-card__title" style="color: var(--db-text-muted);">Ingen kommende</div>
                    <div class="db-next-card__meta">Sjekk kalenderen for oppdateringer.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ QUICK STATS ═══ --}}
    <div class="db-stats">
        <div class="db-stat">
            <div class="db-stat__number">{{ $coursesTaken->count() }}</div>
            <div class="db-stat__label">Aktive kurs</div>
        </div>
        <div class="db-stat">
            <div class="db-stat__number">{{ $webinarTimeline->count() }}</div>
            <div class="db-stat__label">Kommende mentormøter</div>
        </div>
        <div class="db-stat">
            <div class="db-stat__number">{{ $pendingAssignmentCount }}</div>
            <div class="db-stat__label">Oppgaver å levere</div>
        </div>
        <div class="db-stat">
            <div class="db-stat__number">{{ Auth::user()->messages()->count() }}</div>
            <div class="db-stat__label">Beskjeder</div>
        </div>
    </div>

    {{-- ═══ DASHBOARD GRID ═══ --}}
    <div class="db-grid">
        {{-- Left column --}}
        <div>
            {{-- Mine Kurs --}}
            <div class="db-card" style="margin-bottom: 1.5rem;">
                <div class="db-card__header">
                    <h2 class="db-card__title">Mine kurs</h2>
                    <a href="{{ route('learner.course') }}" class="db-card__link">Se alle →</a>
                </div>
                <div class="db-card__body">
                    <div class="db-course-list">
                        @foreach ($coursesTaken as $ct)
                            <a href="{{ route('learner.course.show', ['id' => $ct->id]) }}" class="db-course-item">
                                <div class="db-course-item__thumb">
                                    @if($ct->package && $ct->package->course && $ct->package->course->course_image)
                                        <img src="https://www.forfatterskolen.no/{{ $ct->package->course->course_image }}" alt="" loading="lazy">
                                    @endif
                                </div>
                                <div class="db-course-item__info">
                                    <div class="db-course-item__name">{{ $ct->package->course->title ?? '' }}</div>
                                    <div class="db-course-item__instructor">
                                        {{ $ct->package->course->instructor ?? 'Forfatterskolen' }}
                                    </div>
                                </div>
                                @if($ct->is_active && $ct->hasStarted && !$ct->hasEnded)
                                    <span class="db-course-item__badge db-course-item__badge--active">Aktiv</span>
                                @elseif($ct->is_active && $ct->hasStarted && $ct->hasEnded)
                                    <span class="db-course-item__badge db-course-item__badge--renewal">Forny</span>
                                @elseif(!$ct->is_active)
                                    <span class="db-course-item__badge db-course-item__badge--hold">På vent</span>
                                @endif
                                <span class="db-course-item__arrow">›</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kommende mentormøter --}}
            <div class="db-card">
                <div class="db-card__header">
                    <h2 class="db-card__title">Kommende mentormøter</h2>
                    <a href="{{ route('learner.webinar') }}" class="db-card__link">Se alle →</a>
                </div>
                <div class="db-card__body">
                    @if($webinarTimeline->count())
                        <div class="db-mentor-tl">
                            @foreach($webinarTimeline as $wt)
                                @php
                                    $wtDate = \Carbon\Carbon::parse($wt->start_date);
                                    $isToday = $wtDate->isToday();
                                    $isTomorrow = $wtDate->isTomorrow();
                                @endphp
                                <div class="db-mentor-item">
                                    <div class="db-mentor-item__date">
                                        <div class="db-mentor-item__day">{{ $wtDate->format('d') }}</div>
                                        <div class="db-mentor-item__month">{{ $months_no[$wtDate->month - 1] }}</div>
                                    </div>
                                    <div class="db-mentor-item__info">
                                        <div class="db-mentor-item__name">{{ $wt->title }}</div>
                                        <div class="db-mentor-item__topic">{{ $wt->description ?: '' }}</div>
                                        @if($isToday)
                                            <span class="db-mentor-item__badge-live">I dag</span>
                                        @elseif($isTomorrow)
                                            <span class="db-mentor-item__badge-live">I morgen</span>
                                        @endif
                                    </div>
                                    <span class="db-mentor-item__time">{{ $wtDate->format('H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size: 0.85rem; color: var(--db-text-muted);">Ingen kommende mentormøter.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div>
            {{-- Kalender --}}
            <div class="db-card" style="margin-bottom: 1.5rem;">
                <div class="db-card__header">
                    <h2 class="db-card__title">Kalender</h2>
                    <a href="{{ route('learner.calendar') }}" class="db-card__link">Se alle →</a>
                </div>
                <div class="db-card__body">
                    @php $calCounter = 0; @endphp
                    @foreach($filteredUniqueStart as $start)
                        @if($calCounter < 3)
                            @php $parseStart = \Carbon\Carbon::parse($start); @endphp
                            @foreach($dashboardCalendar as $calendar)
                                @if($calendar['start'] == $start && $calCounter < 3)
                                    <div class="db-cal-item">
                                        <span class="db-cal-item__dot"></span>
                                        <div class="db-cal-item__info">
                                            <div class="db-cal-item__text">{{ $calendar['title'] }}</div>
                                            <div class="db-cal-item__date">{{ $parseStart->format('d') }}. {{ strtolower($months_no[$parseStart->month - 1]) }} {{ $parseStart->format('Y') }} · {{ $parseStart->format('H:i') != '00:00' ? $parseStart->format('H:i') : '' }}</div>
                                        </div>
                                    </div>
                                    @php $calCounter++; @endphp
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    @if($calCounter === 0)
                        <p style="font-size: 0.85rem; color: var(--db-text-muted);">Ingen kommende hendelser.</p>
                    @endif
                </div>
            </div>

            {{-- Skrivefellesskap --}}
            <div class="db-card" style="margin-bottom: 1.5rem;">
                <div class="db-card__header">
                    <h2 class="db-card__title">Skrivefellesskap</h2>
                    <a href="{{ route('learner.community.home') }}" class="db-card__link">Åpne →</a>
                </div>
                <div class="db-card__body">
                    <div class="db-community-box">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <div class="db-community-box__title">Snakk med andre skriveglade</div>
                        <div class="db-community-box__desc">Del tekster, få tilbakemelding og finn skrivevenner i fellesskapet.</div>
                    </div>
                </div>
            </div>

            {{-- Hurtigtilgang --}}
            <div class="db-card">
                <div class="db-card__header">
                    <h2 class="db-card__title">Hurtigtilgang</h2>
                </div>
                <div class="db-card__body">
                    <a href="{{ route('learner.assignment') }}" class="db-quick-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Last opp manus
                    </a>
                    <a href="{{ route('learner.shop-manuscript') }}" class="db-quick-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        Mine manusutviklinger
                    </a>
                    <a href="{{ route('learner.upgrade') }}" class="db-quick-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                        Oppgrader kurspakke
                    </a>
                    <a href="{{ route('learner.change-portal', 'self-publishing') }}" class="db-quick-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Selvpubliseringsportal
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════ MODALS (preserved from original) ═══════════ --}}
<div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <p>{{ trans('site.learner.renew-all.description') }}</p>
                    <div class="text-end margin-top">
                        <button type="submit" class="btn btn-primary">{{ trans('site.front.yes') }}</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ trans('site.front.no') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══ UPLOAD MODAL: Editor (DOC/DOCX only) ═══ --}}
<div id="submitEditorManuscriptModal" class="um-modal modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
                {{ csrf_field() }}
                <div class="um-header">
                    <h3>Last opp manus</h3>
                    <button type="button" class="um-close" data-bs-dismiss="modal" aria-label="Lukk">
                        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="um-body">
                    {{-- Upload zone --}}
                    <div class="um-upload-zone" id="umZoneEditor" onclick="document.getElementById('umFileEditor').click()">
                        <div class="um-upload-zone__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div class="um-upload-zone__title">Dra og slipp filen din her</div>
                        <div class="um-upload-zone__sub">eller klikk for å velge</div>
                        <div class="um-upload-zone__formats">
                            <span class="um-format-tag">.docx</span>
                            <span class="um-format-tag">.doc</span>
                        </div>
                        <input type="file" id="umFileEditor" required name="filename"
                               accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>

                    {{-- Genre --}}
                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label>Sjanger</label>
                            <select name="type" required>
                                <option value="" disabled selected>Velg sjanger</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Hvor i manuset --}}
                    <div class="um-segment-label">Hvor i manuset er teksten fra?</div>
                    <div class="um-segment-buttons">
                        @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                            <button type="button" class="um-segment-btn" data-value="{{ $manu['id'] }}" onclick="umSelectSegment(this)">{{ $manu['option'] }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="manu_type" required class="um-manu-type-input">

                    {{-- Word note --}}
                    <div class="um-word-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        Godkjente filformater er DOC og DOCX.
                    </div>
                </div>
                <div class="um-footer">
                    <button type="button" class="um-btn um-btn--secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="submit" class="um-btn um-btn--primary">Last opp manus</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ UPLOAD MODAL: Regular (DOC/DOCX/PDF/ODT/Pages) ═══ --}}
<div id="submitManuscriptModal" class="um-modal modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data" onsubmit="disableSubmit(this);">
                {{ csrf_field() }}
                <div class="um-header">
                    <h3>Last opp manus</h3>
                    <button type="button" class="um-close" data-bs-dismiss="modal" aria-label="Lukk">
                        <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="um-body">
                    @if($nextAssignment)
                    <div class="um-context">
                        <div class="um-context-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="um-context-text">
                            <strong>{{ $nextAssignment->title }}</strong><br>
                            @if($nextAssignment->max_word)
                                Maks {{ $nextAssignment->max_word }} ord
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Upload zone --}}
                    <div class="um-upload-zone" id="umZoneRegular" onclick="document.getElementById('umFileRegular').click()">
                        <div class="um-upload-zone__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div class="um-upload-zone__title">Dra og slipp filen din her</div>
                        <div class="um-upload-zone__sub">eller klikk for å velge</div>
                        <div class="um-upload-zone__formats">
                            <span class="um-format-tag">.docx</span>
                            <span class="um-format-tag">.doc</span>
                            <span class="um-format-tag">.pdf</span>
                            <span class="um-format-tag">.odt</span>
                            <span class="um-format-tag">.pages</span>
                        </div>
                        <input type="file" id="umFileRegular" required name="filename"
                               accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text, .pages">
                    </div>

                    {{-- Genre --}}
                    <div class="um-form-row">
                        <div class="um-form-group">
                            <label>Sjanger</label>
                            <select name="type" required>
                                <option value="" disabled selected>Velg sjanger</option>
                                @foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Hvor i manuset --}}
                    <div class="um-segment-label">Hvor i manuset er teksten fra?</div>
                    <div class="um-segment-buttons">
                        @foreach(\App\Http\FrontendHelpers::manuscriptType() as $manu)
                            <button type="button" class="um-segment-btn" data-value="{{ $manu['id'] }}" onclick="umSelectSegment(this)">{{ $manu['option'] }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="manu_type" required class="um-manu-type-input">

                    {{-- Word note --}}
                    <div class="um-word-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        PDF, ODT og Pages konverteres automatisk til DOCX.
                    </div>
                </div>
                <div class="um-footer">
                    <button type="button" class="um-btn um-btn--secondary" data-bs-dismiss="modal">Avbryt</button>
                    <button type="submit" class="um-btn um-btn--primary">Last opp manus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editManuscriptModal" class="global-modal modal fade" role="dialog">
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
                        <label>{{ trans('site.learner.manuscript-text') }}</label>
                        <input type="file" class="form-control" required name="filename" accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                        * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                    </div>
                    <button type="submit" class="btn red-global-btn float-end margin-top">{{ trans('site.front.submit') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="deleteManuscriptModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ trans('site.learner.delete-manuscript.title') }}</h3>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{ trans('site.learner.delete-manuscript.question') }}
                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-danger float-end margin-top">{{ trans('site.learner.delete') }}</button>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="errorMaxword" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
        {{ strtr(trans('site.learner.error-max-word-text'), ['_word_count_' => Session::get('editorMaxWord')]) }}
    </div></div></div>
</div>

<div id="submitSuccessModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
        {{ trans('site.learner.submit-success-text') }}
    </div></div></div>
</div>

@if (Auth::user()->need_pass_update)
    <button class="passUpdateBtn hidden" data-bs-toggle="modal" data-bs-target="#passUpdateModal"></button>
    <div class="modal fade" role="dialog" id="passUpdateModal" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.15);">
                <div style="background:linear-gradient(135deg,#862736 0%,#a83347 100%);padding:32px 32px 24px;text-align:center;">
                    <div style="width:56px;height:56px;background:rgba(255,255,255,.15);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;">
                        <svg width="28" height="28" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>
                    </div>
                    <h3 style="color:#fff;font-family:'Lora',serif;font-size:22px;font-weight:500;margin:0 0 6px;">Velkommen, {{ Auth::user()->first_name }}!</h3>
                    <p style="color:rgba(255,255,255,.8);font-size:14px;margin:0;">Opprett et passord for enkel innlogging senere</p>
                </div>
                <div style="padding:28px 32px 32px;">
                    <form action="{{ route('learner.password.update') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
                        {{ csrf_field() }}
                        <label style="font-size:13px;font-weight:600;color:#333;display:block;margin-bottom:6px;">Velg passord</label>
                        <input type="password" name="password" placeholder="Minst 8 tegn" required minlength="8"
                               style="width:100%;padding:12px 16px;border:1.5px solid #ddd;border-radius:10px;font-size:15px;font-family:inherit;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='#862736'" onblur="this.style.borderColor='#ddd'">
                        @if ($errors->has('password'))
                            <div style="color:#dc3545;font-size:13px;margin-top:6px;">{{ $errors->first('password') }}</div>
                        @endif
                        <button type="submit" style="width:100%;margin-top:16px;padding:13px;background:#862736;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .2s;"
                                onmouseover="this.style.background='#6e1e2b'" onmouseout="this.style.background='#862736'">
                            Opprett passord
                        </button>
                    </form>
                    <div style="text-align:center;margin-top:14px;">
                        <button type="button" class="close" data-bs-dismiss="modal" style="background:none;border:none;color:#999;font-size:13px;cursor:pointer;">Hopp over for nå</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if (Session::has('passUpdated'))
    <button class="passUpdatedBtn hidden" data-bs-toggle="modal" data-bs-target="#passUpdatedModal"></button>
    <div id="passUpdatedModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-body text-center">
            <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            <div style="color: green; font-size: 24px"><i class="fa fa-check"></i></div>
            <p>{{ trans('site.learner.update-password.success-text') }}</p>
        </div></div></div>
    </div>
@endif
@stop

@section('scripts')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    @if (Auth::user()->need_pass_update)
        $(".passUpdateBtn").trigger('click');
    @endif
    @if (Session::has('passUpdated'))
        $(".passUpdatedBtn").trigger('click');
    @endif
    @if (Session::has('success'))
        $('#submitSuccessModal').modal('show');
    @endif
    @if (Session::has('errorMaxWord'))
        $('#errorMaxword').modal('show');
    @endif

    $(".renewAllBtn").click(function(){ $('#renewAllModal').find('form').attr('action', $(this).data('action')); });
    $('.submitEditorManuscriptBtn').click(function(){ $('#submitEditorManuscriptModal').find('form').attr('action', $(this).data('action')); });
    $('.submitManuscriptBtn').click(function(){ $('#submitManuscriptModal').find('form').attr('action', $(this).data('action')); });
    $('.editManuscriptBtn').click(function(){ $('#editManuscriptModal').find('form').attr('action', $(this).data('action')); });
    $('.deleteManuscriptBtn').click(function(){ $('#deleteManuscriptModal').find('form').attr('action', $(this).data('action')); });

    $(".webinar-auto-register-toggle").change(function(){
        $.ajax({
            type:'POST',
            url:'/account/webinar-auto-register-update',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { 'auto_renew' : $(this).prop('checked') ? 1 : 0 },
            success: function(data){}
        });
    });

    // ── Upload modal: segment buttons ──
    window.umSelectSegment = function(btn) {
        var parent = btn.closest('.um-body');
        parent.querySelectorAll('.um-segment-btn').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        parent.querySelector('.um-manu-type-input').value = btn.getAttribute('data-value');
    };

    // ── Upload modal: drag & drop + file display ──
    document.querySelectorAll('.um-upload-zone').forEach(function(zone) {
        var fileInput = zone.querySelector('input[type="file"]');

        zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', function() { zone.classList.remove('dragover'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                umShowSelectedFile(zone, fileInput);
            }
        });

        fileInput.addEventListener('change', function() {
            umShowSelectedFile(zone, fileInput);
        });
    });

    function umShowSelectedFile(zone, fileInput) {
        if (!fileInput.files.length) return;
        var file = fileInput.files[0];
        var sizeKB = Math.round(file.size / 1024);

        // Replace zone content with file info
        zone.classList.add('um-upload-zone--selected');
        zone.setAttribute('onclick', ''); // disable click-to-browse
        zone.innerHTML = '<div class="um-upload-file">' +
            '<div class="um-upload-file__icon"><svg viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></div>' +
            '<div class="um-upload-file__info"><div class="um-upload-file__name">' + file.name + '</div><div class="um-upload-file__size">' + sizeKB + ' KB</div></div>' +
            '<button type="button" class="um-upload-file__remove" aria-label="Fjern fil" onclick="umRemoveFile(this)"><svg viewBox="0 0 24 24" fill="none" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>' +
            '</div>';
        // Re-append hidden input
        zone.appendChild(fileInput);
    }

    window.umRemoveFile = function(btn) {
        var zone = btn.closest('.um-upload-zone');
        var fileInput = zone.querySelector('input[type="file"]');
        var inputId = fileInput.id;

        // Reset to empty state
        fileInput.value = '';
        zone.classList.remove('um-upload-zone--selected');
        zone.setAttribute('onclick', "document.getElementById('" + inputId + "').click()");
        zone.innerHTML = '<div class="um-upload-zone__icon"><svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></div>' +
            '<div class="um-upload-zone__title">Dra og slipp filen din her</div>' +
            '<div class="um-upload-zone__sub">eller klikk for å velge</div>' +
            '<div class="um-upload-zone__formats">' +
            (inputId === 'umFileEditor' ? '<span class="um-format-tag">.docx</span><span class="um-format-tag">.doc</span>' :
            '<span class="um-format-tag">.docx</span><span class="um-format-tag">.doc</span><span class="um-format-tag">.pdf</span><span class="um-format-tag">.odt</span><span class="um-format-tag">.pages</span>') +
            '</div>';
        zone.appendChild(fileInput);
    };
</script>
@stop
