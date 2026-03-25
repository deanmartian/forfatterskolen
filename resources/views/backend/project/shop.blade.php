@extends($layout ?? 'backend.layout')

@section('title')
    <title>Nettbutikk &rsaquo; {{ $project->name }} &rsaquo; Admin</title>
@stop

@section('content')
<div class="container-fluid" style="max-width:900px;padding:2rem;">
    <a href="{{ route('admin.project.show', $project->id) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Tilbake til prosjekt</a>

    <h2 style="margin-bottom:0.25rem;">🛒 Nettbutikk-innstillinger</h2>
    <p class="text-muted">{{ $project->name }} — {{ $project->user->full_name ?? 'Ukjent forfatter' }}</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!$book)
        <div class="alert alert-warning">
            Dette prosjektet har ingen bok registrert. Legg til en bok i prosjektet først.
        </div>
    @else
        <form action="{{ route('admin.project.shop.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Synlighet --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Synlighet i nettbutikk</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="shop_visible" value="0">
                                <input class="form-check-input" type="checkbox" name="shop_visible" value="1" {{ $book->shop_visible ? 'checked' : '' }}>
                                <label class="form-check-label"><strong>Synlig i butikken</strong></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="shop_featured" value="0">
                                <input class="form-check-input" type="checkbox" name="shop_featured" value="1" {{ $book->shop_featured ? 'checked' : '' }}>
                                <label class="form-check-label"><strong>Fremhevet</strong></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Sortering</label>
                                <input type="number" name="shop_sort_order" class="form-control" value="{{ old('shop_sort_order', $book->shop_sort_order ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bokinfo --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Bokinfo for nettbutikk</strong></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label><strong>Slug (URL)</strong></label>
                            <div class="input-group">
                                <span class="input-group-text">shop.indiemoon.no/bok/</span>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug', $book->slug) }}" placeholder="auto-genereres fra boknavn">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Sjanger</strong></label>
                            <select name="genre" class="form-control">
                                <option value="">-- Velg --</option>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre }}" {{ ($book->genre ?? '') == $genre ? 'selected' : '' }}>{{ $genre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label><strong>Målgruppe</strong></label>
                            <select name="target_audience" class="form-control">
                                <option value="">-- Velg --</option>
                                <option value="voksen" {{ ($book->target_audience ?? '') == 'voksen' ? 'selected' : '' }}>Voksen</option>
                                <option value="ungdom" {{ ($book->target_audience ?? '') == 'ungdom' ? 'selected' : '' }}>Ungdom</option>
                                <option value="barn" {{ ($book->target_audience ?? '') == 'barn' ? 'selected' : '' }}>Barn</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label><strong>Kort beskrivelse</strong> <small class="text-muted">(maks 500 tegn, brukes i bokkort)</small></label>
                        <textarea name="short_description" class="form-control" rows="3" maxlength="500">{{ old('short_description', $book->short_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label><strong>Lang beskrivelse</strong> <small class="text-muted">(baksidetekst)</small></label>
                        <textarea name="long_description" class="form-control" rows="6">{{ old('long_description', $book->long_description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Priser og formater --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Priser og tilgjengelighet</strong></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="print_available" value="0">
                                <input class="form-check-input" type="checkbox" name="print_available" value="1" {{ $book->print_available ? 'checked' : '' }}>
                                <label class="form-check-label">Pocket/heftet</label>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">kr</span>
                                <input type="number" name="price_paperback" class="form-control" value="{{ old('price_paperback', $book->price_paperback) }}" placeholder="349">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="d-block mb-2">Innbundet</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">kr</span>
                                <input type="number" name="price_hardcover" class="form-control" value="{{ old('price_hardcover', $book->price_hardcover) }}" placeholder="449">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="ebook_available" value="0">
                                <input class="form-check-input" type="checkbox" name="ebook_available" value="1" {{ $book->ebook_available ? 'checked' : '' }}>
                                <label class="form-check-label">E-bok</label>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">kr</span>
                                <input type="number" name="price_ebook" class="form-control" value="{{ old('price_ebook', $book->price_ebook) }}" placeholder="149">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="audiobook_available" value="0">
                                <input class="form-check-input" type="checkbox" name="audiobook_available" value="1" {{ $book->audiobook_available ? 'checked' : '' }}>
                                <label class="form-check-label">Lydbok</label>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">kr</span>
                                <input type="number" name="price_audiobook" class="form-control" value="{{ old('price_audiobook', $book->price_audiobook) }}" placeholder="199">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lagerinfo --}}
            @if($book->inventory)
            <div class="card mb-4">
                <div class="card-header"><strong>Lagerstatus</strong></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3>{{ $book->inventory->balance ?? 0 }}</h3>
                            <small class="text-muted">Tilgjengelig</small>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ $book->inventory->total ?? 0 }}</h3>
                            <small class="text-muted">Totalt trykket</small>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ $book->inventory->delivered ?? 0 }}</h3>
                            <small class="text-muted">Levert</small>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ $book->sales->count() }}</h3>
                            <small class="text-muted">Salg registrert</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <button type="submit" class="btn btn-lg" style="background:#862736;color:#fff;border:none;border-radius:10px;padding:12px 40px;">
                Lagre nettbutikk-innstillinger
            </button>
        </form>
    @endif
</div>
@endsection
