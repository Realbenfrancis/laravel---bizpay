@extends('layouts.dashboard')

@section('content')

    <div class="row">
        <h2 style="color: white;">Merchants</h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-b-30 m-t-0"></h4>

                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Merchant Name</th>
                            <th>Order </th>
                            <th>Amount </th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($payments as $payment)

                            <tr>
                                <td>{{$payment->user_id}}</td>
                                <td>{{$payment->merchant_id}}</td>
                                <td>{{$payment->order_id}}</td>
                                <td>{{$payment->amount}}</td>

                                <td>
                                    {{$payment->created_at}}

                                </td>


                                <td>
                                    {{$payment->status_text}}

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