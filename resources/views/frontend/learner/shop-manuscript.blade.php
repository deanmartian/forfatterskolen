{{-- Manusutviklinger — redesignet --}}
@extends('frontend.layouts.course-portal')

@section('page_title', 'Manusutviklinger &rsaquo; Forfatterskolen')

@section('heading') Manusutviklinger @stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<style>
/* ── RESET / LAYOUT ──────────────────────────────── */
#topbar { display: none !important; }
#main-content { padding-top: 0 !important; margin-top: 0 !important; overflow-x: hidden !important; max-width: 100vw; }
#main-container { overflow-x: hidden !important; }

.mu-redesign {
	font-family: 'Source Sans 3', -apple-system, sans-serif;
	max-width: 880px;
	margin: 0 auto;
	padding: 2rem;
	-webkit-font-smoothing: antialiased;
}

/* ── SIDEBAR TOGGLE — vinrød, stor og tydelig ────── */
.mu-redesign .mu-sidebar-toggle {
	display: none; position: fixed; top: 16px; left: 16px; z-index: 1050;
	width: 50px; height: 50px; border-radius: 14px; border: 2px solid rgba(255,255,255,0.3);
	background: #862736; align-items: center; justify-content: center; cursor: pointer;
	box-shadow: 0 4px 16px rgba(134, 39, 54, 0.4), 0 0 0 3px rgba(134, 39, 54, 0.15);
	padding: 0; transition: background 0.15s, box-shadow 0.15s, transform 0.15s;
}
.mu-redesign .mu-sidebar-toggle:hover { background: #9c2e40; transform: scale(1.05); }
.mu-redesign .mu-sidebar-toggle:active { transform: scale(0.96); }
.mu-redesign .mu-sidebar-toggle svg { width: 24px; height: 24px; stroke: #fff; stroke-width: 2.5; }
@media (max-width: 1026px) { .mu-redesign .mu-sidebar-toggle { display: flex !important; } }

/* ── PAGE HEADER ─────────────────────────────────── */
.mu-header { margin-bottom: 0.5rem; }
.mu-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: #1a1a1a; }
.mu-header p { font-size: 0.875rem; color: #5a5550; margin-bottom: 1.5rem; }

/* ── SECTION LABELS ──────────────────────────────── */
.mu-section-label {
	font-size: 0.7rem; font-weight: 600; letter-spacing: 1.5px;
	text-transform: uppercase; color: #8a8580; margin-bottom: 0.75rem;
}

/* ── MANUSCRIPT CARDS ────────────────────────────── */
.mu-list { display: flex; flex-direction: column; gap: 0.85rem; margin-bottom: 2.5rem; }

.mu-card {
	background: #fff; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; padding: 1.5rem;
	transition: border-color 0.15s, box-shadow 0.15s;
}
.mu-card:hover { border-color: rgba(0,0,0,0.12); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
.mu-card--active { border-left: 3px solid #862736; }

/* Top row */
.mu-card__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
.mu-card__title { font-size: 1.1rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.2rem; }
.mu-card__package { font-size: 0.78rem; color: #8a8580; }
.mu-card__badges { display: flex; gap: 0.35rem; flex-shrink: 0; }

.mu-badge {
	font-size: 0.65rem; font-weight: 600;
	padding: 0.2rem 0.55rem; border-radius: 4px; white-space: nowrap;
}
.mu-badge--not-started { background: rgba(0,0,0,0.05); color: #8a8580; }
.mu-badge--waiting { background: #fff3e0; color: #e65100; }
.mu-badge--in-progress { background: #e3f2fd; color: #1565c0; }
.mu-badge--done { background: #e8f5e9; color: #2e7d32; }

/* Detail grid */
.mu-card__details {
	display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
	gap: 0.75rem; padding: 1rem 0;
	border-top: 1px solid rgba(0,0,0,0.08); border-bottom: 1px solid rgba(0,0,0,0.08);
	margin-bottom: 1rem;
}
.mu-detail { display: flex; flex-direction: column; gap: 0.1rem; }
.mu-detail__label { font-size: 0.68rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #8a8580; }
.mu-detail__value { font-size: 0.875rem; font-weight: 600; color: #1a1a1a; }
.mu-detail__value--muted { color: #8a8580; font-weight: 400; }

/* Actions */
.mu-card__actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.mu-btn {
	display: inline-flex; align-items: center; gap: 0.3rem;
	padding: 0.5rem 1rem; border-radius: 6px;
	font-family: inherit; font-size: 0.8rem; font-weight: 600;
	text-decoration: none; cursor: pointer; border: none; transition: all 0.15s;
}
.mu-btn svg { width: 14px; height: 14px; }
.mu-btn--primary { background: #862736; color: #fff; }
.mu-btn--primary:hover { background: #9c2e40; color: #fff; text-decoration: none; }
.mu-btn--secondary { background: transparent; color: #5a5550; border: 1px solid rgba(0,0,0,0.12); }
.mu-btn--secondary:hover { border-color: #862736; color: #862736; text-decoration: none; }
.mu-btn--download { background: transparent; color: #862736; border: 1px solid #862736; }
.mu-btn--download:hover { background: #862736; color: #fff; text-decoration: none; }
.mu-btn--danger { background: transparent; color: #c62828; border: 1px solid rgba(198,40,40,0.3); padding: 0.4rem 0.7rem; }
.mu-btn--danger:hover { background: #c62828; color: #fff; text-decoration: none; }
.mu-btn--success { background: transparent; color: #2e7d32; border: 1px solid rgba(46,125,50,0.3); padding: 0.4rem 0.7rem; }
.mu-btn--success:hover { background: #2e7d32; color: #fff; text-decoration: none; }

/* ── CTA CARD ────────────────────────────────────── */
.mu-cta {
	background: #faf8f5; border: 1px solid rgba(0,0,0,0.08);
	border-radius: 14px; padding: 2rem; text-align: center;
}
.mu-cta__title { font-size: 1rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.35rem; }
.mu-cta__desc { font-size: 0.85rem; color: #5a5550; margin-bottom: 1.25rem; }

/* ── EMPTY STATE ─────────────────────────────────── */
.mu-empty { text-align: center; padding: 2rem 1rem; color: #8a8580; font-size: 0.9rem; }

/* ── RESPONSIVE ──────────────────────────────────── */
@media (max-width: 768px) {
	.mu-redesign { padding: 1.25rem 1rem; padding-top: 80px; }
	.mu-card__top { flex-direction: column; gap: 0.5rem; }
	.mu-card__details { grid-template-columns: 1fr 1fr; }
	.mu-card__actions { flex-direction: column; }
	.mu-btn { justify-content: center; }
}
@media (max-width: 480px) {
	.mu-redesign { padding: 1rem 0.75rem; padding-top: 76px; }
	.mu-card { padding: 1.1rem; }
	.mu-header h1 { font-size: 1.3rem; }
}
</style>
@stop

@section('content')
<div class="mu-redesign">

	{{-- Mobile sidebar toggle --}}
	<button class="mu-sidebar-toggle" data-sidebar-toggle aria-label="Meny">
		<svg viewBox="0 0 24 24" fill="none" stroke-linecap="round">
			<line x1="4" y1="7" x2="20" y2="7"/>
			<line x1="4" y1="12" x2="20" y2="12"/>
			<line x1="4" y1="17" x2="20" y2="17"/>
		</svg>
	</button>

	<div class="mu-header">
		<h1>Manusutviklinger</h1>
		<p>Dine bestillinger for profesjonell tilbakemelding på manus.</p>
	</div>

	{{-- ═══════════ AKTIVE / IKKE STARTET ═══════════ --}}
	@if($active->count() > 0)
		<div class="mu-section-label">Aktive ({{ $active->count() }})</div>
		<div class="mu-list">
			@foreach($active as $m)
				<div class="mu-card mu-card--active">
					<div class="mu-card__top">
						<div>
							<h3 class="mu-card__title">{{ $m->shop_manuscript->title }}</h3>
							<p class="mu-card__package">{{ $m->shop_manuscript->description }}</p>
						</div>
						<div class="mu-card__badges">
							@if($m->status === 'Not started')
								<span class="mu-badge mu-badge--not-started">Ikke startet</span>
							@elseif($m->status === 'Started')
								<span class="mu-badge mu-badge--waiting">Venter på tilbakemelding</span>
							@elseif($m->status === 'Pending')
								<span class="mu-badge mu-badge--in-progress">Under arbeid</span>
							@endif
						</div>
					</div>

					<div class="mu-card__details">
						<div class="mu-detail">
							<span class="mu-detail__label">Status</span>
							<span class="mu-detail__value">
								@if($m->status === 'Not started')
									Venter på manus
								@elseif($m->status === 'Started')
									Manus innlevert
								@elseif($m->status === 'Pending')
									Tilbakemelding under vurdering
								@endif
							</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Maks ord</span>
							<span class="mu-detail__value">{{ number_format($m->shop_manuscript->max_words, 0, ',', ' ') }}</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Innlevert</span>
							<span class="mu-detail__value {{ $m->manuscript_uploaded_date ? '' : 'mu-detail__value--muted' }}">
								{{ $m->manuscript_uploaded_date ? \Carbon\Carbon::parse($m->manuscript_uploaded_date)->format('d.m.Y') : '—' }}
							</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Forventet ferdig</span>
							<span class="mu-detail__value {{ $m->getRawOriginal('expected_finish') ? '' : 'mu-detail__value--muted' }}">
								{{ $m->expected_finish ?? '—' }}
							</span>
						</div>
					</div>

					<div class="mu-card__actions">
						@if($m->is_active)
							@if($m->status === 'Not started')
								<button type="button" class="mu-btn mu-btn--primary uploadManuscriptBtn"
										data-bs-toggle="modal" data-bs-target="#uploadManuscriptModal"
										data-action="{{ route('learner.shop-manuscript.upload', $m->id) }}">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
									Last opp manus
								</button>
							@else
								<a class="mu-btn mu-btn--secondary"
								   href="{{ route('learner.shop-manuscript.show', $m->id) }}">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
									Se manuskript
								</a>
								@if(!$m->is_manuscript_locked)
									<button class="mu-btn mu-btn--success updateManuscriptBtn" type="button"
											data-bs-toggle="modal" data-bs-target="#updateUploadedManuscriptModal"
											data-fields="{{ json_encode($m) }}"
											data-action="{{ route('learner.shop-manuscript.update-uploaded-manuscript', $m->id) }}">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
										Rediger
									</button>
									<button class="mu-btn mu-btn--danger deleteManuscriptBtn" type="button"
											data-bs-toggle="modal" data-bs-target="#deleteUploadedManuscriptModal"
											data-action="{{ route('learner.shop-manuscript.delete-uploaded-manuscript', $m->id) }}">
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
										Slett
									</button>
								@endif
							@endif
						@else
							<span class="mu-btn mu-btn--secondary" style="opacity: 0.6; cursor: default;">Venter på aktivering</span>
						@endif
					</div>
				</div>
			@endforeach
		</div>
	@endif

	{{-- ═══════════ FULLFØRTE ═══════════ --}}
	@if($completed->count() > 0)
		<div class="mu-section-label">Fullførte ({{ $completed->count() }})</div>
		<div class="mu-list">
			@foreach($completed as $m)
				<div class="mu-card">
					<div class="mu-card__top">
						<div>
							<h3 class="mu-card__title">{{ $m->shop_manuscript->title }}</h3>
							<p class="mu-card__package">{{ $m->shop_manuscript->description }}</p>
						</div>
						<div class="mu-card__badges">
							<span class="mu-badge mu-badge--done">Ferdig</span>
						</div>
					</div>

					<div class="mu-card__details">
						<div class="mu-detail">
							<span class="mu-detail__label">Ordtall</span>
							<span class="mu-detail__value">{{ $m->words ? number_format($m->words, 0, ',', ' ') : '—' }}</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Innlevert</span>
							<span class="mu-detail__value {{ $m->manuscript_uploaded_date ? '' : 'mu-detail__value--muted' }}">
								{{ $m->manuscript_uploaded_date ? \Carbon\Carbon::parse($m->manuscript_uploaded_date)->format('d.m.Y') : '—' }}
							</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Ferdig</span>
							<span class="mu-detail__value">
								@if($m->completed_at)
									{{ $m->completed_at->format('d.m.Y') }}
								@elseif($m->getRawOriginal('expected_finish'))
									{{ $m->expected_finish }}
								@else
									—
								@endif
							</span>
						</div>
						<div class="mu-detail">
							<span class="mu-detail__label">Inkluderer</span>
							<span class="mu-detail__value" style="font-size: 0.78rem; font-weight: 400;">Tilbakemelding + margkommentarer + synopsis</span>
						</div>
					</div>

					<div class="mu-card__actions">
						<a class="mu-btn mu-btn--secondary"
						   href="{{ route('learner.shop-manuscript.show', $m->id) }}">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
							Se manuskript
						</a>
						@php $feedback = $m->feedbacks->first(); @endphp
						@if($feedback)
							<a href="{{ route('learner.shop-manuscript.download-feedback', [$m->id, $feedback->id]) }}?v={{ time() }}"
							   class="mu-btn mu-btn--download">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
								Last ned tilbakemelding
							</a>
						@endif
					</div>
				</div>
			@endforeach
		</div>
	@endif

	{{-- ═══════════ TOM TILSTAND ═══════════ --}}
	@if($active->count() === 0 && $completed->count() === 0)
		<div class="mu-empty">
			<p>Du har ingen manusutviklinger ennå.</p>
		</div>
	@endif

	{{-- ═══════════ BESTILL FLERE ═══════════ --}}
	<div class="mu-cta">
		<div class="mu-cta__title">Trenger du tilbakemelding på mer tekst?</div>
		<div class="mu-cta__desc">Bestill en ny manusutvikling og få profesjonell vurdering fra våre redaktører.</div>
		<a href="{{ route('front.shop-manuscript.index') }}" class="mu-btn mu-btn--primary">Bestill manusutvikling →</a>
	</div>

</div>

{{-- ═══════════ MODALER ═══════════ --}}
<div id="uploadManuscriptModal" class="modal fade global-modal" role="dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Last opp manus</h3>
		  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
      	<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
      		{{ csrf_field() }}
                <div class="form-group mb-3">
                    <label>* Kun .doc, .docx, .pdf, .odt eller .pages</label>
                    <input type="file" class="form-control" required name="manuscript"
                            accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                            application/pdf, application/vnd.oasis.opendocument.text, application/vnd.apple.pages, .doc, .docx, .pdf, .odt, .pages">
                    <input type="hidden" name="word_count" value="">
                    <p class="text-info manuscript-conversion-message d-none mt-2">{{ trans('site.converting-document-please-wait') }}</p>
                    <p class="text-danger manuscript-conversion-error d-none mt-2"></p>
                </div>
			<div class="form-group mb-3">
				<label>Sjanger</label>
				<select class="form-control" name="genre" required>
					<option value="" disabled="disabled" selected>Velg sjanger</option>
					@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
						<option value="{{ $type->id }}"> {{ $type->name }} </option>
					@endforeach
				</select>
			</div>
			<div class="form-group mb-3">
				<label>Synopsis (valgfritt)</label>
                <input type="file" class="form-control" name="synopsis"
                    accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                     application/pdf, application/vnd.oasis.opendocument.text, application/vnd.apple.pages, .doc, .docx, .pdf, .odt, .pages">
			</div>
			<div class="form-group mb-3">
				<label>Beskrivelse av manuset</label>
				<textarea name="description" cols="30" rows="10" class="form-control"></textarea>
			</div>
      		<button type="submit" class="mu-btn mu-btn--primary float-end">Last opp manus</button>
      		<div class="clearfix"></div>
      	</form>
      </div>
    </div>
  </div>
</div>

<div id="updateUploadedManuscriptModal" class="modal fade global-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Oppdater manus</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
                    <div class="form-group mb-3">
                        <label>* Kun .doc, .docx, .pdf, .odt eller .pages</label>
                        <input type="file" class="form-control" required name="manuscript"
                            accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                            application/pdf, application/vnd.oasis.opendocument.text, application/vnd.apple.pages, .doc, .docx, .pdf, .odt, .pages">
                        <input type="hidden" name="word_count" value="">
                        <p class="text-info manuscript-conversion-message d-none mt-2">{{ trans('site.converting-document-please-wait') }}</p>
                        <p class="text-danger manuscript-conversion-error d-none mt-2"></p>
                    </div>
					<div class="form-group mb-3">
						<label>Sjanger</label>
						<select class="form-control" name="genre" required>
							<option value="" disabled="disabled" selected>Velg sjanger</option>
							@foreach(\App\Http\FrontendHelpers::assignmentType() as $type)
								<option value="{{ $type->id }}"> {{ $type->name }} </option>
							@endforeach
						</select>
					</div>
					<div class="form-group mb-3 synopsis">
						<label>Synopsis (valgfritt)</label>
                        <input type="file" class="form-control" name="synopsis"
                            accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                            application/pdf, application/vnd.oasis.opendocument.text, application/vnd.apple.pages, .doc, .docx, .pdf, .odt, .pages">
					</div>
					<div class="form-group mb-3 synopsis">
						<label>Vil du bruke coachingtiden i manuset?</label>
						<input type="checkbox" data-bs-toggle="toggle" data-on="Ja"
							   class="is-free-toggle" data-off="Nei"
							   name="coaching_time_later">
					</div>
					<div class="form-group mb-3">
						<label>Beskrivelse av manuset</label>
						<textarea name="description" cols="30" rows="10" class="form-control"></textarea>
					</div>
					<button type="submit" class="mu-btn mu-btn--primary float-end">Oppdater manus</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteUploadedManuscriptModal" class="modal fade global-modal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Slett manus</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					<p>Er du sikker på at du vil slette dette manuskriptet?</p>
					<div class="clearfix"></div>
					<button type="submit" class="mu-btn mu-btn--danger float-end">Slett</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="exceedModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">{{ trans('site.learner.upgrade') }}</h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="exceed_message">
					<p>
						{!! str_replace(['_break_', '_exceed_', '_max_words_'],
						['<br/>', session('exceed'), session('max_words')] ,
						trans('site.learner.upgrade-exceed-message')) !!}
					</p>
					<button class="btn btn-light" data-bs-dismiss="modal">Lukk</button>
					<a href="{{ url('upgrade-manuscript/'.session('plan').'/checkout') }}" class="mu-btn mu-btn--primary float-end">Oppgrader</a>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>

@if(Session::has('manuscript_test_error'))
	<div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body text-center">
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					<div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
					{!! Session::get('manuscript_test_error') !!}
				</div>
			</div>
		</div>
	</div>
@endif

@if (session('exceed'))
	<input type="hidden" name="exceed">
	<button class="btn btn-success exceedBtn d-none" type="button"
		data-bs-toggle="modal" data-bs-target="#exceedModal">
	</button>
@endif

@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<script src="https://unpkg.com/mammoth@1.4.21/mammoth.browser.min.js"></script>
<script>
	var has_exceed = $("input[name=exceed]").length;
	let translations = {
		convertingPleaseWait : "{{ trans('site.converting-document-please-wait') }}",
		couldNotConvertTryAgain : "{{ trans('site.could-not-convert-file-please-try-again') }}",
		releaseToUpload : "{{ trans('site.release-to-upload') }}",
	};

	if (has_exceed) {
		$(".exceedBtn").trigger('click');
	}

	@if(Session::has('manuscript_test_error'))
		$('#manuscriptTestErrorModal').modal('show');
	@endif

	$('.uploadManuscriptBtn').click(function(){
		var form = $('#uploadManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

	$(".updateManuscriptBtn").click(function(){
		var modal = $('#updateUploadedManuscriptModal');
		var form = $('#updateUploadedManuscriptModal form');
		var fields = $(this).data('fields');
		var action = $(this).data('action');
		if (fields.genre) {
			modal.find('select').val(fields.genre);
		}
		form.attr('action', action);
		modal.find('textarea[name=description]').text(fields.description);
		if (fields.shop_manuscript_id === 9) {
			modal.find('.synopsis').addClass('hide');
		} else {
			modal.find('.synopsis').removeClass('hide');
			if (fields.coaching_time_later) {
				$("input[name=coaching_time_later]").bootstrapToggle('on');
			} else {
				$("input[name=coaching_time_later]").bootstrapToggle('off');
			}
		}
	});

	$('.deleteManuscriptBtn').click(function(){
		var form = $('#deleteUploadedManuscriptModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

	/* Auto-collapse sidebar on mobile */
	setTimeout(function() {
		var sidebar = document.getElementById('sidebar');
		var mainContainer = document.getElementById('main-container');
		if (window.innerWidth <= 1026 && sidebar) {
			sidebar.classList.remove('sidebar-visible');
			if (mainContainer) mainContainer.classList.remove('enlarge');
			document.body.classList.remove('sidebar-open');
		}
	}, 150);

	(function() {
		const parseErrorBlob = async (blob) => {
			if (!blob || typeof blob.text !== 'function') return null;
			const text = await blob.text();
			if (!text) return null;
			try { return JSON.parse(text); } catch (error) { return { message: text }; }
		};

		const getCsrfToken = () => {
			const csrfMeta = document.querySelector('meta[name="csrf-token"]');
			if (!csrfMeta) return null;
			const token = csrfMeta.getAttribute('content');
			return typeof token === 'string' && token.trim() !== '' ? token : null;
		};

		const mammothPreferredExtensions = ['doc', 'docx'];
		const mammothAvailable = typeof window !== 'undefined'
			&& typeof window.mammoth !== 'undefined'
			&& typeof window.mammoth.extractRawText === 'function';

		const shouldUseMammothForExtension = (extension) => {
			if (!extension || typeof extension !== 'string') return false;
			return mammothPreferredExtensions.includes(extension.toLowerCase()) && mammothAvailable;
		};

		const countWordsFromText = (text) => {
			if (typeof text !== 'string') return 0;
			const normalised = text.replace(/[\r\n\t]+/g, ' ').trim();
			if (!normalised) return 0;
			const matches = normalised.match(/\S+/g);
			return matches ? matches.length : 0;
		};

		const extractWordCountWithMammoth = (file) => new Promise((resolve, reject) => {
			if (!file || !mammothAvailable) { resolve(null); return; }
			const reader = new FileReader();
			reader.onload = (event) => {
				const arrayBuffer = event.target ? event.target.result : null;
				if (!arrayBuffer) { resolve(null); return; }
				window.mammoth.extractRawText({ arrayBuffer })
					.then((result) => {
						const text = result && typeof result.value === 'string' ? result.value : '';
						resolve(countWordsFromText(text));
					})
					.catch((error) => reject(error));
			};
			reader.onerror = () => reject(reader.error || new Error('Kunne ikke lese dokumentet.'));
			try { reader.readAsArrayBuffer(file); } catch (error) { reject(error); }
		});

		const createDocxFileName = (originalName) => {
			if (!originalName || typeof originalName !== 'string') return 'document.docx';
			const dotIndex = originalName.lastIndexOf('.');
			if (dotIndex <= 0) return originalName.toLowerCase().endsWith('.docx') ? originalName : originalName + '.docx';
			const baseName = originalName.substring(0, dotIndex);
			const extension = originalName.substring(dotIndex + 1).toLowerCase();
			if (extension === 'docx') return originalName;
			return baseName + '.docx';
		};

		const extractFilenameFromContentDisposition = (header) => {
			if (!header || typeof header !== 'string') return null;
			const utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
			if (utf8Match && utf8Match[1]) {
				try { return decodeURIComponent(utf8Match[1]); } catch (error) { console.error('Failed to decode UTF-8 filename', error); }
			}
			const quotedMatch = header.match(/filename="?([^";]+)"?/i);
			if (quotedMatch && quotedMatch[1]) return quotedMatch[1];
			return null;
		};

		const convertFileToDocx = async (file) => {
			const formData = new FormData();
			formData.append('document', file);
			const csrfToken = getCsrfToken();
			if (csrfToken) formData.append('_token', csrfToken);
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
						try { const parsed = await parseErrorBlob(error.response.data); if (parsed) error.response.data = parsed; } catch (parseError) { console.error('Failed to parse conversion error response', parseError); }
					}
					if (!error.response || !error.response.data) {
						error.response = error.response || {};
						error.response.data = { errors: { manuscript: [translations.couldNotConvertTryAgain] }, message: translations.couldNotConvertTryAgain };
					}
					throw error;
				}
			}

			const headers = { 'X-Requested-With': 'XMLHttpRequest' };
			if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
			const response = await fetch('/documents/convert-to-docx', { method: 'POST', body: formData, headers });
			const contentDisposition = response.headers ? (response.headers.get('content-disposition') || response.headers.get('Content-Disposition')) : null;
			if (!response.ok) {
				const error = new Error(translations.couldNotConvertTryAgain);
				let errorData = null;
				try { errorData = await response.clone().json(); } catch (jsonError) { try { errorData = { message: await response.text() }; } catch (textError) { errorData = null; } }
				error.response = { status: response.status, data: errorData || { errors: { manuscript: [translations.couldNotConvertTryAgain] }, message: translations.couldNotConvertTryAgain } };
				throw error;
			}
			const data = await response.blob();
			const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
			const responseBlob = data instanceof Blob ? data : new Blob([data], { type: mimeType });
			return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
		};

		const getFileExtension = (filename) => {
			if (!filename || typeof filename !== 'string') return '';
			const parts = filename.split('.');
			return parts.length > 1 ? parts.pop().toLowerCase() : '';
		};

		const getErrorMessageFromConversion = (error) => {
			if (!error) return translations.couldNotConvertTryAgain;
			if (error.response && error.response.data) {
				const data = error.response.data;
				if (data.errors && data.errors.manuscript && data.errors.manuscript.length) return data.errors.manuscript[0];
				if (typeof data.message === 'string' && data.message.trim() !== '') return data.message;
			}
			if (error.message && error.message.trim() !== '') return error.message;
			return translations.couldNotConvertTryAgain;
		};

		const assignFilesToInput = (input, file) => {
			if (!input || !file) return false;
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
		};

		const setFormConversionState = (form, isConverting) => {
			if (!form) return;
			const messageElement = form.querySelector('.manuscript-conversion-message');
			if (messageElement) { if (isConverting) messageElement.classList.remove('d-none'); else messageElement.classList.add('d-none'); }
			const submitButton = form.querySelector('button[type="submit"]');
			if (submitButton) submitButton.disabled = !!isConverting;
		};

		const showConversionError = (form, message) => {
			if (!form) return;
			const errorElement = form.querySelector('.manuscript-conversion-error');
			if (errorElement) { errorElement.textContent = message; errorElement.classList.remove('d-none'); }
			else window.alert(message);
		};

		const clearConversionError = (form) => {
			if (!form) return;
			const errorElement = form.querySelector('.manuscript-conversion-error');
			if (errorElement) { errorElement.textContent = ''; errorElement.classList.add('d-none'); }
		};

		const clearWordCountValue = (form) => {
			if (!form) return;
			const wordCountInput = form.querySelector('input[name="word_count"]');
			if (wordCountInput) wordCountInput.value = '';
		};

		const resetConversionUI = (form) => {
			if (!form) return;
			setFormConversionState(form, false);
			clearConversionError(form);
			clearWordCountValue(form);
		};

		const handleFileChange = async (event) => {
			const input = event.target;
			const form = input.closest ? input.closest('form') : input.form;
			clearConversionError(form);
			clearWordCountValue(form);
			const files = input.files;
			if (!files || !files.length) { setFormConversionState(form, false); return; }
			const [selectedFile] = files;
			if (!selectedFile) { setFormConversionState(form, false); return; }
			const extension = getFileExtension(selectedFile.name || input.value);
			if (extension === 'docx') { setFormConversionState(form, false); return; }
			setFormConversionState(form, true);
			try {
				const convertedFile = await convertFileToDocx(selectedFile);
				const assigned = assignFilesToInput(input, convertedFile);
				if (!assigned) throw new Error('Kunne ikke oppdatere filen etter konvertering. Prøv en annen nettleser.');
				clearConversionError(form);
			} catch (error) {
				showConversionError(form, getErrorMessageFromConversion(error));
				clearWordCountValue(form);
				try { input.value = ''; } catch (resetError) { input.value = null; }
			} finally {
				setFormConversionState(form, false);
			}
		};

		const manuscriptForms = document.querySelectorAll('#uploadManuscriptModal form, #updateUploadedManuscriptModal form');

		const attachWordCountHandler = (form) => {
			if (!form) return;
			const manuscriptInput = form.querySelector('input[name="manuscript"]');
			const wordCountInput = form.querySelector('input[name="word_count"]');
			if (!manuscriptInput || !wordCountInput) return;
			let submittingWithMammoth = false;
			form.addEventListener('submit', (event) => {
				if (submittingWithMammoth) { submittingWithMammoth = false; return; }
				const files = manuscriptInput.files;
				if (!files || !files.length) { wordCountInput.value = ''; return; }
				const [file] = files;
				const extension = getFileExtension(file.name || manuscriptInput.value);
				if (!shouldUseMammothForExtension(extension)) { wordCountInput.value = ''; return; }
				event.preventDefault();
				extractWordCountWithMammoth(file)
					.then((wordCount) => { wordCountInput.value = Number.isInteger(wordCount) && wordCount > 0 ? wordCount : ''; })
					.catch((error) => { console.error('Unable to count words for learner manuscript form', error); wordCountInput.value = ''; })
					.finally(() => { submittingWithMammoth = true; form.submit(); });
			});
		};

		manuscriptForms.forEach((form) => {
			const manuscriptInput = form.querySelector('input[name="manuscript"]');
			if (manuscriptInput) manuscriptInput.addEventListener('change', handleFileChange);
			attachWordCountHandler(form);
		});

		const attachModalReset = (modal) => {
			if (!modal || !window.jQuery) return;
			window.jQuery(modal).on('show.bs.modal', () => {
				const form = modal.querySelector('form');
				resetConversionUI(form);
			});
		};

		attachModalReset(document.getElementById('uploadManuscriptModal'));
		attachModalReset(document.getElementById('updateUploadedManuscriptModal'));
	})();
</script>
@stop
