
@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Select Instalment</h4>


                    <div class="panel">
                        <div class="panel-body user-card">
                            <div class="media-main">
                                <a class="pull-left" href="#">
                                    <img class="thumb-lg img-circle" src="" alt="">
                                </a>
                                <div class="info">
                                    <h4>{{$product->name}}</h4>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        </div>
                    </div>


                    <?php

                        $i=1;

                    ?>


                    <div class="row">

                    @foreach($plans as $plan)

                        <?php

                                $str="plan_".$i;
                            $plan=($plan[$str]);

                                ?>


                        <div class="col-md-4">

                        <div class="panel panel-primary">
                            <div class="panel-body">

                                <h4 class="m-b-30 m-t-0">Plan #{{$i}}</h4>

                                <form method="post" action="/client/order-plan">

                                    {{csrf_field()}}

                                    No Installments: {{$plan->instalments}} <br>
                                    Initial Payment Amount: {{$product->currency_code}} {{$plan->initial_payment_amount/100}} <br>
                                    Recurring Payment Amount: {{$product->currency_code}} {{$plan->recurring_payment_amount/100}} <br>

                                    <br>

                                    <input type="hidden" name="plan" value="{{$i}}">
                                    <input type="hidden" name="id" value="{{$product->product_id}}">

                                    <button type="submit" class="btn btn-primary">Select Plan</button>

                                </form>

                            </div> <!-- panel-body -->
                        </div>
                        </div>



                        <?php $i++; ?>

                    @endforeach
                    </div>

                </div>

            </div>

        </div>

    </div>


@endsection
