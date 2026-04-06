@extends('backend.layout')

@section('title')
<title>Diskusjoner &rsaquo; Fellesskap &rsaquo; Forfatterskolen Admin</title>
@stop

@section('page-title', 'Fellesskap')

@section('content')
<div class="col-sm-12">
    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
        <li><a href="{{ route('admin.community.index') }}">Oversikt</a></li>
        <li><a href="{{ route('admin.community.members') }}">Medlemmer</a></li>
        <li><a href="{{ route('admin.community.posts') }}">Innlegg</a></li>
        <li class="active"><a href="{{ route('admin.community.discussions') }}">Diskusjoner</a></li>
        <li><a href="{{ route('admin.community.course-groups') }}">Kursgrupper</a></li>
        <li><a href="{{ route('admin.community.live') }}">🔴 Live fellesskap</a></li>
    </ul>

    <div style="margin-bottom: 15px; display: flex; gap: 8px;">
        <button class="btn btn-primary" data-toggle="modal" data-target="#generateDiscussionModal">
            <i class="fa fa-magic"></i> Generer diskusjon med AI
        </button>
    </div>

    <div class="panel panel-default">
        <table class="table">
            <thead>
                <tr>
                    <th>Tittel</th>
                    <th>Kategori</th>
                    <th>Forfatter</th>
                    <th>Svar</th>
                    <th>Dato</th>
                    <th>Festet</th>
                    <th style="width: 120px;">Handlinger</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discussions as $discussion)
                    @php
                        $profile = $discussion->user->profile ?? null;
                        $name = $profile ? ucwords($profile->name) : ($discussion->user->fullName ?? 'Ukjent');
                    @endphp
                    <tr @if($discussion->pinned) style="background: #fff8e1;" @endif>
                        <td><strong>{{ $discussion->title }}</strong></td>
                        <td><span class="label label-info">{{ $discussion->category }}</span></td>
                        <td>{{ $name }}</td>
                        <td>{{ $discussion->replies_count }}</td>
                        <td>{{ $discussion->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            @if($discussion->pinned)
                                <span class="label label-warning"><i class="fa fa-thumb-tack"></i> Ja</span>
                            @else
                                <span class="text-muted">Nei</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.community.discussions.toggle-pin', $discussion->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $discussion->pinned ? 'btn-default' : 'btn-warning' }}" title="{{ $discussion->pinned ? 'Løsne' : 'Fest' }}">
                                    <i class="fa fa-thumb-tack"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.community.discussions.destroy', $discussion->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Er du sikker på at du vil slette denne diskusjonen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-danger" title="Slett">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Ingen diskusjoner ennå.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {!! $discussions->render() !!}
</div>

{{-- Generate Discussion Modal --}}
<div class="modal fade" id="generateDiscussionModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-magic"></i> Generer diskusjon med AI</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Tema / emne</label>
                    <input type="text" id="aiDiscussionTopic" class="form-control" placeholder="f.eks. hvordan overvinne skrivesperre" value="">
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Hurtigvalg:</strong>
                    <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="skriveteknikk for nybegynnere">Skriveteknikk</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="hvordan overvinne skrivesperre">Skrivesperre</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="tips for dialog i romaner">Dialog</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="hvordan finne din stemme som forfatter">Forfatter-stemme</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="bokanbefalinger for aspirerende forfattere">Bokanbefaling</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="skriveøvelse for å trene kreativiteten">Skriveøvelse</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="veien fra manus til publisering">Publisering</button>
                        <button class="btn btn-xs btn-default ai-topic-btn" data-topic="hvordan gi og motta tilbakemelding på tekst">Tilbakemelding</button>
                    </div>
                </div>
                <button class="btn btn-primary btn-block" id="aiGenerateBtn" onclick="generateDiscussion()">
                    <i class="fa fa-magic"></i> Generer
                </button>

                <div id="aiDiscussionPreview" style="display: none; margin-top: 20px; border: 1px solid #ddd; border-radius: 4px; padding: 20px; background: #fafafa;">
                    <div class="form-group">
                        <label>Tittel</label>
                        <input type="text" id="aiDiscussionTitle" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" id="aiDiscussionCategory" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Innhold</label>
                        <textarea id="aiDiscussionContent" class="form-control" rows="8"></textarea>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="aiDiscussionPinned"> Fest diskusjonen</label>
                    </div>
                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                        <button class="btn btn-success" onclick="publishDiscussion()">
                            <i class="fa fa-check"></i> Publiser diskusjon
                        </button>
                        <button class="btn btn-default" onclick="generateDiscussion()">
                            <i class="fa fa-refresh"></i> Generer på nytt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
    $('.ai-topic-btn').click(function() {
        $('#aiDiscussionTopic').val($(this).data('topic'));
    });

    function generateDiscussion() {
        var topic = $('#aiDiscussionTopic').val() || 'skrivetips og inspirasjon';
        var btn = $('#aiGenerateBtn');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Genererer...');
        $('#aiDiscussionPreview').hide();

        $.ajax({
            url: '{{ route("admin.community.discussions.generate-ai") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            contentType: 'application/json',
            data: JSON.stringify({ topic: topic }),
            success: function(data) {
                $('#aiDiscussionTitle').val(data.title || '');
                $('#aiDiscussionCategory').val(data.category || 'Skriveteknikk');
                $('#aiDiscussionContent').val(data.content || '');
                $('#aiDiscussionPreview').show();
                btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Generer');
            },
            error: function(xhr) {
                var msg = 'Feil ved generering.';
                if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                alert(msg);
                btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Generer');
            }
        });
    }

    function publishDiscussion() {
        var form = $('<form method="POST" action="{{ route("admin.community.discussions.store-ai") }}">' +
            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
            '<input type="hidden" name="title" value="">' +
            '<input type="hidden" name="content" value="">' +
            '<input type="hidden" name="category" value="">' +
            '</form>');

        form.find('[name=title]').val($('#aiDiscussionTitle').val());
        form.find('[name=content]').val($('#aiDiscussionContent').val());
        form.find('[name=category]').val($('#aiDiscussionCategory').val());

        if ($('#aiDiscussionPinned').is(':checked')) {
            form.append('<input type="hidden" name="pinned" value="1">');
        }

        $('body').append(form);
        form.submit();
    }
</script>
