@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Orders</h4>


                    <?php

                        $tax=  \App\Http\Models\Merchant::findorFail(\Illuminate\Support\Facades\Auth::user()->merchant_id)->tax; ;



                        ?>

                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Ordered By</th>
                            <th>Currency </th>
                            <th>Price</th>
                            <th>Tax</th>
                            <th>Adjusted Price (including tax)</th>
                            <th>Initial Payment</th>
                            <th>Balance</th>
                            <th>Installments</th>
                            <th>Merchant</th>

                        </tr>
                        </thead>

                        <tbody>

                        @foreach($orders as $order)

                            <tr>
                                <td>{{\App\User::findorFail($order->user_id)->name}}</td>
                                <td>{{$order->currency_code}}</td>
                                <td> {{$order->price}}</td>
                                <td>{{$order->price*$tax/100}}</td>
                                <td>{{$order->adjusted_price}}</td>
                                <td>{{$order->initial_payment}}</td>
                                <td>{{$order->balance}}</td>
                                <td>
                                    @if($order->installments==-1)
                                        Subscription
                                    @elseif($order->installments==1)
                                        One off purchase
                                    @else
                                    {{$order->installments}}
                                        @endif
                                </td>

                                <td>
                                    {{ \App\Http\Models\Merchant::findorFail($order->merchant_id)->merchant_name}}
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