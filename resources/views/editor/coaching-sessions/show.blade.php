@extends('editor.layout')

@section('title')
    <title>Veiledningssamtale &rsaquo; Forfatterskolen</title>
@stop

@section('page-title', 'Veiledningssamtale')

@section('styles')
<style>
    .cs-detail { max-width: 1100px; }
    .cs-info-card {
        background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        padding: 20px; margin-bottom: 20px;
    }
    .cs-info-row { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 15px; }
    .cs-info-item label { font-size: 11px; text-transform: uppercase; color: #888; font-weight: 600; margin-bottom: 2px; display: block; }
    .cs-info-item span { font-size: 15px; color: #333; }

    .cs-badge {
        display: inline-block; padding: 4px 10px; border-radius: 12px;
        font-size: 12px; font-weight: 600;
    }
    .cs-badge--scheduled { background: #fff3cd; color: #856404; }
    .cs-badge--active { background: #d4edda; color: #155724; }
    .cs-badge--completed { background: #e2e3e5; color: #383d41; }

    .cs-video-container {
        background: #1a1a1a; border-radius: 8px; overflow: hidden; margin-bottom: 20px;
    }
    .cs-video-container iframe { display: block; width: 100%; height: 600px; border: 0; }

    .cs-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
    .cs-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 20px; border-radius: 4px;
        font-size: 14px; font-weight: 600; text-decoration: none;
        background: #852635; color: #fff; border: none; cursor: pointer;
    }
    .cs-btn:hover { background: #5F0000; color: #fff; text-decoration: none; }
    .cs-btn--success { background: #28a745; }
    .cs-btn--success:hover { background: #1e7e34; }
    .cs-btn--danger { background: #dc3545; }
    .cs-btn--danger:hover { background: #a71d2a; }
    .cs-btn--outline {
        background: transparent; color: #852635; border: 1px solid #852635;
    }
    .cs-btn--outline:hover { background: #852635; color: #fff; }
    .cs-btn:disabled, .cs-btn[disabled] { opacity: 0.5; cursor: not-allowed; }

    .cs-recording-box {
        background: #f8f5f0; border-radius: 6px; padding: 20px; margin-bottom: 20px;
        border: 1px solid #e8e0d8;
    }
    .cs-recording-box h5 { margin: 0 0 10px; font-weight: 600; color: #5F0000; }
    .cs-recording-indicator {
        display: inline-flex; align-items: center; gap: 6px;
        color: #dc3545; font-weight: 600; font-size: 14px;
    }
    .cs-recording-dot {
        width: 10px; height: 10px; border-radius: 50%;
        background: #dc3545; animation: cs-pulse 1s infinite;
    }
    @keyframes cs-pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }

    .cs-section {
        background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        padding: 20px; margin-bottom: 20px;
    }
    .cs-section h4 { margin: 0 0 15px; font-weight: 600; color: #5F0000; }
    .cs-section-content {
        background: #fafafa; border: 1px solid #eee; border-radius: 4px;
        padding: 15px; white-space: pre-wrap; font-size: 14px; line-height: 1.7;
    }

    .cs-upload-progress {
        display: none; margin-top: 10px;
    }
    .cs-upload-progress .progress { height: 8px; border-radius: 4px; }
    .cs-upload-progress .progress-bar { background: #852635; }

    .cs-back-link { color: #852635; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; }
    .cs-back-link:hover { text-decoration: underline; color: #5F0000; }
</style>
@stop

@section('content')
<div class="container-fluid cs-detail">
    <a href="{{ route('editor.coaching-sessions.index') }}" class="cs-back-link">
        <i class="fa fa-arrow-left"></i> Tilbake til oversikten
    </a>

    {{-- Sesjoninfo --}}
    <div class="cs-info-card">
        <div class="cs-info-row">
            <div class="cs-info-item">
                <label>Elev</label>
                <span>
                    <a href="{{ route('editor.coaching-sessions.student-history', $session->student_id) }}" style="color: #852635; font-weight: 600;">
                        {{ $session->student->full_name ?? 'Ukjent' }}
                    </a>
                </span>
            </div>
            <div class="cs-info-item">
                <label>Status</label>
                <span>
                    @if($session->status == 'scheduled')
                        <span class="cs-badge cs-badge--scheduled">Planlagt</span>
                    @elseif($session->status == 'active')
                        <span class="cs-badge cs-badge--active">Aktiv</span>
                    @else
                        <span class="cs-badge cs-badge--completed">Fullført</span>
                    @endif
                </span>
            </div>
            <div class="cs-info-item">
                <label>Dato</label>
                <span>
                    @if($session->started_at)
                        {{ $session->started_at->format('d.m.Y H:i') }}
                    @elseif($session->manuscript && $session->manuscript->timeSlot)
                        @php
                            $dt = \Carbon\Carbon::parse(
                                $session->manuscript->timeSlot->date . ' ' . $session->manuscript->timeSlot->start_time,
                                'UTC'
                            )->setTimezone(config('app.timezone'));
                        @endphp
                        {{ $dt->format('d.m.Y H:i') }}
                    @else
                        {{ $session->created_at->format('d.m.Y') }}
                    @endif
                </span>
            </div>
            @if($session->started_at && $session->ended_at)
                <div class="cs-info-item">
                    <label>Varighet</label>
                    <span>{{ $session->started_at->diffInMinutes($session->ended_at) }} min</span>
                </div>
            @endif
        </div>
        @if($session->manuscript && $session->manuscript->help_with)
            <div class="cs-info-item">
                <label>Hva eleven trenger hjelp med</label>
                <span>{{ $session->manuscript->help_with }}</span>
            </div>
        @endif
    </div>

    {{-- Handlingsknapper --}}
    <div class="cs-actions">
        @if($session->status == 'scheduled')
            <form method="POST" action="{{ route('editor.coaching-sessions.start', $session->id) }}">
                @csrf
                <button type="submit" class="cs-btn cs-btn--success">
                    <i class="fa fa-play"></i> Start samtale
                </button>
            </form>
        @elseif($session->status == 'active')
            <form method="POST" action="{{ route('editor.coaching-sessions.end', $session->id) }}">
                @csrf
                <button type="submit" class="cs-btn cs-btn--danger">
                    <i class="fa fa-stop"></i> Avslutt samtale
                </button>
            </form>
        @endif
    </div>

    {{-- Whereby Videomøte --}}
    @if($session->status == 'scheduled' || $session->status == 'active')
        @if($session->whereby_host_url)
            <div class="cs-video-container">
                <iframe
                    src="{{ $session->whereby_host_url }}"
                    allow="camera; microphone; fullscreen; display-capture; autoplay; compute-pressure"
                ></iframe>
            </div>
        @else
            <div class="cs-info-card" style="text-align: center; color: #888;">
                <i class="fa fa-video-camera" style="font-size: 48px; margin-bottom: 10px;"></i>
                <p>Videomøte er ikke tilgjengelig. Whereby-rommet ble ikke opprettet.</p>
            </div>
        @endif
    @endif

    {{-- Lydopptak --}}
    @if($session->status == 'active' || ($session->status == 'completed' && !$session->recording_path))
        <div class="cs-recording-box">
            <h5><i class="fa fa-microphone"></i> Lydopptak</h5>
            <p style="color: #666; font-size: 13px; margin-bottom: 15px;">
                Ta opp samtalen som lydfil. Opptaket transkriberes automatisk og oppsummeres av AI.
            </p>

            <div id="recordingControls">
                <button type="button" id="startRecordingBtn" class="cs-btn" onclick="startRecording()">
                    <i class="fa fa-circle" style="color: #ff4444;"></i> Start opptak
                </button>
                <button type="button" id="stopRecordingBtn" class="cs-btn cs-btn--danger" style="display:none;" onclick="stopRecording()">
                    <i class="fa fa-stop"></i> Stopp opptak
                </button>
                <span id="recordingStatus" class="cs-recording-indicator" style="display:none;">
                    <span class="cs-recording-dot"></span> Tar opp...
                </span>
            </div>

            <div id="uploadArea" style="display:none; margin-top: 15px;">
                <audio id="audioPreview" controls style="width: 100%; margin-bottom: 10px;"></audio>
                <button type="button" class="cs-btn" onclick="uploadRecording()">
                    <i class="fa fa-upload"></i> Last opp og transkriber
                </button>
            </div>

            <div class="cs-upload-progress" id="uploadProgress">
                <p style="margin: 0 0 5px; font-size: 13px;">Laster opp...</p>
                <div class="progress">
                    <div class="progress-bar" id="uploadProgressBar" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    @elseif($session->recording_path)
        <div class="cs-recording-box">
            <h5><i class="fa fa-microphone"></i> Lydopptak</h5>
            <p style="color: #28a745; margin: 0;"><i class="fa fa-check"></i> Opptak er lastet opp og behandlet.</p>
        </div>
    @endif

    {{-- Transkripsjon --}}
    @if($session->transcription)
        <div class="cs-section">
            <h4><i class="fa fa-file-text-o"></i> Transkripsjon</h4>
            <div class="cs-section-content">{{ $session->transcription }}</div>
        </div>
    @endif

    {{-- AI-oppsummering --}}
    @if($session->summary)
        <div class="cs-section">
            <h4><i class="fa fa-magic"></i> AI-oppsummering</h4>
            <div class="cs-section-content">{!! nl2br(e($session->summary)) !!}</div>
        </div>
    @endif

    {{-- Elevlink til rom (for deling) --}}
    @if($session->whereby_room_url && $session->status != 'completed')
        <div class="cs-info-card">
            <label style="font-size: 11px; text-transform: uppercase; color: #888; font-weight: 600;">Elevens møtelink (del med eleven)</label>
            <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                <input type="text" id="studentLink" value="{{ $session->whereby_room_url }}" class="form-control" readonly style="flex: 1;">
                <button type="button" class="cs-btn--outline cs-btn" onclick="copyStudentLink()">
                    <i class="fa fa-copy"></i> Kopier
                </button>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
let mediaRecorder;
let recordedChunks = [];
let audioBlob;

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
            recordedChunks = [];

            mediaRecorder.ondataavailable = function(e) {
                if (e.data.size > 0) {
                    recordedChunks.push(e.data);
                }
            };

            mediaRecorder.onstop = function() {
                audioBlob = new Blob(recordedChunks, { type: 'audio/webm' });
                let audioUrl = URL.createObjectURL(audioBlob);
                document.getElementById('audioPreview').src = audioUrl;
                document.getElementById('uploadArea').style.display = 'block';

                // Stopp mikrofon
                stream.getTracks().forEach(function(track) { track.stop(); });
            };

            mediaRecorder.start(1000); // Samle data hvert sekund

            document.getElementById('startRecordingBtn').style.display = 'none';
            document.getElementById('stopRecordingBtn').style.display = 'inline-flex';
            document.getElementById('recordingStatus').style.display = 'inline-flex';
        })
        .catch(function(err) {
            alert('Kunne ikke starte opptak. Sjekk at mikrofon er tilgjengelig.\n\n' + err.message);
        });
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }
    document.getElementById('stopRecordingBtn').style.display = 'none';
    document.getElementById('recordingStatus').style.display = 'none';
    document.getElementById('startRecordingBtn').style.display = 'inline-flex';
}

function uploadRecording() {
    if (!audioBlob) return;

    let formData = new FormData();
    formData.append('recording', audioBlob, 'opptak_{{ $session->id }}.webm');

    let progressDiv = document.getElementById('uploadProgress');
    let progressBar = document.getElementById('uploadProgressBar');
    progressDiv.style.display = 'block';

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route("editor.coaching-sessions.upload-recording", $session->id) }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            let pct = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = pct + '%';
        }
    };

    xhr.onload = function() {
        if (xhr.status === 200) {
            progressDiv.innerHTML = '<p style="color: #28a745; margin:0;"><i class="fa fa-check"></i> Opptak lastet opp! Transkripsjon er startet i bakgrunnen.</p>';
            document.getElementById('uploadArea').style.display = 'none';
        } else {
            progressDiv.innerHTML = '<p style="color: #dc3545; margin:0;"><i class="fa fa-exclamation-triangle"></i> Opplasting feilet. Prøv igjen.</p>';
        }
    };

    xhr.onerror = function() {
        progressDiv.innerHTML = '<p style="color: #dc3545; margin:0;"><i class="fa fa-exclamation-triangle"></i> Nettverksfeil. Prøv igjen.</p>';
    };

    xhr.send(formData);
}

function copyStudentLink() {
    let input = document.getElementById('studentLink');
    input.select();
    document.execCommand('copy');
    let btn = event.currentTarget;
    let originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-check"></i> Kopiert!';
    setTimeout(function() { btn.innerHTML = originalHtml; }, 2000);
}
</script>
@endsection
