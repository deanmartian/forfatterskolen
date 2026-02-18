{{--
    Reusable drag-and-drop file upload component
    
    Variables (passed via @include):
        $uploadName   – input name attribute (e.g. 'manuscript', 'cover[]')
        $acceptTypes  – accept attribute string (e.g. 'image/*', '.docx,.pdf,.odt')
        $maxMb        – max file size in MB (e.g. 50)
        $label        – visible label text (e.g. 'Last opp manuskript')
        $multiple     – (optional) bool, default false
        $required     – (optional) bool, default false
--}}

@php
    $inputId    = 'file-upload-' . Str::slug($uploadName) . '-' . Str::random(4);
    $multiple   = $multiple ?? false;
    $required   = $required ?? false;
    $maxBytes   = ($maxMb ?? 50) * 1024 * 1024;
@endphp

<div class="fu-wrapper" data-max-bytes="{{ $maxBytes }}" data-max-mb="{{ $maxMb ?? 50 }}" data-accept="{{ $acceptTypes }}">
    <label class="fu-label">{{ $label }}</label>

    <div class="fu-dropzone" tabindex="0" role="button"
         aria-label="{{ $label }}">
        <div class="fu-dropzone-content">
            <div class="fu-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--brand-primary, #862736)"
                     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </div>
            <p class="fu-instruction">
                <span class="fu-drag-text">Dra og slipp filen hit</span>
                <span class="fu-or">eller</span>
                <span class="fu-browse-link">velg fil</span>
            </p>
            <p class="fu-hint">
                Maks {{ $maxMb ?? 50 }} MB
                @if($acceptTypes)
                    &middot; {{ $acceptTypes }}
                @endif
            </p>
        </div>

        <input type="file"
               id="{{ $inputId }}"
               name="{{ $uploadName }}"
               accept="{{ $acceptTypes }}"
               class="fu-native-input"
               {{ $multiple ? 'multiple' : '' }}
               {{ $required ? 'required' : '' }}>
    </div>

    {{-- File list --}}
    <ul class="fu-file-list"></ul>

    {{-- Error area --}}
    <div class="fu-error" role="alert"></div>
</div>

{{-- Styles (rendered once) --}}
@once
@push('styles')
<style>
/* ── File-upload component ─────────────────────────────────── */
.fu-wrapper {
    margin-bottom: 1.25rem;
}

.fu-label {
    display: block;
    font-weight: 600;
    font-size: .875rem;
    color: var(--text-primary, #2D2A26);
    margin-bottom: .5rem;
}

/* Dropzone */
.fu-dropzone {
    position: relative;
    border: 2px dashed var(--border-color, #E0D6CC);
    border-radius: var(--radius-lg, 12px);
    background: var(--bg-secondary, #FAF8F5);
    padding: 2rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s, box-shadow .2s;
}

.fu-dropzone:hover,
.fu-dropzone:focus-visible {
    border-color: var(--brand-primary, #862736);
    background: #fdf6f7;
    outline: none;
}

.fu-dropzone.fu-dragover {
    border-color: var(--brand-primary, #862736);
    background: rgba(134, 39, 54, .06);
    box-shadow: 0 0 0 3px rgba(134, 39, 54, .10);
}

.fu-dropzone.fu-has-files {
    border-style: solid;
    border-color: var(--success-color, #2E7D52);
    background: #f4faf6;
}

/* Hidden native input */
.fu-native-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.fu-icon {
    margin-bottom: .5rem;
}

.fu-instruction {
    margin: 0;
    font-size: .9rem;
    color: var(--text-secondary, #6B5E54);
}

.fu-drag-text {
    font-weight: 500;
}

.fu-or {
    margin: 0 .35rem;
    color: var(--text-muted, #A09486);
}

.fu-browse-link {
    color: var(--brand-primary, #862736);
    font-weight: 600;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.fu-hint {
    margin: .5rem 0 0;
    font-size: .75rem;
    color: var(--text-muted, #A09486);
}

/* File list */
.fu-file-list {
    list-style: none;
    padding: 0;
    margin: .75rem 0 0;
}

.fu-file-list li {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .5rem .75rem;
    background: var(--bg-primary, #FFFFFF);
    border: 1px solid var(--border-color, #E0D6CC);
    border-radius: var(--radius-md, 8px);
    margin-bottom: .375rem;
    font-size: .85rem;
    color: var(--text-primary, #2D2A26);
    animation: fu-slide-in .25s ease;
}

.fu-file-list li .fu-check {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--success-color, #2E7D52);
    display: flex;
    align-items: center;
    justify-content: center;
}

.fu-file-list li .fu-check svg {
    width: 11px;
    height: 11px;
}

.fu-file-list li .fu-fname {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 500;
}

.fu-file-list li .fu-fsize {
    flex-shrink: 0;
    font-size: .75rem;
    color: var(--text-muted, #A09486);
}

.fu-file-list li .fu-remove {
    flex-shrink: 0;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted, #A09486);
    padding: 2px;
    line-height: 1;
    font-size: 1.1rem;
    transition: color .15s;
}

.fu-file-list li .fu-remove:hover {
    color: var(--danger-color, #C0392B);
}

/* Error */
.fu-error {
    font-size: .8rem;
    color: var(--danger-color, #C0392B);
    margin-top: .35rem;
    min-height: 0;
}

.fu-error:empty {
    display: none;
}

@keyframes fu-slide-in {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
@endonce

{{-- Script (rendered once) --}}
@once
@push('scripts')
<script>
$(function () {

    /* ── Helpers ──────────────────────────────── */
    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function extensionFromName(name) {
        var parts = name.split('.');
        return parts.length > 1 ? '.' + parts.pop().toLowerCase() : '';
    }

    function mimeMatchesAccept(file, acceptStr) {
        if (!acceptStr || acceptStr.trim() === '') return true;
        var tokens = acceptStr.split(',').map(function (t) { return t.trim().toLowerCase(); });
        var ext = extensionFromName(file.name);
        var mime = (file.type || '').toLowerCase();

        for (var i = 0; i < tokens.length; i++) {
            var tok = tokens[i];
            if (tok === ext) return true;                         // .pdf
            if (tok === mime) return true;                        // application/pdf
            if (tok.endsWith('/*') && mime.startsWith(tok.replace('/*', '/'))) return true; // image/*
            // Match long MIME tokens that contain the extension keyword
            if (tok.indexOf('/') !== -1 && mime === tok) return true;
        }
        return false;
    }

    function checkmarkSvg() {
        return '<svg viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
               '<polyline points="2.5 6 5 8.5 9.5 3.5"/></svg>';
    }

    /* ── Per-wrapper init ────────────────────── */
    $('.fu-wrapper').each(function () {
        var $w      = $(this);
        var $dz     = $w.find('.fu-dropzone');
        var $input  = $w.find('.fu-native-input');
        var $list   = $w.find('.fu-file-list');
        var $err    = $w.find('.fu-error');
        var maxB    = parseInt($w.data('max-bytes'), 10) || 52428800;
        var maxMb   = $w.data('max-mb') || 50;
        var accept  = ($w.data('accept') || '').toString();

        /* Drag events */
        $dz.on('dragenter dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $dz.addClass('fu-dragover');
        }).on('dragleave drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $dz.removeClass('fu-dragover');
        }).on('drop', function (e) {
            var dt = e.originalEvent.dataTransfer;
            if (dt && dt.files.length) {
                $input[0].files = dt.files;   // assign to native input
                $input.trigger('change');
            }
        });

        /* Keyboard a11y */
        $dz.on('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $input.trigger('click');
            }
        });

        /* Change handler */
        $input.on('change', function () {
            $list.empty();
            $err.empty();
            var files = this.files;
            if (!files || !files.length) {
                $dz.removeClass('fu-has-files');
                return;
            }

            var errors = [];

            for (var i = 0; i < files.length; i++) {
                var f = files[i];

                /* Validate type */
                if (!mimeMatchesAccept(f, accept)) {
                    errors.push('<strong>' + $('<span/>').text(f.name).html() + '</strong>: filtypen er ikke tillatt.');
                    continue;
                }

                /* Validate size */
                if (f.size > maxB) {
                    errors.push('<strong>' + $('<span/>').text(f.name).html() + '</strong>: filen er for stor (maks ' + maxMb + ' MB).');
                    continue;
                }

                /* Render accepted file */
                var $li = $('<li/>');
                $li.append('<span class="fu-check">' + checkmarkSvg() + '</span>');
                $li.append('<span class="fu-fname">' + $('<span/>').text(f.name).html() + '</span>');
                $li.append('<span class="fu-fsize">' + formatSize(f.size) + '</span>');
                $list.append($li);
            }

            if (errors.length) {
                $err.html(errors.join('<br>'));
                /* If ALL files failed, clear the input */
                if (errors.length === files.length) {
                    $input.val('');
                    $dz.removeClass('fu-has-files');
                    return;
                }
            }

            $dz.addClass('fu-has-files');
        });
    });
});
</script>
@endpush
@endonce
