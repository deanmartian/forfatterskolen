@extends('frontend.layouts.course-portal')

@section('page_title', 'Kurspakker & oppgradering › Forfatterskolen')

@section('styles')
<style>
:root {
	--wine: #862736;
	--wine-hover: #9c2e40;
	--wine-light-solid: #f4e8ea;
	--cream: #faf8f5;
	--green: #2e7d32;
	--green-bg: #e8f5e9;
	--amber: #e65100;
	--amber-bg: #fff3e0;
	--text-primary: #1a1a1a;
	--text-secondary: #5a5550;
	--text-muted: #8a8580;
	--border: rgba(0,0,0,0.08);
	--border-strong: rgba(0,0,0,0.12);
	--font-body: 'Source Sans 3', -apple-system, sans-serif;
	--radius: 10px;
	--radius-lg: 14px;
}

/* ── RESET ──────────────────────────────────────── */
.kp-redesign { max-width: 880px; margin: 0 auto; padding: 2rem; }
.kp-redesign * { box-sizing: border-box; }

.kp-header { margin-bottom: 1.5rem; }
.kp-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-primary); }
.kp-header p { font-size: 0.875rem; color: var(--text-secondary); margin: 0; }

/* ── SUBSCRIPTION CARD ──────────────────────────── */
.sub-card {
	background: #fff;
	border: 1px solid var(--border);
	border-radius: var(--radius-lg);
	overflow: hidden;
	margin-bottom: 1.5rem;
}
.sub-card__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 1.5rem;
	background: var(--cream);
	border-bottom: 1px solid var(--border);
	gap: 1rem;
}
.sub-card__title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.15rem; }
.sub-card__price { font-size: 0.875rem; color: var(--text-secondary); }
.sub-card__price strong { font-size: 1.25rem; font-weight: 700; color: var(--wine); }

.sub-card__badge {
	font-size: 0.65rem;
	font-weight: 600;
	padding: 0.25rem 0.6rem;
	border-radius: 4px;
	white-space: nowrap;
}
.sub-card__badge--active { background: var(--green-bg); color: var(--green); }
.sub-card__badge--expiring { background: var(--amber-bg); color: var(--amber); }

.sub-card__body { padding: 1.5rem; }

.sub-details {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 1rem;
	margin-bottom: 1.25rem;
}
.sub-detail__label {
	font-size: 0.68rem;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: var(--text-muted);
	margin-bottom: 0.15rem;
}
.sub-detail__value { font-size: 0.9rem; font-weight: 600; color: var(--text-primary); }

/* Auto-renew toggle */
.auto-renew {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 1rem 1.25rem;
	background: var(--cream);
	border-radius: var(--radius);
	gap: 1rem;
}
.auto-renew__info { flex: 1; }
.auto-renew__title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.1rem; }
.auto-renew__desc { font-size: 0.75rem; color: var(--text-muted); }

.toggle-switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
	position: absolute; cursor: pointer;
	top: 0; left: 0; right: 0; bottom: 0;
	background: rgba(0,0,0,0.12);
	border-radius: 12px;
	transition: background 0.2s;
}
.toggle-slider::before {
	content: '';
	position: absolute;
	height: 18px; width: 18px;
	left: 3px; bottom: 3px;
	background: #fff;
	border-radius: 50%;
	transition: transform 0.2s;
	box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
.toggle-switch input:checked + .toggle-slider { background: var(--wine); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }

.sub-card__renew-btn {
	display: inline-flex;
	align-items: center;
	gap: 0.3rem;
	margin-top: 1rem;
	padding: 0.5rem 1rem;
	border-radius: 6px;
	font-family: var(--font-body);
	font-size: 0.8rem;
	font-weight: 600;
	background: var(--wine);
	color: #fff;
	border: none;
	cursor: pointer;
	transition: background 0.15s;
}
.sub-card__renew-btn:hover { background: var(--wine-hover); }

/* ── SECTION LABEL ──────────────────────────────── */
.section-label {
	font-size: 0.7rem;
	font-weight: 600;
	letter-spacing: 1.5px;
	text-transform: uppercase;
	color: var(--text-muted);
	margin-bottom: 0.75rem;
	margin-top: 2rem;
}

/* ── PACKAGE LIST ───────────────────────────────── */
.package-list { display: flex; flex-direction: column; gap: 0.75rem; }

.package-item {
	background: #fff;
	border: 1px solid var(--border);
	border-radius: var(--radius-lg);
	padding: 1.25rem 1.5rem;
	display: flex;
	align-items: center;
	gap: 1.25rem;
	transition: border-color 0.15s;
}
.package-item:hover { border-color: var(--border-strong); }
.package-item--expired { opacity: 0.65; }

.package-item__icon {
	width: 44px; height: 44px;
	border-radius: 10px;
	display: flex; align-items: center; justify-content: center;
	flex-shrink: 0;
}
.package-item__icon--active { background: var(--green-bg); }
.package-item__icon--basic { background: var(--cream); }
.package-item__icon svg { width: 22px; height: 22px; }

.package-item__info { flex: 1; }
.package-item__name { font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.1rem; }
.package-item__package { font-size: 0.78rem; color: var(--wine); font-weight: 500; margin-bottom: 0.2rem; }
.package-item__features {
	font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;
	display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
	overflow: hidden; text-overflow: ellipsis;
}

.package-item__badge {
	font-size: 0.65rem;
	font-weight: 600;
	padding: 0.2rem 0.55rem;
	border-radius: 4px;
	white-space: nowrap;
	flex-shrink: 0;
}
.package-item__badge--active { background: var(--green-bg); color: var(--green); }
.package-item__badge--basic { background: rgba(0,0,0,0.04); color: var(--text-muted); }

.package-item__upgrade { flex-shrink: 0; }

.kp-btn {
	display: inline-flex;
	align-items: center;
	gap: 0.3rem;
	padding: 0.5rem 1rem;
	border-radius: 6px;
	font-family: var(--font-body);
	font-size: 0.8rem;
	font-weight: 600;
	text-decoration: none;
	cursor: pointer;
	border: none;
	transition: all 0.15s;
}
.kp-btn--outline-wine {
	background: transparent;
	color: var(--wine);
	border: 1px solid var(--wine);
}
.kp-btn--outline-wine:hover { background: var(--wine); color: #fff; text-decoration: none; }
.kp-btn--primary { background: var(--wine); color: #fff; }
.kp-btn--primary:hover { background: var(--wine-hover); color: #fff; text-decoration: none; }

.no-upgrade { font-size: 0.75rem; color: var(--text-muted); font-style: italic; white-space: nowrap; }

/* ── CTA CARD ───────────────────────────────────── */
.cta-card {
	background: var(--cream);
	border: 1px solid var(--border);
	border-radius: var(--radius-lg);
	padding: 2rem;
	text-align: center;
	margin-top: 2rem;
}
.cta-card__title { font-size: 1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.35rem; }
.cta-card__desc { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.25rem; }
.cta-card__links { display: flex; justify-content: center; gap: 0.75rem; }

/* ── SIDEBAR TOGGLE ─────────────────────────────── */
.kp-sidebar-toggle {
	display: none;
	position: fixed; top: 12px; left: 12px; z-index: 1051;
	width: 38px; height: 38px;
	background: #862736; color: #fff; border: none; border-radius: 8px;
	font-size: 1.15rem; cursor: pointer;
	align-items: center; justify-content: center;
	box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
@media (max-width: 1026px) {
	.kp-sidebar-toggle { display: flex; }
	.kp-redesign { padding-top: 4.5rem; }
}

/* ── RESPONSIVE ─────────────────────────────────── */
@media (max-width: 600px) {
	.kp-redesign { padding: 1rem; padding-top: 4.5rem; }
	.kp-header h1 { font-size: 1.25rem; }
	.sub-card__header { flex-direction: column; align-items: flex-start; }
	.sub-details { grid-template-columns: 1fr; }
	.package-item { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
	.package-item__upgrade { width: 100%; }
	.package-item__upgrade .kp-btn { width: 100%; justify-content: center; }
	.cta-card__links { flex-direction: column; }
	.cta-card__links .kp-btn { width: 100%; justify-content: center; }
}
</style>
@stop

@section('heading')
	Kurspakker & oppgradering
@stop

@section('content')
<button class="kp-sidebar-toggle" onclick="document.querySelector('.course-portal-sidebar, .sidebar, .sb-sidebar, [class*=sidebar]').classList.toggle('show')">☰</button>

<div class="kp-redesign">

	<div class="kp-header">
		<h1>Kurspakker & oppgradering</h1>
		<p>Ditt abonnement og dine kurspakker.</p>
	</div>

	{{-- ═══════════ ABONNEMENTSKORT ═══════════ --}}
	@php
		$webinarPakke = \App\Http\AdminHelpers::getWebinarPakkeDetails(Auth::user()->id);
	@endphp

	@if($webinarPakke)
		@php
			$endDate = \Carbon\Carbon::parse($webinarPakke->end_date);
			$daysUntilExpiry = now()->diffInDays($endDate, false);
			$isExpiring = $daysUntilExpiry <= 30 && $daysUntilExpiry > 0;
			$isExpired = $daysUntilExpiry <= 0;
			$nextPayment = $endDate->copy()->subDays(7);

			// withinAMonth calculation for renew button
			$now = new DateTime();
			$checkDate = date('m/Y', strtotime($webinarPakke->end_date));
			$input = DateTime::createFromFormat('m/Y', $checkDate);
			$diff = $input->diff($now);
			$withinAMonth = $diff->y === 0 && $diff->m <= 1;
		@endphp

		<div class="sub-card">
			<div class="sub-card__header">
				<div>
					<div class="sub-card__title">Forfatterskolen-abonnement</div>
					<div class="sub-card__price"><strong>kr 1 490</strong> per år</div>
				</div>
				@if($isExpired)
					<span class="sub-card__badge sub-card__badge--expiring">Utløpt</span>
				@elseif($isExpiring)
					<span class="sub-card__badge sub-card__badge--expiring">Utløper snart</span>
				@else
					<span class="sub-card__badge sub-card__badge--active">Aktivt</span>
				@endif
			</div>
			<div class="sub-card__body">
				<div class="sub-details">
					<div>
						<div class="sub-detail__label">Utløper</div>
						<div class="sub-detail__value">{{ $endDate->translatedFormat('j. F Y') }}</div>
					</div>
					<div>
						<div class="sub-detail__label">Neste betaling</div>
						<div class="sub-detail__value">{{ $nextPayment->translatedFormat('j. F Y') }}</div>
					</div>
					<div>
						<div class="sub-detail__label">Inkluderer</div>
						<div class="sub-detail__value" style="font-size:0.8rem;font-weight:400;">Mentormøter, skrivefellesskap, kursmoduler</div>
					</div>
				</div>

				<div class="auto-renew">
					<div class="auto-renew__info">
						<div class="auto-renew__title">Forny automatisk</div>
						<div class="auto-renew__desc">Abonnementet fornyes automatisk 7 dager før utløp. Du belastes kr 1 490.</div>
					</div>
					<label class="toggle-switch">
						<input type="checkbox" id="auto-renew-toggle"
							@if(Auth::user()->auto_renew_courses) checked @endif>
						<span class="toggle-slider"></span>
					</label>
				</div>

				{{-- Hidden modal triggers --}}
				<button class="d-none" id="autoRenewBtn" data-bs-toggle="modal" data-bs-target="#autoRenewModal"></button>
				<button class="d-none" id="cancelAutoRenewBtn" data-bs-toggle="modal" data-bs-target="#cancelAutoRenewModal"></button>
				<button class="d-none" id="successAutoRenewBtn" data-bs-toggle="modal" data-bs-target="#successAutoRenewModal"></button>

				@if($withinAMonth)
					<button class="sub-card__renew-btn" data-bs-toggle="modal" data-bs-target="#renewAllModal">
						Forny abonnement →
					</button>
				@endif
			</div>
		</div>
	@endif

	{{-- ═══════════ DINE KURSPAKKER ═══════════ --}}
	<div class="section-label">Dine kurspakker ({{ $coursesTaken->count() }})</div>

	<div class="package-list">
		@foreach($coursesTaken as $key => $courseTaken)
			@php
				$currentCourseType = $courseTaken->package->course_type;
				$upgradeOptions = collect($courseTaken->otherPackages ?? [])->map(function ($package) use ($courseTaken, $currentCourseType) {
					$upgradePrice = 0;
					$displayBtn = true;

					if (in_array($package->course_type, [3, 2])) {
						$upgradePrice = ($package->course_type == 3 && $currentCourseType == 2)
							? $package->full_payment_standard_upgrade_price
							: $package->full_payment_upgrade_price;
					}

					$today = \Carbon\Carbon::today();
					$disableUpgradeDate = \Carbon\Carbon::parse($package->disable_upgrade_price_date);
					$orderDate = \Carbon\Carbon::parse($courseTaken->created_at);
					$dateDiff = (int) round(\Carbon\Carbon::now()->diffInDays($orderDate, false));

					if ($package->course->type == 'Single') {
						$displayBtn = $dateDiff <= 14
							? !($package->disable_upgrade_price_date
								&& $package->disable_upgrade_price == 1
								&& $today->gte($disableUpgradeDate))
								&& !($package->disable_upgrade_price)
							: false;
					} else {
						$displayBtn = $package->disable_upgrade_price_date
							? !($package->disable_upgrade_price == 1 || $today->gte($disableUpgradeDate))
							: !($package->disable_upgrade_price);
					}

					return [
						'package' => $package,
						'price' => $upgradePrice,
						'can_display' => $displayBtn && $courseTaken->package->is_upgradeable,
					];
				})->filter(function ($option) {
					return $option['can_display'];
				});

				// Badge & icon based on course_type
				$tierLabel = match((int)$currentCourseType) {
					3 => 'Pro',
					2 => 'Standard',
					default => 'Grunnpakke',
				};
				$isHighTier = in_array((int)$currentCourseType, [2, 3]);
				$hasEnded = $courseTaken->hasEnded ?? false;

				// Features one-liner: max 4 items
				$rawDesc = trim(strip_tags($courseTaken->package->description ?? ''));
				$lines = preg_split('/[\r\n]+/', $rawDesc);
				$lines = array_filter(array_map(function($l) {
					return trim(preg_replace('/^[\s\-–•]+/', '', trim($l)));
				}, $lines));
				$lines = array_values(array_filter($lines, fn($l) => strlen($l) > 0));
				$features = implode(' · ', array_slice($lines, 0, 4));
				if (count($lines) > 4) $features .= ' …';
			@endphp

			<div class="package-item {{ $hasEnded ? 'package-item--expired' : '' }}">
				<div class="package-item__icon {{ $isHighTier ? 'package-item__icon--active' : 'package-item__icon--basic' }}">
					<svg viewBox="0 0 24 24" fill="none" stroke="{{ $isHighTier ? '#2e7d32' : '#8a8580' }}" stroke-width="1.5" stroke-linecap="round">
						<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
						<path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
					</svg>
				</div>
				<div class="package-item__info">
					<div class="package-item__name">{{ $courseTaken->package->course->title }}</div>
					<div class="package-item__package">{{ $courseTaken->package->variation }}</div>
					@if($features)
						<div class="package-item__features">{{ $features }}</div>
					@endif
				</div>
				<span class="package-item__badge {{ $isHighTier ? 'package-item__badge--active' : 'package-item__badge--basic' }}">{{ $tierLabel }}</span>

				<div class="package-item__upgrade">
					@if($upgradeOptions->count())
						@php $firstOption = $upgradeOptions->first(); @endphp
						@if(!Auth::user()->isDisabled)
							<a href="{{ route('learner.get-upgrade-course', ['course_taken_id' => $courseTaken->id, 'package_id' => $firstOption['package']->id]) }}"
							   class="kp-btn kp-btn--outline-wine">
								Oppgrader til {{ $firstOption['package']->variation }} →
							</a>
						@endif
					@else
						<span class="no-upgrade">Høyeste nivå</span>
					@endif
				</div>
			</div>
		@endforeach

		@if($coursesTaken->isEmpty())
			<div style="text-align:center;padding:2rem;color:var(--text-muted);font-size:0.9rem;">
				Du har ingen aktive kurspakker ennå.
			</div>
		@endif
	</div>

	{{-- ═══════════ CTA KORT ═══════════ --}}
	<div class="cta-card">
		<div class="cta-card__title">Vil du utvide?</div>
		<div class="cta-card__desc">Se alle kurs og tjenester vi tilbyr.</div>
		<div class="cta-card__links">
			<a href="/course" class="kp-btn kp-btn--primary">Se alle kurs →</a>
			<a href="/account/shop-manuscript" class="kp-btn kp-btn--outline-wine">Bestill manusutvikling →</a>
		</div>
	</div>

</div>

{{-- ═══════════ MODALER ═══════════ --}}

{{-- Auto-renew ON --}}
<div id="autoRenewModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Forny automatisk</h5>
			</div>
			<div class="modal-body">
				<form action="{{ route('learner.upgrade-auto-renew') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
					{{ csrf_field() }}
					<p>Ønsker du å fornye abonnementet automatisk?</p>
					<input type="hidden" name="auto_renew" value="1">
					<div class="text-end mt-4">
						<button type="button" class="btn btn-light float-start" data-bs-dismiss="modal" style="width:49%"
							onclick="autoRenewToggleOption(false)">
							Nei
						</button>
						<button type="submit" class="btn btn-primary float-end" style="width:49%">
							Ja
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Auto-renew OFF --}}
<div id="cancelAutoRenewModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Avbestille abonnementet</h5>
			</div>
			<div class="modal-body">
				<form action="{{ route('learner.upgrade-auto-renew') }}" method="POST" onsubmit="disableSubmitOrigText(this)">
					{{ csrf_field() }}
					<p>Ønsker du å si opp abonnementet?</p>
					<input type="hidden" name="auto_renew" value="0">
					<div class="text-end mt-4">
						<button type="button" class="btn btn-light float-start" data-bs-dismiss="modal" style="width:49%"
							onclick="autoRenewToggleOption(true)">
							Nei
						</button>
						<button type="submit" class="btn btn-primary float-end" style="width:49%">
							Ja
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Renew All --}}
<div id="renewAllModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ trans('site.learner.renew-all.title') }}</h5>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('learner.renew-all-courses') }}" onsubmit="disableSubmitOrigText(this)">
					{{ csrf_field() }}
					<p>{{ trans('site.learner.renew-all.description') }}</p>
					<div class="text-end mt-4">
						<button type="button" class="btn btn-light float-start" data-bs-dismiss="modal" style="width:49%">
							Nei
						</button>
						<button type="submit" class="btn btn-primary float-end" style="width:49%">
							Ja
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Success --}}
<div id="successAutoRenewModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center">{{ trans('site.success-text') }}</h5>
			</div>
			<div class="modal-body text-center">
				<img src="{{ asset('images-new/icon/big-green-check.png') }}" alt="" style="width:60px;margin-bottom:1rem;">
				<h5>{{ trans('site.renewed-success-message') }}</h5>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Lukk</button>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
@if(session('success'))
<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('successAutoRenewBtn').click();
	});
</script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
	var autoRenewCourses = {{ Auth::user()->auto_renew_courses ? 'true' : 'false' }};
	var toggle = document.getElementById('auto-renew-toggle');

	if (toggle) {
		toggle.addEventListener('change', function() {
			if (this.checked && !autoRenewCourses) {
				document.getElementById('autoRenewBtn').click();
			}
			if (!this.checked && autoRenewCourses) {
				document.getElementById('cancelAutoRenewBtn').click();
			}
		});
	}
});

function autoRenewToggleOption(checked) {
	var toggle = document.getElementById('auto-renew-toggle');
	if (toggle) toggle.checked = checked;
}
</script>
@stop
