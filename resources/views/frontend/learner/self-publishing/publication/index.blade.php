@extends('frontend.layout')

@section('title')
    <title>Mine bøker | Indiemoon Publishing</title>
@endsection

@section('content')
<div class="container" style="padding: 40px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-family: 'Cormorant Garamond', Georgia, serif; font-size: 2rem;">Mine bøker</h1>
        <a href="{{ route('learner.publication.create') }}" class="btn" style="background: #862736; color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none;">
            + Ny bok
        </a>
    </div>

    @if($publications->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: #f8f6f3; border-radius: 12px;">
            <p style="font-size: 1.2rem; color: #666; margin-bottom: 20px;">Du har ingen bøker ennå.</p>
            <a href="{{ route('learner.publication.create') }}" style="display: inline-block; background: #862736; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                Last opp ditt første manus
            </a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach($publications as $pub)
                <a href="{{ route('learner.publication.show', $pub->id) }}" style="text-decoration: none; color: inherit;">
                    <div style="background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 20px; transition: box-shadow 0.2s;">
                        <h3 style="font-size: 1.1rem; margin-bottom: 6px;">{{ $pub->title }}</h3>
                        <p style="color: #888; font-size: 0.85rem; margin-bottom: 12px;">{{ $pub->author_name }}</p>
                        <div style="display: flex; gap: 10px; font-size: 0.8rem; color: #666;">
                            @if($pub->word_count)
                                <span>{{ number_format($pub->word_count) }} ord</span>
                            @endif
                            @if($pub->page_count)
                                <span>{{ $pub->page_count }} sider</span>
                            @endif
                        </div>
                        <div style="margin-top: 10px;">
                            @php
                                $statusLabels = [
                                    'draft' => ['Utkast', '#6c757d'],
                                    'parsing' => ['Analyserer...', '#f0ad4e'],
                                    'composing' => ['Setter opp...', '#f0ad4e'],
                                    'generating' => ['Genererer...', '#f0ad4e'],
                                    'preview' => ['Klar til nedlasting', '#28a745'],
                                    'approved' => ['Godkjent', '#28a745'],
                                    'published' => ['Publisert', '#007bff'],
                                    'error' => ['Feil', '#dc3545'],
                                ];
                                [$label, $color] = $statusLabels[$pub->status] ?? ['Ukjent', '#6c757d'];
                            @endphp
                            <span style="display: inline-block; background: {{ $color }}; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem;">
                                {{ $label }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
