<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Søknad2024</title>

    @include('frontend.partials.frontend-css')
    <style>
        .pre {
            font-family: 'Barlow Regular',sans serif;
            font-size: 15px;
            padding: 9.5px;
            margin: 0 0 10px;
            word-break: break-all;
            word-wrap: break-word;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
    
        .main-container {
            padding-top: 30px;
            padding-bottom: 30px;
        }
    
        h3 {
            font-weight: 300;
            font-size: 20px;
            line-height: 1.5em;
            color: rgb(102, 102, 102);
        }
    
        .tcb-plain-text {
            letter-spacing: 2px;
            font-size: 15px !important;
            color: rgb(72, 72, 72) !important;
            text-transform: uppercase !important;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding-bottom: 5px;
        }
    
        .form-wrapper {
            background-image: linear-gradient(rgb(248, 248, 248), rgb(248, 248, 248));
            max-width: 900px;
            padding: 20px
        }
    
        #filePickerButton {
            background-color: white !important;
            color: red !important;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 50px;
            padding: 8px 15px;
            transition: background-color 0.3s, transform 0.3s;
        }
    
        #filePickerButton:hover {
            background-color: red !important;
            color: white !important;
            animation: bounce 1s infinite;
        }
    
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }
    
        .dropdown-container {
            position: relative;
            width: 100%;
        }
    
        .dropdown-results {
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            background-color: #fff;
            z-index: 1000;
        }
    
        .dropdown-results div {
            padding: 8px;
            cursor: pointer;
        }
    
        .dropdown-results div:hover {
            background-color: #f1f1f1;
        }
    
        .file-name {
            color: rgb(59,136,253);
            font-size: 16px;
        }
    
        .file-size {
            color: #94A3b0;
            font-size: 14px;
        }
    
        .btn-red {
            border-radius: 0;
        }
    </style>
</head>
<body>

    <nav id="navbar-latest" class="navbar navbar-expand-md">
        <div class="container">
            <!-- Logo or Brand -->
            <a class="navbar-brand" href="{{ route('front.home') }}" style="position: relative; height: auto">
                <img src="https://www.rskolen.no/wp-content/uploads/2023/03/redaktor-skolen-logo-02.png" alt="rskolen"
                style="height: 100px">
            </a>

            <!-- Toggler/collapsibe Button -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Items -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav nav-fill">
                    <li class="nav-item">
                        <a href="https://www.rskolen.no/" class="nav-link">Hjem</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://www.rskolen.no/vare-kurs-details/" class="nav-link">Kurset</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Søknad</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://www.rskolen.no/hvem-er-vi/" class="nav-link">Hvem er vi?</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <p class="pre">
            Kursstart er lørdag 21.september 2024, og kurset avsluttes i juni 2025.  <br>
            <strong>Søknadsfristen er 1.juni</strong>, og du vil få svar medio/slutten av juni. <br>
            Vi tar inn 18 studenter. 
        </p>
    
        <h3 class="mt-4">
            Ved opptak krever vi generell studiekompetanse og relevant høyere utdanning og/eller tilsvarende arbeidserfaring, 
            samt god motivasjon for kurset. Vi tilbyr studieplass til 18 studenter vi ser har et godt utgangspunkt for å tre 
            inn i redaktør yrket etter endt kurs. Søknadsfrist er 1.juni 2024.
        </h3>
    
        <h3 class="mt-5">
            Du vil raskt få svar på om du får plass. Søknad er bindende.
        </h3>
    
        <div class="form-wrapper mx-auto mt-5">
            <form action="" method="POST" enctype="multipart/form-data" onsubmit="disableSubmit(this)">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <label>
                            Name
                        </label>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="first_name" class="form-control" placeholder="{{ trans('site.first-name') }}"
                                value="{{ old('first_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="last_name" class="form-control" placeholder="{{ trans('site.last-name') }}"
                        value="{{ old('last_name') }}">
                    </div>
                </div>
        
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('site.phone-number') }}</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ trans('site.phone-number') }}"
                            value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>{{ trans('site.email-address') }}</label>
                        <input type="text" name="email" class="form-control" placeholder="{{ trans('site.email-address') }}"
                        value="{{ old('email') }}">
                    </div>
                </div>
        
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ trans('site.learner.address') }}</label>
                            <input type="text" name="address" class="form-control" placeholder="{{ trans('site.learner.address') }}"
                            value="{{ old('address') }}">
                        </div>
                    </div>
                </div>
        
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="zip" class="form-control" placeholder="{{ trans('site.zip') }}"
                            value="{{ old('zip') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="city" class="form-control" placeholder="{{ trans('site.city') }}"
                            value="{{ old('city') }}">
                        </div>
                    </div>
                </div>
    
                <div class="tcb-plain-text mb-4">
                    LAST OPP DIN SØKNAD
                </div>
            
                <div class="row">
                    <div class="col-md-4">
                         <!-- File Picker Button -->
                        <button type="button" id="filePickerButton" class="btn btn-primary">Velg Fil</button>
                        
                        <!-- Hidden File Input -->
                        <input type="file" id="fileInput" name="file" style="display: none;">
                    </div>
                    <div class="col-md-3">
                        Ingen fil valgt
                    </div>
                    <div class="col-md-5">
                        <p>
                            <strong>Aksepterte fil typer:</strong>
                            doc, docx, pdf, txt, odt
                        </p>
                    </div>
    
                    <div class="col-md-12 my-4">
                        Du laster opp ett dokument, med relevant utdanning <br>
                        og yrkeserfaring, samt kort redegjørelse for din motivasjon for kurset.
                    </div>
    
                    <div class="col-md-12">
                        <hr>
    
                        <!-- Display Selected File Name -->
                        <div id="fileInfo" class="my-3"></div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-12">
                        <input type="checkbox" name="terms" required>
                        Jeg samtykker i at mine opplysninger behandles for å motta personlig tilpasset markedsføringsmateriale 
                        via e-post eller telefon i samsvar med regelverket
                    </div>
    
                    <div class="col-md-12">
                        <button type="submit" class="btn w-100 p-4 btn-red mt-4">
                            Send søknad
                        </button>
                    </div>
                </div>
    
            </form>
        </div> <!-- end form-wrapper -->
    </div>

    @if($errors->count())
        <?php
        $alert_type = session('alert_type');
        if(!Session::has('alert_type')) {
            $alert_type = 'danger';
        }
        ?>
        <div class="alert alert-{{ $alert_type }} global-alert-box" style="z-index: 9; min-width: 300px"
                id="fixed_to_bottom_alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filePickerButton = document.getElementById('filePickerButton');
            const fileInput = document.getElementById('fileInput');
            const fileInfo = document.getElementById('fileInfo');
    
            filePickerButton.addEventListener('click', function() {
                fileInput.click();
            });
    
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileName = file.name;
                    const fileSize = (file.size / 1024).toFixed(2); // Size in KB
                    fileInfo.innerHTML = `<span class='file-name'>${fileName}</span> <span class='file-size'>${fileSize} KB</span>`;
                } else {
                    fileInfo.innerHTML = '';
                }
            });
        });

        function disableSubmit(t) {
            let submit_btn = $(t).find('[type=submit]');
            submit_btn.text('');
            submit_btn.append('<i class="fa fa-spinner fa-pulse"></i> Please wait...');
            submit_btn.attr('disabled', 'disabled');
        }
    </script>
</body>
</html>