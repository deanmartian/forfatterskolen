@extends('frontend.layout')

@section('content')
<div class="manuscript-page">
    <div class="container main-container">
        <div class="row">
            <div class="col-md-6">
                <form action="" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>
                            Months
                        </label>
                        <input type="text" name="payment_plan_in_months" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>
                            Date
                        </label>
                        <input type="date" name="date" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>
                            Issue Date
                        </label>
                        <input type="date" name="issueDate" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>
                            amount
                        </label>
                        <input type="text" name="amount" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>
                            user id
                        </label>
                        <input type="text" name="user_id" class="form-control">
                    </div>
                    
                    <button type="submit">
                        submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
