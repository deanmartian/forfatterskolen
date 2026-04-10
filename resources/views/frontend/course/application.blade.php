@extends('frontend.layout')

@section('page_title', $course->title . ' &rsaquo; Forfatterskolen')

@section('content')
@php
    $today 	= \Carbon\Carbon::today()->format('Y-m-d');
    $from 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_from)->format('Y-m-d');
    $to 	= \Carbon\Carbon::parse($course->packagesIsShow[0]->full_payment_sale_price_to)->format('Y-m-d');
    $isBetween = (($today >= $from) && ($today <= $to)) ? 1 : 0;
    $start_date = \Carbon\Carbon::parse($course->start_date);
    $price = \App\Http\FrontendHelpers::currencyFormat($isBetween && $course->packagesIsShow[0]->full_payment_sale_price
            ? $course->packagesIsShow[0]->full_payment_sale_price
            : $course->packagesIsShow[0]->full_payment_price);
@endphp
<div class="course-application-wrapper">
    <div class="header" data-bg="https://www.forfatterskolen.no/images-new/course/application-header.png">
    </div>
    <div class="body">
        <div class="container">
            <div class="col-md-8 col-sm-offset-2">
                <div class="form-wrapper">
                    <h3 class="price">
                        {{ $price }} {{ trans('site.currency-text') }}
                    </h3>

                    <form class="form-theme" method="POST" action="{{ route('front.course.process-application', $course->id) }}"
								  id="place_order_form" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="email" class="control-label">
                                {{ trans('site.front.form.email-address') }}
                            </label>
                            <input type="email" id="email" class="form-control" name="email" required
                                   @if(Auth::guest()) value="{{old('email')}}" @else value="{{Auth::user()->email}}"
                                   readonly @endif placeholder="{{ trans('site.front.form.email-address') }}">
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="first_name" class="control-label">
                                    {{ trans('site.front.form.first-name') }}
                                </label>
                                <input type="text" id="first_name" class="form-control" name="first_name" required
                                       @if(Auth::guest()) value="{{old('first_name')}}" @else
                                       value="{{Auth::user()->first_name}}" readonly @endif
                                       placeholder="{{ trans('site.front.form.first-name') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="control-label">
                                    {{ trans('site.front.form.last-name') }}
                                </label>
                                <input type="text" id="last_name" class="form-control" name="last_name" required
                                       @if(Auth::guest()) value="{{old('last_name')}}" @else
                                       value="{{Auth::user()->last_name}}" readonly @endif
                                       placeholder="{{ trans('site.front.form.last-name') }}">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 mb-4">
                                <label for="phone" class="control-label">
                                    {{ trans('site.front.form.phone-number') }}
                                </label>
                                <input type="text" id="phone" class="form-control large-input" name="phone" required
                                       @if(Auth::guest()) value="{{old('phone')}}"
                                       @else value="{{Auth::user()->address['phone']}}" @endif>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="street" class="control-label">
                                    Adresse
                                </label>
                                <input type="text" id="street" class="form-control large-input" name="street" required
                                       @if(Auth::guest()) value="{{old('street')}}"
                                       @else value="{{Auth::user()->address['street'] ?? ''}}" @endif
                                       placeholder="Gateadresse">
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-3 mb-4">
                                <label for="zip" class="control-label">
                                    Postnummer
                                </label>
                                <input type="text" id="zip" class="form-control large-input" name="zip" required
                                       @if(Auth::guest()) value="{{old('zip')}}"
                                       @else value="{{Auth::user()->address['zip'] ?? ''}}" @endif
                                       placeholder="0000">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label for="city" class="control-label">
                                    Sted
                                </label>
                                <input type="text" id="city" class="form-control large-input" name="city" required
                                       @if(Auth::guest()) value="{{old('city')}}"
                                       @else value="{{Auth::user()->address['city'] ?? ''}}" @endif
                                       placeholder="By">
                            </div>
                            @if(Auth::guest())
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="control-label">
                                        {{ trans('site.front.form.create-password') }}
                                    </label>
                                    <input type="password" id="password" class="form-control large-input"
                                           name="password" required>
                                </div>
                            @endif
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 mb-4">
                                <label for="age" class="control-label">
                                    {{ trans('site.front.form.age') }}
                                </label>
                                <input type="number" id="age" class="form-control large-input" name="age"
                                       step="1" value="{{ old('age') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <b>
                                {{ trans('site.application-instruction-title') }}
                            </b>
                            {{-- ul and li stored in translation --}}
                            {!! trans('site.application-instruction-details') !!}
                        </div>

                        <div class="form-group">
                            <div class="file-upload" id="file-upload-application">
                                <i class="fa fa-cloud-upload-alt"></i>
                                <div class="file-upload-text" id="file-upload-application-text">
                                    {{ trans('site.drag-and-drop-files') }} {{ trans('site.or-text') }} 
                                    <a href="javascript:void(0)" class="file-upload-btn">
                                        {{ trans('site.click-here') }}
                                    </a>
                                </div>
                                <input type="file" class="form-control hidden input-file-upload" name="manuscript"
                                id="file-upload" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf,application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/x-iwork-pages-sffpages,.doc,.docx,.pdf,.odt,.pages">
                              </div>
                            <label class="file-label">
                                * {{ trans('site.learner.manuscript.doc-pdf-odt-text') }}
                            </label>
                            <p id="file-upload-application-conversion-message" class="text-info mt-2 d-none">
                                {{ trans('site.converting-document-please-wait') }}
                            </p>
                            <div id="file-upload-application-conversion-error" class="alert alert-danger d-none mt-2" role="alert"></div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn site-btn-global float-end" id="submitOrder">
                                {{ trans('site.submit-application') }}
                            </button>
                        </div>

                        <div class="clearfix"></div>
                    </form>
                </div> <!-- end form-wrapper -->
            </div> <!-- end col-md-10 col-md-offset-1 -->
        </div> <!-- end container -->
    </div> <!-- end body -->
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
    let translations = {
        convertingPleaseWait : "{{ trans('site.converting-document-please-wait') }}",
        couldNotConvertTryAgain : "{{ trans('site.could-not-convert-file-please-try-again') }}",
        releaseToUpload : "{{ trans('site.release-to-upload') }}",
               
    };

    $(document).ready(function () {
        const fileUploadArea = document.getElementById('file-upload-application');
        const fileInput = document.getElementById('file-upload');
        const fileUploadText = document.getElementById('file-upload-application-text');
        const submitButton = document.getElementById('submitOrder');
        const conversionMessageElement = document.getElementById('file-upload-application-conversion-message');
        const conversionErrorElement = document.getElementById('file-upload-application-conversion-error');
        const conversionMessageText = translations.convertingPleaseWait;
        const defaultUploadText = fileUploadText ? fileUploadText.innerHTML : '';
        let isConvertingApplicationFile = false;
        let suppressChangeHandler = false;

        const getFileExtension = (fileName) => {
            if (!fileName) {
                return '';
            }

            const match = fileName.toLowerCase().match(/\.([^.]+)$/);
            return match ? match[1] : '';
        };

        const createDocxFileName = (originalName) => {
            if (!originalName || typeof originalName !== 'string') {
                return 'document.docx';
            }

            const dotIndex = originalName.lastIndexOf('.');

            if (dotIndex <= 0) {
                return originalName.toLowerCase().endsWith('.docx')
                    ? originalName
                    : `${originalName}.docx`;
            }

            const baseName = originalName.substring(0, dotIndex);
            const extension = originalName.substring(dotIndex + 1).toLowerCase();

            if (extension === 'docx') {
                return originalName;
            }

            return `${baseName}.docx`;
        };

        const extractFilenameFromContentDisposition = (header) => {
            if (!header || typeof header !== 'string') {
                return null;
            }

            const utf8Match = header.match(/filename\*=UTF-8''([^;]+)/i);
            if (utf8Match && utf8Match[1]) {
                try {
                    return decodeURIComponent(utf8Match[1]);
                } catch (error) {
                    console.error('Failed to decode UTF-8 filename', error);
                }
            }

            const quotedMatch = header.match(/filename="?([^";]+)"?/i);
            if (quotedMatch && quotedMatch[1]) {
                return quotedMatch[1];
            }

            return null;
        };

        const parseErrorBlob = async (blob) => {
            if (!blob || typeof blob.text !== 'function') {
                return null;
            }

            const text = await blob.text();

            if (!text) {
                return null;
            }

            try {
                return JSON.parse(text);
            } catch (error) {
                return { message: text };
            }
        };

        const getCsrfToken = () => {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');

            if (!csrfMeta) {
                return null;
            }

            const token = csrfMeta.getAttribute('content');

            return typeof token === 'string' && token.trim() !== '' ? token : null;
        };

        const convertFileToDocx = async (file) => {
            const formData = new FormData();
            formData.append('document', file);

            const csrfToken = getCsrfToken();

            if (csrfToken) {
                formData.append('_token', csrfToken);
            }

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
                    const responseBlob = response.data instanceof Blob
                        ? response.data
                        : new Blob(response.data ? [response.data] : [], { type: mimeType });

                    return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
                } catch (error) {
                    if (error && error.response && error.response.data instanceof Blob) {
                        try {
                            const parsed = await parseErrorBlob(error.response.data);
                            if (parsed) {
                                error.response.data = parsed;
                            }
                        } catch (parseError) {
                            console.error('Failed to parse conversion error response', parseError);
                        }
                    }

                    if (!error.response || !error.response.data) {
                        error.response = error.response || {};
                        error.response.data = {
                            errors: {
                                manuscript: [translations.couldNotConvertTryAgain],
                            },
                            message: translations.couldNotConvertTryAgain
                        };
                    }

                    throw error;
                }
            }

            const headers = { 'X-Requested-With': 'XMLHttpRequest' };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch('/documents/convert-to-docx', {
                method: 'POST',
                body: formData,
                headers,
            });

            const contentDisposition = response.headers
                ? (response.headers.get('content-disposition') || response.headers.get('Content-Disposition'))
                : null;

            if (!response.ok) {
                const error = new Error(translations.couldNotConvertTryAgain);
                let errorData = null;

                try {
                    errorData = await response.clone().json();
                } catch (jsonError) {
                    try {
                        errorData = { message: await response.text() };
                    } catch (textError) {
                        errorData = null;
                    }
                }

                error.response = {
                    status: response.status,
                    data: errorData || {
                        errors: {
                            manuscript: [translations.couldNotConvertTryAgain],
                        },
                        message: translations.couldNotConvertTryAgain
                    }
                };

                throw error;
            }

            const data = await response.blob();
            const filename = extractFilenameFromContentDisposition(contentDisposition) || fallbackName;
            const responseBlob = data instanceof Blob ? data : new Blob([data], { type: mimeType });

            return new File([responseBlob], filename, { type: mimeType, lastModified: Date.now() });
        };

        const getErrorMessageFromConversion = (error) => {
            if (!error) {
                return translations.couldNotConvertTryAgain;
            }

            if (error.response && error.response.data) {
                const data = error.response.data;

                if (data.errors && data.errors.manuscript && data.errors.manuscript.length) {
                    return data.errors.manuscript[0];
                }

                if (typeof data.message === 'string' && data.message.trim() !== '') {
                    return data.message;
                }
            }

            if (error.message && error.message.trim() !== '') {
                return error.message;
            }

            return translations.couldNotConvertTryAgain;
        };

        const assignFilesToInput = (input, file) => {
            if (!input || !file) {
                return false;
            }

            const files = Array.isArray(file) ? file : [file];

            try {
                if (typeof DataTransfer !== 'undefined') {
                    const dataTransfer = new DataTransfer();
                    files.forEach((item) => dataTransfer.items.add(item));
                    input.files = dataTransfer.files;
                    return true;
                }
            } catch (error) {
                console.warn('DataTransfer is not available for file assignment.', error);
            }

            try {
                if (typeof ClipboardEvent !== 'undefined') {
                    const clipboardEvent = new ClipboardEvent('');
                    if (clipboardEvent.clipboardData) {
                        files.forEach((item) => clipboardEvent.clipboardData.items.add(item));
                        input.files = clipboardEvent.clipboardData.files;
                        return true;
                    }
                }
            } catch (error) {
                console.warn('ClipboardEvent fallback failed for file assignment.', error);
            }

            return false;
        };

        const updateUploadText = (text) => {
            if (fileUploadText) {
                fileUploadText.innerHTML = text;
            }
        };

        const resetUploadText = () => {
            updateUploadText(defaultUploadText);
        };

        const clearConversionError = () => {
            if (conversionErrorElement) {
                conversionErrorElement.textContent = '';
                conversionErrorElement.classList.add('d-none');
            }
        };

        const showConversionError = (message) => {
            if (conversionErrorElement) {
                conversionErrorElement.textContent = message || translations.couldNotConvertTryAgain;
                conversionErrorElement.classList.remove('d-none');
            }
        };

        const showConversionMessage = () => {
            if (conversionMessageElement) {
                conversionMessageElement.textContent = conversionMessageText;
                conversionMessageElement.classList.remove('d-none');
            }
        };

        const hideConversionMessage = () => {
            if (conversionMessageElement) {
                conversionMessageElement.classList.add('d-none');
            }
        };

        const setConversionState = (state) => {
            isConvertingApplicationFile = !!state;

            if (submitButton) {
                submitButton.disabled = !!state;
            }
        };

        const clearFileSelection = () => {
            if (fileInput) {
                suppressChangeHandler = true;
                fileInput.value = '';
                window.setTimeout(() => {
                    suppressChangeHandler = false;
                }, 0);
            }
        };

        const selectApplicationFile = async (files) => {
            clearConversionError();

            if (!files || !files.length) {
                hideConversionMessage();
                setConversionState(false);
                resetUploadText();
                return;
            }

            const [selectedFile] = files;

            if (!selectedFile) {
                hideConversionMessage();
                setConversionState(false);
                resetUploadText();
                return;
            }

            updateUploadText(selectedFile.name);

            const extension = getFileExtension(selectedFile.name);
            let processedFile = selectedFile;
            let conversionFailed = false;

            if (extension !== 'docx') {
                setConversionState(true);
                showConversionMessage();

                try {
                    processedFile = await convertFileToDocx(selectedFile);
                } catch (error) {
                    conversionFailed = true;
                    showConversionError(getErrorMessageFromConversion(error));
                    clearFileSelection();
                    resetUploadText();
                } finally {
                    hideConversionMessage();
                    setConversionState(false);
                }
            } else {
                hideConversionMessage();
                setConversionState(false);
            }

            if (conversionFailed) {
                return;
            }

            if (processedFile && processedFile.name) {
                updateUploadText(processedFile.name);
            }

            if (fileInput) {
                suppressChangeHandler = true;
                const assigned = assignFilesToInput(fileInput, processedFile);
                window.setTimeout(() => {
                    suppressChangeHandler = false;
                }, 0);

                if (!assigned) {
                    showConversionError('Kunne ikke legge til den konverterte filen automatisk. Prøv igjen i en annen nettleser eller kontakt oss.');
                    clearFileSelection();
                    resetUploadText();
                    return;
                }
            }
        };

        if (fileUploadArea) {
            const dragOverText = translations.releaseToUpload;

            fileUploadArea.addEventListener('dragover', (event) => {
                event.preventDefault();
                fileUploadArea.classList.add('dragover');
                updateUploadText(dragOverText);
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.classList.remove('dragover');
                resetUploadText();
            });

            fileUploadArea.addEventListener('drop', async (event) => {
                event.preventDefault();
                fileUploadArea.classList.remove('dragover');

                const files = event.dataTransfer ? event.dataTransfer.files : null;

                if (files && files.length) {
                    await selectApplicationFile(files);
                } else {
                    resetUploadText();
                }
            });

            fileUploadArea.addEventListener('click', (event) => {
                if (event.target && typeof event.target.closest === 'function' && event.target.closest('input[type="file"]')) {
                    return;
                }

                if (fileInput) {
                    fileInput.click();
                }
            });

            fileUploadArea.querySelectorAll('.file-upload-btn').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();

                    if (fileInput) {
                        fileInput.click();
                    }
                });
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', async (event) => {
                if (suppressChangeHandler) {
                    return;
                }

                await selectApplicationFile(event.target.files);
            });
        }

        const formElement = document.getElementById('place_order_form');

        if (formElement) {
            formElement.addEventListener('submit', (event) => {
                if (isConvertingApplicationFile) {
                    event.preventDefault();
                }
            });
        }
    });

    let editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '15em',
        selector: 'textarea',
        menubar:false,
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        relative_urls: false,

        setup: function(ed) {
            ed.on('keydown', function (e) {
                let body = ed.getBody(), text = tinymce.trim(body.innerText || body.textContent);
                let words = text.split(/[\w\u2019\'-]+/).length - 1;

                // allow delete and f5 keys
                if (words > max_words && e.keyCode !== 8 && e.keyCode !== 116) {
                    return tinymce.dom.Event.cancel(e);
                }
            });

        }
    };
    tinymce.init(editor_config);
</script>
@stop