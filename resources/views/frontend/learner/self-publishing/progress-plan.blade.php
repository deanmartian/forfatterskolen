@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Progress Plan &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
    body {
        background-color: #f8f9fa;
    }
    .timeline {
        position: relative;
        max-width: 800px;
        margin: 50px auto;
    }
    /* Gray timeline line */
    .timeline::after {
        content: "";
        position: absolute;
        width: 4px;
        background-color: #6c757d; /* Gray color */
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -2px;
    }
    /* Timeline item styling */
    .timeline-item {
        padding: 20px;
        position: relative;
        background: white;
        border-radius: 6px;
        width: 45%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 28px;
        right: -13px;
        background-color: #939597;
        border: 5px solid #F5DF4D;
        top: 15px;
        z-index: 1;
    }
    .timeline-item:nth-child(odd) {
        left: 0;
        text-align: right;
    }
    .timeline-item:nth-child(even) {
        left: 55%;
    }
    /* Half-circle markers */
    /* .timeline-item::before {
        content: "";
        position: absolute;
        width: 15px;
        height: 15px;
        background: #f1c40f;
        border-radius: 50%;
        top: 30px;
        margin-left: -7px;
        z-index: 1;
    }
    .timeline-item:nth-child(odd)::before {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        right: -38px;
    } */
    .timeline-item:nth-child(odd)::after {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        border-right: none;
        right: -38px;
    }
    /* .timeline-item:nth-child(even)::before {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        left: -31px;
    } */
    .timeline-item:nth-child(even)::after {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        border-left: none;
        left: -38px;
    }
    /* Header and date styles */
    .timeline-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .timeline-date {
        font-size: 14px;
        color: #666;
    }
    /* Sub-steps styling */
    .sub-steps {
        padding: 0;
        list-style: none;
        margin-top: 10px;
        text-align: left;
    }
    .sub-steps li {
        font-size: 14px;
        color: #666;
    }

    .sub-steps li::before {
        content: '➡';
        margin-right: 12px;
    }
</style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="card card-global">
                <div class="card-header">
                    Prosjektfremdrift
                </div>
                <div class="card-body">
                    {{-- <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="info">
                                <th>Steg</th>
                                <th>Oppgave</th>
                                <th>Status</th>
                                <th>Forventet dato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Ferdig manuskript</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Redaktør & korrektur</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Bokdesign & layout</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>ISBN & metadata</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Publisering</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Markedsføring</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>Oppfølging & salg</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>Publisering (sende til trykk)</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>Utsending av bøker når de har kommet inn på lager</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>Markedføring</td>
                                <td class="status-pending">Ikke påbegynt</td>
                                <td>
                                    {{ now() }}
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    {{-- <div class="timeline">
                        @php
                            $steps = [
                                ['title' => 'Ferdig manuskript', 'sub' => ['Skrive utkast', 'Gjennomgå innhold'], 'date' => now()->addDays(5)],
                                ['title' => 'Redaktør & korrektur', 'sub' => ['Første redigering', 'Andre gjennomgang'], 'date' => now()->addDays(15)],
                                ['title' => 'Bokdesign & layout', 'sub' => ['Velge font', 'Sette opp layout'], 'date' => now()->addDays(25)],
                                ['title' => 'ISBN & metadata', 'sub' => ['Registrere ISBN', 'Legge inn metadata'], 'date' => now()->addDays(35)],
                                ['title' => 'Publisering', 'sub' => ['Trykke boken', 'Godkjenne prøvetrykk'], 'date' => now()->addDays(45)],
                                ['title' => 'Markedsføring', 'sub' => ['Lage kampanje', 'Sosiale medier'], 'date' => now()->addDays(55)],
                                ['title' => 'Oppfølging & salg', 'sub' => ['Kontakt bokhandlere', 'Oppdatere nettside'], 'date' => now()->addDays(65)]
                            ];
                        @endphp
                
                        @foreach($steps as $index => $step)
                            <div class="timeline-item">
                                <div class="timeline-header">{{ $index + 1 }}. {{ $step['title'] }}</div>
                                <p class="timeline-date"><strong>Forventet dato:</strong> {{ $step['date']->format('d-m-Y') }}</p>
                                <p><strong>Status:</strong> Ikke påbegynt</p>
                                @if (!empty($step['sub']))
                                    <ul class="sub-steps">
                                        @foreach($step['sub'] as $subStep)
                                            <li>➡ {{ $subStep }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div> --}}
                    <div class="timeline">
                        @foreach ( $steps as $step)
                            <div class="timeline-item">
                                <div class="timeline-header">
                                    <a href="{{ route('learner.progress-plan.step', $step['step_number']) }}">
                                        <h3>
                                            {{ $step['step_number'] }}. {{ $step['title'] }}
                                        </h3>
                                    </a>
                                </div>
                                <p class="timeline-date"><strong>Forventet dato:</strong> 
                                    {{ $step['expected_date'] 
                                    ? \Carbon\Carbon::parse($step['expected_date'])->format('d.m.Y') 
                                    : '—' }}
                                    {{-- {{ \Carbon\Carbon::now()->addDays($step['step_number'])->format('d.m.Y')  }} --}}
                                </p>
                                <p><strong>Status:</strong> {{ $step['status_text'] }}</p>

                                {{-- @if ($step['step_number'] == 1)
                                    <ul class="sub-steps">
                                        <li>Step 1. (we can have more than one here) since some deliver more times to get it finish</li>
                                        <li>Step 2. Språkvask</li>
                                        <li>Step 3. Korrektur</li>
                                        <li>Step 4. Omslag</li>
                                        <li>Step 5. Ombrekk</li>
                                        <li>Step 6. Ebok</li>
                                        <li>Step 7. Lybok</li>
                                    </ul>
                                @endif --}}
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@stop