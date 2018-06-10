@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Agreements</h4>




                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>

                        <tr>
                            <th>Agreement Id</th>
                            <th>Date Created</th>
                            <th>Instalments</th>
                            <th>First Payment</th   >
                            <th>First Payment Date</th>
                            <th>Cancellation </th>
                            <th>Renewal</th>
                            <th>Status</th>
                        </tr>

                        </thead>

                        <tbody>

                        @foreach($agreements as $agreement)

                            <tr>
                                <td>{{$agreement->merchant_slug}}</td>
                                <td>{{ \Carbon\Carbon::parse($agreement->created_at)->toFormattedDateString()  }}</td>
                                <td>{{$agreement->instalments}}</td>
                                <td>{{$agreement->currency_code}} {{$agreement->first_payment/100}}</td>
                                <td>{{\Carbon\Carbon::parse($agreement->first_payment_date)->toFormattedDateString() }}</td>
                                <td>
                                    @if($agreement->can_cancel)
                                        Yes
                                    @else
                                        No
                                    @endif
                                </td>
                                <td>
                                    @if($agreement->renewal)
                                        Yes
                                    @else
                                        No
                                    @endif

                                </td>

                                <td>

                                    @if($agreement->status==null)
                                        On going
                                    @elseif($agreement->status=="1")
                                        On going
                                    @elseif($agreement->status=="10")
                                        Archived
                                    @elseif($agreement->status=="0")
                                        Agreement Completed
                                    @elseif($agreement->status=="2")
                                        Agreement Cancelled
                                    @elseif($agreement->status=="-1")
                                        Payment Info not added
                                    @elseif($agreement->status=="-2")
                                        Payment Failed
                                    @endif

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