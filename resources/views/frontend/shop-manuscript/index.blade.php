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
        <div class="third-section">
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
                            <label class="mb-4 mt-3">
                                <span class="instruction">{{ trans('site.front.shop-manuscript.form.instruction') }}</span>
                                <br>
                                <span class="note"><i class="info-icon"></i> {{ trans('site.front.shop-manuscript.form.note') }}</span>
                            </label>
                            {{-- <div class="input-group mb-4">
                                <input type="text" readonly class="form-control disabled" required>
                                <div class="input-group-append">
                                    <button class="btn bg-site-red select-manuscript" type="button">
                                        {{ trans('site.front.shop-manuscript.form.select-document') }}
                                    </button>
                                </div>
                            </div> --}}
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
        </div>

        <div class="third-section word-count-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 left-container">
                        <img src="{{ asset('images-new/shop-manuscript/notepad.png') }}" class="w-100" alt="notepad illustration">
                    </div>
                    <div class="col-md-6 details" id="wordCountTool">
                        <h2 class="title mb-4">
                            Sjekk antall ord i manuskriptet ditt
                        </h2>
                        <p>
                            Last opp manuskriptet ditt som en DOCX-fil for å få en rask oversikt over antall ord før du sender det til oss.
                        </p>

                        <input type="file" class="hidden" id="word-count-file" accept=".docx">
                        <label for="word-count-file" class="file-upload-label">
                            <div class="file-upload" id="word-count-upload-area">
                                <div class="file-upload-text" id="word-count-upload-text">
                                    <a href="javascript:void(0)" class="word-count-file-trigger">Klikk her</a> for å laste opp filen din eller <br>
                                    dra filen din hit.
                                </div>
                            </div>
                        </label>

                        <div class="word-count-feedback mt-3" id="word-count-feedback">
                            Velg en DOCX-fil og klikk på knappen under for å beregne ord og pris.
                        </div>

                        <div class="word-count-feedback mt-2" id="word-count-price-feedback"></div>

                        <div class="margin-top">
                            <button class="btn site-btn-global-w-arrow word-count-process-btn" type="button">
                                Beregn ord og pris
                                <img src="{{ asset('images-new/icon/upload.png') }}" alt="">
                            </button>
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

@stop

@section('scripts')
    <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
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

    <script>
        const manuscriptPlans = @json($manuscriptPlans);
        const excessPerWordPrice = {{ $excessPerWordPrice }};
        const manuscriptBaseWordLimit = 17500;

        $(document).ready(function(){
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

            const fileUploadArea = document.getElementById('file-upload-area');
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
            });

            const wordCountContainer = document.getElementById('wordCountTool');
            if (wordCountContainer) {
                const wordCountFileInput = document.getElementById('word-count-file');
                const wordCountUploadArea = document.getElementById('word-count-upload-area');
                const wordCountUploadText = document.getElementById('word-count-upload-text');
                const wordCountFeedback = document.getElementById('word-count-feedback');
                const wordCountPriceFeedback = document.getElementById('word-count-price-feedback');
                const defaultWordCountText = wordCountUploadText ? wordCountUploadText.innerHTML : '';
                let selectedWordCountFile = null;
                let processingWordCount = false;

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

                const processFile = (file) => {
                    if (!file) {
                        return;
                    }

                    if (!/\.docx$/i.test(file.name)) {
                        setFeedback('Vennligst velg en DOCX-fil for ordtelling.', true);
                        setPriceFeedback('');
                        if (wordCountFileInput) {
                            wordCountFileInput.value = '';
                        }
                        resetUploadText();
                        selectedWordCountFile = null;
                        return;
                    }

                    updateWordCountText(file.name);
                    setFeedback('Beregner antall ord ...');
                    setPriceFeedback('');
                    processingWordCount = true;

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (typeof mammoth === 'undefined') {
                            setFeedback('Mammoth-biblioteket er ikke tilgjengelig akkurat nå. Prøv igjen senere.', true);
                            setPriceFeedback('');
                            processingWordCount = false;
                            return;
                        }

                        mammoth.extractRawText({ arrayBuffer: event.target.result })
                            .then((result) => {
                                const text = (result.value || '').trim();
                                const wordCount = text ? text.split(/\s+/).length : 0;
                                const priceDetails = calculatePrice(wordCount);

                                setFeedback(`Manuskriptet inneholder omtrent ${wordCount} ord.`);

                                if (priceDetails && priceDetails.formattedPrice) {
                                    const planTitle = priceDetails.plan && priceDetails.plan.title ? ` (${priceDetails.plan.title})` : '';
                                    const checkoutLink = priceDetails.plan && priceDetails.plan.checkout_url ? priceDetails.plan.checkout_url : null;
                                    const priceMessage = checkoutLink
                                        ? `Estimert pris${planTitle}: ${priceDetails.formattedPrice}. <a href="${checkoutLink}" class="btn btn-link p-0 align-baseline">Bestill nå</a>`
                                        : `Estimert pris${planTitle}: ${priceDetails.formattedPrice}.`;
                                    setPriceFeedback(priceMessage);
                                } else {
                                    setPriceFeedback('Kunne ikke beregne prisen basert på dette manuset.', true);
                                }

                                processingWordCount = false;
                            })
                            .catch(() => {
                                setFeedback('Kunne ikke lese denne filen. Prøv igjen med en gyldig DOCX-fil.', true);
                                setPriceFeedback('');
                                resetUploadText();
                                selectedWordCountFile = null;
                                processingWordCount = false;
                            });
                    };

                    reader.onerror = () => {
                        setFeedback('Det oppstod en feil ved lesing av filen. Prøv igjen.', true);
                        setPriceFeedback('');
                        resetUploadText();
                        selectedWordCountFile = null;
                        processingWordCount = false;
                    };

                    reader.readAsArrayBuffer(file);
                };

                const selectFile = (files) => {
                    if (!files || !files.length) {
                        selectedWordCountFile = null;
                        if (wordCountFileInput) {
                            wordCountFileInput.value = '';
                        }
                        resetUploadText();
                        setFeedback('Velg en DOCX-fil og klikk på knappen for å beregne ord og pris.');
                        setPriceFeedback('');
                        return;
                    }
                    const [file] = files;
                    selectedWordCountFile = file;
                    updateWordCountText(file.name);
                    setFeedback('Fil valgt. Klikk på knappen for å beregne ord og pris.');
                    setPriceFeedback('');
                };

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

                const processButton = wordCountContainer.querySelector('.word-count-process-btn');
                if (processButton) {
                    processButton.addEventListener('click', () => {
                        if (processingWordCount) {
                            return;
                        }

                        if (!selectedWordCountFile) {
                            setFeedback('Vennligst velg en DOCX-fil før du beregner.', true);
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