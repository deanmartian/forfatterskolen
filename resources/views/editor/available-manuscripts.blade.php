@extends('editor.layout')

@section('title')
<title>Ledige manus &rsaquo; Forfatterskolen Redaktørportal</title>
@stop

@section('page-title', 'Ledige manus')

@section('styles')
	<link rel="stylesheet" href="{{asset('css/editor.css')}}">
	<style>
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

		.available-manuscripts {
			padding: 0;
			font-family: var(--font-body);
			color: var(--text);
			background: var(--bg);
		}

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
		.section-badge.brand { background: var(--brand-primary); }
		.section-body {
			padding: 12px 24px 20px;
			overflow-x: auto;
		}

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

		.btn-claim {
			background: var(--brand-primary);
			color: #fff;
			border: none;
			padding: 6px 16px;
			border-radius: var(--radius-sm);
			font-size: .85rem;
			font-weight: 600;
			cursor: pointer;
			transition: background .2s;
		}
		.btn-claim:hover {
			background: var(--brand-dark);
			color: #fff;
		}

		.empty-state {
			text-align: center;
			padding: 60px 20px;
			color: var(--text-secondary);
		}
		.empty-state i {
			font-size: 3rem;
			color: var(--border);
			margin-bottom: 16px;
		}
		.empty-state p {
			font-size: 1rem;
			margin: 0;
		}
	</style>
@stop

@section('content')
<div class="available-manuscripts">
	<div class="dashboard-section">
		<div class="section-header">
			<h4>Ledige manus <span class="section-badge brand">{{ $manuscripts->count() }}</span></h4>
		</div>

		@if($manuscripts->count() > 0)
		<div class="section-body">
			<table class="table">
				<thead>
					<tr>
						<th>Tittel</th>
						<th>Sjanger</th>
						<th>Opplastet</th>
						<th>Antall ord</th>
						<th>Elev</th>
						<th>Frist</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach($manuscripts as $manuscript)
					<tr>
						<td>
							<strong>{{ $manuscript->shop_manuscript->title ?? 'Uten tittel' }}</strong>
						</td>
						<td>
							@if($manuscript->genre)
								{{ \App\Genre::find($manuscript->genre)->name ?? '-' }}
							@else
								-
							@endif
						</td>
						<td>
							{{ $manuscript->manuscript_uploaded_date ? date_format(date_create($manuscript->manuscript_uploaded_date), 'd.m.Y') : '-' }}
						</td>
						<td>{{ $manuscript->words ?? '-' }}</td>
						<td>{{ $manuscript->user->full_name ?? '-' }}</td>
						<td>{{ $manuscript->expected_finish ? date_format(date_create($manuscript->expected_finish), 'd.m.Y') : '-' }}</td>
						<td>
							<form method="POST" action="{{ route('editor.claim-manuscript', $manuscript->id) }}"
								  onsubmit="return confirm('Er du sikker på at du vil ta dette manuset?')">
								{{ csrf_field() }}
								<button type="submit" class="btn-claim">
									<i class="fa fa-hand-paper-o"></i> Ta dette manuset
								</button>
							</form>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@else
		<div class="empty-state">
			<i class="fa fa-book"></i>
			<p>Ingen ledige manus for øyeblikket.</p>
		</div>
		@endif
	</div>
</div>
@stop
