@extends('layouts.dashboard')


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Business Intelligence</h4>

                    <form method="get" action="">

                        <label for="">Merchant</label>


                        <select name="merchant-id">
                            @foreach($merchants as $merchant)
                                <option value="{{$merchant->id}}" @if(request()->has('merchant-id')) @if($merchant->id==request()->get('merchant-id')) selected   @endif @endif>{{$merchant->merchant_name}}</option>
                            @endforeach
                        </select>


                        <input type="submit" value="Fetch">

                    </form>


                    @if(request()->has('merchant-id'))

                        <div class="row">
                            <div class="container">

                                <div class="panel-body">

                                <div class="col-sm-6 col-lg-3">
                                    <div class="panel text-center">
                                        <div class="panel-heading">
                                            <h4 class="panel-title text-muted font-light">No. of Products</h4>
                                        </div>
                                        <div class="panel-body p-t-10">
                                            <h2 class="m-t-0 m-b-15"><i class="mdi  text-danger m-r-10"></i><b>{{$productCount}}</b></h2>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3">
                                    <div class="panel text-center">
                                        <div class="panel-heading">
                                            <h4 class="panel-title text-muted font-light">No. of Agreements</h4>
                                        </div>
                                        <div class="panel-body p-t-10">
                                            <h2 class="m-t-0 m-b-15"><i class="mdi  text-danger m-r-10"></i><b>{{$agreementCount}}</b></h2>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6 col-lg-3">
                                    <div class="panel text-center">
                                        <div class="panel-heading">
                                            <h4 class="panel-title text-muted font-light">Best Selling Product</h4>
                                        </div>
                                        <div class="panel-body p-t-10">
                                            <h2 class="m-t-0 m-b-15"><i class="mdi  text-danger m-r-10"></i><b>
                                                    @if(count($product)>0)
                                                        {{$product->name}}
                                                    @endif
                                                </b></h2>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6 col-lg-3">
                                    <div class="panel text-center">
                                        <div class="panel-heading">
                                            <h4 class="panel-title text-muted font-light">Best Selling Quote</h4>
                                        </div>
                                        <div class="panel-body p-t-10">
                                            <h2 class="m-t-0 m-b-15"><i class="mdi  text-danger m-r-10"></i><b>
                                                    @if(count($quote)>0)
                                                        {{$quote->name}}
                                                    @endif
                                                </b></h2>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6 col-lg-3">
                                    <div class="panel text-center">
                                        <div class="panel-heading">
                                            <h4 class="panel-title text-muted font-light">Average API Response Rate</h4>
                                        </div>
                                        <div class="panel-body p-t-10">
                                            <h2 class="m-t-0 m-b-15"><i class="mdi  text-danger m-r-10"></i><b>

                                                    @if(is_numeric($avgResponseTime))

                                                    {{number_format((float)$avgResponseTime, 4, '.', '') }}

                                                        @else

                                                        0

                                                    @endif

                                                    s</b></h2>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>



                        @endif


                    <div class="container">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="panel panel-default">




                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>

@endsection