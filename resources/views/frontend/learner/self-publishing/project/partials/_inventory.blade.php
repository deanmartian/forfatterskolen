<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.total-sold-books') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSold }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.total-sales') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="{{ $totalBookSale }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.inventory-text') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>{{ trans('site.front.total') }}</td>
                                <td>{{ ucfirst(trans('site.learner.delivered-text')) }}</td>
                                <td>{{ trans('site.author-portal.physical-items') }}</td>
                                <td>{{ trans('site.author-portal.returns') }}</td>
                                <td>{{ trans('site.balance') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{ $inventoryDelivered + $inventoryPhysicalItems + $inventoryReturns  }}
                                </td>
                                <td>
                                    {{ $inventoryDelivered }}
                                </td>
                                <td>
                                    {{ $inventoryPhysicalItems }}
                                </td>
                                <td>{{ $inventoryReturns }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.order-text') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.reservations') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control"
                        value="" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <form action="{{ request()->url() }}" id="inventory-form" method="GET" class="w-100">
                    <input type="hidden" name="tab" value="inventory">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('site.year') }}</label>
                            <select name="year" id="inventory-year-selector" class="form-control inventory-selector">
                                <option value="all">All</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" 
                                    {{ request('year') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('site.learner.month-text') }}</label>
                            <select name="month" id="inventory-month-selector" class="form-control inventory-selector">
                                <option value="all">All</option>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}"
                                    {{ request('month') == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromFormat('!m', $month)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
                
                <table class="table">
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ trans('site.front.total') }}</td>
                        </tr>

                        @foreach ($yearlyData as $yearly)
                            <tr>
                                <td>
                                    {{ $yearly['name'] }}
                                </td>
                                <td>
                                    {{ $yearly['value'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div> <!-- col-md-6 -->
    </div>
</div>