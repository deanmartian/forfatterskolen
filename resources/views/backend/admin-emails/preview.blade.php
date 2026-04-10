@extends('backend.layout')

@section('page_title')Forhåndsvis: {{ $info['name'] }} &rsaquo; Forfatterskolen Admin@endsection

@section('styles')
<style>
    .preview-card {
        background: #fff; border-radius: 8px; padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.08); max-width: 800px;
    }
    .preview-meta {
        background: #f8f8f8; border-radius: 8px; padding: 1rem 1.25rem;
        margin-bottom: 1.5rem; font-size: 0.9rem;
    }
    .preview-meta strong { color: #555; }
    .preview-frame {
        border: 1px solid #e5e5e5; border-radius: 6px; padding: 1.5rem;
        background: #fff; min-height: 200px;
    }
    .preview-empty {
        color: #999; text-align: center; padding: 3rem;
    }
</style>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-eye"></i> Forhåndsvis: {{ $info['name'] }}</h3>
        <div class="pull-right">
            <a href="{{ route('admin.emails.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Tilbake
            </a>
            <a href="{{ route('admin.emails.edit', $type) }}" class="btn btn-default btn-sm">
                <i class="fa fa-pencil"></i> Rediger
            </a>
        </div>
    </div>

    <div class="preview-card">
        <div class="preview-meta">
            <p><strong>Type:</strong> {{ $info['name'] }}</p>
            <p><strong>Kategori:</strong> {{ $info['category'] }}</p>
            <p><strong>Beskrivelse:</strong> {{ $info['description'] }}</p>
            @if($template && $template->subject)
                <p><strong>Emne:</strong> {{ $template->subject }}</p>
            @endif
        </div>

        <h5 style="margin-bottom:1rem;">Innhold</h5>
        <div class="preview-frame">
            @if($template && $template->email_content)
                {!! $template->email_content !!}
            @else
                <div class="preview-empty">
                    <i class="fa fa-inbox" style="font-size:2rem; margin-bottom:.5rem; display:block;"></i>
                    Ingen mal lagret enn&aring;.<br>
                    Denne e-posten bruker standardmalen fra koden.
                    <br><br>
                    <a href="{{ route('admin.emails.edit', $type) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-pencil"></i> Opprett mal
                    </a>
                </div>
            @endif
        </div>
    </div>
@stop
