@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Marketing &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
<style>
body {
    background-color: #f8f9fa;
}
.timeline {
    position: relative;
    padding: 20px 0;
    list-style: none;
}
.timeline:before {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #007bff;
    left: 50%;
    margin-left: -2px;
}
.timeline > li {
    position: relative;
    margin-bottom: 30px;
    width: 50%;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.timeline > li:nth-child(odd) {
    float: left;
    clear: left;
    text-align: right;
}
.timeline > li:nth-child(even) {
    float: right;
    clear: right;
    text-align: left;
}
.timeline > li:before {
    content: "";
    position: absolute;
    top: 30px;
    width: 15px;
    height: 15px;
    background: #007bff;
    border-radius: 50%;
    left: 50%;
    margin-left: -7px;
}
.timeline h4 {
    margin-top: 0;
}
.timeline .date {
    font-size: 14px;
    color: #666;
}
.sub-steps {
    list-style: none;
    padding: 0;
    margin-top: 10px;
}
.sub-steps li {
    font-size: 14px;
    color: #666;
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
                    <ul class="timeline">
                        @php
                            $steps = [
                                ['title' => 'Ferdig manuskript', 'sub' => ['Skrive utkast', 'Gjennomgå innhold'], 'date' => now()->addDays(5)],
                                ['title' => 'Redaktør & korrektur', 'sub' => ['Første redigering', 'Andre gjennomgang'], 'date' => now()->addDays(15)],
                                ['title' => 'Bokdesign & layout', 'sub' => ['Velge font', 'Sette opp layout'], 'date' => now()->addDays(25)],
                                ['title' => 'ISBN & metadata', 'sub' => ['Registrere ISBN', 'Legge inn metadata'], 'date' => now()->addDays(35)],
                                ['title' => 'Publisering', 'sub' => ['Trykke boken', 'Godkjenne prøvetrykk'], 'date' => now()->addDays(45)],
                                ['title' => 'Markedsføring', 'sub' => ['Lage kampanje', 'Sosiale medier'], 'date' => now()->addDays(55)],
                                ['title' => 'Oppfølging & salg', 'sub' => ['Kontakt bokhandlere', 'Oppdatere nettside'], 'date' => now()->addDays(65)],
                                ['title' => 'Publisering (sende til trykk)', 'sub' => ['Sluttgodkjenning', 'Trykkerikontakt'], 'date' => now()->addDays(75)],
                                ['title' => 'Utsending av bøker', 'sub' => ['Pakking', 'Frakt'], 'date' => now()->addDays(85)],
                                ['title' => 'Videre markedsføring', 'sub' => ['Annonser', 'Intervjuer'], 'date' => now()->addDays(95)]
                            ];
                        @endphp
                
                        @foreach($steps as $index => $step)
                            <li>
                                <h4>{{ $index + 1 }}. {{ $step['title'] }}</h4>
                                <p class="date"><strong>Forventet dato:</strong> {{ $step['date']->format('d-m-Y') }}</p>
                                <p><strong>Status:</strong> Ikke påbegynt</p>
                                @if (!empty($step['sub']))
                                    <ul class="sub-steps">
                                        @foreach($step['sub'] as $subStep)
                                            <li>➡ {{ $subStep }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
@stop