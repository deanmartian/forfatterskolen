@extends('frontend.layout')

@section('title')
    <?php
        $pageMeta = \App\PageMeta::where('url', url()->current())->first();
    ?>

    @if ($pageMeta)
        <title>{{ $pageMeta->meta_title }}</title>
    @else
        <title>Shop Manuscripts &rsaquo; Forfatterskolen</title>
    @endif
@stop

@section('content')

    <div class="manuscript-page manuscript-page-new">
        <div class="header">
            <div class="container main-container">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="text-center mb-5">
                            {{ trans('site.front.shop-manuscript.title') }}
                        </h1>
                    </div>
                    {{--<div class="col-sm-12 top-page-container">
                        --}}{{--<img src="{{ asset('images-new/adult-reading-book.jpg') }}" alt="">--}}{{--
                        <iframe src="https://fast.wistia.com/embed/medias/scuv6yv5qy" frameborder="0" allowfullscreen="allowfullscreen"></iframe>
                    </div>--}}
                </div>
            </div>
        </div>        

        {{--  data-bg="https://www.forfatterskolen.no/images-new/coffee-book.png" --}}
        <div class="row second-section mx-0">
            <div class="container details">
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-7">
                        <div class="title mb-5 h1 mt-0">
                            {{ trans('site.front.shop-manuscript.first-section.title') }}
                        </div>
    
                        {!! html_entity_decode(trans('site.front.shop-manuscript.first-section.description')) !!}
                        {{--<button class="btn site-btn-global-w-arrow" data-toggle="modal" data-target="#editorsModal">Redaktører</button>--}}
                    </div>
                    <div class="col-md-5">
                        <img class="w-100" src="{{ asset('images-new/shop-manuscript/top-right.png') }}" 
                        alt="shop manuscript top right">
                    </div>
                </div>
            </div>
        </div>

        {{-- data-bg="https://www.forfatterskolen.no/images-new/notebook-pen.png" --}}
        {{-- <div class="third-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 left-container">
                        <img src="{{ asset('images-new/shop-manuscript/notepad.png') }}" class="w-100" alt="notepad">
                    </div>
                    <div class="col-md-6 details" id="testManuscript">
                        <h2 class="title mb-4">
                            {{ trans('site.front.shop-manuscript.form.title') }}
                        </h2>

                        <form method="POST" enctype="multipart/form-data" action="{{ route('front.shop-manuscript.test_manuscript') }}">
                            {{ csrf_field() }}
                            <input type="file" class="hidden" name="manuscript" id="file-upload"
                            accept="application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/pdf, application/vnd.oasis.opendocument.text">
                            <input type="hidden" name="word_count" id="test-manuscript-word-count">
                            <label class="mb-4 mt-3">
                                <span class="instruction">{{ trans('site.front.shop-manuscript.form.instruction') }}</span>
                                <br>
                                <span class="note"><i class="info-icon"></i> {{ trans('site.front.shop-manuscript.form.note') }}</span>
                            </label>
                            <div class="input-group mb-4">
                                <input type="text" readonly class="form-control disabled" required>
                                <div class="input-group-append">
                                    <button class="btn bg-site-red select-manuscript" type="button">
                                        {{ trans('site.front.shop-manuscript.form.select-document') }}
                                    </button>
                                </div>
                            </div>
                            <label for="file-upload" class="file-upload-label">
                                <div class="file-upload" id="file-upload-area">
                                    <div class="file-upload-text">
                                        <a href="javascript:void(0)" class="file-upload-btn">Klikk her</a> for å laste opp filen din eller <br>
                                        dra filen din hit.
                                    </div>
                                  </div>
                              </label>
                            <div class="margin-top">
                                <button class="btn site-btn-global-w-arrow" type="submit">
                                    {{ trans('site.front.upload') }} 
                                    <img src="{{ asset('images-new/icon/upload.png') }}" alt="">
                                </button>
                            </div>

                            <div class="price-increase-note">
                                {!! trans('site.shop-manuscript-price-increase-note') !!}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="third-section word-count-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 left-container">
                        <img src="{{ asset('images-new/shop-manuscript/notepad.png') }}" class="w-100" alt="notepad illustration">
                    </div>
                    <div class="col-md-6 details" id="wordCountTool">
                        <h2 class="title mb-4">
                            {{ trans('site.front.shop-manuscript.form.title') }}
                        </h2>
                        <label class="mb-4 mt-3">
                            <span class="instruction">{{ trans('site.front.shop-manuscript.form.instruction') }}</span>
                            <br>
                            <span class="note"><i class="info-icon"></i> {{ trans('site.front.shop-manuscript.form.note') }}</span>
                        </label>

                        <input type="file" class="hidden" id="word-count-file" accept=".doc,.docx,.pdf,.odt">
                        <label for="word-count-file" class="file-upload-label">
                            <div class="file-upload" id="word-count-upload-area">
                                <div class="file-upload-text" id="word-count-upload-text">
                                    <a href="javascript:void(0)"
                                    class="word-count-file-trigger file-upload-btn">Klikk her</a>
                                    for å laste opp filen din eller <br>
                                    dra filen din hit.
                                </div>
                            </div>
                        </label>

                        <div class="form-group mt-3">
                            <label for="manual-word-count">{{ trans('site.enter-words-manually') }}</label>
                            <input type="number" min="1" step="1" class="form-control" id="manual-word-count" 
                            placeholder="{{ trans('site.enter-words-manually-placeholder') }}">
                            <small class="form-text text-muted">{{ trans('site.enter-words-manually-note') }}</small>
                        </div>

                        {{-- <div class="word-count-feedback mt-3" id="word-count-feedback">
                            Velg en DOCX-, PDF-, DOC- eller ODT-fil og klikk på knappen under for å beregne ord og pris.
                        </div>

                        <div class="word-count-feedback mt-2" id="word-count-price-feedback"></div> --}}

                        <div class="margin-top">
                            <button class="btn site-btn-global-w-arrow word-count-process-btn" type="button">
                                {{ trans('site.front.upload') }} 
                                <img src="{{ asset('images-new/icon/upload.png') }}" alt="">
                            </button>
                        </div>

                        <div class="price-increase-note">
                            {!! trans('site.shop-manuscript-price-increase-note') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="fourth-section">
            <div class="container">
                <div class="row">

                    <?php
                        $shopManuscripts_chunk = $shopManuscripts->chunk(3);
                    ?>

                    <div id="manuscripts-carousel" class="carousel slide" data-ride="carousel" data-interval="false">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            @for($i=0; $i<=$shopManuscripts_chunk->count() - 1;$i++)
                            <li data-target="#manuscripts-carousel" data-slide-to="{{$i}}" @if($i == 0) class="active" @endif></li>
                            @endfor
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">
                            @foreach($shopManuscripts_chunk as $k => $shopManuscripts)
                                <div class="carousel-item {{ $k==0 ? 'active' : '' }}">
                                    @foreach($shopManuscripts as $shopManuscript)
                                        <div class="col-xs-4 col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-header text-center">
                                                    <div class="image-wrapper">
                                                        <img src="{{ asset('images-new/icon/open-book.png') }}" alt="open-book">
                                                    </div>
                                                    <h2>{{ $shopManuscript->max_words }} ORD</h2>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="h1">{{ $shopManuscript->title }}</div>
                                                    <p>{{ $shopManuscript->description }}</p>
                                                    <div class="h1 price">
                                                        @if(!Str::contains($shopManuscript->title, 'Start') &&
                                                        !Str::contains($shopManuscript->title, '1'))
                                                            @php
                                                                $extra_price = ($shopManuscript->max_words - 17500) * 
                                                                FrontendHelpers::manuscriptExcessPerWordPrice();
                                                                $new_price = $shopManuscript->full_payment_price + $extra_price;
                                                            @endphp
                                                            {{ \App\Http\FrontendHelpers::formatCurrency($new_price) }} KR
                                                        @else
                                                            {{ \App\Http\FrontendHelpers::formatCurrency($shopManuscript->full_payment_price) }} KR
                                                        @endif
                                                    </div>
                                                    <a class="btn buy-btn" href="{{ route($checkoutRoute, $shopManuscript->id) }}">
                                                        {{ trans('site.front.buy') }}
                                                        <i class="fa fa-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- <div id="manuscripts-carousel" class="carousel slide" data-ride="carousel" data-interval="false">

                        <!-- Indicators -->
                        <ul class="carousel-indicators">
                            @for($i=0; $i<=$shopManuscripts_chunk->count() - 1;$i++)
                            <li data-target="#manuscripts-carousel" data-slide-to="{{$i}}" @if($i == 0) class="active" @endif></li>
                            @endfor
                        </ul>

                        <!-- The slideshow -->
                        <div class="container carousel-inner no-padding">
                            @foreach($shopManuscripts_chunk as $k => $shopManuscripts)
                                <div class="carousel-item {{ $k==0 ? 'active' : '' }}">
                                    @foreach($shopManuscripts as $shopManuscript)
                                        <div class="col-xs-3 col-sm-3 col-md-3">
                                            <div class="panel panel-default">
                                                <div class="overlay"></div>
                                                <div class="panel-body">
                                                    <div class="circle">
                                                        <div class="circle-white">
                                                            <h3 class="word-count">{{ $shopManuscript->max_words }}</h3>
                                                            <h2>ORD</h2>
                                                        </div>
                                                    </div>
                                                    <div class="h1">{{ $shopManuscript->title }}</div>
                                                    <p>{{ $shopManuscript->description }}</p>
                                                    <div class="h1 price">{{ \App\Http\FrontendHelpers::formatCurrency($shopManuscript->full_payment_price) }} KR</div>
                                                    <a class="btn buy-btn" href="{{ route($checkoutRoute, $shopManuscript->id) }}">
                                                        {{ trans('site.front.buy') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <!-- Left and right controls -->
                        <a class="carousel-control-prev" href="#manuscripts-carousel" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#manuscripts-carousel" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>
                    </div> --}}
                </div>
            </div>
        </div> <!-- end fourth section -->

        <div class="last-section" data-bg="https://www.forfatterskolen.no/images-new/shop-manuscript/coaching-bg.png">
            <div class="container">
                <div class="text-center">
                    <div class="coaching-details">
                        <img data-src="https://www.forfatterskolen.no/images-new/other-services/open-book.png" alt="open book">
                        <h2>
                            {{ trans('site.front.coaching-timer.title') }}
                        </h2> 
                    </div>

                    <a href="{{ route('front.coaching-timer') }}">
                        {{ ucwords(trans('site.front.view')) }}
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </div>

                {{-- <div class="col-sm-5 other-services-container">
                    <a href="{{ route('front.coaching-timer') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/open-book.png" alt="open book">
                            </div>

                            <div class="col-sm-9">
                                <div class="h1 mt-0">
                                    {{ trans('site.front.coaching-timer.title') }}
                                </div>
                                <a href="{{ route('front.coaching-timer') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('front.correction') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/book.png">
                            </div>

                            <div class="col-sm-9">
                                <h1>
                                    {{ trans('site.front.correction.title') }}
                                </h1>
                                <a href="{{ route('front.correction') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('front.copy-editing') }}">
                        <div class="row box-white">
                            <div class="col-sm-3">
                                <img data-src="https://www.forfatterskolen.no/images-new/other-services/list.png">
                            </div>

                            <div class="col-sm-9">
                                <h1>{{ trans('site.front.copy-editing.title') }}</h1>
                                <a href="{{ route('front.copy-editing') }}" class="link-with-arrow">
                                    {{ ucwords(trans('site.front.view')) }}
                                </a>
                            </div>
                        </div>
                    </a>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="editorsModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        X
                    </button>
                </div>
                <div class="modal-body">
                    {{--@foreach($editors->chunk(3) as $editor_chunk)
                        <div class="row masonry-grid">
                            @foreach($editor_chunk as $editor)
                                <div class="col-sm-4 masonry-column">
                                    <div class="panel panel-default">
                                        <div class="panel-header">
                                        </div>
                                        <div class="panel-body text-center">
                                            <div class="editor-circle">
                                                <img src="{{ asset($editor['editor_image']) }}" alt="" class="rounded-circle">
                                            </div>
                                            <p>
                                                <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach--}}
                    <div class="card-columns">
                        @foreach($editors->chunk(3) as $editor_chunk)
                            <div class="card-container">
                            @foreach($editor_chunk as $editor)
                                <div class="card">
                                    <div class="card-header">
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="editor-circle">
                                            <img src="{{ asset($editor['editor_image']) }}" alt="editor image" class="rounded-circle">
                                        </div>
                                        <p>
                                            <strong class="editor-name">{{ $editor['name'] }}</strong> {{ $editor['description'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Session::has('manuscript_test'))
        <div id="manuscriptTestModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        {!! Session::get('manuscript_test') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Session::has('manuscript_test_error'))
        <div id="manuscriptTestErrorModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div style="color: red; font-size: 24px"><i class="fa fa-close"></i></div>
                        {!! Session::get('manuscript_test_error') !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="testManuscriptModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Regn ut for meg</h4>
                </div>
                <div class="modal-body">

                </div>
            </div>

        </div>
    </div>

    <div id="wordCountResultModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <div id="wordCountResultBody"></div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    @php
        $manuscriptPlans = $shopManuscripts->map(function ($plan) use ($checkoutRoute) {
            return [
                'id' => $plan->id,
                'title' => $plan->title,
                'max_words' => $plan->max_words,
                'full_payment_price' => $plan->full_payment_price,
                'checkout_url' => route($checkoutRoute, $plan->id),
            ];
        })->values();

        $excessPerWordPrice = \App\Http\FrontendHelpers::manuscriptExcessPerWordPrice();
    @endphp

    <script src="https://unpkg.com/mammoth@1.4.21/mammoth.browser.min.js"></script>
    <script>
        const manuscriptPlans = @json($manuscriptPlans);
        const excessPerWordPrice = {{ $excessPerWordPrice }};
        const manuscriptBaseWordLimit = 17500;
        const storeTempUploadUrl = '{{ route('front.shop-manuscript.store-temp-upload') }}';
        const csrfToken = '{{ csrf_token() }}';

        $(document).ready(function(){
            const getFileExtension = (fileName) => {
                if (!fileName) {
                    return '';
                }

                const match = fileName.toLowerCase().match(/\.([^.]+)$/);
                return match ? match[1] : '';
            };

            const mammothPreferredExtensions = ['doc', 'docx'];
            const mammothAvailable = typeof window !== 'undefined'
                && typeof window.mammoth !== 'undefined'
                && typeof window.mammoth.extractRawText === 'function';

            const shouldUseMammothForExtension = (extension) => {
                if (!extension) {
                    return false;
                }

                return mammothPreferredExtensions.includes(extension) && mammothAvailable;
            };

            const countWordsFromText = (text) => {
                if (typeof text !== 'string') {
                    return 0;
                }

                const normalised = text.replace(/[\r\n\t]+/g, ' ').trim();
                if (!normalised) {
                    return 0;
                }

                const matches = normalised.match(/\S+/g);
                return matches ? matches.length : 0;
            };

            const extractWordCountWithMammoth = (file) => new Promise((resolve, reject) => {
                if (!file || !mammothAvailable) {
                    resolve(null);
                    return;
                }

                const reader = new FileReader();

                reader.onload = (event) => {
                    const arrayBuffer = event.target ? event.target.result : null;

                    if (!arrayBuffer) {
                        resolve(null);
                        return;
                    }

                    window.mammoth.extractRawText({ arrayBuffer })
                        .then((result) => {
                            const text = result && typeof result.value === 'string' ? result.value : '';
                            resolve(countWordsFromText(text));
                        })
                        .catch((error) => {
                            reject(error);
                        });
                };

                reader.onerror = () => {
                    reject(reader.error || new Error('Kunne ikke lese dokumentet.'));
                };

                try {
                    reader.readAsArrayBuffer(file);
                } catch (error) {
                    reject(error);
                }
            });

            @if(Session::has('manuscript_test'))
                $('#manuscriptTestModal').modal('show');
            @endif

            @if(Session::has('manuscript_test_error'))
                $('#manuscriptTestErrorModal').modal('show');
            @endif

            let form = $('#testManuscript form');
            $('.file-upload-btn').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=text]').click(function(){
                form.find('input[type=file]').click();
            });
            form.find('input[type=file]').on('change', function(){
                let file = $(this).val().split('\\').pop();
                form.find('input[type=text]').val(file);
            });
            form.on('submit', function(e){
                let file = form.find('input[type=file]').val().split('\\').pop();
                if( file == '' ){
                    alert('Please select a document file.');
                    e.preventDefault();
                }
            });

            const manuscriptTestFormElement = document.querySelector('#testManuscript form');
            const manuscriptTestHiddenInput = document.getElementById('test-manuscript-word-count');
            const manuscriptTestFileInput = document.getElementById('file-upload');
            let manuscriptSubmittingWithMammoth = false;

            if (manuscriptTestFormElement && manuscriptTestFileInput) {
                manuscriptTestFormElement.addEventListener('submit', (event) => {
                    if (manuscriptSubmittingWithMammoth) {
                        manuscriptSubmittingWithMammoth = false;
                        return;
                    }

                    const files = manuscriptTestFileInput.files;
                    if (!files || !files.length) {
                        if (manuscriptTestHiddenInput) {
                            manuscriptTestHiddenInput.value = '';
                        }

                        return;
                    }

                    const [file] = files;
                    const extension = getFileExtension(file.name || manuscriptTestFileInput.value);

                    if (!shouldUseMammothForExtension(extension)) {
                        if (manuscriptTestHiddenInput) {
                            manuscriptTestHiddenInput.value = '';
                        }

                        return;
                    }

                    event.preventDefault();

                    extractWordCountWithMammoth(file)
                        .then((wordCount) => {
                            if (manuscriptTestHiddenInput) {
                                manuscriptTestHiddenInput.value = Number.isInteger(wordCount) && wordCount > 0
                                    ? wordCount
                                    : '';
                            }
                        })
                        .catch((error) => {
                            console.error('Unable to count words with Mammoth for manuscript test form', error);
                            if (manuscriptTestHiddenInput) {
                                manuscriptTestHiddenInput.value = '';
                            }
                        })
                        .finally(() => {
                            manuscriptSubmittingWithMammoth = true;
                            manuscriptTestFormElement.submit();
                        });
                });
            }

            /* const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('file-upload');
            const fileUploadText = document.querySelector('.file-upload-text');
            const textWithBrowseButton = '<a href="javascript:void(0)" class="file-upload-btn">Klikk her</a> for å laste opp filen din eller <br>' 
                +'dra filen din hit.';

            const updateText = (text) => {
                fileUploadText.innerHTML = text;
            };

            fileUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
                updateText('Release to upload');
            });

            fileUploadArea.addEventListener('dragleave', () => {
                fileUploadArea.classList.remove('dragover');
                updateText(textWithBrowseButton);
            });

            fileUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;

                // Do something with the dropped files (e.g., display file names)
                for (let i = 0; i < files.length; i++) {
                    console.log('Dropped file:', files[i].name);
                }

                // You can also update the file input value
                fileInput.files = files;
                
                const selectedText = fileInput.files.length > 0 ? fileInput.files[0].name 
                : textWithBrowseButton;
                updateText(selectedText);
            });

            // Handle file input change event (when a file is selected using the Browse button)
            fileInput.addEventListener('change', () => {
                const selectedText = fileInput.files.length > 0 ? fileInput.files[0].name
                    : textWithBrowseButton;
                updateText(selectedText);
            }); */

        const wordCountContainer = document.getElementById('wordCountTool');
        if (wordCountContainer) {
            const wordCountFileInput = document.getElementById('word-count-file');
            const wordCountUploadArea = document.getElementById('word-count-upload-area');
            const wordCountUploadText = document.getElementById('word-count-upload-text');
            const wordCountFeedback = document.getElementById('word-count-feedback');
            const wordCountPriceFeedback = document.getElementById('word-count-price-feedback');
            const wordCountModal = $('#wordCountResultModal');
            const wordCountModalBody = document.getElementById('wordCountResultBody');
            const manualWordCountInput = document.getElementById('manual-word-count');
            const defaultWordCountText = wordCountUploadText ? wordCountUploadText.innerHTML : '';
            let selectedWordCountFile = null;
            let processingWordCount = false;
            let processButton = null;
            const allowedWordCountExtensions = ['docx', 'pdf', 'doc', 'odt'];
            const manualWordCountErrorMessage = 'Vennligst skriv inn et gyldig tall i feltet for ordantall eller la det stå tomt.';

            const showGlobalAlert = (messages, type = 'danger') => {
                const normalisedMessages = Array.isArray(messages)
                    ? messages.filter((message) => !!message)
                    : (messages ? [messages] : []);
                const uniqueMessages = Array.from(new Set(normalisedMessages.map((message) => message.trim())));

                if (!uniqueMessages.length) {
                    return;
                }

                let alertElement = document.getElementById('fixed_to_bottom_alert');
                if (!alertElement) {
                    alertElement = document.createElement('div');
                    alertElement.id = 'fixed_to_bottom_alert';
                    alertElement.className = 'alert global-alert-box';
                    alertElement.setAttribute('role', 'alert');
                    alertElement.style.zIndex = '9';
                    alertElement.style.minWidth = '300px';

                    const closeButton = document.createElement('a');
                    closeButton.href = '#';
                    closeButton.className = 'close';
                    closeButton.setAttribute('data-dismiss', 'alert');
                    closeButton.setAttribute('aria-label', 'close');
                    closeButton.setAttribute('title', 'close');
                    closeButton.innerHTML = '&times;';
                    closeButton.addEventListener('click', (event) => {
                        event.preventDefault();
                        if (alertElement.parentNode) {
                            alertElement.parentNode.removeChild(alertElement);
                        } else {
                            alertElement.remove();
                        }
                    });

                    const list = document.createElement('ul');
                    alertElement.appendChild(closeButton);
                    alertElement.appendChild(list);

                    document.body.appendChild(alertElement);
                }

                alertElement.classList.add('alert', 'global-alert-box');
                alertElement.classList.remove('alert-danger', 'alert-success', 'alert-info', 'alert-warning', 'alert-primary');
                alertElement.classList.add(`alert-${type}`);

                let list = alertElement.querySelector('ul');
                if (!list) {
                    list = document.createElement('ul');
                    alertElement.appendChild(list);
                }

                list.innerHTML = '';
                uniqueMessages.forEach((message) => {
                    const item = document.createElement('li');
                    item.innerHTML = message;
                    list.appendChild(item);
                });

                alertElement.style.display = 'block';
                alertElement.classList.remove('d-none');
            };

            const getManualWordCount = (options = {}) => {
                const { validate = false } = options;

                if (!manualWordCountInput) {
                    return { value: null, isValid: true, hasValue: false };
                }

                const rawValue = manualWordCountInput.value;
                const trimmed = typeof rawValue === 'string'
                    ? rawValue.trim()
                    : String(rawValue || '').trim();

                if (!trimmed) {
                    if (validate) {
                        manualWordCountInput.classList.remove('is-invalid');
                    }

                    return { value: null, isValid: true, hasValue: false };
                }

                const normalised = trimmed.replace(/\s+/g, '');
                const parsed = Number.parseInt(normalised, 10);
                const isValid = Number.isFinite(parsed) && parsed > 0;

                if (validate) {
                    manualWordCountInput.classList.toggle('is-invalid', !isValid);
                }

                return {
                    value: isValid ? parsed : null,
                    isValid,
                    hasValue: true,
                };
            };

            const setFeedback = (message, isError = false) => {
                if (!wordCountFeedback) {
                    return;
                }
                wordCountFeedback.textContent = message;
                wordCountFeedback.classList.toggle('text-danger', isError);
            };

            const setPriceFeedback = (message, isError = false) => {
                if (!wordCountPriceFeedback) {
                    return;
                }
                wordCountPriceFeedback.innerHTML = message;
                wordCountPriceFeedback.classList.toggle('text-danger', !!isError && message !== '');
            };

            const updateWordCountText = (text) => {
                if (wordCountUploadText) {
                    wordCountUploadText.innerHTML = text;
                }
            };

            const resetUploadText = () => {
                if (selectedWordCountFile) {
                    updateWordCountText(selectedWordCountFile.name);
                } else {
                    updateWordCountText(defaultWordCountText);
                }
            };

            const formatPrice = (value) => {
                if (typeof value !== 'number' || Number.isNaN(value)) {
                    return null;
                }

                return `${new Intl.NumberFormat('no-NO').format(Math.round(value))} KR`;
            };

            const findSuggestedPlan = (wordCount) => {
                if (!Array.isArray(manuscriptPlans) || manuscriptPlans.length === 0) {
                    return null;
                }

                const sortedPlans = manuscriptPlans.slice().sort((a, b) => a.max_words - b.max_words);
                return sortedPlans.find((plan) => wordCount <= plan.max_words) || null;
            };

            const calculatePrice = (wordCount) => {
                const plan = findSuggestedPlan(wordCount);

                if (!plan) {
                    return null;
                }

                let price = parseFloat(plan.full_payment_price);
                if (Number.isNaN(price)) {
                    price = 0;
                }

                if (wordCount > manuscriptBaseWordLimit) {
                    const excessWords = wordCount - manuscriptBaseWordLimit;
                    price += excessWords * excessPerWordPrice;
                }

                return {
                    plan,
                    price,
                    formattedPrice: formatPrice(price),
                };
            };

            const showWordCountModal = (content, isError = false) => {
                if (!wordCountModalBody) {
                    return;
                }

                wordCountModalBody.innerHTML = content;
                wordCountModalBody.classList.toggle('text-danger', isError);

                if (wordCountModal && typeof wordCountModal.modal === 'function') {
                    wordCountModal.modal('show');
                }
            };

            const handleWordCountOutcome = ({
                effectiveWordCount = null,
                formattedPrice = null,
                checkoutLink = null,
                message = '',
                feedbackMessage = null,
            } = {}) => {
                let modalContent = message || '';

                if (!modalContent && effectiveWordCount) {
                    const priceLine = formattedPrice
                        ? `<h3 class="no-margin-top">Prisen for ditt manus er kroner: ${formattedPrice}</h3>`
                        : '';
                    const linkLine = checkoutLink
                        ? `<a href="${checkoutLink}" class="btn btn-theme">Bestill nå</a>`
                        : '';
                    const contactLine = !priceLine && !linkLine
                        ? '<p>Ta kontakt med oss for et tilbud tilpasset ditt manus.</p>'
                        : '';

                    modalContent = `Manuset ditt er på ${effectiveWordCount} ord <br />${priceLine}${linkLine}${contactLine}`;
                }

                if (modalContent) {
                    showWordCountModal(modalContent);
                }

                if (effectiveWordCount) {
                    const finalFeedbackMessage = feedbackMessage
                        ? feedbackMessage
                        : `Manuskriptet inneholder omtrent ${effectiveWordCount} ord.`;
                    setFeedback(finalFeedbackMessage);
                    setPriceFeedback('Resultatet er klart i pop-up-vinduet.');
                } else if (modalContent) {
                    setFeedback('Beregningen ble fullført.');
                    setPriceFeedback('Resultatet er klart i pop-up-vinduet.');
                } else {
                    setFeedback('Beregningen ble fullført.');
                    setPriceFeedback('');
                }
            };

            const storeTempFileOnServer = (file, providedWordCount = null) => {
                if (!storeTempUploadUrl) {
                    return Promise.resolve(null);
                }

                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('manuscript', file);
                if (Number.isInteger(providedWordCount) && providedWordCount > 0) {
                    formData.append('word_count', providedWordCount);
                }

                return fetch(storeTempUploadUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                }).then(async (response) => {
                    const responseText = await response.text();
                    let data = null;

                    if (responseText) {
                        try {
                            data = JSON.parse(responseText);
                        } catch (error) {
                            // Ignore JSON parse errors and fall back to generic handling.
                        }
                    }

                    if (!response.ok) {
                        const errorMessages = [];

                        if (data && data.message && data.message !== 'The given data was invalid.') {
                            errorMessages.push(data.message);
                        }

                        if (data && data.errors) {
                            Object.values(data.errors).forEach((errorEntry) => {
                                if (Array.isArray(errorEntry)) {
                                    errorEntry.forEach((entry) => {
                                        if (entry) {
                                            errorMessages.push(entry);
                                        }
                                    });
                                } else if (errorEntry) {
                                    errorMessages.push(errorEntry);
                                }
                            });
                        }

                        if (!errorMessages.length) {
                            errorMessages.push('Kunne ikke lagre resultatet på serveren. Prøv igjen senere.');
                        }

                        const error = new Error(errorMessages[0]);
                        error.alertMessages = errorMessages;
                        error.responseData = data;

                        throw error;
                    }

                    return data || {};
                });
            };

            const finalizeProcessing = () => {
                processingWordCount = false;
                if (processButton) {
                    processButton.disabled = false;
                }
            };

            const processFile = (file) => {
                if (!file) {
                    return;
                }

                const extension = getFileExtension(file.name);
                const useMammoth = shouldUseMammothForExtension(extension);

                if (!allowedWordCountExtensions.includes(extension)) {
                    setFeedback('Vennligst velg en DOCX-, PDF-, DOC- eller ODT-fil, eller skriv inn antall ord manuelt.', true);
                    setPriceFeedback('');
                    if (wordCountFileInput) {
                        wordCountFileInput.value = '';
                    }
                    resetUploadText();
                    selectedWordCountFile = null;
                    return;
                }

                const manualWordCountDetails = getManualWordCount({ validate: true });
                if (manualWordCountDetails.hasValue && !manualWordCountDetails.isValid) {
                    showGlobalAlert(manualWordCountErrorMessage, 'danger');
                    setFeedback(manualWordCountErrorMessage, true);
                    setPriceFeedback('');
                    return;
                }

                const providedManualWordCount = manualWordCountDetails.value;

                updateWordCountText(file.name);
                setFeedback(useMammoth
                    ? 'Bruker Mammoth til å beregne antall ord ...'
                    : 'Laster opp og beregner antall ord ...');
                setPriceFeedback('');
                processingWordCount = true;
                if (processButton) {
                    processButton.disabled = true;
                }

                const uploadPromise = useMammoth
                    ? extractWordCountWithMammoth(file)
                        .then((wordCount) => {
                            const mammothWordCount = Number.isInteger(wordCount) && wordCount > 0 ? wordCount : null;
                            const effectiveWordCount = Number.isInteger(providedManualWordCount) && providedManualWordCount > 0
                                ? providedManualWordCount
                                : mammothWordCount;

                            return storeTempFileOnServer(file, effectiveWordCount);
                        })
                        .catch((error) => {
                            console.error('Unable to count words with Mammoth for word-count tool', error);
                            return storeTempFileOnServer(file, providedManualWordCount);
                        })
                    : storeTempFileOnServer(file, providedManualWordCount);

                uploadPromise
                    .then((serverData) => {
                        const serverWordCount = Number.isInteger(serverData && serverData.word_count)
                            ? serverData.word_count
                            : null;
                        const manualWordCountToUse = Number.isInteger(providedManualWordCount) && providedManualWordCount > 0
                            ? providedManualWordCount
                            : null;
                        const effectiveWordCount = manualWordCountToUse !== null ? manualWordCountToUse : serverWordCount;
                        const priceDetails = effectiveWordCount ? calculatePrice(effectiveWordCount) : null;
                        const formattedPrice = serverData && serverData.formatted_price
                            ? serverData.formatted_price
                            : (priceDetails && priceDetails.formattedPrice ? priceDetails.formattedPrice : null);
                        const checkoutLink = serverData && serverData.plan && serverData.plan.checkout_url
                            ? serverData.plan.checkout_url
                            : (priceDetails && priceDetails.plan && priceDetails.plan.checkout_url
                                ? priceDetails.plan.checkout_url
                                : null);

                        handleWordCountOutcome({
                            effectiveWordCount,
                            formattedPrice,
                            checkoutLink,
                            message: serverData && serverData.message ? serverData.message : '',
                        });
                    })
                    .catch((error) => {
                        const errorMessages = Array.isArray(error && error.alertMessages)
                            ? error.alertMessages.filter((message) => !!message)
                            : [];
                        const fallbackMessage = typeof error === 'string'
                            ? error
                            : (error && error.message)
                                ? error.message
                                : 'Kunne ikke beregne antall ord. Prøv igjen senere.';
                        const messagesToShow = errorMessages.length ? errorMessages : [fallbackMessage];
                        showGlobalAlert(messagesToShow, 'danger');
                        setFeedback('Kunne ikke beregne antall ord. Se varselet for detaljer.', true);
                        setPriceFeedback('');
                    })
                    .finally(() => {
                        finalizeProcessing();
                    });
            };

            const processManualWordCount = (wordCount) => {
                if (!Number.isInteger(wordCount) || wordCount <= 0) {
                    showGlobalAlert(manualWordCountErrorMessage, 'danger');
                    setFeedback(manualWordCountErrorMessage, true);
                    setPriceFeedback('');
                    return;
                }

                setFeedback('Beregner pris basert på manuelt ordantall ...');
                setPriceFeedback('');
                processingWordCount = true;
                if (processButton) {
                    processButton.disabled = true;
                }

                const priceDetails = calculatePrice(wordCount);
                const formattedPrice = priceDetails && priceDetails.formattedPrice
                    ? priceDetails.formattedPrice
                    : null;
                const checkoutLink = priceDetails && priceDetails.plan && priceDetails.plan.checkout_url
                    ? priceDetails.plan.checkout_url
                    : null;

                handleWordCountOutcome({
                    effectiveWordCount: wordCount,
                    formattedPrice,
                    checkoutLink,
                    feedbackMessage: `Manuelt ordantall: ${wordCount} ord.`,
                });

                finalizeProcessing();
            };

            const selectFile = (files) => {
                if (!files || !files.length) {
                    selectedWordCountFile = null;
                    if (wordCountFileInput) {
                        wordCountFileInput.value = '';
                    }
                    resetUploadText();
                    setFeedback('Velg en fil eller skriv inn antall ord manuelt og klikk på knappen for å beregne ord og pris.');
                    setPriceFeedback('');
                    return;
                }

                const [file] = files;
                const extension = getFileExtension(file.name);

                if (!allowedWordCountExtensions.includes(extension)) {
                    selectedWordCountFile = null;
                    if (wordCountFileInput) {
                        wordCountFileInput.value = '';
                    }
                    resetUploadText();
                    setFeedback('Vennligst velg en DOCX-, PDF-, DOC- eller ODT-fil, eller skriv inn antall ord manuelt.', true);
                    setPriceFeedback('');
                    return;
                }

                selectedWordCountFile = file;
                updateWordCountText(file.name);
                setFeedback('Fil valgt. Klikk på knappen for å beregne ord og pris.');
                setPriceFeedback('');
            };

            if (manualWordCountInput) {
                manualWordCountInput.addEventListener('input', () => {
                    getManualWordCount({ validate: true });
                });
            }

            if (wordCountFileInput) {
                wordCountFileInput.addEventListener('change', (event) => {
                    selectFile(event.target.files);
                });
            }

            wordCountContainer.querySelectorAll('.word-count-file-trigger').forEach((button) => {
                button.addEventListener('click', () => {
                    if (wordCountFileInput) {
                        wordCountFileInput.click();
                    }
                });
            });

            processButton = wordCountContainer.querySelector('.word-count-process-btn');
            if (processButton) {
                processButton.addEventListener('click', () => {
                    if (processingWordCount) {
                        return;
                    }

                    const manualWordCountDetails = getManualWordCount({ validate: true });
                    const manualWordCountValue = manualWordCountDetails && manualWordCountDetails.isValid
                        ? manualWordCountDetails.value
                        : null;

                    if (!selectedWordCountFile) {
                        if (manualWordCountDetails && manualWordCountDetails.hasValue) {
                            if (!manualWordCountDetails.isValid || !Number.isInteger(manualWordCountValue)) {
                                showGlobalAlert(manualWordCountErrorMessage, 'danger');
                                setFeedback(manualWordCountErrorMessage, true);
                                setPriceFeedback('');
                                return;
                            }

                            processManualWordCount(manualWordCountValue);
                            return;
                        }

                        setFeedback('Vennligst velg en fil eller skriv inn antall ord før du beregner.', true);
                        setPriceFeedback('');
                        return;
                    }

                    processFile(selectedWordCountFile);
                });
            }

            if (wordCountUploadArea) {
                wordCountUploadArea.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    wordCountUploadArea.classList.add('dragover');
                    updateWordCountText('Slipp filen for å laste opp');
                });

                wordCountUploadArea.addEventListener('dragleave', () => {
                    wordCountUploadArea.classList.remove('dragover');
                    resetUploadText();
                });

                wordCountUploadArea.addEventListener('drop', (event) => {
                    event.preventDefault();
                    wordCountUploadArea.classList.remove('dragover');
                    if (event.dataTransfer && event.dataTransfer.files) {
                        selectFile(event.dataTransfer.files);
                    }
                });
            }
        }
        });
    </script>
@stop
