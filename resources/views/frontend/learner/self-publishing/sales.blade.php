@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Sales &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
    <style>
        .fa-bar-chart-red:before {
            content: "\f080";
        }

        .fa-bar-chart-red {
            color: #862736 !important;
            font-size: 20px;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 100%;
        }
    </style>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 learner-assignment no-left-padding">
                    <ul class="nav nav-tabs mb-5">
                        <li @if( Request::input('tab') == 'sales' || Request::input('tab') == '') class="active" @endif>
                            <a href="?tab=sales&year={{ FrontendHelpers::getLearnerSaleYear() }}">
                                {{ trans('site.author-portal-menu.sales') }}
                            </a>
                        </li>
                        <li @if( Request::input('tab') == 'distribution' ) class="active" @endif>
                            <a href="?tab=distribution">
                                {{ trans('site.author-portal.distribution-cost') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            @if( Request::input('tab') == 'distribution')
                                <div class="card global-car">
                                    <div class="card-body">
                                        <table class="table margin-top">
                                            <thead>
                                                <tr>
                                                    <th>Nr</th>
                                                    <th>Service</th>
                                                    <th>Number</th>
                                                    <th>Amount</th>
                                                    <th>Learner Price</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($projectUserBook)
                                                    @foreach ($projectUserBook->distributionCosts as $distributionCost)
                                                        <tr>
                                                            <td>
                                                                {{ $distributionCost->nr }}
                                                            </td>
                                                            <td>
                                                                {{ AdminHelpers::distributionServices($distributionCost->service)['value'] }}
                                                            </td>
                                                            <td>
                                                                {{ $distributionCost->number }}
                                                            </td>
                                                            <td>
                                                                {{ AdminHelpers::currencyFormat($distributionCost->amount) }}
                                                            </td>
                                                            <td>
                                                                {{ AdminHelpers::currencyFormat($distributionCost->learner_amount) }}
                                                            </td>
                                                            <td>
                                                                {{ $distributionCost->date 
                                                                ? FrontendHelpers::formatDate($distributionCost->date) : '' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @if ($projectUserBook->distributionCosts()->count())
                                                        <tr>
                                                            <td colspan="3" style="font-weight: bold">
                                                                Total
                                                            </td>
                                                            <td colspan="3">
                                                                {{ FrontendHelpers::currencyFormat(
                                                                    $projectUserBook->totalDistributionCost()) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="card global-card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h1>
                                                    {{ trans('site.author-portal-menu.sales') }}
                                                </h1>
                                            </div>
                                            <div class="col-md-4">
                                                <form action="">
                                                    <div class="form-group">
                                                        <label>
                                                            {{ trans('site.year') }}
                                                        </label>
                        
                                                        <select name="year" id="yearSelector" class="form-control" 
                                                        onchange="this.form.submit()">
                                                            @foreach ($uniqueYears as $year)
                                                                <option value="{{ $year }}" 
                                                                @if (request()->get('year') == $year)
                                                                    selected
                                                                @endif>
                                                                    {{ $year }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container" style="position: relative; height:400px; width: 100%">
                                        <canvas id="chart-line" width="299" height="200" class="chartjs-render-monitor"
                                                style="display: block; width: 299px; height: 200px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="card global-card mt-5">
                        <div class="card-header">
                            <button class="btn btn-primary pull-right btn-xs booksForSaleBtn" data-toggle="modal"
                                    data-action=""
                                    data-target="#booksForSaleModal">
                                + Add Books for Sale
                            </button>

                            <h1>
                                Books for sale
                            </h1>
                        </div>
                        <div class="card-body p-3">
                            <table class="table dt-table">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(Auth::user()->booksForSale as $bookForSale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('learner.book-for-sale', $bookForSale->id) }}">
                                                {{ $bookForSale->project ? $bookForSale->project->book_name : '' }}
                                            </a>
                                        </td>
                                        <td>{{ $bookForSale->description }}</td>
                                        <td>{{ $bookForSale->price_formatted }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs booksForSaleBtn" data-toggle="modal"
                                                    data-record="{{ json_encode($bookForSale) }}"
                                                    data-target="#booksForSaleModal">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal"
                                                    data-target="#deleteRecordModal"
                                                    data-title="Delete Books for Sale"
                                                    data-action="{{ route('learner.delete-for-sale-books', $bookForSale->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div id="booksForSaleModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Books for sale</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('learner.save-for-sale-books', $learner->id) }}"
                          onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        <input type="hidden" name="id">

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="10" cols="30"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" class="form-control" name="price" required>
                        </div>

                        <button class="btn btn-primary pull-right" type="submit">
                            {{ trans('site.save') }}
                        </button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteRecordModal" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}

                        <p>{{ trans('site.delete-item-question') }}</p>

                        <div class="text-right margin-top">
                            <button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('site.cancel') }}</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js'></script>
    <script>
        console.log(i18n);
        console.log(i18n.site['author-portal-menu'].sales);
        $(".dt-table").DataTable({
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            pageLength: 10,
            "aaSorting": []
        });

        $(document).ready(function() {
            let ctx = $("#chart-line");
            const monthAbbreviations = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];

            const options = {
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 10,
                            stepSize: 2,
                            callback: function(value, index, values) {
                                // Only show whole numbers
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                            }
                        }
                    }]
                },
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true,
                    mode: 'single',
                    callbacks: {
                        label: function(tooltipItems, data) {
                            const currencyFormatter = new Intl.NumberFormat('no-NO', {
                                style: 'currency',
                                currency: 'NOK',
                            });
                            return i18n.site['author-portal-menu'].sales + ': ' + currencyFormatter.format(tooltipItems.yLabel);
                        }
                    }
                }
            };

            let myLineChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthAbbreviations,
                    datasets: [{
                        data: [],
                        label: i18n.site['author-portal.total-sales'] + ': ', // label on top, this is being changed on ajax_chart
                        borderColor: "#862736",
                        backgroundColor:'#862736',
                        fill: false
                    }]
                },
                options: options
            });

            let year = "{{ request()->get('year') }}";
            const currentYear = new Date().getFullYear();
            
            if (!year) {
                year = currentYear;
            }

            // get the chart data
            ajax_chart(myLineChart, '/account/book-sale/list-by-month/' + year);

            $(".booksForSaleBtn").click(function() {
                let record = $(this).data('record');
                let modal = $('#booksForSaleModal');
                modal.find('[name=id]').val('');

                if (record) {
                    modal.find('[name=id]').val(record.id);
                    modal.find('[name=title]').val(record.title);
                    modal.find('[name=description]').text(record.description);
                    modal.find('[name=price]').val(record.price);
                }
            });

            $(".deleteRecordBtn").click(function() {
                let modal = $("#deleteRecordModal");
                let action = $(this).data('action');
                let title = $(this).data('title');
                modal.find('.modal-title').text(title);
                modal.find('form').attr('action', action);
            });
        });

        // function to update our chart
        function ajax_chart(chart, url) {
            let data = {};

            $.getJSON(url, data).done(function(response) {
                const maxValue = Math.max(...response);

                const totalAmount = response.reduce((accumulator, currentValue) => accumulator + currentValue, 0);

                const currencyFormatter = new Intl.NumberFormat('no-NO', {
                    style: 'currency',
                    currency: 'NOK',
                });
                const formattedTotalAmount = currencyFormatter.format(totalAmount);

                // Dynamically adjust max ticks based on totalSales
                const dynamicMax = Math.ceil(maxValue / 10) * 10; // Round up to the nearest multiple of 10
                const stepSize = Math.ceil(dynamicMax / 5); // Adjust step size dynamically

                chart.data.datasets[0].data = response; // or you can iterate for multiple datasets
                chart.data.datasets[0].label = 'Total Sales: ' + formattedTotalAmount;

                const hasValueGreaterThanZero = response.some(value => value > 0);

                if (hasValueGreaterThanZero) {
                    // Update chart options dynamically
                    chart.options.scales.yAxes[0].ticks.max = dynamicMax;
                    chart.options.scales.yAxes[0].ticks.stepSize = stepSize;
                }
                
                chart.update(); // finally update our chart
            });
        }
    </script>
@stop
