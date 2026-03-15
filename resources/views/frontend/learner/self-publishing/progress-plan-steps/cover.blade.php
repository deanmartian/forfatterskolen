@extends('frontend.learner.self-publishing.layout')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('title')
    <title>Omslag &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <a href="{{ route('learner.progress-plan') }}" class="btn btn-outline-brand mb-3">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Tilbake
            </a>

            <div class="card">
                <div class="sp-card-header">
                    {{ trans('site.homepage.illustration-cover-design') }}

                    <button type="button" class="btn btn-brand btn-sm float-end coverBtn" data-bs-toggle="modal" 
                                data-bs-target="#coverModal" data-type="cover">
                        + Legg til omslag
                    </button>
                </div>
                <div class="sp-card-body">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th>Omslag</th>
                                <th width="500">Trykkeklar</th>
                                <th width="300"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($covers as $cover)
                                <tr>
                                    <td>
                                        @php
                                            $coverFiles = explode(',', $cover->value);
                                        @endphp
                                         @foreach ($coverFiles as $coverFile)
                                            @if (strpos($coverFile, 'Forfatterskolen_app'))
                                                <a href="/dropbox/download/{{ trim($coverFile) }}">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </a>&nbsp;
                                                <a href="/dropbox/shared-link/{{ trim($coverFile) }}" target="_blank" 
                                                style="margin-right: 5px">
                                                    {{ basename($coverFile) }}
                                                </a>
                                            @else
                                                @if ($coverFile)
                                                    <a href="{{ $coverFile }}" class="btn btn-brand btn-sm" download>
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{{ asset($coverFile) }}" target="_blank" style="margin-right: 5px">
                                                        {{ basename($coverFile) }}
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($cover->print_ready)
                                            <a href="/dropbox/download/{{ trim($cover->print_ready) }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>&nbsp;
                                            {!! basename($cover->print_ready) !!}
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-outline-brand btn-sm view-cover-btn" data-bs-toggle="modal"
                                            data-bs-target="#coverDetailsModal"
                                            data-id="{{ $cover->id }}" aria-label="Vis omslag">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </button>

                                        <button class="btn btn-brand btn-sm coverBtn" data-bs-toggle="modal"
                                                data-bs-target="#coverModal"
                                                data-type="cover" data-id="{{ $cover->id }}"
                                                data-record="{{ json_encode($cover) }}"
                                                aria-label="Rediger omslag">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </button>
                                        <div id="cover-data-{{ $cover->id }}" class="d-none">
                                            {{-- Copy your table HTML here and make sure to use raw data --}}
                                            {!! view('frontend.learner.self-publishing.progress-plan-steps._cover-details', 
                                                ['cover' => $cover]) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="coverModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-palette" style="color:var(--brand-primary);margin-right:6px"></i>
                        <span class="sp-modal__title-text">Omslag</span>
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('learner.self-publishing.save-cover', $standardProject->id) }}"
                    enctype="multipart/form-data" onsubmit="disableSubmit(this)" data-sp-validate>
                    {{ csrf_field() }}
                    <input type="hidden" name="id">
                    <input type="hidden" name="type" value="cover">
                    <div class="sp-modal__body">
                        <div class="sp-form-group">
                            <label class="sp-label">Omslag</label>
                            <input type="file" class="sp-input" name="cover[]" accept="image/*" multiple
                            data-sp-file-preview="coverFilePreview">
                            <div id="coverFilePreview"></div>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Beskrivelse</label>
                            <textarea name="description" cols="30" rows="10" class="sp-input"></textarea>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Størrelse</label>
                            <select class="sp-input" name="cover_format" id="cover-format-select">
                                <option value="">Valgfri størrelse</option>
                                    @foreach (AdminHelpers::projectFormats() as $format)
                                        <option value="{{ $format['id'] }}">
                                            {{ $format['option'] }}
                                        </option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Bredde (mm)</label>
                            <input type="text" class="sp-input" name="cover_width" id="cover-width-input"
                            onkeypress="return numeralsOnly(event)">
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Høyde (mm)</label>
                            <input type="text" class="sp-input" name="cover_height" id="cover-height-input"
                            onkeypress="return numeralsOnly(event)">
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">ISBN</label>
                            <select class="sp-input" name="isbn_id" required>
                                <option value="" disabled selected>- Velg ISBN -</option>
                                @foreach ($isbns as $isbn)
                                    <option value="{{ $isbn->id }}">
                                        {{ $isbn->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Baksidetekst (valgfritt)</label> <br>
                            <input type="checkbox" data-bs-toggle="toggle" data-on="Tekst" data-off="Dokument"
                                name="backside_type" data-width="100" class="backsideToggle" checked
                                >

                            <textarea name="backside_text" cols="30" rows="3" class="sp-input backside-text"
                            style="margin-top: 10px"></textarea>
                            <input type="file" name="backside_file" class="sp-input backside-file"
                        accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        style="display: none; margin-top: 10px"
                        data-sp-file-preview="coverBacksidePreview">
                            <div id="coverBacksidePreview"></div>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Baksidebilde (valgfritt)</label>
                            <input type="file" class="sp-input" name="backside_image[]" accept="image/*" multiple
                            data-sp-file-preview="coverBacksideImagePreview">
                            <div id="coverBacksideImagePreview"></div>
                        </div>

                        <div class="sp-form-group">
                            <label class="sp-label">Instruksjon (til grafisk designer)</label>
                            <textarea name="instruction" cols="30" rows="10" class="sp-input"></textarea>
                        </div>
                    </div>
                    <div class="sp-modal__footer">
                        <button type="button" class="btn-outline-brand" data-bs-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="coverDetailsModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-palette" style="color:var(--brand-primary);margin-right:6px"></i>
                        Omslagsdetaljer
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="sp-modal__body" id="coverModalContent" style="overflow: auto">
                    {{-- Table content will be loaded here dynamically --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <script>

        $(".coverBtn").click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let record = $(this).data('record');
            let modal = $("#coverModal");
            let form = modal.find("form");

            let coverContainer = $(".cover-container");
            let descriptionContainer = $(".description-container");

            coverContainer.addClass('hide');
            descriptionContainer.addClass('hide');

            switch (type) {
                case 'cover':
                        modal.find('.sp-modal__title-text').text('Omslag');
                        coverContainer.removeClass('hide');
                    break;

                case 'cover-print-ready':
                        modal.find('.sp-modal__title-text').text('Trykkeklar');
                        descriptionContainer.removeClass('hide');
                    break;
            }

            form.find('[name=type]').val(type);

            if (id) {
                form.find('[name=id]').val(id);
                form.find('[name=format]').val(record.format);

                if (type == 'cover') {
                    form.find("[name=description]").val(record.description);
                    form.find("[name=cover_format]").val(record.format);
                    form.find("[name=isbn_id]").val(record.isbn_id);
                    form.find("[name=instruction]").val(record.instruction);
                    
                    if (record.backside_type == 'text') {
                        form.find("[name=backside_text]").val(record.backside_text);
                        $(".backsideToggle").prop("checked", true).change();
                    } else {
                        form.find("[name=backside_text]").val("");
                        $(".backsideToggle").prop("checked", false).change();
                    }

                    var formatSelect = document.getElementById('cover-format-select');
                    var widthInput = document.getElementById('cover-width-input');
                    var heightInput = document.getElementById('cover-height-input');

                    var formatExists = false;

                    // Check if the format matches any predefined options
                    for (var i = 0; i < formatSelect.options.length; i++) {
                        if (formatSelect.options[i].value === record.format) {
                            formatSelect.value = record.format;
                            formatExists = true;

                            // If it's a predefined format like '125x200', split it for width/height
                            var dimensions = record.format.split('x');
                            if (dimensions.length == 2) {
                                widthInput.value = dimensions[0];
                                heightInput.value = dimensions[1];
                            }
                            break;
                        }
                    }
                    
                    if (!formatExists) {
                        formatSelect.value = ''; // Select "other" option

                        // Assuming `printData` contains custom width and height
                        if (record.format) {
                            var dimensions = record.format.split('x');
                            if (dimensions.length == 2) {
                                widthInput.value = dimensions[0];
                                heightInput.value = dimensions[1];
                            }
                        } else {
                            // You can also fallback to width and height fields if needed
                            widthInput.value = record.width || ''; // Use width from printData
                            heightInput.value = record.height || ''; // Use height from printData
                        }
                    }
                }
            }
        });

        $(".backsideToggle").change(function() {
            if ($(this).prop('checked')) {
                $(".backside-text").show();
                $(".backside-file").hide();
            } else {
                $(".backside-text").hide();
                $(".backside-file").show();
            }
        });

        $('#cover-format-select').on('change', function () {
            var selectedFormat = this.value;
            var widthInput = document.getElementById('cover-width-input');
            var heightInput = document.getElementById('cover-height-input');
            
            // If the selected value is "other", clear the width and height inputs
            if (selectedFormat !== "") {
                // Split the selected format (e.g., '125x200' => ['125', '200'])
                var dimensions = selectedFormat.split('x');
                widthInput.value = dimensions[0];  // Set the width
                heightInput.value = dimensions[1]; // Set the height
            } else {
                widthInput.value = '';
                heightInput.value = '';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.view-cover-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const html = document.getElementById(`cover-data-${id}`).innerHTML;
                    document.getElementById('coverModalContent').innerHTML = html;
                });
            });
        });

        function numeralsOnly(event) {
            const charCode = event.which ? event.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
@endsection