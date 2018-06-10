@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Payments</h4>



                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Paid By</th>
                            <th>Merchant</th>
                            <th>Amount</th>
                            <th>Date</th>
                            @if(\Illuminate\Support\Facades\Auth::user()->user_type==1)

                             <th>Action</th>

                                @endif

                        </tr>
                        </thead>

                        <tbody>

                        @foreach($payments as $payment)

                            <tr>
                                <td>{{\App\User::findorFail($payment->user_id)->name}}</td>
                                <td>{{\App\Http\Models\Merchant::findorFail($payment->merchant_id)->merchant_name}}</td>

                                <td>
                                    {{$payment->currency_code}}
                                    {{$payment->amount/100}}
                                </td>

                                <td>
                                    {{\Carbon\Carbon::parse($payment->created_at)->diffForHumans()}}
                                </td>

                                @if(\Illuminate\Support\Facades\Auth::user()->user_type==1)



                                 <td>

                                     @if(strlen($payment->charge_id)>3)

                                         <form method="post" action="/merchant-admin/refund">
                                             {{csrf_field()}}
                                             <input type="hidden" name="id" value="{{$payment->charge_id}}">
                                             <button type="submit" class="btn btn-primary">Refund</button>
                                         </form>



                                     @endif
                                 </td>



                                @endif


                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

@endsection