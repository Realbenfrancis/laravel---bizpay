@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Subscriptions</h4>



                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Subscribed By</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Merchant</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($subscriptions as $subscription)

                            <tr>
                                <td>{{\App\User::findorFail($subscription->user_id)->name}}</td>
                                <td>{{\Carbon\Carbon::createFromTimestamp($subscription->start_date)->toDateString()}}</td>
                                <td>{{\Carbon\Carbon::createFromTimestamp($subscription->end_date)->toDateString()}}</td>
                                <td>
                                    {{ \App\Http\Models\Merchant::findorFail($subscription->merchant_id)->merchant_name}}
                                </td>
                                <td>
                                    <form method="post" action="cancel-subscription">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$subscription->subscription_id}}">
                                        <button type="submit" class="btn btn-primary">Cancel Subscription</button>
                                    </form>
                                </td>

                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

@endsection