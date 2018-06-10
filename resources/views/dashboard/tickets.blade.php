@extends('layouts.dashboard')




@section('content')


    <div class="content-page">
        <div class="content">

            <div class="">
                <div class="page-header-title">
                    <h4 class="page-title">Tickets</h4>
                </div>
            </div>

            <div class="page-content-wrapper ">

                <div class="container">


                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h4 class="m-b-30 m-t-0"></h4>

                                    <table id="datatable-buttons" class="table table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>price</th>
                                            <th>Adjusted Price</th>
                                            <th>Credit Offered</th>
                                            <th>Balance</th>
                                            <th>Airline Ticket No</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                        @foreach($tickets as $ticket)

                                        <tr>
                                        <td>{{$ticket['ticket_id']}}</td>
                                        <td>{{$ticket['price']/100}}</td>
                                        <td>{{$ticket['adjusted_price']/100}}</td>
                                        <td>{{$ticket['credit_offered']/100}}</td>
                                        <td>{{$ticket['balance']/100}}</td>
                                        <td>{{$ticket['airline_ticket_id']}}</td>

                                        <td>

                                        @if($ticket['status'])

                                        Active

                                        @else

                                        Cancelled

                                        @endif


                                        </td>

                                        <td>


                                            @if($ticket['status'])

                                                @if($ticket['balance']>0)

                                        <form method="post" action="/make-payment">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$ticket['ticket_id']}}">
                                        <button class="btn btn-success waves-effect waves-light m-l-10">Make Payment</button>
                                        </form>
                                        <form method="post" action="/payment-failed">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$ticket['ticket_id']}}">
                                        <button class="btn btn-dark waves-effect waves-light m-l-10">Failed Payment</button>
                                        </form>
                                        <form method="post" action="/cancel-ticket">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$ticket['ticket_id']}}">
                                        <button class="btn btn-danger waves-effect waves-light m-l-10">Cancel Ticket</button></td>
                                        </form>

                                            @else
                                                Fully Paid!
                                            @endif

                                            @else

                                                Cancelled Ticket

                                            @endif

                                        </tr>

                                        @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>

            </div>

        </div>

        <footer class="footer">
            © 2017 Bizpay - All Rights Reserved.
        </footer>

    </div>

    </div>


@endsection

