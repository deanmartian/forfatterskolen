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
                        <form id="wordCountForm" method="POST" action="{{ route('front.shop-manuscript.test_manuscript') }}" enctype="multipart/form-data">
                            @csrf

                            <h2 class="title mb-4">
                                {{ trans('site.front.shop-manuscript.form.title') }}
                            </h2>
                            <label class="mb-4 mt-3">
                                <span class="instruction">{{ trans('site.front.shop-manuscript.form.instruction') }}</span>
                                <br>
                                <span class="note"><i class="info-icon"></i> {{ trans('site.front.shop-manuscript.form.note') }}</span>
                            </label>

                            <input type="file" class="hidden" id="word-count-file" name="manuscript" accept=".doc,.docx,.pdf,.odt">
                            <input type="hidden" name="word_count" id="word-count-hidden">
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
                            @error('manuscript')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="form-group mt-3">
                                <label for="manual-word-count">{{ trans('site.enter-words-manually') }}</label>
                                <input type="number" min="1" step="1" class="form-control @error('manual_word_count') is-invalid @enderror" id="manual-word-count" name="manual_word_count" value="{{ old('manual_word_count') }}"
                                placeholder="{{ trans('site.enter-words-manually-placeholder') }}">
                                <small class="form-text text-muted">{{ trans('site.enter-words-manually-note') }}</small>
                                @error('manual_word_count')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(session('manuscript_test'))
                                <div class="alert alert-success" role="alert">
                                    {!! session('manuscript_test') !!}
                                </div>
                            @endif

                            @if(session('manuscript_test_error'))
                                <div class="alert alert-danger" role="alert">
                                    {!! session('manuscript_test_error') !!}
                                </div>
                            @endif

                            <div class="margin-top">
                                <button class="btn site-btn-global-w-arrow word-count-process-btn" type="submit">
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
    <script src="https://unpkg.com/mammoth@1.4.21/mammoth.browser.min.js"></script>
    <script>
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

            const wordCountFormElement = document.getElementById('wordCountForm');
            const wordCountFileInput = document.getElementById('word-count-file');
            const wordCountUploadArea = document.getElementById('word-count-upload-area');
            const wordCountUploadText = document.getElementById('word-count-upload-text');
            const manualWordCountInput = document.getElementById('manual-word-count');
            const wordCountHiddenInput = document.getElementById('word-count-hidden');
            const defaultWordCountText = wordCountUploadText ? wordCountUploadText.innerHTML : '';
            let wordCountSubmittingWithMammoth = false;

            const updateWordCountText = (text) => {
                if (wordCountUploadText) {
                    wordCountUploadText.innerHTML = text;
                }
            };

            const resetUploadText = () => {
                updateWordCountText(defaultWordCountText);
            };

            const selectWordCountFile = (files) => {
                if (!files || !files.length) {
                    resetUploadText();
                    if (wordCountHiddenInput) {
                        wordCountHiddenInput.value = '';
                    }
                    return;
                }

                const [file] = files;
                updateWordCountText(file.name);
                if (wordCountHiddenInput) {
                    wordCountHiddenInput.value = '';
                }
            };

            if (wordCountFileInput) {
                wordCountFileInput.addEventListener('change', (event) => {
                    selectWordCountFile(event.target.files);
                });
            }

            document.querySelectorAll('.word-count-file-trigger').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    if (wordCountFileInput) {
                        wordCountFileInput.click();
                    }
                });
            });

            if (wordCountUploadArea) {
                wordCountUploadArea.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    wordCountUploadArea.classList.add('dragover');
                });

                wordCountUploadArea.addEventListener('dragleave', () => {
                    wordCountUploadArea.classList.remove('dragover');
                    resetUploadText();
                });

                wordCountUploadArea.addEventListener('drop', (event) => {
                    event.preventDefault();
                    wordCountUploadArea.classList.remove('dragover');
                    const files = event.dataTransfer ? event.dataTransfer.files : null;

                    if (files && files.length) {
                        selectWordCountFile(files);

                        if (wordCountFileInput) {
                            try {
                                if (typeof DataTransfer !== 'undefined') {
                                    const dataTransfer = new DataTransfer();
                                    Array.from(files).forEach((file) => dataTransfer.items.add(file));
                                    wordCountFileInput.files = dataTransfer.files;
                                } else if (wordCountFileInput.files !== undefined) {
                                    wordCountFileInput.files = files;
                                }
                            } catch (error) {
                                console.warn('Kunne ikke tilordne droppet fil til input-elementet.', error);
                            }

                            wordCountFileInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        resetUploadText();
                    }
                });
            }

            if (wordCountFormElement && wordCountFileInput) {
                wordCountFormElement.addEventListener('submit', (event) => {
                    if (wordCountSubmittingWithMammoth) {
                        wordCountSubmittingWithMammoth = false;
                        return;
                    }

                    const files = wordCountFileInput.files;

                    if (!files || !files.length) {
                        if (wordCountHiddenInput) {
                            wordCountHiddenInput.value = '';
                        }

                        return;
                    }

                    const [file] = files;
                    const extension = getFileExtension(file.name || wordCountFileInput.value);

                    if (!shouldUseMammothForExtension(extension)) {
                        if (wordCountHiddenInput) {
                            wordCountHiddenInput.value = '';
                        }

                        return;
                    }

                    event.preventDefault();

                    extractWordCountWithMammoth(file)
                        .then((wordCount) => {
                            if (wordCountHiddenInput) {
                                wordCountHiddenInput.value = Number.isInteger(wordCount) && wordCount > 0
                                    ? wordCount
                                    : '';
                            }
                        })
                        .catch((error) => {
                            console.error('Unable to count words with Mammoth for word count form', error);
                            if (wordCountHiddenInput) {
                                wordCountHiddenInput.value = '';
                            }
                        })
                        .finally(() => {
                            wordCountSubmittingWithMammoth = true;
                            wordCountFormElement.submit();
                        });
                });
            }

            if (manualWordCountInput) {
                manualWordCountInput.addEventListener('input', () => {
                    manualWordCountInput.classList.remove('is-invalid');
                });
            }
        });
    </script>
@stop
