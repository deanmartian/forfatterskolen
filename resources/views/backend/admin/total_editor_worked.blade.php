@extends('backend.layout')

@section('title')
<title>Admins &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop

@section('content')
<div class="col-sm-12 dashboard-left">
    <div class="row">
        <div class="col-sm-12">
            <div class="table-users table-responsive">
                <h4>{{ $editor }}</h4>
                <table id="totalWorked" class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Shop Manuscript (Price)</th>
                            <th>Assignment (Price)</th>
                            <th>Coaching Timer (Price)</th>
                            <th>Correction (Price)</th>
                            <th>Copy Editing (Price)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $month = $var['minMonth'];
                            $year =  $var['minYear'];
                            $sumPAssgn = 0;
                            $sumShpMan = 0;
                            $sumGAssgn = 0;
                            $sumChngTmr = 0;
                            $sumCrrctn = 0;
                            $sumCpyEdtng = 0;
                            for($y = $var['maxYear']; $y >= $var['minYear']; $y--){
                                $year = $y;
                                
                                for($m=12; $m>=1; $m--){
                                    
                                    echo '<tr>';
                                    echo '<td>'.$year.'-'.sprintf("%02d", $m).'</td>';
                                    echo '<td>';
                                    $varShpMan = 0;
                                    foreach($data['shpMan'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumShpMan = $sumShpMan + $key->total;
                                            $varShpMan += $key->total;
                                        }
                                    }
                                    echo $varShpMan?$varShpMan.' hours ('.$price['shpManPrice'].')':'';
                                    echo '</td>';
                                    echo '<td>';
                                    $allAssgn = 0;
                                    foreach($data['pAssgn'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumPAssgn = $sumPAssgn + $key->total;
                                            $allAssgn += $key->total;
                                        }
                                    }
                                    foreach($data['gAssgn'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumGAssgn = $sumGAssgn + $key->total;
                                            $allAssgn += $key->total;
                                        }
                                    }
                                    echo $allAssgn?$allAssgn.' ('.$price['assgnPrice'].')':'';
                                    echo '</td>';
                                    echo '<td>';
                                    $valChngTmr = 0;
                                    foreach($data['chngTmr'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumChngTmr = $sumChngTmr + $key->total;
                                            $valChngTmr += $key->total;
                                        }
                                    }
                                    echo $valChngTmr?$valChngTmr.' ('.$price['chngTmrPrice'].')':'';
                                    echo '</td>';
                                    echo '<td>';
                                    $valCrrctn = 0;
                                    foreach($data['crrctn'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumCrrctn = $sumCrrctn + $key->total;
                                            $valCrrctn += $key->total;
                                        }
                                    }
                                    echo $valCrrctn?$valCrrctn.' hours ('.$price['crrctnPrice'].')':'';
                                    echo '</td>';
                                    echo '<td>';
                                    $valCpyEdtng = 0;
                                    foreach($data['cpyEdtng'] as $key){
                                        if($key->year_month == $year.sprintf("%02d", $m)){
                                            $sumCpyEdtng = $sumCpyEdtng + $key->total;
                                            $valCpyEdtng += $key->total;
                                        }
                                    }
                                    echo $valCpyEdtng?$valCpyEdtng.' hours ('.$price['cpyEdtngPrice'].')':'';
                                    echo '</td>';
                                    echo '</tr>';
                    
                                }
                            }
                    
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                           <td><strong>Total</strong></td>
                           <td><strong>{{ $sumShpMan.' hours' }}</strong></td>
                           <td><strong>{{ $sumGAssgn + $sumPAssgn }}</strong></td>
                           <td><strong>{{ $sumChngTmr }}</strong></td>
                           <td><strong>{{ $sumCrrctn }}</strong></td>
                           <td><strong>{{ $sumCpyEdtng }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#totalWorked tr:has(td:nth-child(2):empty):has(td:nth-child(3):empty):has(td:nth-child(4):empty):has(td:nth-child(5):empty):has(td:nth-child(6):empty)').hide();
        });
    </script>
@stop